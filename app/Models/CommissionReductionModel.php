<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionReductionModel extends Model
{
    protected $table            = 'commission_reduction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['operateur_id', 'pourcentage_reduction'];

     public function trouverReduction(?int $operateurId): ?array
    {
        if (empty($operateurId)) {
            return null;
        }

        return $this->where('operateur_id', $operateurId)
            ->first();
    }
}
