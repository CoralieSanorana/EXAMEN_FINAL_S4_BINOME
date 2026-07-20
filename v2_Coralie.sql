-- 1. Nouvelle table : LES OPÉRATEURS
CREATE TABLE IF NOT EXISTS operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,       -- 'Orange', 'Airtel', 'Telma'
    est_interne INTEGER DEFAULT 0   -- 1 = Notre réseau (ex: Orange), 0 = Concurrents
);

-- 2. Table des Préfixes (liée à l'opérateur)
DROP TABLE IF EXISTS operateur_prefixes;
CREATE TABLE operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,  -- Lien vers la table operateurs
    prefixe TEXT NOT NULL UNIQUE,   -- '033', '037', '032'
    statut TEXT DEFAULT 'actif',
    FOREIGN KEY (operateur_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    CONSTRAINT chk_prefixe_format CHECK (length(prefixe) = 3 AND prefixe GLOB '[0-9][0-9][0-9]')
);

-- Insertion des opérateurs (On imagine que votre application simule le réseau Orange)
INSERT INTO operateurs (nom, est_interne) VALUES 
('Telma Madagascar', 1),  
('Orange Madagascar', 0), 
('Airtel Madagascar', 0);

-- Liaison des préfixes à leurs opérateurs respectifs
INSERT INTO operateur_prefixes (operateur_id, prefixe, statut) VALUES 
(1, '034', 'actif'),
(2, '032', 'actif'), 
(3, '033', 'actif'); 

-- NOUVELLE TABLE : CONFIGURATION DES COMMISSIONS INTER-OPÉRATEURS
CREATE TABLE IF NOT EXISTS configuration_commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_source_id INTEGER NOT NULL,      -- Ton réseau (interne)
    operateur_destination_id INTEGER NOT NULL, -- Le réseau concurrent (externe)
    pourcentage_commission REAL NOT NULL DEFAULT 0.0,
    FOREIGN KEY (operateur_source_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateurs(id) ON DELETE CASCADE,
    -- Contrainte pour éviter les doublons de configuration entre deux mêmes opérateurs
    UNIQUE(operateur_source_id, operateur_destination_id),
    CONSTRAINT chk_commission_positive CHECK (pourcentage_commission >= 0)
);

INSERT INTO configuration_commissions (operateur_source_id, operateur_destination_id, pourcentage_commission) VALUES
(1, 2, 2.5),  
(1, 3, 2.0);  

-- 6. TABLE : HISTORIQUE DES TRANSACTIONS (VERSION OPTIMISÉE CORALIE)
CREATE TABLE IF NOT EXISTS historique_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER NOT NULL,
    numero_destinataire TEXT,                  -- Modifié en optionnel pour DEPOT/RETRAIT
    compte_destination_id INTEGER,             -- NULL si externe ou DEPOT/RETRAIT
    operateur_destination_id INTEGER,          -- Lié à la table operateurs
    montant REAL NOT NULL,
    frais_bareme REAL NOT NULL DEFAULT 0.0,
    frais_commission REAL NOT NULL DEFAULT 0.0,
    effectue_le TEXT DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_source_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_destination_id) REFERENCES comptes_clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateurs(id) ON DELETE RESTRICT,
    CONSTRAINT chk_montant_transaction CHECK (montant > 0),
    CONSTRAINT chk_frais_bareme CHECK (frais_bareme >= 0),
    CONSTRAINT chk_frais_commission CHECK (frais_commission >= 0)
);


DROP VIEW IF EXISTS vue_situation_gains;
CREATE VIEW vue_situation_gains AS
SELECT 
    t.code AS type_operation,
    CASE 
        -- Si ce n'est pas un transfert (Dépôt/Retrait), c'est forcément sur notre propre réseau
        WHEN t.code != 'TRANSFERT' THEN 'Notre Réseau'
        -- Si c'est un transfert, on regarde si l'opérateur de destination est le nôtre
        WHEN op.est_interne = 1 THEN 'Notre Réseau'
        -- Sinon, on affiche le nom de l'opérateur concurrent concerné
        ELSE 'Autres Opérateurs (' || op.nom || ')'
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


-- MISE À JOUR DE LA VUE HISTORIQUE CLIENT (Pour le portefeuille)
DROP VIEW IF EXISTS vue_historique_portefeuille_clients;
CREATE VIEW vue_historique_portefeuille_clients AS
-- CAS 1 : Le client est l'envoyeur
SELECT 
    h.id AS transaction_id,
    h.compte_source_id AS compte_concerne_id,
    t.code AS type_code,
    t.nom AS type_nom,
    h.montant AS montant_brut,
    (h.frais_bareme + h.frais_commission) AS frais_appliques,
    '-' AS sens_mouvement,
    (h.montant + h.frais_bareme + h.frais_commission) AS impact_solde,
    h.numero_destinataire AS telephone_tiers, -- Utilisation directe de ta nouvelle colonne !
    h.effectue_le
FROM historique_transactions h
JOIN types_operations t ON h.type_operation_id = t.id
WHERE t.code IN ('RETRAIT', 'TRANSFERT')

UNION ALL

-- CAS 2 : Le client effectue un dépôt
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

-- CAS 3 : Le client est le receveur (Transfert reçu interne)
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