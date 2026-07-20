-- =====================================================
-- Script d'évolution de la base existante
-- Ajout de la colonne reference_groupe pour les transferts groupés
-- =====================================================

ALTER TABLE historique_transactions
ADD COLUMN reference_groupe TEXT NULL;
