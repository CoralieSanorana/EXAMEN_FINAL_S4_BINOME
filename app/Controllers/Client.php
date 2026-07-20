<?php

namespace App\Controllers;

use App\Models\CompteClientModel;
use App\Models\HistoriqueTransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;

class Client extends BaseController
{
    protected $session;
    protected $compteModel;
    protected $historiqueModel;
    protected $typeOperationModel;
    protected $baremeFraisModel;

    public function __construct()
    {
        $this->session = session();
        $this->compteModel = new CompteClientModel();
        $this->historiqueModel = new HistoriqueTransactionModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
    }

    public function index()
    {
        // Redirect to login as default
        return redirect()->to('/client/login');
    }

    public function login(): string
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
                'compte_destination_id' => null,
                'montant' => $montant,
                'frais_appliques' => $fraisAppliques
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
                'compte_destination_id' => null,
                'montant' => $montant,
                'frais_appliques' => $fraisAppliques
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
        return view('client/transfert');
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
        $destinataireId = $this->request->getPost('destinataire_id');
        $clientId = $this->session->get('client_id');
        
        // Validate amount
        if (!$montant || !is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }
        
        $montant = floatval($montant);
        
        // Validate recipient
        if (!$destinataireId || $destinataireId == $clientId) {
            return redirect()->back()->with('error', 'Destinataire invalide');
        }
        
        // Get current account
        $compte = $this->compteModel->find($clientId);
        if (!$compte) {
            return redirect()->back()->with('error', 'Compte introuvable');
        }
        
        // Get recipient account
        $destinataire = $this->compteModel->find($destinataireId);
        if (!$destinataire) {
            return redirect()->back()->with('error', 'Destinataire introuvable');
        }
        
        // Get TRANSFERT operation type
        $typeTransfert = $this->typeOperationModel->where('code', 'TRANSFERT')->first();
        if (!$typeTransfert) {
            return redirect()->back()->with('error', 'Type d\'opération transfert non configuré');
        }
        
        // Calculate fees
        $frais = $this->baremeFraisModel->trouverFrais($typeTransfert['id'], $montant);
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
            // Deduct from sender's account
            $nouveauSoldeEmetteur = $compte['solde'] - $totalDebit;
            $this->compteModel->update($clientId, ['solde' => $nouveauSoldeEmetteur]);
            
            // Add to recipient's account (no fees for recipient)
            $nouveauSoldeDestinataire = $destinataire['solde'] + $montant;
            $this->compteModel->update($destinataireId, ['solde' => $nouveauSoldeDestinataire]);
            
            // Insert transaction history for sender
            $this->historiqueModel->insert([
                'type_operation_id' => $typeTransfert['id'],
                'compte_source_id' => $clientId,
                'compte_destination_id' => $destinataireId,
                'montant' => $montant,
                'frais_appliques' => $fraisAppliques
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du traitement du transfert');
            }
            
            // Update session with new balance
            $this->session->set('client_solde', $nouveauSoldeEmetteur);
            
            return redirect()->to('/client/solde')->with('success', 'Transfert de ' . number_format($montant, 0, '.', ' ') . ' Ar effectué avec succès vers ' . $destinataire['prenom'] . ' ' . $destinataire['nom'] . ' (Frais: ' . number_format($fraisAppliques, 0, '.', ' ') . ' Ar)');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
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
