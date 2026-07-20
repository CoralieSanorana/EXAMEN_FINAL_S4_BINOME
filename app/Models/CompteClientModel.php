<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteClientModel extends Model
{
    protected $table            = 'comptes_clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['numero_telephone', 'solde'];

    protected $validationRules      = [
        'numero_telephone' => 'required|numeric|min_length[8]|is_unique[comptes_clients.numero_telephone]',
        'solde'            => 'permit_empty|numeric|greater_than_equal_to[0]'
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