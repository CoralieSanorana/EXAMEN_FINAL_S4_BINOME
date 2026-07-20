<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueTransactionModel extends Model
{
    protected $table            = 'historique_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['type_operation_id', 'compte_source_id', 'compte_destination_id', 'montant', 'frais_appliques'];

    protected $validationRules      = [
        'type_operation_id'     => 'required|integer',
        'compte_source_id'      => 'required|integer',
        'compte_destination_id' => 'permit_empty|integer',
        'montant'               => 'required|numeric|greater_than[0]',
        'frais_appliques'       => 'required|numeric|greater_than_equal_to[0]'
    ];
    protected $skipValidation       = false;

    /**
     * Récupère l'historique complet d'un client spécifique avec le nom de l'opération
     */
    public function obtenirHistoriqueClient($compteId)
    {
        return $this->select('historique_transactions.*, types_operations.nom as type_nom, types_operations.code as type_code')
                    ->join('types_operations', 'types_operations.id = historique_transactions.type_operation_id')
                    ->groupStart()
                        ->where('compte_source_id', $compteId)
                        ->orWhere('compte_destination_id', $compteId)
                    ->groupEnd()
                    ->orderBy('effectue_le', 'DESC')
                    ->findAll();
    }

    /**
     * Calcule la situation globale des gains pour l'opérateur (utilise la vue SQL)
     */
    public function obtenirSituationGains()
    {
        $db = \Config\Database::connect();
        return $db->table('vue_situation_gains')->get()->getResultArray();
    }
}