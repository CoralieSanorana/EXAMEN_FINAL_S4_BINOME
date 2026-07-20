<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteOperateurModel extends Model
{
    protected $table            = 'comptes_operateurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'email', 'mot_de_passe'];

    protected $validationRules      = [
        'username'      => 'required|min_length[3]|is_unique[comptes_operateurs.username]',
        'email'         => 'required|valid_email|is_unique[comptes_operateurs.email]',
        'mot_de_passe'  => 'required|min_length[6]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Ce nom d\'utilisateur est déjà pris.',
            'min_length' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères.'
        ],
        'email' => [
            'is_unique' => 'Cet email est déjà utilisé.',
            'valid_email' => 'Veuillez entrer une adresse email valide.'
        ],
        'mot_de_passe' => [
            'min_length' => 'Le mot de passe doit contenir au moins 6 caractères.'
        ]
    ];
    protected $skipValidation       = false;
}
