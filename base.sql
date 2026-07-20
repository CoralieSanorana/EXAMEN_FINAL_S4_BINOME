-- ============================================================================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES (SQLite) - VERSION 1
-- ============================================================================

-- Activation du support des clés étrangères dans SQLite
PRAGMA foreign_keys = ON;

-- ----------------------------------------------------------------------------
-- 1. TABLE : CONFIGURATION DES PRÉFIXES OPÉRATEUR
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    CONSTRAINT chk_prefixe_format CHECK (length(prefixe) = 3 AND prefixe GLOB '[0-9][0-9][0-9]')
);

-- ----------------------------------------------------------------------------
-- 2. TABLE : TYPES D'OPÉRATIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE, -- 'DEPOT', 'RETRAIT', 'TRANSFERT'
    nom TEXT NOT NULL
);

-- ----------------------------------------------------------------------------
-- 3. TABLE : BARÈMES DES FRAIS
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
-- 4. TABLE : COMPTES CLIENTS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comptes_clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    solde REAL NOT NULL DEFAULT 0.0,
    cree_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    CONSTRAINT chk_solde_positif CHECK (solde >= 0)
);

-- ----------------------------------------------------------------------------
-- 5. TABLE : HISTORIQUE DES TRANSACTIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS historique_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER NOT NULL,
    compte_destination_id INTEGER, -- NULL si Dépôt ou Retrait standard
    montant REAL NOT NULL,
    frais_appliques REAL NOT NULL,
    effectue_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_source_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_destination_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    CONSTRAINT chk_montant_transaction CHECK (montant > 0),
    CONSTRAINT chk_frais_transaction CHECK (frais_appliques >= 0)
);

-- ----------------------------------------------------------------------------
-- 6. VUES ALIMENTATION RAPIDE (STATISTIQUES OPÉRATEUR)
-- ----------------------------------------------------------------------------
CREATE VIEW IF NOT EXISTS vue_situation_gains AS
SELECT 
    t.code AS type_operation,
    COUNT(h.id) AS nombre_transactions,
    SUM(h.montant) AS volume_total,
    SUM(h.frais_appliques) AS total_gains_frais
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
GROUP BY t.code;

-- ============================================================================
-- INJECTION DES DONNÉES INITIALES ET DE TEST
-- ============================================================================

-- Insertion des préfixes valides demandés
INSERT INTO operateur_prefixes (prefixe) VALUES ('033'), ('037');

-- Insertion des types d'opérations requis
INSERT INTO types_operations (code, nom) VALUES 
('DEPOT', 'Dépôt de fonds'),
('RETRAIT', 'Retrait de fonds'),
('TRANSFERT', 'Transfert d''argent');

-- Insertion du barème de frais officiel du sujet (Exemple basé sur l'image)
-- On applique ici ce barème pour le RETRAIT et le TRANSFERT par exemple
INSERT INTO bareme_frais (type_operation_id, montant_min, montant_max, frais) VALUES
-- Pour les Retraits (exemple basé sur votre liste)
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

-- Pour les Transferts (Même barème appliqué par défaut ou adaptable)
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10001, 25000, 200),
(3, 25001, 50000, 400),
(3, 50011, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000);

-- Quelques comptes fictifs pour démarrer les tests de login/opérations
INSERT INTO comptes_clients (numero_telephone, solde) VALUES 
('0331234567', 50000.0),
('0379876543', 5000.0);