<?php

namespace App\Controllers;

class Client extends BaseController
{
    public function login(): string
    {
        return view('client/login');
    }

    public function depot(): string
    {
        return view('client/depot');
    }

    public function historique(): string
    {
        return view('client/historique');
    }

    public function retrait(): string
    {
        return view('client/retrait');
    }

    public function solde(): string
    {
        return view('client/solde');
    }

    public function transfert(): string
    {
        return view('client/transfert');
    }
}
