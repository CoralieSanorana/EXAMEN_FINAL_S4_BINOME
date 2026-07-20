-- ============================================================================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES INTEGRAL (SQLite) - VERSION FINALE
-- ============================================================================

-- Activation du support des clés étrangères dans SQLite
PRAGMA foreign_keys = ON;

-- ----------------------------------------------------------------------------
-- 1. TABLE : COMPTES OPÉRATEURS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comptes_operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL
);

-- ----------------------------------------------------------------------------
-- 2. TABLE : CONFIGURATION DES PRÉFIXES OPÉRATEUR
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    libelle TEXT,
    statut TEXT DEFAULT 'actif',
    CONSTRAINT chk_prefixe_format CHECK (length(prefixe) = 3 AND prefixe GLOB '[0-9][0-9][0-9]')
);

-- ----------------------------------------------------------------------------
-- 3. TABLE : TYPES D'OPÉRATIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE, -- 'DEPOT', 'RETRAIT', 'TRANSFERT'
    nom TEXT NOT NULL
);

-- ----------------------------------------------------------------------------
-- 4. TABLE : BARÈMES DES FRAIS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id) ON DELETE RESTRICT,
    CONSTRAINT chk_montants CHECK (montant_min >= 0 AND montant_max >= montant_min),
    CONSTRAINT chk_frais_positif CHECK (frais >= 0)
);

-- ----------------------------------------------------------------------------
-- 5. TABLE : COMPTES CLIENTS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comptes_clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    nom TEXT,
    prenom TEXT,
    solde REAL NOT NULL DEFAULT 0.0,
    cree_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    CONSTRAINT chk_solde_positif CHECK (solde >= 0)
);

-- ----------------------------------------------------------------------------
-- 6. TABLE : HISTORIQUE DES TRANSACTIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS historique_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER NOT NULL,
    compte_destination_id INTEGER, -- NULL si Dépôt ou Retrait standard
    montant REAL NOT NULL,
    frais_appliques REAL NOT NULL,
    reference_groupe TEXT NULL,
    effectue_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_source_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_destination_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    CONSTRAINT chk_montant_transaction CHECK (montant > 0),
    CONSTRAINT chk_frais_transaction CHECK (frais_appliques >= 0)
);

-- ----------------------------------------------------------------------------
-- 7. VUE : SITUATION GLOBAL DES GAINS (OPÉRATEUR)
-- ----------------------------------------------------------------------------
DROP VIEW IF EXISTS vue_situation_gains;
CREATE VIEW vue_situation_gains AS
SELECT 
    t.code AS type_operation,
    COUNT(h.id) AS nombre_transactions,
    SUM(h.montant) AS volume_total,
    SUM(h.frais_appliques) AS total_gains_frais
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
GROUP Clyde t.code;

-- ----------------------------------------------------------------------------
-- 8. VUE : HISTORIQUE UNIFIÉ DES CLIENTS (AVEC LES GESTIONS DES SIGNES + ET -)
-- ----------------------------------------------------------------------------
DROP VIEW IF EXISTS vue_historique_portefeuille_clients;
CREATE VIEW vue_historique_portefeuille_clients AS
-- CAS 1 : Le client est l'envoyeur (Source) -> Il est débité du montant et des frais
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    h.frais_appliques,
    '-' AS sens_mouvement,
    (h.montant + h.frais_appliques) AS impact_solde,
    c_dest.numero_telephone AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
LEFT JOIN comptes_clients c_dest ON h.compte_destination_id = c_dest.id
WHERE t.code IN ('RETRAIT', 'TRANSFERT')

UNION ALL

-- CAS 2 : Le client effectue un dépôt -> Il est crédité du montant (sans frais)
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    h.frais_appliques,
    '+' AS sens_mouvement,
    h.montant AS impact_solde,
    NULL AS telephone_tiers,
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
WHERE t.code = 'DEPOT'

UNION ALL

-- CAS 3 : Le client est le receveur (Destination) -> Il reçoit l'argent (+) sans frais
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


-- ============================================================================
-- INJECTION DES DONNÉES INITIALES ET DE TEST
-- ============================================================================

-- Compte de test Opérateur
INSERT INTO comptes_operateurs (username, email, mot_de_passe) VALUES 
('Operateur', 'operateur@gmail.com', 'operateur123');

-- Préfixes valides avec opérateurs à Madagascar
INSERT INTO operateur_prefixes (prefixe, libelle, statut) VALUES 
('033', 'Orange Madagascar', 'actif'),
('037', 'Airtel Madagascar', 'actif');

-- Types d'opérations requis
INSERT INTO types_operations (code, nom) VALUES 
('DEPOT', 'Dépôt de fonds'),
('RETRAIT', 'Retrait de fonds'),
('TRANSFERT', 'Transfert d''argent');

-- Barème officiel des frais (pour les retraits id=2 et transferts id=3)
INSERT INTO bareme_frais (type_operation_id, montant_min, montant_max, frais) VALUES
-- Retraits
(2, 100, 1000, 50),
(2, 1001, 5000, 50),
(2, 5001, 10000, 100),
(2, 10001, 25000, 200),
(2, 25001, 50000, 400),
(2, 50011, 100000, 800),
(2, 100001, 250000, 1500),
(2, 250001, 500000, 1500),
(2, 500001, 1000000, 2500),
(2, 1000001, 2000000, 3000),
-- Transferts
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10011, 25000, 200),
(3, 25001, 50000, 400),
(3, 50011, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000);

-- Comptes clients fictifs complets pour démarrer les tests
INSERT INTO comptes_clients (numero_telephone, nom, prenom, solde) VALUES 
('0331234567', 'RASOANAIVO', 'Jean', 50000.0),
('0379876543', 'RAKOTO', 'Marie', 5000.0);

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