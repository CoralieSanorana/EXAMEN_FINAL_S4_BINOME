<?php

namespace App\Controllers;

use App\Models\CompteClientModel;

class Client extends BaseController
{
    protected $session;
    protected $compteModel;

    public function __construct()
    {
        $this->session = session();
        $this->compteModel = new CompteClientModel();
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
        
        // Validate phone number format
        if (!$telephone || !preg_match('/^[0-9]{8,10}$/', $telephone)) {
            return redirect()->back()->with('error', 'Numéro de téléphone invalide');
        }

        // Check if account exists
        $compte = $this->compteModel->where('numero_telephone', $telephone)->first();

        if ($compte) {
            // Set session data
            $this->session->set([
                'client_id' => $compte['id'],
                'client_telephone' => $compte['numero_telephone'],
                'client_solde' => $compte['solde'],
                'logged_in' => true
            ]);

            return redirect()->to('/client/solde')->with('success', 'Connexion réussie');
        } else {
            return redirect()->back()->with('error', 'Ce numéro de téléphone n\'a pas de compte');
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

    public function historique(): string
    {
        $this->checkAuth();
        return view('client/historique');
    }

    public function retrait(): string
    {
        $this->checkAuth();
        return view('client/retrait');
    }

    public function solde(): string
    {
        $this->checkAuth();
        
        // Update session with current balance
        $compte = $this->compteModel->find($this->session->get('client_id'));
        if ($compte) {
            $this->session->set('client_solde', $compte['solde']);
        }
        
        return view('client/solde');
    }

    public function transfert(): string
    {
        $this->checkAuth();
        return view('client/transfert');
    }
}
