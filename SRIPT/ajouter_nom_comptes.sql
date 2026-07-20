-- Script pour ajouter les colonnes nom et prenom à la table comptes_clients
-- Exécuter ce script pour mettre à jour la structure de la base de données

-- Ajouter les colonnes nom et prenom
ALTER TABLE comptes_clients ADD COLUMN nom VARCHAR(100);
ALTER TABLE comptes_clients ADD COLUMN prenom VARCHAR(100);

-- Mettre à jour les données existantes avec des noms fictifs
UPDATE comptes_clients SET nom = 'RASOANAIVO', prenom = 'Jean' WHERE numero_telephone = '0331234567';
UPDATE comptes_clients SET nom = 'RAKOTO', prenom = 'Marie' WHERE numero_telephone = '0379876543';

-- Vérifier les modifications
SELECT * FROM comptes_clients;
