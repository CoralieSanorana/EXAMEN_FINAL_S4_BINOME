<!DOCTYPE html>
<html lang="fr">
<head>
    <?= $this->include('templates/head') ?>
</head>
<body>

    <?= $this->include('templates/header_client') ?>

    <div class="mm-shell">
        <?= $this->include('templates/sidebar_client') ?>
        <div class="mm-sidebar-backdrop" aria-hidden="true"></div>

        <main class="mm-main mm-client-main">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->include('templates/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const sidebar = document.getElementById('mobileSidebar');
            const toggle = document.querySelector('.mm-sidebar-toggle');
            const backdrop = document.querySelector('.mm-sidebar-backdrop');
            if (!sidebar || !toggle || !backdrop) return;
            const closeMenu = () => {
                sidebar.classList.remove('is-open');
                backdrop.classList.remove('is-visible');
                document.body.classList.remove('mm-menu-open');
                toggle.setAttribute('aria-expanded', 'false');
            };
            toggle.addEventListener('click', () => {
                const isOpen = sidebar.classList.toggle('is-open');
                backdrop.classList.toggle('is-visible', isOpen);
                document.body.classList.toggle('mm-menu-open', isOpen);
                toggle.setAttribute('aria-expanded', String(isOpen));
            });
            backdrop.addEventListener('click', closeMenu);
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
            sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
        })();
    </script>
</body>
</html>
