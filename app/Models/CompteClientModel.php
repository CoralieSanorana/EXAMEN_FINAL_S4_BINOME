<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteClientModel extends Model
{
    protected $table            = 'comptes_clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['numero_telephone', 'solde', 'nom', 'prenom'];

    protected $validationRules      = [
        'numero_telephone' => 'required|numeric|min_length[8]|is_unique[comptes_clients.numero_telephone,id,{id}]',
        'solde'            => 'permit_empty|numeric|greater_than_equal_to[0]',
        'nom'              => 'permit_empty|max_length[100]',
        'prenom'           => 'permit_empty|max_length[100]'
    ];
    
    protected $validationMessages = [
        'numero_telephone' => [
            'is_unique' => 'Ce numéro de téléphone possède déjà un compte.'
        ],
        'solde' => [
            'greater_than_equal_to' => 'Le solde d\'un compte ne peut pas être négatif.'
        ]
    ];
    protected $skipValidation       = false;
}