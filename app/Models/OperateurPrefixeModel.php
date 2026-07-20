<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurPrefixeModel extends Model
{
    protected $table            = 'operateur_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['operateur_id', 'prefixe', 'statut'];

    // Règles de validation strictes (3 chiffres exactement)
    protected $validationRules      = [
        'operateur_id' => 'required|numeric',
        'prefixe' => 'required|exact_length[3]|numeric|is_unique[operateur_prefixes.prefixe]',
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

    public function trouverOperateurParNumero(?string $numero): ?array
    {
        $numero = preg_replace('/[\s\-\.]/', '', trim((string) $numero));
        $prefixe = substr($numero, 0, 3);

        if ($prefixe === false || strlen($prefixe) !== 3) {
            return null;
        }

        return $this->select('operateur_prefixes.*, operateurs.nom as operateur_nom, operateurs.est_interne')
            ->join('operateurs', 'operateurs.id = operateur_prefixes.operateur_id')
            ->where('operateur_prefixes.prefixe', $prefixe)
            ->where('operateur_prefixes.statut', 'actif')
            ->first();
    }
}