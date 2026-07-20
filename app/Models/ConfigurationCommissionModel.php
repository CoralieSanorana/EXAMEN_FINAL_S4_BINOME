<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigurationCommissionModel extends Model
{
    protected $table            = 'configuration_commissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['operateur_source_id', 'operateur_destination_id', 'pourcentage_commission'];

    protected $validationRules      = [
        'operateur_source_id' => 'required|numeric',
        'operateur_destination_id' => 'required|numeric',
        'pourcentage_commission' => 'required|numeric|greater_than_equal_to[0]'
    ];
    
    protected $validationMessages   = [
        'operateur_source_id' => [
            'required' => 'L\'opérateur source est obligatoire.'
        ],
        'operateur_destination_id' => [
            'required' => 'L\'opérateur destination est obligatoire.',
            'diff_fields' => 'L\'opérateur destination doit être différent de l\'opérateur source.'
        ],
        'pourcentage_commission' => [
            'required' => 'Le pourcentage de commission est obligatoire.',
            'numeric' => 'Le pourcentage doit être un nombre.',
            'greater_than_equal_to' => 'Le pourcentage doit être positif ou nul.'
        ]
    ];
    protected $skipValidation       = false;

    public function trouverConfiguration(?int $operateurSourceId, ?int $operateurDestinationId): ?array
    {
        if (empty($operateurSourceId) || empty($operateurDestinationId)) {
            return null;
        }

        return $this->where('operateur_source_id', $operateurSourceId)
            ->where('operateur_destination_id', $operateurDestinationId)
            ->first();
    }
}
