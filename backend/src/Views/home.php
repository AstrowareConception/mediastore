<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mediastore API — Démo Slim + Medoo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:linear-gradient(120deg,#0d6efd0d,#6610f20d)}
    .card{box-shadow:0 10px 30px rgba(0,0,0,.05);border:0}
    pre{background:#0d1117;color:#c9d1d9;border-radius:.5rem;padding:1rem;}
    code{color:#e6edf3}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Mediastore</a>
    <span class="badge text-bg-primary">ENV: <?= htmlspecialchars($appEnv) ?></span>
  </div>
</nav>
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body p-4">
            <h1 class="h3">Bienvenue 👋 — Slim 4 + Medoo</h1>
            <p class="text-secondary">Voici une mini‑documentation interactive pour démarrer. Cliquez les liens pour tester les routes. Le code source est désormais séparé en contrôleurs, modèles et vues (voir dossier src/).</p>

            <h2 class="h5 mt-4">1) Routes simples (sans base de données)</h2>
            <ul>
              <li><a href="/api/time" class="link-primary">GET /api/time</a> — renvoie l'heure serveur en JSON</li>
              <li><a href="/api/hello/Marie" class="link-primary">GET /api/hello/{name}</a> — exemple de paramètre d'URL</li>
              <li><a href="/api/diagnostics" class="link-primary">GET /api/diagnostics</a> — diagnostic rapide de l'environnement</li>
            </ul>

            <h2 class="h5 mt-4">2) Base de données (Medoo)</h2>
            <ol>
              <li>
                <a class="btn btn-sm btn-outline-primary" href="/api/db/ping">Tester la connexion DB</a>
                <span class="text-secondary ms-2">SELECT 1 via Medoo</span>
              </li>
              <li class="mt-2">
                <a class="btn btn-sm btn-outline-success" href="/api/db/setup">Créer la table d'exemple</a>
                <span class="text-secondary ms-2">idempotent: crée la table users_demo et insère 2 lignes</span>
              </li>
              <li class="mt-2">
                <a class="btn btn-sm btn-outline-dark" href="/api/examples/users">Lister les utilisateurs</a>
                <span class="text-secondary ms-2">GET /api/examples/users</span>
              </li>
            </ol>

            <h2 class="h5 mt-4">3) Insérer un utilisateur (POST)</h2>
            <p class="text-secondary">Exemple d'appel cURL :</p>
            <pre><code>curl -X POST http://localhost:8080/api/examples/user \
  -H "Content-Type: application/json" \
  -d '{"name":"Alice","email":"alice@example.com"}'
</code></pre>

            <p class="mt-4">Consultez src/Controllers et src/Models pour voir comment prolonger ce starter.</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-header">Configuration</div>
          <div class="card-body">
            <ul class="list-unstyled small text-secondary">
              <li><strong>DB_HOST:</strong> <?= htmlspecialchars($dbInfo['host']) ?></li>
              <li><strong>DB_NAME:</strong> <?= htmlspecialchars($dbInfo['name']) ?></li>
              <li><strong>DB_USER:</strong> <?= htmlspecialchars($dbInfo['user']) ?></li>
            </ul>
            <a class="btn btn-outline-secondary w-100" href="/">Rafraîchir</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
