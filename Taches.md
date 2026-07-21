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

**Coralie ETU004250**
- [ok] Coté Opérateur (Back-office)
    - [ok] Login operateur
        - [ok] Formulaire de login avec champs:
            - Nom d'utilisateur
            - Mot de passe
            - Bouton "Se connecter"
        - [ok] Fonction athenticate() dans controller Operateur
            - redirect vers 'operateur/comptes' si login réussi
            - redirect vers 'operateur/login' si login échoué

    - [ok] Interface de configuration des préfixes valides de l'opérateur
        - [ok] Recuperer la liste des préfixes
        - [ok] Affichage des prefixes avec boutons (modifier & supprimer)
            - [ok] Fonction editPrefixe() dans controller Operateur pour valider les update
            - [ok] Fonction deletePrefixe() dans controller Operateur pour valider la suppression d'un prefixe
        - [ok] Formulaire d'ajout d'un nouveau prefixe
            - [ok] Fonction addPrefixe() dans controller Operateur pour valider l'insertion

    - [ok] Interface de gestion et modification du barème de frais par tranche de montant
        - [ok] Page operations.php avec liste des types d'opérations
            - [ok] Recuperer la liste des types d'operation
            - [ok] Affichage de la liste des types d'operation avec bouton 'Modifier' et 'Voir bareme'
            - [ok] CRUD complet pour les types d'opérations (ajout, modification)
        - [ok] Page bareme.php pour gérer les barèmes par type d'opération
            - [ok] Affichage de la liste des bareme par type d'operation
            - [ok] CRUD complet pour les barèmes de frais (ajout, modification, suppression)

    - [ok] Page de situation des gains générés via les frais (retraits et transferts)
        - [ok] Statistiques dynamiques (gains retrait, gains transfert, total cumulé)
        - [ok] Liste des transactions avec pagination
        - [ok] Données récupérées depuis la vue SQL vue_situation_gains

     - [ok] Page de situation globale et consultation des comptes clients
        - [ok] Recuperer la liste des clients
        - [ok] Affichage de la liste avec pagination
        - [ok] Statistiques dynamiques (nombre de comptes, solde total, solde moyen)
        - [ok] Recherche par numéro de téléphone

**Funaki ETU004169**
- [ok] Coté Client (Interface Mobile)
    - [ok] Login automatique via le numéro de téléphone (authentification/création directe)
        - [ok] Controller Client.php: login(), authenticate(), logout()
        - [ok] Session management with client_id, client_telephone, client_solde
        - [ok] Security checks via checkAuth() method
        - [ok] CSRF protection on login form
        - [ok] NoCache filter to prevent back-button access after logout
        - [ok] Routes grouped and secured in Routes.php
        - [ok] Default route redirects to login
    - [ok] Consultation du solde actuel
        - [ok] Dynamic balance display from session
        - [ok] Real-time balance updates after operations
        - [ok] Sidebar navigation with active page highlighting
        - [ok] Transaction history display on dashboard with pagination
        - [ok] Filter by operation type (all, depot, retrait, transfert)
        - [ok] "Voir tout" button to show all transactions
        - [ok] Real pagination with page navigation
    - [ok] Consultation des Historiques du comptes
        - [ok] Controller Client.php: historique() method with pagination
        - [ok] Parameters: page, per_page, filter
        - [ok] Transaction history retrieval via HistoriqueTransactionModel
        - [ok] Filter by operation type (DEPOT, RETRAIT, TRANSFERT)
        - [ok] Pagination with array_slice
        - [ok] View historique.php: dynamic table with real data
        - [ok] Filter dropdown with onchange redirect
        - [ok] Functional pagination (Précédent/Suivant, page numbers)
        - [ok] Disabled state for pagination buttons at boundaries
        - [ok] Transaction counter: "X sur Y transaction(s)"
        - [ok] Empty state when no transactions found
        - [ok] Dynamic badges and signs (+/-) based on operation type  
    - [ok] Formulaire de dépôt (simulation automatique)
        - [ok] Controller Client.php: processDepot() method
        - [ok] Validation: montant positif
        - [ok] No fees for deposit (frais = 0)
        - [ok] Database transaction for integrity
        - [ok] Balance update in comptes_clients table (addition)
        - [ok] Transaction history insertion in historique_transactions table
        - [ok] Session update with new balance
        - [ok] Flash messages (success/error) with Bootstrap alerts
        - [ok] View depot.php: form with CSRF, dynamic IDs
        - [ok] JavaScript depot.js: real-time validation, summary update
        - [ok] Client-side: minimum 100 Ar, maximum 10 000 000 Ar
        - [ok] Real-time summary: montant, frais (0), nouveau solde
        - [ok] Visual indicators (green for deposit)
        - [ok] Double submission prevention with loading state
    - [ok] Formulaire de transfert vers un autre numéro (avec validation du destinataire)
        - [ok] SQL script: ajouter_nom_comptes.sql to add nom and prenom columns
        - [ok] CompteClientModel: updated allowedFields to include nom, prenom
        - [ok] Controller Client.php: rechercherClient() AJAX endpoint
        - [ok] AJAX search by phone number (excluding current user)
        - [ok] Returns client id, nom, prenom, numero_telephone
        - [ok] Controller Client.php: processTransfert() method
        - [ok] Validation: montant positif, destinataire valide (pas soi-même)
        - [ok] Fee calculation using BaremeFraisModel (same as retrait)
        - [ok] Balance check including fees
        - [ok] Database transaction: deduct from sender, add to recipient
        - [ok] Transaction history insertion with source and destination
        - [ok] Session update with new sender balance
        - [ok] Flash messages with recipient name in success message
        - [ok] View transfert.php: form with CSRF, dynamic IDs
        - [ok] AJAX recipient lookup display (nom + prenom)
        - [ok] Hidden field for destinataire_id
        - [ok] JavaScript transfert.js: real-time validation, AJAX lookup
        - [ok] Debounced AJAX search (500ms)
        - [ok] Client-side: minimum 100 Ar, balance checks
        - [ok] Real-time summary: montant, frais, nouveau solde
        - [ok] Visual indicators for low balance
        - [ok] Submit button disabled until recipient found and amount valid
        - [ok] Double submission prevention with loading state
        - [ok] Controller Client.php: processRetrait() method
        - [ok] Validation: montant positif, solde suffisant, solde incluant frais
        - [ok] Automatic fee calculation using BaremeFraisModel
        - [ok] Database transaction for integrity
        - [ok] Balance update in comptes_clients table
        - [ok] Transaction history insertion in historique_transactions table
        - [ok] Session update with new balance
        - [ok] Flash messages (success/error) with Bootstrap alerts
        - [ok] View retrait.php: form with CSRF, dynamic IDs
        - [ok] JavaScript retrait.js: real-time validation, fee calculation, summary update
        - [ok] Client-side: minimum 100 Ar, balance checks, visual indicators
        - [ok] Double submission prevention with loading state
    - [ok] Page profil.php avec statistiques et graphique
        - [ok] HistoriqueTransactionModel: getStatistiquesClient() method
        - [ok] Statistics: total transactions, count by type (depot, retrait, transfert envoyé/recu)
        - [ok] Statistics: total amounts by type
        - [ok] Statistics: transactions grouped by month for chart
        - [ok] CompteClientModel: updated validation rules for name update
        - [ok] Controller Client.php: profil() method
        - [ok] Controller Client.php: updateProfil() method
        - [ok] View profil.php: personal info form (nom, prenom modifiables, telephone non modifiable)
        - [ok] View profil.php: statistics cards with counts and amounts
        - [ok] View profil.php: Chart.js line chart for monthly evolution
        - [ok] Chart.js: 4 datasets (depot, retrait, transfert envoyé, transfert recu)
        - [ok] Chart.js: minimalist design with lines, filled areas
        - [ok] Routes.php: added profil and updateProfil routes
        - [ok] sidebar_client.php: added "Mon profil" link

**Coralie ETU004250**
- [ok] Securite
    - [ok] Securiser les url, on ne peut pas naviguer sans etre connecter
        - [ok] Creer AhthFilter.php pour verifier la connection
        - [ok] Securiser les routes

# Mobile Money - Version 2

**Funaki ETU004169**
- [ok] Option : Inclure les frais de retrait lors de l'envoi (Frais cumulés)
    - [ok] Modifier la méthode `processTransfert()` pour récupérer l'état du switch d'inclusion des frais de retrait
    - [ok] Adapter l'algorithme financier de calcul des frais :
    - [ok] Étape 1 : Si coché, chercher d'abord les frais théoriques dans `bareme_frais` pour un RETRAIT (type 2) basé sur le montant de base divisé par destinataire
    - [ok] Étape 2 : Définir le nouveau sous-total à transférer : `Montant_Transfert = Montant_de_base + Frais_Retrait_Theorique`
    - [ok] Étape 3 : Calculer les frais de TRANSFERT finaux (type 3) à partir de ce nouveau sous-total
    - [ok] Étape 4 : Créditer le destinataire du sous-total `Montant_Transfert` complet (Montant + frais de retrait inclus)
    - [ok] Étape 5 : Débiter l'expéditeur de `Montant_Transfert + Frais_Transfert_Finaux`
    - [ok] Mettre à jour `transfert.js` pour refléter exactement ce double calcul dynamique sur le récapitulatif visuel avant soumission
    - [ok] Garantir que les contraintes de solde minimum vérifient bien ce grand total cumulé

- [ok] Option : Envoi multiple vers plusieurs numéros simultanés
    - [ok] Évolution de la base de données : ajouter une colonne `reference_groupe TEXT NULL` dans la table `historique_transactions` pour identifier les envois groupés
    - [ok] Modifier l'interface `transfert.php` : remplacer l'input de téléphone par un `<textarea>` acceptant plusieurs numéros séparés par des virgules
    - [ok] Adapter le script `transfert.js` : parser la saisie, compter les destinataires et afficher en temps réel la part brute allouée à chacun (`Montant_Total / Nbr_Destinataires`)
    - [ok] Gérer l'algorithme d'envoi dans le contrôleur :
    - [ok] Découper les numéros, valider l'existence de chaque compte
    - [ok] Exécuter la boucle de calcul (en appliquant la règle des frais cumulés ou standards par tranche individuelle) à l'intérieur d'un bloc `$db->transStart()`
    - [ok] Si le solde total requis pour l'ensemble des numéros dépasse le solde de l'expéditeur, effectuer un rollback complet

**Coralie ETU004250**
- [ok] Coté opérateur
    - [ok] Configuration des préfixes valable pour les autres opérateurs (ex: 032 et 031, …)
        - [ok] Creer/Mofifier tables/view:
            - [ok] table `operateur`
            - [ok] table `operateur_prefixe`
            - [ok] table `historique_transactions`
            - [ok] view `vue_historique_portefeuille_clients`
        - [ok] Rearanger/Creer les models
            - [ok] `OperateurPrefixeModel`
            - [ok] `HistoriqueTransactionModel`
        - [ok] Modifier la page `prefixes.php`
            - [ok] Formulaire d'ajout de prefixe
                - [ok] Ajouter champ pour choisir a quel operateur appartient le prefixe
            - [ok] Tableau d'affichage des prefixes
                - [ok] Tableau 1: liste des prefixes de mon operateur
                - [ok] Tableau 2: liste des prefixes des autres operateurs

    - [ok] Configuration % en plus de commissions pour les transferts vers les autres opérateurs 
        - [ok] Creer tables:
            - [ok] table `configuration_commissions`
        - [ok] Creer les models:
            - [ok] `ConfigurationCommissionModel`
        - [ok] Creer page `commission.php`
            - [ok] Afficher la liste des % de commission entre mon operateur et les autres operateurs
            - [ok] Bouton `Modifier` et  `Supprimer` pour chaque % de commission
            - [ok] Formulaire d'ajout d'un nouveau % de commission
        - [ok] Ajouter dans menu de l'operateur le lien qui mene vers `commission.php`

    - [ok] Sur la page “Situation gain via les différents frais” , séparer opérateur et autres opérateurs
        - [ok] Mofifier view:
            - [ok] view `vue_situation_gains`
        - [ok] Creer fonction `obtenirGainsSepares()` pour recuperer le gains otenu par mon operateur et par les 
        autres operateurs sur les `transferts` ou `retrait`
        - [ok] Modifier la page `gains.php` pour afficher les gains séparés
        - [ok] Ajouter un filtre par type d'operation (transfert/retrait)

    - [ok] Situation des montants à envoyer à chaque opérateur
        - [ok] Creer fonction `obtenirMontantsParOperateur()` pour recuperer le montant verser vers chaque operateur different de mon operateur
        - [ok] Creer page `montant.php`
            - [ok] Afficher la somme total cumuler des autres operateur
            - [ok] Afficher dans un tableau le montant a envoyer pour chaque operateur different de mon operateur


- [ok] Corriger la fonction qui insert un nouvel historique_transaction

# Alea 1
Commission de reduction en % de frais de transfert de meme operateur
- [] table 'commission_reduction'
- [] cree model de la nouvelle table
- [] appliquer la reduction lors du transfert vers meme operateur (Client.php controller)
- [] verification resultat

# Alea 2 
Creation d'une epargne pour chaque client 
- [] creation de table 'epargnes'
- [] creation du model epargnes 
- [] creation du function d'insertion de l'epargnes 
- [] Ajouter la page  pour inserer une epargne
- [] creer la routes vers la page 
- [] Ajouter le lients dans le sidebar vers la pages
- [] Ajoutrer la condition dans process transfert pour separer l'argent 