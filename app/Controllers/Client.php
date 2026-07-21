<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\HistoriqueTransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\OperateurPrefixeModel;
use App\Models\ConfigurationCommissionModel;
use App\Models\CommissionReductionModel;
use App\Models\EpargnesModel;

class Client extends BaseController
{
    protected $session;
    protected $compteModel;
    protected $historiqueModel;
    protected $typeOperationModel;
    protected $baremeFraisModel;
    protected $operateurPrefixeModel;
    protected $configurationCommissionModel;
    protected $commissionReductionModel;


    public function __construct()
    {
        $this->session = session();
        $this->compteModel = new CompteClientModel();
        $this->historiqueModel = new HistoriqueTransactionModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->operateurPrefixeModel = new OperateurPrefixeModel();
        $this->configurationCommissionModel = new ConfigurationCommissionModel();
        $this->commissionReductionModel = new CommissionReductionModel();
    }
    public function epargnes()
    {
        $this->checkAuth();
        $id_client = $this->session->get('client_id');
        $model = new EpargnesModel();
        $data['epargnes'] = $model->where('id_clients', $id_client);
        return view('client/epargens',$data);
    }
    public function InsertEpargnes()
    {
         
        $this->checkAuth();
        $clientId = $this->session->get('client_id');
        $epargnes = $this->request->getPost('epargnes');
        // regarder si il y a deja un epargne
        $epargnesModel = new EpargnesModel();
 
        if ($epargnesModel->find()->where('', $clientId)->count() > 0) {
            $epargnesModel->where('id_clients', $clientId)->update(['pourcentage'=>$epargnes]);
        } else { 
            $epargnesModel->insert(['id_clients'=>$clientId,
            'pourcentage'=>$epargnes]);
        }
    return redirect()->to('/client/pargens');


    }
    public function index()
    {
        // Redirect to login as default
        return redirect()->to('/client/login');
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('client_id')) {
            return redirect()->to('/client/solde');
        }

        return view('client/login');
    }

    public function authenticate()
    {
        $telephone = $this->request->getPost('telephone');
        
        // Clean phone number (remove spaces and special characters)
        $telephone = preg_replace('/[\s\-\.]/', '', $telephone);
        
        // Validate phone number format (8-10 digits)
        if (!$telephone || !preg_match('/^[0-9]{8,10}$/', $telephone)) {
            return redirect()->back()->with('error', 'Numéro de téléphone invalide')->withInput();
        }

        // Check if account exists
        $compte = $this->compteModel->where('numero_telephone', $telephone)->first();

        if ($compte) {
            // Set session data with nom and prenom
            $this->session->set([
                'client_id' => $compte['id'],
                'client_telephone' => $compte['numero_telephone'],
                'client_solde' => $compte['solde'],
                'client_nom' => $compte['nom'] ?? '',
                'client_prenom' => $compte['prenom'] ?? '',
                'logged_in' => true
            ]);

            return redirect()->to('/client/solde')->with('success', 'Bienvenue ' . ($compte['prenom'] ?? '') . ' !');
        } else {
            // Auto-create account if not found
            try {
                $newAccountId = $this->compteModel->insert([
                    'numero_telephone' => $telephone,
                    'solde' => 0,
                    'nom' => null,
                    'prenom' => null
                ]);

                // Set session data for new account
                $this->session->set([
                    'client_id' => $newAccountId,
                    'client_telephone' => $telephone,
                    'client_solde' => 0,
                    'client_nom' => '',
                    'client_prenom' => '',
                    'logged_in' => true
                ]);

                return redirect()->to('/client/solde')->with('success', 'Compte créé automatiquement ! Bienvenue sur MobileMoney.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Erreur lors de la création du compte: ' . $e->getMessage())->withInput();
            }
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/client/login')->with('success', 'Déconnexion réussie');
    }

    protected function checkAuth()
    {
        if (!$this->session->get('client_id')) {
            return redirect()->to('/client/login');
        }
    }

    public function depot(): string
    {
        $this->checkAuth();
        return view('client/depot');
    }

    public function processDepot()
    {
        $this->checkAuth();
        
        $montant = $this->request->getPost('montant');
        $clientId = $this->session->get('client_id');
        
        // Validate amount
        if (!$montant || !is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }
        
        $montant = floatval($montant);
        
        // Get current account
        $compte = $this->compteModel->find($clientId);
        if (!$compte) {
            return redirect()->back()->with('error', 'Compte introuvable');
        }
        
        // Get DEPOT operation type
        $typeDepot = $this->typeOperationModel->where('code', 'DEPOT')->first();
        if (!$typeDepot) {
            return redirect()->back()->with('error', 'Type d\'opération dépôt non configuré');
        }
        
        // Deposit has no fees
        $fraisAppliques = 0;
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Update account balance
            $nouveauSolde = $compte['solde'] + $montant;
            $this->compteModel->update($clientId, ['solde' => $nouveauSolde]);
            
            // Insert transaction history
            $this->historiqueModel->insert([
                'type_operation_id' => $typeDepot['id'],
                'compte_source_id' => $clientId,
                'numero_destinataire' => null,
                'compte_destination_id' => null,
                'operateur_destination_id' => null,
                'montant' => $montant,
                'frais_bareme' => $fraisAppliques,
                'frais_commission' => 0,
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du traitement du dépôt');
            }
            
            // Update session with new balance
            $this->session->set('client_solde', $nouveauSolde);
            
            return redirect()->to('/client/solde')->with('success', 'Dépôt de ' . number_format($montant, 0, '.', ' ') . ' Ar effectué avec succès (Frais: 0 Ar)');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function historique(): string
    {
        $this->checkAuth();
        
        // Get pagination parameters
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $filter = $this->request->getGet('filter') ?? 'all';
        
        // Get client's transaction history using the view
        $clientId = $this->session->get('client_id');
        $historique = $this->historiqueModel->obtenirHistoriqueClientVue($clientId);
        
        // Apply filter
        if ($filter !== 'all') {
            $historique = array_filter($historique, function($transaction) use ($filter) {
                if ($filter === 'transfert_recu') {
                    return $transaction['type_code'] === 'TRANSFERT_RECU';
                }
                return $transaction['type_code'] === strtoupper($filter);
            });
        }
        
        // Pagination
        $totalTransactions = count($historique);
        $totalPages = ceil($totalTransactions / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedHistorique = array_slice($historique, $offset, $perPage);
        
        $data = [
            'historique' => $paginatedHistorique,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTransactions' => $totalTransactions,
            'perPage' => $perPage,
            'filter' => $filter
        ];
        
        return view('client/historique', $data);
    }

    public function retrait(): string
    {
        $this->checkAuth();
        return view('client/retrait');
    }

    public function processRetrait()
    {
        $this->checkAuth();
        
        $montant = $this->request->getPost('montant');
        $clientId = $this->session->get('client_id');
        
        // Validate amount
        if (!$montant || !is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }
        
        $montant = floatval($montant);
        
        // Get current account
        $compte = $this->compteModel->find($clientId);
        if (!$compte) {
            return redirect()->back()->with('error', 'Compte introuvable');
        }
        
        // Check if balance is sufficient
        if ($compte['solde'] < $montant) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer ce retrait');
        }
        
        // Get RETRAIT operation type
        $typeRetrait = $this->typeOperationModel->where('code', 'RETRAIT')->first();
        if (!$typeRetrait) {
            return redirect()->back()->with('error', 'Type d\'opération retrait non configuré');
        }
        
        // Calculate fees
        $frais = $this->baremeFraisModel->trouverFrais($typeRetrait['id'], $montant);
        $fraisAppliques = $frais ? $frais['frais'] : 0;
        $totalDebit = $montant + $fraisAppliques;
        
        // Check if balance is sufficient including fees
        if ($compte['solde'] < $totalDebit) {
            return redirect()->back()->with('error', 'Solde insuffisant (incluant les frais de ' . number_format($fraisAppliques, 0, '.', ' ') . ' Ar)');
        }
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Update account balance
            $nouveauSolde = $compte['solde'] - $totalDebit;
            $this->compteModel->update($clientId, ['solde' => $nouveauSolde]);
            
            // Insert transaction history
            $this->historiqueModel->insert([
                'type_operation_id' => $typeRetrait['id'],
                'compte_source_id' => $clientId,
                'numero_destinataire' => null,
                'compte_destination_id' => null,
                'operateur_destination_id' => null,
                'montant' => $montant,
                'frais_bareme' => $fraisAppliques,
                'frais_commission' => 0,
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du traitement du retrait');
            }
            
            // Update session with new balance
            $this->session->set('client_solde', $nouveauSolde);
            
            return redirect()->to('/client/solde')->with('success', 'Retrait de ' . number_format($montant, 0, '.', ' ') . ' Ar effectué avec succès (Frais: ' . number_format($fraisAppliques, 0, '.', ' ') . ' Ar)');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function solde(): string
    {
        $this->checkAuth();
        
        // Update session with current balance
        $compte = $this->compteModel->find($this->session->get('client_id'));
        if ($compte) {
            $this->session->set('client_solde', $compte['solde']);
        }
        
        // Get pagination parameters
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 5;
        $filter = $this->request->getGet('filter') ?? 'all';
        $showAll = $this->request->getGet('show_all') ?? false;
        
        // If show all, increase per page
        if ($showAll) {
            $perPage = 1000;
        }
        
        // Get client's transaction history using the view
        $clientId = $this->session->get('client_id');
        $historique = $this->historiqueModel->obtenirHistoriqueClientVue($clientId);
        
        // Apply filter
        if ($filter !== 'all') {
            $historique = array_filter($historique, function($transaction) use ($filter) {
                if ($filter === 'transfert_recu') {
                    return $transaction['type_code'] === 'TRANSFERT_RECU';
                }
                return $transaction['type_code'] === strtoupper($filter);
            });
        }
        
        // Pagination
        $totalTransactions = count($historique);
        $totalPages = ceil($totalTransactions / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedHistorique = array_slice($historique, $offset, $perPage);
        
        $data = [
            'historique' => $paginatedHistorique,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTransactions' => $totalTransactions,
            'perPage' => $perPage,
            'filter' => $filter,
            'showAll' => $showAll
        ];
        
        return view('client/solde', $data);
    }

    public function transfert(): string
    {
        $this->checkAuth();

        $prefixesOperateurs = $this->operateurPrefixeModel
            ->select('operateur_prefixes.prefixe, operateur_prefixes.operateur_id, operateurs.nom as operateur_nom')
            ->join('operateurs', 'operateurs.id = operateur_prefixes.operateur_id')
            ->where('operateur_prefixes.statut', 'actif')
            ->findAll();

        return view('client/transfert', [
            'prefixesOperateurs' => $prefixesOperateurs,
        ]);
    }

    public function rechercherClient()
    {
        $this->checkAuth();
        
        $numero = $this->request->getGet('numero');
        $clientId = $this->session->get('client_id');
        
        if (!$numero) {
            return $this->response->setJSON(['success' => false, 'message' => 'Numéro requis']);
        }
        
        // Search for client by phone number (excluding current user)
        $client = $this->compteModel->where('numero_telephone', $numero)
                                     ->where('id !=', $clientId)
                                     ->first();
        
        if ($client) {
            return $this->response->setJSON([
                'success' => true,
                'client' => [
                    'id' => $client['id'],
                    'nom' => $client['nom'],
                    'prenom' => $client['prenom'],
                    'numero_telephone' => $client['numero_telephone']
                ]
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Client introuvable']);
        }
    }

    public function processTransfert()
    {
        $this->checkAuth();

        $montant = $this->request->getPost('montant');
        $saisieDestinataires = $this->request->getPost('numero_destinataires');
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');
        $clientId = $this->session->get('client_id');

        if (!$montant || !is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide');
        }

        $montant = (float) $montant;
        $numerosDestinataires = $this->extraireNumerosDestinataires($saisieDestinataires);

        if (empty($numerosDestinataires)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez saisir au moins un numéro destinataire');
        }

        $compte = $this->compteModel->find($clientId);
        if (!$compte) {
            return redirect()->back()->withInput()->with('error', 'Compte introuvable');
        }

        $numeroEmetteur = $this->normaliserNumeroTelephone($compte['numero_telephone'] ?? '');
        $regexNumero = '/^[0-9]{8,10}$/';
        $erreursNumeros = [];

        $compteurs = array_count_values($numerosDestinataires);
        $doublons = array_keys(array_filter($compteurs, static fn ($total) => $total > 1));
        if (!empty($doublons)) {
            $erreursNumeros[] = 'Numéros en doublon : ' . implode(', ', $doublons);
        }

        foreach ($numerosDestinataires as $numero) {
            if (!preg_match($regexNumero, $numero)) {
                $erreursNumeros[] = 'Format invalide : ' . $numero;
                continue;
            }

            if ($numero === $numeroEmetteur) {
                $erreursNumeros[] = 'Vous ne pouvez pas vous transférer à vous-même : ' . $numero;
            }
        }

        if (!empty($erreursNumeros)) {
            return redirect()->back()->withInput()->with('error', implode(' | ', array_unique($erreursNumeros)));
        }

        $numerosUniques = array_values(array_unique($numerosDestinataires));
        $nombreDestinataires = count($numerosUniques);

        if ($nombreDestinataires > 1) {
            $operateursDestinataires = [];
            $nomsOperateurs = [];
            $numerosSansOperateur = [];

            foreach ($numerosUniques as $numero) {
                $operateur = $this->operateurPrefixeModel->trouverOperateurParNumero($numero);

                if (!$operateur) {
                    $numerosSansOperateur[] = $numero;
                    continue;
                }

                $operateursDestinataires[$numero] = (int) $operateur['operateur_id'];
                $nomsOperateurs[(int) $operateur['operateur_id']] = $operateur['operateur_nom'] ?? ('Préfixe ' . $operateur['prefixe']);
            }

            if (!empty($numerosSansOperateur)) {
                return redirect()->back()->withInput()->with('error', 'Préfixes destinataires non reconnus : ' . implode(', ', $numerosSansOperateur));
            }

            if (count(array_unique($operateursDestinataires)) > 1) {
                return redirect()->back()->withInput()->with('error', 'Les numéros d\'un envoi multiple doivent appartenir au même opérateur');
            }
        }

        $montantParDestinataire = $montant / $nombreDestinataires;

        if ($montantParDestinataire < 100) {
            return redirect()->back()->withInput()->with('error', 'La part par destinataire doit être au moins de 100 Ar');
        }

        $destinatairesTrouves = $this->compteModel->whereIn('numero_telephone', $numerosUniques)->findAll();
        $destinatairesParNumero = [];

        foreach ($destinatairesTrouves as $destinataire) {
            $destinatairesParNumero[$destinataire['numero_telephone']] = $destinataire;
        }

        $numerosIntrouvables = array_values(array_diff($numerosUniques, array_keys($destinatairesParNumero)));
        if (!empty($numerosIntrouvables)) {
            return redirect()->back()->withInput()->with('error', 'Destinataires introuvables : ' . implode(', ', $numerosIntrouvables));
        }

        $typeTransfert = $this->typeOperationModel->where('code', 'TRANSFERT')->first();
        if (!$typeTransfert) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération transfert non configuré');
        }

        $typeRetrait = null;
        if ($inclureFraisRetrait) {
            $typeRetrait = $this->typeOperationModel->where('code', 'RETRAIT')->first();
            if (!$typeRetrait) {
                return redirect()->back()->withInput()->with('error', 'Type d\'opération retrait non configuré');
            }
        }

        $detailsTransferts = [];
        $totalFraisRetrait = 0.0;
        $totalMontantTransfere = 0.0;
        $totalFraisTransfert = 0.0;
        $totalFraisCommission = 0.0;
        $totalFraisReduction = 0.0;
        $totalDebit = 0.0;

        /*foreach ($numerosUniques as $numero) {
            $destinataire = $destinatairesParNumero[$numero];
            $detail = $this->calculerDetailTransfert($montantParDestinataire, $destinataire['numero_telephone'], $typeTransfert, $typeRetrait, $inclureFraisRetrait);
            $detail['destinataire'] = $destinataire;
            $detailsTransferts[] = $detail;

            $totalFraisRetrait += $detail['frais_retrait'];
            $totalMontantTransfere += $detail['montant_transfert'];
            $totalFraisTransfert += $detail['frais_transfert'];
            $totalFraisCommission += $detail['frais_commission'];
            $totalDebit += $detail['total_debit'];
        }*/
        // Récupérer d'abord l'opérateur de l'émetteur via son numéro de téléphone
        $operateurEmetteur = $this->operateurPrefixeModel->trouverOperateurParNumero($numeroEmetteur);
        $operateurEmetteurId = $operateurEmetteur ? (int)$operateurEmetteur['operateur_id'] : null;

        foreach ($numerosUniques as $numero) {
            $destinataire = $destinatairesParNumero[$numero];
            
            // AJOUT de $operateurEmetteurId en dernier paramètre ici :
            $detail = $this->calculerDetailTransfert(
                $montantParDestinataire, 
                $destinataire['numero_telephone'], 
                $typeTransfert, 
                $typeRetrait, 
                $inclureFraisRetrait, 
                $operateurEmetteurId
            );
            
            $detail['destinataire'] = $destinataire;
            $detailsTransferts[] = $detail;

            $totalFraisRetrait += $detail['frais_retrait'];
            $totalMontantTransfere += $detail['montant_transfert'];
            $totalFraisTransfert += $detail['frais_transfert'];
            $totalFraisCommission += $detail['frais_commission'];
            $totalFraisReduction += $detail['reduction'];
            $totalDebit += $detail['total_debit'];
        }

        if ((float) $compte['solde'] < $totalDebit) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant (débit total de ' . number_format($totalDebit, 2, '.', ' ') . ' Ar)');
        }

        $referenceGroupe = $nombreDestinataires > 1 ? uniqid('GRP-') : null;
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $nouveauSoldeEmetteur = (float) $compte['solde'] - $totalDebit;
            if (!$this->compteModel->update($clientId, ['solde' => $nouveauSoldeEmetteur])) {
                throw new \RuntimeException('Impossible de débiter le compte émetteur');
            }

            foreach ($detailsTransferts as $detail) {
                $destinataire = $detail['destinataire'];
                $nouveauSoldeDestinataire = (float) $destinataire['solde'] + $detail['montant_transfert'];

                if (!$this->compteModel->update($destinataire['id'], ['solde' => $nouveauSoldeDestinataire])) {
                    throw new \RuntimeException('Impossible de créditer le destinataire ' . $destinataire['numero_telephone']);
                }

                if (!$this->historiqueModel->insert([
                    'type_operation_id' => $typeTransfert['id'],
                    'compte_source_id' => $clientId,
                    'numero_destinataire' => $destinataire['numero_telephone'],
                    'compte_destination_id' => $destinataire['id'],
                    'operateur_destination_id' => $detail['operateur_destination_id'],
                    'montant' => $detail['montant_transfert'],
                    'frais_bareme' => $detail['frais_transfert'],
                    'frais_commission' => $detail['frais_commission'],
                    'reduction'=> $detail['reduction'],
                    'reference_groupe' => $referenceGroupe,
                ])) {
                    throw new \RuntimeException('Impossible d\'enregistrer l\'historique du transfert');
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors du traitement du transfert');
            }

            $this->session->set('client_solde', $nouveauSoldeEmetteur);

            $message = $nombreDestinataires > 1
                ? 'Transfert groupé effectué avec succès vers ' . $nombreDestinataires . ' destinataires'
                : 'Transfert effectué avec succès vers ' . $detailsTransferts[0]['destinataire']['prenom'] . ' ' . $detailsTransferts[0]['destinataire']['nom'];

            $message .= ' - Montant de base total : ' . number_format($montant, 2, '.', ' ') . ' Ar'
                . ' - Montant envoyé cumulé : ' . number_format($totalMontantTransfere, 2, '.', ' ') . ' Ar'
                . ' - Frais de transfert cumulés : ' . number_format($totalFraisTransfert, 2, '.', ' ') . ' Ar'
                . ' - Commissions cumulées : ' . number_format($totalFraisCommission, 2, '.', ' ') . ' Ar'
                . ' - Débit total : ' . number_format($totalDebit, 2, '.', ' ') . ' Ar';

            if ($inclureFraisRetrait) {
                $message .= ' - Frais de retrait cumulés : ' . number_format($totalFraisRetrait, 2, '.', ' ') . ' Ar';
            }

            if ($referenceGroupe) {
                $message .= ' - Référence groupe : ' . $referenceGroupe;
            }

            return redirect()->to('/client/solde')->with('success', $message);
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    private function extraireNumerosDestinataires($saisie): array
    {
        if (is_array($saisie)) {
            $elements = $saisie;
        } else {
            $elements = preg_split('/[\r\n,;]+/', (string) $saisie) ?: [];
        }

        return array_values(array_filter(array_map(function ($numero) {
            return $this->normaliserNumeroTelephone($numero);
        }, $elements), static fn ($numero) => $numero !== ''));
    }

    private function normaliserNumeroTelephone(?string $numero): string
    {
        return preg_replace('/[\s\-\.]/', '', trim((string) $numero)) ?? '';
    }

    /*
    private function calculerDetailTransfert(float $montantBase, ?string $numeroDestinataire, array $typeTransfert, ?array $typeRetrait, bool $inclureFraisRetrait): array
    {
        $fraisRetraitTheorique = 0.0;

        if ($inclureFraisRetrait && $typeRetrait) {
            $baremeRetrait = $this->baremeFraisModel->trouverFrais($typeRetrait['id'], $montantBase);
            $fraisRetraitTheorique = $baremeRetrait ? (float) $baremeRetrait['frais'] : 0.0;
        }

        $montantTransfert = $montantBase + $fraisRetraitTheorique;
        $baremeTransfert = $this->baremeFraisModel->trouverFrais($typeTransfert['id'], $montantTransfert);
        $fraisTransfert = $baremeTransfert ? (float) $baremeTransfert['frais'] : 0.0;

        $operateurDestination = $this->operateurPrefixeModel->trouverOperateurParNumero($numeroDestinataire);
        $operateurDestinationId = $operateurDestination['operateur_id'] ?? null;
        $fraisCommission = 0.0;

        return [
            'montant_base' => $montantBase,
            'frais_retrait' => $fraisRetraitTheorique,
            'montant_transfert' => $montantTransfert,
            'frais_transfert' => $fraisTransfert,
            'frais_commission' => $fraisCommission,
            'operateur_destination_id' => $operateurDestinationId,
            'total_debit' => $montantTransfert + $fraisTransfert + $fraisCommission,
        ];
    }
    */

    private function calculerDetailTransfert(float $montantBase, ?string $numeroDestinataire, array $typeTransfert, ?array $typeRetrait, bool $inclureFraisRetrait, ?int $operateurEmetteurId): array
    {
        $fraisRetraitTheorique = 0.0;

        if ($inclureFraisRetrait && $typeRetrait) {
            $baremeRetrait = $this->baremeFraisModel->trouverFrais($typeRetrait['id'], $montantBase);
            $fraisRetraitTheorique = $baremeRetrait ? (float) $baremeRetrait['frais'] : 0.0;
        }

        $montantTransfert = $montantBase + $fraisRetraitTheorique;
        $baremeTransfert = $this->baremeFraisModel->trouverFrais($typeTransfert['id'], $montantTransfert);
        $fraisTransfert = $baremeTransfert ? (float) $baremeTransfert['frais'] : 0.0;

        $operateurDestination = $this->operateurPrefixeModel->trouverOperateurParNumero($numeroDestinataire);
        $operateurDestinationId = $operateurDestination ? (int)$operateurDestination['operateur_id'] : null;
        
        // Calcul de la commission inter-opérateur
        $fraisCommission = 0.0;
        
        if ($operateurEmetteurId !== null && $operateurDestinationId !== null) {
            // Si l'opérateur du destinataire est différent de celui de l'émetteur
            if ($operateurEmetteurId !== $operateurDestinationId) {
                // Recherche de la configuration correspondante dans la table
                $config = $this->configurationCommissionModel->trouverConfiguration($operateurEmetteurId, $operateurDestinationId);
                
                if ($config) {
                    $pourcentage = (float) $config['pourcentage_commission'];
                    // Formule demandée : (frais_bareme * commission) / 100
                    $fraisCommission = ($fraisTransfert * $pourcentage) / 100;
                }
            }
        }

        // Appliquer commission de reduction
        $fraisReduction = 0; 
        if ($operateurEmetteurId !== null && $operateurDestinationId !== null) {
            // Si l'opérateur du destinataire est différent de celui de l'émetteur
            if ($operateurEmetteurId == $operateurDestinationId) {
                // Recherche de la reduction correspondante dans la table
                $reduction = $this->commissionReductionModel->trouverReduction($operateurEmetteurId);
                
                if ($reduction) {
                    $pourcentage = (float) $reduction['pourcentage_reduction'];
                    // Formule demandée : (frais_bareme * commission) / 100
                    $fraisReduction = ($fraisTransfert * $pourcentage) / 100;
                }
            }
        }
        
        $total = $montantTransfert + $fraisTransfert + $fraisCommission - $fraisReduction;

        return [
            'montant_base' => $montantBase,
            'frais_retrait' => $fraisRetraitTheorique,
            'montant_transfert' => $montantTransfert,
            'frais_transfert' => $fraisTransfert,
            'frais_commission' => $fraisCommission,
            'operateur_destination_id' => $operateurDestinationId,
            'reduction'=> $fraisReduction,
            'total_debit' => $total,
        ];
    }

    public function profil(): string
    {
        $this->checkAuth();
        
        $clientId = $this->session->get('client_id');
        $compte = $this->compteModel->find($clientId);
        $statistiques = $this->historiqueModel->getStatistiquesClient($clientId);
        
        $data = [
            'compte' => $compte,
            'statistiques' => $statistiques
        ];
        
        return view('client/profil', $data);
    }

    public function updateProfil()
    {
        $this->checkAuth();
        
        $clientId = $this->session->get('client_id');
        $nom = $this->request->getPost('nom');
        $prenom = $this->request->getPost('prenom');
        
        // Validate names
        if (empty($nom) && empty($prenom)) {
            return redirect()->back()->with('error', 'Au moins un nom doit être renseigné');
        }
        
        $updateData = [];
        if (!empty($nom)) {
            $updateData['nom'] = $nom;
        }
        if (!empty($prenom)) {
            $updateData['prenom'] = $prenom;
        }
        
        try {
            $this->compteModel->update($clientId, $updateData);
            
            // Update session
            if (!empty($nom)) {
                $this->session->set('client_nom', $nom);
            }
            if (!empty($prenom)) {
                $this->session->set('client_prenom', $prenom);
            }
            
            return redirect()->to('/client/profil')->with('success', 'Profil mis à jour avec succès');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}
