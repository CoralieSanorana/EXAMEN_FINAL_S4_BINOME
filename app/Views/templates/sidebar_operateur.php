<aside id="mobileSidebar" class="mm-sidebar" aria-label="Navigation principale">
    <div class="mm-nav-title">Supervision</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= uri_string() === 'operateur/gains' ? 'active' : '' ?>" href="<?= base_url('operateur/gains'); ?>">
            <i class="bi bi-graph-up-arrow"></i> Situation des gains
        </a>
        <a class="nav-link <?= uri_string() === 'operateur/comptes' ? 'active' : '' ?>" href="<?= base_url('operateur/comptes'); ?>">
            <i class="bi bi-people"></i> Comptes clients
        </a>
    </nav>

    <div class="mm-nav-title">Configuration</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= uri_string() === 'operateur/prefixes' ? 'active' : '' ?>" href="<?= base_url('operateur/prefixes'); ?>">
            <i class="bi bi-sliders"></i> Préfixes opérateur
        </a>
        <a class="nav-link <?= (uri_string() === 'operateur/operations' || strpos(uri_string(), 'operateur/bareme') === 0) ? 'active' : '' ?>" href="<?= base_url('operateur/operations'); ?>">
            <i class="bi bi-list-check"></i> Types &amp; barèmes de frais
        </a>
        <a class="nav-link <?= uri_string() === 'operateur/commissions' ? 'active' : '' ?>" href="<?= base_url('operateur/commissions'); ?>">
            <i class="bi bi-list-check"></i> Commissions
        </a>
    </nav>

    <div class="mm-nav-title">Compte</div>
    <nav class="nav flex-column">
        <a class="nav-link" href="<?= base_url('operateur/logout'); ?>">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </nav>
</aside>
