<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'types_operations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['code', 'nom'];

    protected $validationRules      = [
        'code' => 'required|alpha_dash|is_unique[types_operations.code]',
        'nom'  => 'required|min_length[3]'
    ];
    protected $skipValidation       = false;
}