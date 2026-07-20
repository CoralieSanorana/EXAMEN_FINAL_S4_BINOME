<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'bareme_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];

    protected $validationRules      = [
        'type_operation_id' => 'required|integer',
        'montant_min'       => 'required|numeric|greater_than_equal_to[0]',
        'montant_max'       => 'required|numeric|greater_than_equal_to[0]',
        'frais'             => 'required|numeric|greater_than_equal_to[0]'
    ];
    protected $skipValidation       = false;

    /**
     * Récupère le frais correspondant à un type d'opération et un montant spécifique
     */
    public function trouverFrais($typeOperationId, $montant)
    {
        return $this->where('type_operation_id', $typeOperationId)
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->first();
    }
}