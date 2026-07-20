<!DOCTYPE html>
<html lang="fr">
<head>
    <?= $this->include('templates/head') ?>
</head>
<body>

    <?= $this->include('templates/header_operateur') ?>

    <div class="mm-shell">
        <?= $this->include('templates/sidebar_operateur') ?>

        <main class="mm-main">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->include('templates/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
