<aside id="mobileSidebar" class="mm-sidebar" aria-label="Navigation principale">
    <div class="mm-nav-title">Mon compte</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= uri_string() === 'client/solde' ? 'active' : '' ?>" href="/client/solde">
            <i class="bi bi-wallet2"></i> Solde actuel
        </a>
        <a class="nav-link <?= uri_string() === 'client/historique' ? 'active' : '' ?>" href="/client/historique">
            <i class="bi bi-clock-history"></i> Historique
        </a>
    </nav>

    <div class="mm-nav-title">Opérations</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= uri_string() === 'client/depot' ? 'active' : '' ?>" href="/client/depot">
            <i class="bi bi-download"></i> Dépôt
        </a>
        <a class="nav-link <?= uri_string() === 'client/retrait' ? 'active' : '' ?>" href="/client/retrait">
            <i class="bi bi-upload"></i> Retrait
        </a>
        <a class="nav-link <?= uri_string() === 'client/transfert' ? 'active' : '' ?>" href="/client/transfert">
            <i class="bi bi-arrow-left-right"></i> Transfert
        </a>
    </nav>

    <div class="mm-nav-title">Compte</div>
    <nav class="nav flex-column">
        <a class="nav-link <?= uri_string() === 'client/profil' ? 'active' : '' ?>" href="/client/profil">
            <i class="bi bi-person"></i> Mon profil
        </a>
        <a class="nav-link" href="/client/logout">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </nav>
</aside>
