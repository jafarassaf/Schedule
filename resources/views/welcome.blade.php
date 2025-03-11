<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application de Gestion des Plannings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f8fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .welcome-card {
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card welcome-card p-5">
            <div class="text-center mb-4">
                <h1 class="display-4 fw-bold">Gestion des Plannings</h1>
                <p class="lead">Application de gestion des horaires de travail des employés</p>
            </div>
            
            <div class="text-center mb-4">
                <p>Bienvenue ! Cette application vous permet de gérer facilement les plannings de vos employés.</p>
            </div>
            
            <div class="d-grid gap-2 col-6 mx-auto">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    Connexion
                </a>
            </div>
        </div>
    </div>
</body>
</html>
