<!DOCTYPE html>
<html lang="fr">
<head>
    <?= $this->include('templates/head') ?>
</head>
<body>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
