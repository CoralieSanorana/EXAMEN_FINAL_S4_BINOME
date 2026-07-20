-- =====================================================
-- Migration SQLite vers le schéma v2_Coralie
-- Base cible : writable/MobileMoney.db
-- =====================================================

PRAGMA foreign_keys = OFF;

DROP VIEW IF EXISTS vue_situation_gains;
DROP VIEW IF EXISTS vue_historique_portefeuille_clients;

-- 1) Table des opérateurs
CREATE TABLE IF NOT EXISTS operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    est_interne INTEGER DEFAULT 0
);

INSERT OR IGNORE INTO operateurs (id, nom, est_interne) VALUES
(1, 'Telma Madagascar', 1),
(2, 'Orange Madagascar', 0),
(3, 'Airtel Madagascar', 0);

-- 2) Recréer la table des préfixes selon le nouveau schéma
DROP TABLE IF EXISTS operateur_prefixes;
CREATE TABLE operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    prefixe TEXT NOT NULL UNIQUE,
    statut TEXT DEFAULT 'actif',
    FOREIGN KEY (operateur_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    CONSTRAINT chk_prefixe_format CHECK (length(prefixe) = 3 AND prefixe GLOB '[0-9][0-9][0-9]')
);

INSERT INTO operateur_prefixes (operateur_id, prefixe, statut) VALUES
(1, '034', 'actif'),
(2, '032', 'actif'),
(3, '033', 'actif');

-- 3) Configuration des commissions inter-opérateurs
CREATE TABLE IF NOT EXISTS configuration_commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_source_id INTEGER NOT NULL,
    operateur_destination_id INTEGER NOT NULL,
    pourcentage_commission REAL NOT NULL DEFAULT 0.0,
    FOREIGN KEY (operateur_source_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    UNIQUE(operateur_source_id, operateur_destination_id),
    CONSTRAINT chk_commission_positive CHECK (pourcentage_commission >= 0)
);

INSERT OR IGNORE INTO configuration_commissions (operateur_source_id, operateur_destination_id, pourcentage_commission) VALUES
(1, 2, 2.5),
(1, 3, 2.0);

-- 4) Reconstruction de l'historique des transactions
CREATE TABLE historique_transactions_new (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER NOT NULL,
    numero_destinataire TEXT,
    compte_destination_id INTEGER,
    operateur_destination_id INTEGER,
    montant REAL NOT NULL,
    frais_bareme REAL NOT NULL DEFAULT 0.0,
    frais_commission REAL NOT NULL DEFAULT 0.0,
    reference_groupe TEXT NULL,
    effectue_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_source_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_destination_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateurs(id) ON DELETE RESTRICT,
    CONSTRAINT chk_montant_transaction CHECK (montant > 0),
    CONSTRAINT chk_frais_bareme CHECK (frais_bareme >= 0),
    CONSTRAINT chk_frais_commission CHECK (frais_commission >= 0)
);

INSERT INTO historique_transactions_new (
    id,
    type_operation_id,
    compte_source_id,
    numero_destinataire,
    compte_destination_id,
    operateur_destination_id,
    montant,
    frais_bareme,
    frais_commission,
    reference_groupe,
    effectue_le
)
SELECT
    h.id,
    h.type_operation_id,
    h.compte_source_id,
    (
        SELECT c.numero_telephone
        FROM comptes_clients c
        WHERE c.id = h.compte_destination_id
    ) AS numero_destinataire,
    h.compte_destination_id,
    (
        SELECT opx.operateur_id
        FROM comptes_clients c
        JOIN operateur_prefixes opx ON opx.prefixe = substr(c.numero_telephone, 1, 3)
        WHERE c.id = h.compte_destination_id
        LIMIT 1
    ) AS operateur_destination_id,
    h.montant,
    COALESCE(h.frais_appliques, 0.0) AS frais_bareme,
    0.0 AS frais_commission,
    h.reference_groupe,
    h.effectue_le
FROM historique_transactions h;

DROP TABLE historique_transactions;
ALTER TABLE historique_transactions_new RENAME TO historique_transactions;

-- 5) Recréation des vues v2
CREATE VIEW vue_situation_gains AS
SELECT 
    t.code AS type_operation,
    CASE 
        WHEN t.code != 'TRANSFERT' THEN 'Notre Réseau'
        WHEN op.est_interne = 1 THEN 'Notre Réseau'
        WHEN op.nom IS NOT NULL THEN 'Autres Opérateurs (' || op.nom || ')'
        ELSE 'Opérateur inconnu'
    END AS reseau_concerne,
    COUNT(h.id) AS nombre_transactions,
    SUM(h.montant) AS volume_total,
    SUM(h.frais_bareme) AS total_gains_bareme,
    SUM(h.frais_commission) AS total_gains_commission,
    SUM(h.frais_bareme + h.frais_commission) AS total_gains_frais
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
LEFT JOIN operateurs op ON h.operateur_destination_id = op.id
GROUP BY t.code, reseau_concerne;

CREATE VIEW vue_historique_portefeuille_clients AS
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    (h.frais_bareme + h.frais_commission) AS frais_appliques,
    '-' AS sens_mouvement,
    (h.montant + h.frais_bareme + h.frais_commission) AS impact_solde,
    h.numero_destinataire AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
WHERE t.code IN ('RETRAIT', 'TRANSFERT')

UNION ALL

SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    0.0 AS frais_appliques,
    '+' AS sens_mouvement,
    h.montant AS impact_solde,
    NULL AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
WHERE t.code = 'DEPOT'

UNION ALL

SELECT 
    h.id AS transaction_id,
    h.compte_destination_id AS compte_concerne_id,
    'TRANSFERT_RECU' AS type_code,
    'Transfert reçu' AS type_nom,
    h.montant AS montant_brut,
    0.0 AS frais_appliques,
    '+' AS sens_mouvement,
    h.montant AS impact_solde,
    c_source.numero_telephone AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN comptes_clients c_source ON h.compte_source_id = c_source.id
WHERE h.compte_destination_id IS NOT NULL;

PRAGMA foreign_keys = ON;
