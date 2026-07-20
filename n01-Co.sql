create table comptes_operateurs(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL
);

insert into comptes_operateurs (username,email,mot_de_passe) VALUES 
('Operateur','operateur@gmail.com','operateur123');

drop table operateur_prefixes;

CREATE TABLE IF NOT EXISTS operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    libelle TEXT,
    statut TEXT DEFAULT 'actif',
    CONSTRAINT chk_prefixe_format CHECK (length(prefixe) = 3 AND prefixe GLOB '[0-9][0-9][0-9]')
);

INSERT INTO operateur_prefixes (prefixe, libelle, statut) VALUES 
('033', 'Orange Madagascar', 'actif'),
('037', 'Airtel Madagascar', 'actif');