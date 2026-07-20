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