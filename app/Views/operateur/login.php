<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>

<div class="mm-auth-wrapper mm-auth-operator">
    <section class="mm-auth-shell" aria-label="Connexion opérateur MobileMoney">
        <aside class="mm-auth-showcase">
            <a href="/client/login" class="mm-auth-brand" aria-label="MobileMoney">
                <span class="mm-auth-logo"><span>M</span><span>M</span></span>
                <span>MobileMoney</span>
            </a>
            <div class="mm-auth-showcase-content">
                <span class="mm-auth-eyebrow"><i class="bi bi-bar-chart-fill"></i> Centre de supervision</span>
                <h1>Pilotez vos opérations,<br>en toute confiance.</h1>
                <p>Une vue de gestion fiable pour suivre les transactions, comptes et paramètres de votre service.</p>
            </div>
            <div class="mm-auth-assurance">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Accès réservé aux opérateurs</span>
            </div>
        </aside>

        <div class="mm-auth-panel">
            <div class="mm-auth-panel-inner">
                <div class="mm-auth-mobile-brand"><span class="mm-auth-logo"><span>M</span><span>M</span></span> MobileMoney</div>
                <span class="mm-auth-role mm-auth-role-operator"><i class="bi bi-shield-lock-fill"></i> Espace opérateur</span>
                <h2>Connexion sécurisée</h2>
                <p class="mm-auth-intro">Identifiez-vous pour accéder au centre de supervision.</p>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger"><?= session('error') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('operateur/authenticate'); ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="operatorEmail">Adresse e-mail</label>
                        <div class="input-group mm-auth-input">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="operatorEmail" class="form-control" value="operateur@gmail.com" autocomplete="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="operatorPassword">Mot de passe</label>
                        <div class="input-group mm-auth-input">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="operatorPassword" class="form-control" value="operateur123" autocomplete="current-password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-mm-primary mm-auth-submit w-100 mt-2">
                        Accéder à la supervision <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="mm-auth-divider"><span>ou</span></div>
                <a href="/client/login" class="mm-auth-switch" data-auth-switch>
                    <span class="mm-auth-switch-icon"><i class="bi bi-person"></i></span>
                    <span><strong>Accès client</strong><small>Gérer votre compte et vos opérations</small></span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <p class="mm-auth-footer">MobileMoney Simulateur &copy; 2026</p>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>