-- Migration pour séparer les gains par opérateur (interne vs externe)

DROP VIEW IF EXISTS vue_situation_gains;

CREATE VIEW vue_situation_gains AS
SELECT 
    t.code AS type_operation,
    CASE 
        WHEN op.est_interne = 1 THEN 'interne'
        ELSE 'externe'
    END AS type_operateur,
    op.nom AS operateur_nom,
    COUNT(h.id) AS nombre_transactions,
    SUM(h.montant) AS volume_total,
    SUM(h.frais_appliques) AS total_gains_frais
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
JOIN comptes_clients cs ON cs.id = h.compte_source_id
LEFT JOIN operateur_prefixes op_pref ON SUBSTR(cs.numero_telephone, 1, 3) = op_pref.prefixe
LEFT JOIN operateurs op ON op.id = op_pref.operateur_id
GROUP BY t.code, op.est_interne, op.nom;
