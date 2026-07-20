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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('auth-enter');
            document.querySelectorAll('[data-auth-switch]').forEach(link => {
                link.addEventListener('click', event => {
                    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    document.body.classList.remove('auth-enter');
                    document.body.classList.add('auth-leave-left');
                    window.setTimeout(() => { window.location.href = link.href; }, 360);
                });
            });
        });
    </script>
</body>
</html>
