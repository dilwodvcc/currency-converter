<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My Application'); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="main.css">
</head>
<body>
<header class="bg-light border-bottom mb-4">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="/">My Application</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/weather">Weather</a></li>
                    <li class="nav-item"><a class="nav-link" href="/currency">Currency</a></li>
                    <li class="nav-item"><a class="nav-link" href="/telegram">Telegram</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<main class="container">
    <?php if (isset($content)) include $content; ?>
</main>

<footer class="bg-light text-center py-3 border-top">
    <p>&copy; <?= date('Y'); ?> My Application. All rights reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
