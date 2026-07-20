<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueTransactionModel extends Model
{
    protected $table            = 'historique_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['type_operation_id', 'compte_source_id', 'numero_destinataire', 'compte_destination_id', 'operateur_destination_id', 'montant', 'frais_bareme', 'frais_commission', 'reference_groupe'];

    protected $validationRules      = [
        'type_operation_id'       => 'required|integer',
        'compte_source_id'        => 'required|integer',
        'numero_destinataire'     => 'permit_empty|max_length[20]',
        'compte_destination_id'   => 'permit_empty|integer',
        'operateur_destination_id'=> 'permit_empty|integer',
        'montant'                 => 'required|numeric|greater_than[0]',
        'frais_bareme'            => 'required|numeric|greater_than_equal_to[0]',
        'frais_commission'        => 'required|numeric|greater_than_equal_to[0]',
        'reference_groupe'        => 'permit_empty|max_length[100]'
    ];
    protected $skipValidation       = false;

    /**
     * Récupère l'historique complet d'un client spécifique avec le nom de l'opération
     */
    public function obtenirHistoriqueClient($compteId)
    {
        return $this->select('historique_transactions.*, (historique_transactions.frais_bareme + historique_transactions.frais_commission) as frais_appliques, types_operations.nom as type_nom, types_operations.code as type_code')
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

    /**
     * Récupère l'historique d'un client en utilisant la vue vue_historique_portefeuille_clients
     * Cette vue gère automatiquement le signe (+/-) selon le rôle du client (envoyeur/receveur)
     */
    public function obtenirHistoriqueClientVue($compteId)
    {
        $db = \Config\Database::connect();
        return $db->table('vue_historique_portefeuille_clients')
                    ->where('compte_concerne_id', $compteId)
                    ->orderBy('effectue_le', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Récupère les statistiques d'un client (nombre de transactions par type)
     */
    public function getStatistiquesClient($compteId)
    {
        $historique = $this->obtenirHistoriqueClientVue($compteId);
        
        $stats = [
            'total' => count($historique),
            'depot' => 0,
            'retrait' => 0,
            'transfert_envoye' => 0,
            'transfert_recu' => 0,
            'montant_total_depot' => 0,
            'montant_total_retrait' => 0,
            'montant_total_transfert_envoye' => 0,
            'montant_total_transfert_recu' => 0,
            'transactions_par_mois' => []
        ];

        foreach ($historique as $transaction) {
            $type = $transaction['type_code'];
            $montant = $transaction['montant_brut'];
            $date = date('Y-m', strtotime($transaction['effectue_le']));
            
            // Compter par type
            if ($type === 'DEPOT') {
                $stats['depot']++;
                $stats['montant_total_depot'] += $montant;
            } elseif ($type === 'RETRAIT') {
                $stats['retrait']++;
                $stats['montant_total_retrait'] += $montant;
            } elseif ($type === 'TRANSFERT') {
                $stats['transfert_envoye']++;
                $stats['montant_total_transfert_envoye'] += $montant;
            } elseif ($type === 'TRANSFERT_RECU') {
                $stats['transfert_recu']++;
                $stats['montant_total_transfert_recu'] += $montant;
            }

            // Grouper par mois pour le graphique
            if (!isset($stats['transactions_par_mois'][$date])) {
                $stats['transactions_par_mois'][$date] = [
                    'depot' => 0,
                    'retrait' => 0,
                    'transfert' => 0,
                    'transfert_recu' => 0
                ];
            }

            if ($type === 'DEPOT') {
                $stats['transactions_par_mois'][$date]['depot']++;
            } elseif ($type === 'RETRAIT') {
                $stats['transactions_par_mois'][$date]['retrait']++;
            } elseif ($type === 'TRANSFERT') {
                $stats['transactions_par_mois'][$date]['transfert']++;
            } elseif ($type === 'TRANSFERT_RECU') {
                $stats['transactions_par_mois'][$date]['transfert_recu']++;
            }
        }

        // Trier les mois par ordre chronologique
        ksort($stats['transactions_par_mois']);

        return $stats;
    }
}