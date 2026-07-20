<?php

namespace App\Controllers;

class Operateur extends BaseController
{
    public function comptes(): string
    {
        return view('operateur/comptes');
    }

    public function gains(): string
    {
        return view('operateur/gains');
    }

    public function operations(): string
    {
        return view('operateur/operations');
    }

    public function prefixes(): string
    {
        return view('operateur/prefixes');
    }
}
