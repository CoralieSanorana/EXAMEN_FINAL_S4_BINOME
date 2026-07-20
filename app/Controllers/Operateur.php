<?php

namespace App\Controllers;
use App\Models\CompteOperateurModel;
use App\Models\CompteClientModel;
use App\Models\OperateurPrefixeModel;
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
            session()->set('connecter', true);
            return redirect()->to('/operateur/prefixes');
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
        $compteModel = new CompteClientModel();
        
        // Récupérer les statistiques de gains depuis la vue
        $statsGains = $historiqueModel->obtenirSituationGains();
        
        // Calculer les totaux
        $gainsRetrait = 0;
        $gainsTransfert = 0;
        $totalCumule = 0;
        
        foreach ($statsGains as $stat) {
            if ($stat['type_operation'] === 'RETRAIT') {
                $gainsRetrait = $stat['total_gains_frais'];
            } elseif ($stat['type_operation'] === 'TRANSFERT') {
                $gainsTransfert = $stat['total_gains_frais'];
            }
        }
        $totalCumule = $gainsRetrait + $gainsTransfert;
        
        // Récupérer le détail des transactions avec pagination
        $pager = \Config\Services::pager();
        $perPage = 10;
        $page = $this->request->getVar('page') ?? 1;
        
        $transactions = $historiqueModel->select('historique_transactions.*, types_operations.code as type_code, types_operations.nom as type_nom, cs.numero_telephone as compte_source_numero')
                                       ->join('types_operations', 'types_operations.id = historique_transactions.type_operation_id')
                                       ->join('comptes_clients cs', 'cs.id = historique_transactions.compte_source_id')
                                       ->whereIn('types_operations.code', ['RETRAIT', 'TRANSFERT'])
                                       ->orderBy('historique_transactions.effectue_le', 'DESC')
                                       ->paginate($perPage, 'default', $page);
        $pagerLinks = $historiqueModel->pager->links();
        
        $data = [
            'stats' => [
                'gains_retrait' => number_format($gainsRetrait, 0, ',', ' '),
                'gains_transfert' => number_format($gainsTransfert, 0, ',', ' '),
                'total_cumule' => number_format($totalCumule, 0, ',', ' ')
            ],
            'transactions' => $transactions,
            'pager' => $pagerLinks
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

    public function bareme($typeOperationId): string
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
        $model = new OperateurPrefixeModel();
        $prefixes = $model->findAll();
        $data = [
            'prefixes' => $prefixes
        ];
        return view('operateur/prefixes', $data);
    }

    public function addPrefixe()
    {
        $model = new OperateurPrefixeModel();
        
        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'libelle' => $this->request->getPost('libelle'),
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
            'prefixe' => $this->request->getPost('prefixe'),
            'libelle' => $this->request->getPost('libelle'),
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
}
