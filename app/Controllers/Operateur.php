<?php

namespace App\Controllers;
use App\Models\CompteOperateurModel;
use App\Models\CompteClientModel;
use App\Models\OperateurPrefixeModel;
use App\Models\OperateurModel;
use App\Models\ConfigurationCommissionModel;
use App\Models\OperateurTarifModel;
use App\Models\HistoriqueTransactionModel;
use App\Models\BaremeFraisModel;
use App\Models\TypeOperationModel;

class Operateur extends BaseController
{
    public function login(): string
    {
        return view('operateur/login');
    }
    public function authenticate()
    {
        $model = new CompteOperateurModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $operateur = $model->where('email', $email)->first();

        if ($operateur && $password==$operateur['mot_de_passe']) {
            session()->set('operateur_name', $operateur['username']);
            session()->set('operateur_email', $operateur['email']);
            session()->set('logged_in', true);
            return redirect()->to('/operateur/gains');
        }

        return redirect()->to('/operateur/login')->with('error', 'Email ou mot de passe incorrect');
    }
    public function comptes(): string
    {
        $model = new CompteClientModel();
        
        // Recherche
        $search = $this->request->getVar('search');
        
        // Calculer les statistiques
        if ($search) {
            $model->like('numero_telephone', $search);
            $totalComptes = $model->countAllResults(false);
        } else {
            $totalComptes = $model->countAll();
        }
        
        $allComptes = $model->findAll();
        $soldeTotal = 0;
        foreach ($allComptes as $compte) {
            $soldeTotal += $compte['solde'];
        }
        $soldeMoyen = $totalComptes > 0 ? $soldeTotal / $totalComptes : 0;
        
        // Pagination
        $pager = \Config\Services::pager();
        $perPage = 10;
        $page = $this->request->getVar('page') ?? 1;
        
        if ($search) {
            $model->like('numero_telephone', $search);
        }
        
        $comptes = $model->paginate($perPage, 'default', $page);
        $pagerLinks = $model->pager->links();
        
        $data = [
            'comptes' => $comptes,
            'pager' => $pagerLinks,
            'search' => $search,
            'stats' => [
                'nombre_comptes' => $totalComptes,
                'solde_total' => number_format($soldeTotal, 0, ',', ' '),
                'solde_moyen' => number_format($soldeMoyen, 0, ',', ' ')
            ]
        ];
        
        return view('operateur/comptes', $data);
    }

    public function gains(): string
    {
        $historiqueModel = new HistoriqueTransactionModel();
        
        // Récupérer les gains séparés par réseau (interne vs externe)
        $gainsSepares = $historiqueModel->obtenirGainsSepares();
        
        // Récupérer le détail des transactions avec pagination
        $pager = \Config\Services::pager();
        $perPage = 10;
        $page = $this->request->getVar('page') ?? 1;
        $filter = $this->request->getVar('filter') ?? 'all';
        
        $query = $historiqueModel->select("historique_transactions.*, (historique_transactions.frais_bareme + historique_transactions.frais_commission) as frais_percus, types_operations.code as type_code, types_operations.nom as type_nom, cs.numero_telephone as compte_source_numero, COALESCE(historique_transactions.numero_destinataire, cd.numero_telephone) as numero_destinataire_affiche, CASE WHEN types_operations.code != 'TRANSFERT' THEN 'Notre Réseau' WHEN od.est_interne = 1 THEN 'Notre Réseau' WHEN od.nom IS NOT NULL THEN 'Autres Opérateurs (' || od.nom || ')' ELSE 'Opérateur inconnu' END as reseau_concerne")
                                 ->join('types_operations', 'types_operations.id = historique_transactions.type_operation_id')
                                 ->join('comptes_clients cs', 'cs.id = historique_transactions.compte_source_id')
                                 ->join('comptes_clients cd', 'cd.id = historique_transactions.compte_destination_id', 'left')
                                 ->join('operateurs od', 'od.id = historique_transactions.operateur_destination_id', 'left')
                                 ->whereIn('types_operations.code', ['RETRAIT', 'TRANSFERT']);
        
        if ($filter !== 'all') {
            $query->where('types_operations.code', $filter);
        }
        
        $transactions = $query->orderBy('historique_transactions.effectue_le', 'DESC')
                            ->paginate($perPage, 'default', $page);
        $pagerLinks = $historiqueModel->pager->links();
        
        $data = [
            'stats' => [
                'interne' => [
                    'retrait' => number_format($gainsSepares['interne']['retrait'], 2, ',', ' '),
                    'transfert' => number_format($gainsSepares['interne']['transfert'], 2, ',', ' '),
                    'total' => number_format($gainsSepares['interne']['total'], 2, ',', ' ')
                ],
                'externe' => [
                    'transfert' => number_format($gainsSepares['externe']['transfert'], 2, ',', ' '),
                    'total' => number_format($gainsSepares['externe']['total'], 2, ',', ' '),
                    'details' => $gainsSepares['externe']['details']
                ],
                'total_cumule' => number_format($gainsSepares['interne']['total'] + $gainsSepares['externe']['total'], 2, ',', ' ')
            ],
            'transactions' => $transactions,
            'pager' => $pagerLinks,
            'filter' => $filter
        ];
        
        return view('operateur/gains', $data);
    }

    public function operations(): string
    {
        $typeOperationModel = new TypeOperationModel();
        
        // Récupérer les types d'opérations
        $typesOperations = $typeOperationModel->findAll();
        
        $data = [
            'types_operations' => $typesOperations
        ];
        
        return view('operateur/operations', $data);
    }

    public function addTypeOperation()
    {
        $typeOperationModel = new TypeOperationModel();
        
        $data = [
            'code' => strtoupper($this->request->getPost('code')),
            'nom' => $this->request->getPost('nom')
        ];
        
        if ($typeOperationModel->insert($data)) {
            return redirect()->to('/operateur/operations')->with('success', 'Type d\'opération ajouté avec succès');
        }
        
        return redirect()->to('/operateur/operations')->with('error', 'Erreur lors de l\'ajout du type d\'opération');
    }

    public function editTypeOperation($id)
    {
        $typeOperationModel = new TypeOperationModel();
        
        // Récupérer l'ancien type d'opération
        $oldType = $typeOperationModel->find($id);
        
        $newCode = strtoupper($this->request->getPost('code'));
        $newNom = $this->request->getPost('nom');
        
        // Si le code n'a pas changé, désactiver la validation pour éviter le conflit is_unique
        if ($oldType && $oldType['code'] === $newCode) {
            $typeOperationModel->skipValidation(true);
        }
        
        $data = [
            'code' => $newCode,
            'nom' => $newNom
        ];
        
        if ($typeOperationModel->update($id, $data)) {
            return redirect()->to('/operateur/operations')->with('success', 'Type d\'opération modifié avec succès');
        }
        
        return redirect()->to('/operateur/operations')->with('error', 'Erreur lors de la modification du type d\'opération');
    }

    public function bareme($typeOperationId)
    {
        $typeOperationModel = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();
        
        // Récupérer le type d'opération
        $typeOperation = $typeOperationModel->find($typeOperationId);
        
        if (!$typeOperation) {
            return redirect()->to('/operateur/operations')->with('error', 'Type d\'opération non trouvé');
        }
        
        // Récupérer les barèmes de frais pour ce type d'opération
        $baremes = $baremeModel->where('type_operation_id', $typeOperationId)->findAll();
        
        $data = [
            'type_operation' => $typeOperation,
            'baremes' => $baremes
        ];
        
        return view('operateur/bareme', $data);
    }

    public function addBareme()
    {
        $baremeModel = new BaremeFraisModel();
        
        $typeOperationId = $this->request->getPost('type_operation_id');
        
        $data = [
            'type_operation_id' => $typeOperationId,
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais')
        ];
        
        if ($baremeModel->insert($data)) {
            return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('success', 'Tranche de frais ajoutée avec succès');
        }
        
        return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('error', 'Erreur lors de l\'ajout de la tranche');
    }

    public function editBareme($id, $typeOperationId = null)
    {
        $baremeModel = new BaremeFraisModel();
        
        if ($typeOperationId === null) {
            $typeOperationId = $this->request->getPost('type_operation_id');
        }
        
        $data = [
            'type_operation_id' => $this->request->getPost('type_operation_id'),
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais')
        ];
        
        if ($baremeModel->update($id, $data)) {
            return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('success', 'Tranche de frais modifiée avec succès');
        }
        
        return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('error', 'Erreur lors de la modification de la tranche');
    }

    public function deleteBareme($id, $typeOperationId = null)
    {
        $baremeModel = new BaremeFraisModel();
        
        if ($typeOperationId === null) {
            $bareme = $baremeModel->find($id);
            $typeOperationId = $bareme['type_operation_id'];
        }
        
        if ($baremeModel->delete($id)) {
            return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('success', 'Tranche de frais supprimée avec succès');
        }
        
        return redirect()->to('/operateur/bareme/' . $typeOperationId)->with('error', 'Erreur lors de la suppression de la tranche');
    }

    public function prefixes(): string
    {
        $prefixeModel = new OperateurPrefixeModel();
        $operateurModel = new OperateurModel();
        
        // Récupérer tous les opérateurs
        $operateurs = $operateurModel->findAll();
        
        // Récupérer tous les préfixes avec jointure pour avoir le nom de l'opérateur
        $prefixes = $prefixeModel->select('operateur_prefixes.*, operateurs.nom as operateur_nom, operateurs.est_interne')
                                 ->join('operateurs', 'operateurs.id = operateur_prefixes.operateur_id')
                                 ->findAll();
        
        // Séparer les préfixes : interne vs externe
        $prefixesInternes = [];
        $prefixesExternes = [];
        
        foreach ($prefixes as $prefixe) {
            if ($prefixe['est_interne'] == 1) {
                $prefixesInternes[] = $prefixe;
            } else {
                $prefixesExternes[] = $prefixe;
            }
        }
        
        $data = [
            'prefixes_internes' => $prefixesInternes,
            'prefixes_externes' => $prefixesExternes,
            'operateurs' => $operateurs
        ];
        return view('operateur/prefixes', $data);
    }

    public function addPrefixe()
    {
        $model = new OperateurPrefixeModel();
        
        $data = [
            'operateur_id' => $this->request->getPost('operateur_id'),
            'prefixe' => $this->request->getPost('prefixe'),
            'statut' => $this->request->getPost('statut') ?? 'actif'
        ];
        
        if ($model->insert($data)) {
            return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe ajouté avec succès');
        }
        
        return redirect()->to('/operateur/prefixes')->with('error', 'Erreur lors de l\'ajout du préfixe');
    }

    public function editPrefixe($id)
    {
        $model = new OperateurPrefixeModel();
        
        // Récupérer l'ancien préfixe pour comparer
        $oldPrefixe = $model->find($id);
        
        $data = [
            'operateur_id' => $this->request->getPost('operateur_id'),
            'prefixe' => $this->request->getPost('prefixe'),
            'statut' => $this->request->getPost('statut')
        ];
        
        // Si le préfixe n'a pas changé, on ne vérifie pas l'unicité
        if ($oldPrefixe && $oldPrefixe['prefixe'] === $data['prefixe']) {
            $model->skipValidation(true);
        }
        
        if ($model->update($id, $data)) {
            return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe modifié avec succès');
        }
        
        return redirect()->to('/operateur/prefixes')->with('error', 'Erreur lors de la modification du préfixe');
    }

    public function deletePrefixe($id)
    {
        $model = new OperateurPrefixeModel();
        
        if ($model->delete($id)) {
            return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe supprimé avec succès');
        }
        
        return redirect()->to('/operateur/prefixes')->with('error', 'Erreur lors de la suppression du préfixe');
    }

    public function logout(){
        session()->destroy();
        return redirect()->to('/operateur/login');
    }

    public function commissions(): string
    {
        $commissionModel = new ConfigurationCommissionModel();
        $operateurModel = new OperateurModel();
        
        // Récupérer tous les opérateurs
        $operateurs = $operateurModel->findAll();
        
        // Récupérer toutes les configurations de commissions avec jointures
        $commissions = $commissionModel->select('configuration_commissions.*, 
                                                    os.nom as operateur_source_nom, 
                                                    od.nom as operateur_destination_nom')
                                         ->join('operateurs os', 'os.id = configuration_commissions.operateur_source_id')
                                         ->join('operateurs od', 'od.id = configuration_commissions.operateur_destination_id')
                                         ->findAll();
        
        $data = [
            'commissions' => $commissions,
            'operateurs' => $operateurs
        ];
        return view('operateur/commission', $data);
    }

    public function montant(): string
    {
        $historiqueModel = new HistoriqueTransactionModel();
        
        // Récupérer les montants transférés par opérateur de destination
        $montantsParOperateur = $historiqueModel->obtenirMontantsParOperateur();
        
        // Calculer le total global
        $totalGlobal = 0;
        foreach ($montantsParOperateur as $montant) {
            $totalGlobal += (float) ($montant['total_a_envoyer'] ?? 0);
        }
        
        $data = [
            'montants' => $montantsParOperateur,
            'total_global' => $totalGlobal
        ];
        return view('operateur/montant', $data);
    }

    public function addCommission()
    {
        $model = new ConfigurationCommissionModel();
        
        $operateurSourceId = $this->request->getPost('operateur_source_id');
        $operateurDestinationId = $this->request->getPost('operateur_destination_id');
        
        // Vérifier que les opérateurs sont différents
        if ($operateurSourceId == $operateurDestinationId) {
            return redirect()->to('/operateur/commissions')->with('error', 'L\'opérateur destination doit être différent de l\'opérateur source');
        }
        
        $data = [
            'operateur_source_id' => $operateurSourceId,
            'operateur_destination_id' => $operateurDestinationId,
            'pourcentage_commission' => $this->request->getPost('pourcentage_commission')
        ];
        
        if ($model->insert($data)) {
            return redirect()->to('/operateur/commissions')->with('success', 'Commission ajoutée avec succès');
        }
        
        return redirect()->to('/operateur/commissions')->with('error', 'Erreur lors de l\'ajout de la commission');
    }

    public function editCommission($id)
    {
        $model = new ConfigurationCommissionModel();
        
        // Récupérer l'ancienne commission pour comparer
        $oldCommission = $model->find($id);
        
        $data = [
            'operateur_source_id' => $this->request->getPost('operateur_source_id'),
            'operateur_destination_id' => $this->request->getPost('operateur_destination_id'),
            'pourcentage_commission' => $this->request->getPost('pourcentage_commission')
        ];
        
        // Si les opérateurs n'ont pas changé, on ne vérifie pas l'unicité
        if ($oldCommission && 
            $oldCommission['operateur_source_id'] == $data['operateur_source_id'] && 
            $oldCommission['operateur_destination_id'] == $data['operateur_destination_id']) {
            $model->skipValidation(true);
        }
        
        if ($model->update($id, $data)) {
            return redirect()->to('/operateur/commissions')->with('success', 'Commission modifiée avec succès');
        }
        
        return redirect()->to('/operateur/commissions')->with('error', 'Erreur lors de la modification de la commission');
    }

    public function deleteCommission($id)
    {
        $model = new ConfigurationCommissionModel();
        
        if ($model->delete($id)) {
            return redirect()->to('/operateur/commissions')->with('success', 'Commission supprimée avec succès');
        }
        
        return redirect()->to('/operateur/commissions')->with('error', 'Erreur lors de la suppression de la commission');
    }
}
