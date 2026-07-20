# TODO - Simulateur d'Opérateur Mobile Money (Version 1)

## Description du projet
Ce projet consiste à développer un système web simulant un opérateur de mobile money. Le projet est réalisé en binôme sur une durée de lundi et mardi, en utilisant le framework PHP CodeIgniter 4 avec une base de données SQLite embarquée, et une interface en HTML, CSS, JS (Bootstrap ou équivalent). La livraison finale et intermédiaire se fait sous forme de tags Git (`v1`) sur un dépôt public, accompagnée d'un fichier `Taches.md` listant les travaux par étudiant et d'un script unique `base.sql`.

---

- [ ] **Base de données :**
    - [ ] `->` Créer le script unique `base.sql` à la racine du projet contenant la création des tables, vues et données initiales.
    - [ ] `->` Table de configuration de l'opérateur (stockage des préfixes valides comme 033, 037).
    - [ ] `->` Table des types d'opérations (dépôt, retrait, transfert).
    - [ ] `->` Table des barèmes de frais par tranche de montant (configurable/modifiable).
    - [ ] `->` Table des comptes clients (numéro de téléphone, solde, etc.).
    - [ ] `->` Table d'historique des transactions/opérations effectuées.

- [ ] **Pages & Fonctionnalités (Version 1) :**
    - [ ] **Coté Opérateur :**
        - [ ] `->` Interface de configuration des préfixes valides de l'opérateur (ex: 033 et 037).
        - [ ] `->` Interface de gestion/modification des types d'opérations et de leurs barèmes de frais par tranche de montant.
        - [ ] `->` Page de consultation de la situation des gains via les différents frais (retrait et transfert).
        - [ ] `->` Page de consultation globale de la situation des comptes clients.
    - [ ] **Coté Client :**
        - [ ] `->` Page de Login automatique avec le numéro de téléphone (sans inscription préalable).
        - [ ] `->` Interface de consultation du solde actuel.
        - [ ] `->` Formulaire pour effectuer un dépôt (supposé automatique).
        - [ ] `->` Formulaire pour effectuer un retrait (supposé automatique).
        - [ ] `->` Formulaire pour effectuer un transfert vers un autre numéro.
        - [ ] `->` Page de consultation de l'historique des opérations du client.

- [ ] **Gestion du Projet & Livrables :**
    - [ ] `->` Remplir le formulaire d'informations initiales au début du projet (https://forms.gle/nCv6xJYHVvVj2FKA).
    - [ ] `->` Créer le fichier `Taches.md` à la racine pour suivre les travaux effectués par chaque étudiant.
    - [ ] `->` Mettre à jour `Taches.md` avec les nouveaux travaux pour la livraison.
    - [ ] `->` Effectuer la livraison de la Version 1 avant 13h en créant et poussant le tag `v1`.