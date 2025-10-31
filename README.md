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

* PHP 8.1+
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

## 7. Structure MVC (Slim) — Backend

Depuis cette version, le backend est organisé avec une séparation simple mais réelle des préoccupations :

- public/index.php — Front controller minimal (bootstrap de Slim, inclusion des routes)
- src/routes.php — Déclaration centralisée des routes Slim (mapping URI -> contrôleurs)
- src/Controllers/ — Contrôleurs HTTP
  - HomeController.php — rend la page d'accueil (vue HTML)
  - Api/StatusController.php — endpoints simples (time, hello, diagnostics)
  - Api/DbController.php — utilitaires DB (ping, setup)
  - Api/UsersController.php — exemples CRUD (liste, création)
- src/Models/ — Modèles / Repositories
  - UserRepository.php — accès à la table users_demo via Medoo
- src/Infrastructure/Database.php — service Medoo (singleton), centralise la connexion
- src/Support/View.php — helper très léger pour rendre des vues PHP
- src/Views/home.php — template HTML de la page d'accueil

Comment ajouter une nouvelle route API:
1) Créez un contrôleur dans src/Controllers/Api/MyFeatureController.php
2) Ajoutez une méthode publique, par ex. getAll(Request $req, Response $res)
3) Mappez l'URL dans src/routes.php: $app->get('/api/my-feature', [new MyFeatureController(), 'getAll']);
4) Si besoin de DB, utilisez App\Infrastructure\Database::get() ou un Repository dédié.

Remarques:
- Les endpoints existants conservent les mêmes URL qu'avant.
- Medoo est accessible via Database::get() (évitez la fonction globale db()).
- Les vues restent facultatives: la majorité des routes renvoient du JSON (API).

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

---

## 14. Démarrage rapide avec Docker Compose

Cette section explique comment lancer l’environnement complet dans 3 conteneurs :
- 1 conteneur MySQL
- 1 conteneur phpMyAdmin
- 1 conteneur backend PHP (Apache)

Prérequis:
- Docker Desktop installé et démarré
- Git installé (optionnel)

Étapes:
1) Copier le fichier d’exemple d’environnement
   - Dupliquer .env.example en .env à la racine du projet puis adapter si besoin:
     - Mots de passe: MYSQL_ROOT_PASSWORD, MYSQL_PASSWORD
     - Ports: MYSQL_PORT (par défaut 3307), PHPMYADMIN_PORT (8081), APP_PORT (8080)

2) Lancer les conteneurs
   - Ouvrir un terminal dans le dossier du projet (C:\laragon\www\mediastore)
   - Exécuter: docker compose up -d

3) Accéder aux services
   - Backend (Apache/PHP): http://localhost:%APP_PORT% (ex: http://localhost:8080)
   - phpMyAdmin: http://localhost:%PHPMYADMIN_PORT% (ex: http://localhost:8081)
     - Serveur: db
     - Utilisateur: valeur de MYSQL_USER (ex: mediastore)
     - Mot de passe: valeur de MYSQL_PASSWORD (ex: changeme-app)

Notes:
- Les données MySQL sont persistées dans un volume nommé db_data.
- Les variables d’environnement de l’application (DB_HOST, DB_NAME, etc.) sont injectées via docker-compose (DB_HOST=\"db\").
- Le backend monte le dossier ./backend dans /var/www/html. Placez votre code (ex: Slim) dans mediastore/backend. Le DocumentRoot est /public.

Commandes utiles:
- Démarrer: docker compose up -d
- Arrêter: docker compose down
- Redémarrer un service: docker compose restart app
- Logs du backend: docker compose logs -f app
- Rebuild si Dockerfile modifié: docker compose build app --no-cache

Dépannage (FAQ):
- Port 3306 déjà utilisé? Modifiez MYSQL_PORT dans .env (ex: 3308), puis relancez.
- Erreur de connexion depuis le backend: vérifiez que DB_HOST=\"db\" et que les identifiants correspondent à ceux de MySQL dans .env.
- Page blanche/404 sur le backend: votre code n’est peut‑être pas encore dans backend/public. Ajoutez au minimum un fichier backend/public/index.php.

Sécurité (dev vs prod):
- Ces réglages sont pour le développement local. En production, utilisez des secrets, sauvegardes et un reverse proxy HTTPS (ex: Traefik/Nginx) et des volumes dédiés pour les uploads.



---

## Guide pas à pas — Corriger l’erreur « vendor/autoload.php manquant » et démarrer le projet

Cette erreur survient quand les dépendances PHP (Slim, Medoo, etc.) ne sont pas installées par Composer. Voici deux manières de démarrer l’application et de corriger l’erreur, selon que vous utilisiez Docker (recommandé) ou un environnement local type Laragon/XAMPP/WAMP.

### Option A — Avec Docker Compose (recommandé)

Prérequis: Docker Desktop installé et lancé.

1) Cloner le projet et préparer l’environnement
- Ouvrez un terminal dans le dossier du projet (où se trouve `docker-compose.yml`).
- Copiez la configuration d’exemple puis ajustez-la si besoin:
  - Windows PowerShell: `Copy-Item .env.example .env`
  - macOS/Linux: `cp .env.example .env`
- Vérifiez les variables dans `.env` (ports, mots de passe). Les valeurs par défaut conviennent pour un test local.

2) Lancer les conteneurs (construction incluse)
- `docker compose up -d --build`
  - Au premier démarrage, l’image PHP/Apache est construite.
  - L’entrypoint du conteneur `mediastore-app` exécute automatiquement `composer install` si le dossier `vendor/` est absent.

3) Vérifier que Composer a bien installé les dépendances
- `docker logs mediastore-app --tail=100`
  - Vous devez voir un message du type: `[entrypoint] Installing Composer dependencies...` puis la fin de l’installation.
- Si jamais les dépendances ne sont pas installées, lancez-les manuellement dans le conteneur:
  - `docker exec -it mediastore-app composer install`

4) Accéder aux services
- Backend (Slim): `http://localhost:${APP_PORT}` (par défaut: `http://localhost:8080`)
- phpMyAdmin: `http://localhost:${PHPMYADMIN_PORT}` (par défaut: `http://localhost:8081`)

5) Vérifier que l’erreur a disparu
- Ouvrez `http://localhost:${APP_PORT}`.
- Si tout va bien, vous voyez la belle page d’accueil (Bootstrap) qui liste les routes.
- Test rapide: ouvrez aussi `http://localhost:${APP_PORT}/api/diagnostics` — vous devez voir un JSON avec `vendor_exists: true`, `packages.slim/slim`, `packages.catfan/medoo`, et `db.status` ("connected" si la DB est prête).
- En cas d’erreur, vérifiez que le dossier `backend/vendor/` existe sur votre machine (il est monté depuis le conteneur). Si absent, refaites l’étape 3.

6) (Optionnel) Initialiser la base d’exemple
- Via la route: `http://localhost:${APP_PORT}/api/db/setup`
- Via le script CLI: `docker exec -it mediastore-app php scripts/setup_example_db.php`

Notes utiles:
- Le conteneur `mediastore-app` monte `./backend` vers `/var/www/html`. Le fichier `public/index.php` charge `../vendor/autoload.php`, donc le dossier `vendor/` doit exister à la racine de `backend`.
- L’Apache du conteneur pointe sur `backend/public` (DocumentRoot) et `.htaccess` est actif.

---

### Option B — Sans Docker (Laragon/XAMPP/WAMP ou PHP natif)

Prérequis: PHP 8.1+ et Composer installés sur votre machine. Sous Laragon, Composer est généralement disponible.

1) Installer les dépendances PHP
- Ouvrez un terminal dans le dossier `backend` du projet:
  - Windows PowerShell:
    - `cd backend`
    - `composer install`
  - macOS/Linux:
    - `cd backend`
    - `composer install`
- Vérifiez que `backend/vendor/autoload.php` existe.

2) Lancer le serveur de développement (au choix)
- Option 2.1 — Serveur PHP intégré:
  - `php -S 127.0.0.1:8000 -t public`
  - Accédez à `http://127.0.0.1:8000`
- Option 2.2 — Laragon/XAMPP avec Apache:
  - Configurez le VirtualHost (ou DocumentRoot) pour pointer sur le dossier `backend/public`.
  - Assurez-vous que `mod_rewrite` est actif.
  - Redémarrez Apache puis accédez au host configuré (ex: `http://mediastore.test/`).

3) Configurer la base de données (si nécessaire)
- Mettez à jour les variables d’environnement (DB_HOST/DB_NAME/DB_USER/DB_PASS) via votre outil (Laragon) ou un fichier `.env` à la racine utilisé par `docker-compose.yml` (si vous utilisez Docker pour la DB).
- Testez la connexion avec `GET /api/db/ping`.
- Créez la table d’exemple avec `GET /api/db/setup` ou via CLI: `php scripts/setup_example_db.php` (depuis `backend`).

4) Vérifier les routes de démonstration
- `GET /` — page d’accueil (doc + liens)
- `GET /api/time`
- `GET /api/hello/{name}` (ex: `/api/hello/Marie`)
- `GET /api/db/ping`
- `GET /api/db/setup`
- `GET /api/examples/users`
- `POST /api/examples/user` (Exemple: `curl -X POST http://127.0.0.1:8000/api/examples/user -H "Content-Type: application/json" -d '{"name":"Alice","email":"alice@example.com"}'`)

---

### Dépannage rapide (FAQ)
- Erreur « require vendor/autoload.php failed »: Exécutez `composer install` dans `backend` (ou laissez Docker le faire à l’étape 2). Assurez-vous que `backend/vendor/` existe bien après l’installation.
- Composer introuvable: installez Composer globalement (https://getcomposer.org/) ou utilisez l’image Docker (via `docker exec -it mediastore-app composer --version`).
- 404 sur les routes Slim: vérifiez que le serveur pointe sur `backend/public` et que `mod_rewrite` est activé (.htaccess inclus).
- DB non accessible: confirmez les variables DB (`DB_HOST`, souvent `db` en Docker), puis testez `/api/db/ping`. Consultez aussi `docker logs mediastore-db`.

Ce guide est destiné aux étudiants pour qu’ils puissent passer de l’erreur « autoload manquant » à une application Slim + Medoo fonctionnelle, étape par étape.