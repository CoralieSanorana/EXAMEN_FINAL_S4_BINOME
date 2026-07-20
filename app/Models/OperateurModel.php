<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table            = 'operateurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nom', 'est_interne'];

    protected $validationRules      = [
        'nom' => 'required|min_length[3]|is_unique[operateurs.nom]',
        'est_interne' => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages   = [
        'nom' => [
            'required' => 'Le nom de l\'opérateur est obligatoire.',
            'min_length' => 'Le nom doit comporter au moins 3 caractères.',
            'is_unique' => 'Cet opérateur existe déjà.'
        ]
    ];
    protected $skipValidation       = false;
}
