<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurPrefixeModel extends Model
{
    protected $table            = 'operateur_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['prefixe', 'libelle', 'statut'];

    // Règles de validation strictes (3 chiffres exactement)
    protected $validationRules      = [
        'prefixe' => 'required|exact_length[3]|numeric|is_unique[operateur_prefixes.prefixe]',
        'libelle' => 'required|min_length[3]',
        'statut'  => 'required|in_list[actif,inactif]'
    ];
    
    protected $validationMessages   = [
        'prefixe' => [
            'required'     => 'Le préfixe est obligatoire.',
            'exact_length' => 'Le préfixe doit comporter exactement 3 chiffres.',
            'numeric'      => 'Le préfixe ne doit contenir que des chiffres.',
            'is_unique'    => 'Ce préfixe existe déjà.'
        ]
    ];
    protected $skipValidation       = false;
}