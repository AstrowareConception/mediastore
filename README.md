# Cahier des charges — Coffre‑fort numérique (Projet RP BTS SIO)

---

## 1. Contexte & objectifs

**Objectif général :** créer un coffre‑fort numérique sécurisé permettant de déposer, organiser et partager des fichiers chiffrés.

**Public :** Étudiants BTS SIO 2ᵉ année (SLAM & SISR)

**Objectifs pédagogiques :**

* Industrialiser le développement avec **Slim (PHP MVC)** + **Medoo (micro‑ORM)** ;
* Mettre en œuvre un **double front** : Web (client léger) et JavaFX (client lourd) ;
* Intégrer des notions de **sécurité, chiffrement, API REST, CI/CD** ;
* Produire des livrables conformes aux annexes E4/E5 (portfolio, fiches, environnement).

---

## 2. Périmètre fonctionnel

### 2.1 Rôles utilisateurs

* **Utilisateur** : dépose, structure, partage et suit ses fichiers ;
* **Destinataire** : télécharge via lien sécurisé ;
* **Administrateur** : gère quotas et supervise les logs.

### 2.2 Cas d’usage principaux

1. Dépôt et chiffrement des fichiers.
2. Organisation en dossiers/catégories.
3. Génération d’un lien de partage sécurisé (temporaire ou permanent).
4. Révocation de lien et suivi des téléchargements.
5. Gestion du quota par utilisateur.

---

## 3. MVP — Fonctionnalités minimales

### Authentification & comptes

* Création de comptes utilisateurs (email + mot de passe Argon2id).
* Connexion/déconnexion sécurisée (JWT).
* Quotas définis par utilisateur (1 Go par défaut).

### Gestion des fichiers

* Upload, suppression, renommage, déplacement.
* Chiffrement automatique (AES‑256‑GCM).
* Organisation en dossiers hiérarchiques.

### Partage & accès

* Génération de lien signé (HMAC/JWT) : durée limitée ou permanente.
* Révocation manuelle.
* Journalisation des téléchargements (horodatage, IP, user agent).

### Suivi & interface

* Tableau de bord : espace utilisé, partages actifs, téléchargements.
* Front Web complet.
* Client lourd JavaFX : login, liste de fichiers, upload, création de lien.

---

## 4. Améliorations futures (lots post‑MVP)

* 2FA TOTP (double authentification).
* Limite de téléchargements par lien.
* Notifications de téléchargement.
* Prévisualisation fichiers (PDF, images).
* Tableau de bord d’administration (logs, alertes).
* Version mobile et monétisation (paliers de quotas, options payantes).

---

## 5. Exigences non fonctionnelles

* Communication HTTPS obligatoire.
* Performances : upload jusqu’à 200 Mo (paramétrable).
* Sauvegardes et restauration testées.
* Logs structurés (JSON).
* Respect du RGPD (droit à l’effacement, export des données).

---

## 6. Architecture technique

### Backend (Slim + Medoo)

* PHP 8.2+
* Framework : **Slim (MVC, routes REST)**
* ORM : **Medoo**
* SGBD : MySQL/MariaDB ou PostgreSQL
* Sécurité : libsodium/OpenSSL, JWT, Argon2id

### Front Web

* HTML5, CSS3, JavaScript (Bootstrap recommandé)
* Interface : login, gestion dossiers/fichiers, génération liens

### Client lourd (JavaFX)

* Authentification + interactions via API REST
* Upload et gestion des fichiers chiffrés
* Création et affichage des liens partagés

### Déploiement

* Serveur Web (nginx ou Apache + PHP‑FPM)
* Docker pour environnement de développement (optionnel)

---

## 7. Modèle de données simplifié

* **users**(id, email, pass_hash, quota_total, quota_used, created_at)
* **folders**(id, user_id, parent_id, name)
* **files**(id, folder_id, user_id, filename, size, enc_key, iv, auth_tag, created_at)
* **shares**(id, user_id, label, expires_at, max_downloads, revoked)
* **downloads**(id, share_id, file_id, date, ip, user_agent)

---

## 8. Endpoints principaux (API)

* `POST /auth/login` — Connexion
* `GET /folders` — Liste des dossiers
* `POST /files/upload` — Dépôt d’un fichier
* `POST /shares` — Création d’un lien
* `GET /shares` — Liste des liens
* `GET /s/{token}` — Téléchargement public

---

## 9. Tests & validation

* Upload > 50 Mo fonctionne.
* Fichiers stockés chiffrés ; vérif intégrité OK.
* Liens expirés → 403/410.
* Quota respecté et message clair.
* Journalisation complète.

---

## 10. Livrables attendus

* Dépôt GitHub (issues, branches, README).
* Documentation technique (schémas, OpenAPI, installation).
* Procédure de sauvegarde/restauration.
* Portfolio et fiches E4/E5 à jour (annexes 8‑9‑10).

---

## 11. Planning prévisionnel (8 journées de travail intensives)

* **Jour 1** : Initialisation du projet, configuration Slim + Medoo, création BDD, authentification de base.
* **Jour 2** : Mise en place du modèle de données et des premières routes API (utilisateurs, dossiers, fichiers).
* **Jour 3** : Upload de fichiers avec chiffrement + gestion des quotas.
* **Jour 4** : Génération et gestion des liens sécurisés (création, révocation, expiration).
* **Jour 5** : Journalisation des téléchargements + tableau de bord Web.
* **Jour 6** : Intégration du front Web complet (auth, explorateur, partage, suivi).
* **Jour 7** : Développement du client lourd JavaFX (authentification, upload, partage).
* **Jour 8** : Tests, corrections, documentation et démo finale + livrables E4/E5.

---

## 12. Points à trancher / personnaliser

* **Taille maximale des fichiers :** à définir selon contraintes réseau/serveur.
* **Quota par utilisateur :** valeur par défaut à fixer (ex. 1 Go), ajustable ensuite.
* **Durée d’expiration des liens :** à préciser (ex. 3 à 7 jours par défaut).
* **Technologie de chiffrement :** les étudiants réalisent une **veille comparative** (AES‑256, ChaCha20, etc.) et justifient leur choix.
* **Client lourd dans le MVP :** inclus, mais version plus simple que le front Web (fonctionnalités limitées à dépôt et lecture).

---

## 13. Hors périmètre MVP (acté)

* **Paiement et monétisation :** non implémenté dans la V1, mais réflexion demandée comme **évolution future**.

* **Application mobile :** non prévue.

* **Chiffrement côté client (zero‑knowledge) :** à étudier dans le cadre d’une **veille technique**.

* **Analyse comportementale / logs externes :** non concerné par ce projet.
