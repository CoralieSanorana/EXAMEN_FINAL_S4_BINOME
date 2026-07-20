# Mobile Money - Version 1

- [ok] Lecture du sujet

**Coralie ETU004250**
- [ok] Configuration initiale et Livrables
    - [ok] Remplir le formulaire d'informations au début du projet (https://forms.gle/nCv6xJYHVvVj2FKA)
    - [ok] Créer le fichier Taches.md à la racine pour le suivi individuel du binôme

**Funaki ETU004169**
- [ok] Creation de base de donnees
    - [ok] Créer le script unique base.sql à la racine (tables, vues, données initiales)
    - [ok] Tables:
        - [ok] configurations_prefixes: id, prefixe (ex: 033, 037)
        - [ok] types_operation: id, libelle (depot, retrait, transfert)
        - [ok] bareme_frais: id, type_operation_id, montant_min, montant_max, valeur_frais
        - [ok] comptes_clients: id, numero_telephone, solde
        - [ok] transactions: id, client_id, type_operation_id, montant, frais, destinataire, date_operation

**Funaki ETU004169**
- [ok] Initialisation du projet
    - [ok] Creation du squelette codeinter4
    - [ok] Initialisation de la base de donnee Sqlite (MobileMoney.db)
    - [ok] Configuration de la connexion a la base de donnee (MobileMoney.db)

**Funaki ETU004169**
- [ok] Trouver un template de depart

**Funaki ETU004169**
- [ok] Creation des models:
    - [ok] BaremeFraisModel.php
    - [ok] CompteClientModel.php
    - [ok] HistoriqueTransactionModel.php
    - [ok] OperateurPrefixeModel.php
    - [ok] TypeOperationModel.php

**Funaki ETU004169**
- [] Coté Opérateur (Back-office)
    - [] Interface de configuration des préfixes valides de l'opérateur
    - [] Interface de gestion et modification du barème de frais par tranche de montant
    - [] Page de situation des gains générés via les frais (retraits et transferts)
    - [] Page de situation globale et consultation des comptes clients

**Coralie ETU004250**
- [] Coté Client (Interface Mobile)
    - [] Login automatique via le numéro de téléphone (authentification/création directe)
    - [] Consultation du solde actuel
    - [] Formulaire de dépôt (simulation automatique)
    - [] Formulaire de retrait (simulation automatique avec calcul des frais)
    - [] Formulaire de transfert vers un autre numéro (avec validation du destinataire)
    - [] Page de consultation de l'historique personnel des opérations

**Coralie ETU004250**
- [] Validations et Logique Métier
    - [] Vérification de la validité du préfixe du numéro au login
    - [] Application dynamique des frais selon la tranche du montant saisi (retrait/transfert)
    - [] Vérification du solde suffisant avant d'autoriser un retrait ou un transfert
    - [] Mise à jour du fichier Taches.md à chaque livraison intermédiaire