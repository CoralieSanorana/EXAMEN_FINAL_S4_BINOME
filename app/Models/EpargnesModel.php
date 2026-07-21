<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargnesModel extends Model
{
    protected $table            = 'epargnes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_clients','pourcentage'];

}
