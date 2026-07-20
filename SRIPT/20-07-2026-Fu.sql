-- ============================================================================
-- SCRIPT DE MISE À JOUR : CORRECTIF DE L'AFFICHAGE DE L'HISTORIQUE CLIENT
-- Date : 20/07/2026
-- Fichier : 20-07-2026-Fu.sql
-- ============================================================================

-- 1. Suppression de l'ancienne vue si elle existait sous une autre forme
DROP VIEW IF EXISTS vue_historique_portefeuille_clients;

-- 2. Création d'une vue unifiée qui gère le signe (+ ou -) selon le rôle du client
CREATE VIEW vue_historique_portefeuille_clients AS
-- CAS 1 : Le client est l'envoyeur (Source) -> Il est débité du montant et des frais
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    h.frais_appliques,
    -- Pour l'envoyeur, le mouvement total est négatif (Montant + Frais)
    '-' AS sens_mouvement,
    (h.montant + h.frais_appliques) AS impact_solde,
    c_dest.numero_telephone AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
LEFT JOIN comptes_clients c_dest ON h.compte_destination_id = c_dest.id
WHERE t.code IN ('RETRAIT', 'TRANSFERT')

UNION ALL

-- CAS 2 : Le client effectue un dépôt -> Il est crédité du montant (pas de frais)
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    h.frais_appliques,
    -- Pour le dépôt, le mouvement est positif (+)
    '+' AS sens_mouvement,
    h.montant AS impact_solde,
    NULL AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
WHERE t.code = 'DEPOT'

UNION ALL

-- CAS 3 : Le client est le receveur (Destination) -> Il reçoit l'argent sans payer de frais
SELECT 
    h.id AS transaction_id,
    h.compte_destination_id AS compte_concerne_id,
    'TRANSFERT_RECU' AS type_code,
    'Transfert reçu' AS type_nom,
    h.montant AS montant_brut,
    0.0 AS frais_appliques,
    -- Pour le receveur, le mouvement est positif (+)
    '+' AS sens_mouvement,
    h.montant AS impact_solde,
    c_source.numero_telephone AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN comptes_clients c_source ON h.compte_source_id = c_source.id
WHERE h.compte_destination_id IS NOT NULL;