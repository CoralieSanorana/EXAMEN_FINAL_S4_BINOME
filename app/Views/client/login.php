<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>

<div class="mm-auth-wrapper">
    <section class="mm-auth-shell" aria-label="Connexion MobileMoney">
        <aside class="mm-auth-showcase">
            <a href="/client/login" class="mm-auth-brand" aria-label="MobileMoney">
                <span class="mm-auth-logo"><span>M</span><span>M</span></span>
                <span>MobileMoney</span>
            </a>
            <div class="mm-auth-showcase-content">
                <span class="mm-auth-eyebrow"><i class="bi bi-shield-check"></i> Paiements sécurisés</span>
                <h1>Votre argent,<br>simplement maîtrisé.</h1>
                <p>Une expérience de paiement claire, rapide et conçue pour vous accompagner au quotidien.</p>
            </div>
            <div class="mm-auth-assurance">
                <i class="bi bi-lock-fill"></i>
                <span>Accès chiffré et sécurisé</span>
            </div>
        </aside>

        <div class="mm-auth-panel">
            <div class="mm-auth-panel-inner">
                <div class="mm-auth-mobile-brand"><span class="mm-auth-logo"><span>M</span><span>M</span></span> MobileMoney</div>
                <span class="mm-auth-role"><i class="bi bi-person-fill"></i> Espace client</span>
                <h2>Bienvenue</h2>
                <p class="mm-auth-intro">Connectez-vous avec votre numéro de téléphone.</p>

                <form action="/client/authenticate" method="post" id="loginForm">
                    <?= csrf_field() ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" for="telephoneInput">Numéro de téléphone</label>
                        <div class="input-group mm-auth-input">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="text" name="telephone" class="form-control" id="telephoneInput" placeholder="033 12 345 67" value="<?= old('telephone') ?>" autocomplete="tel" required>
                        </div>
                        <div class="form-text">Un compte est créé automatiquement si ce numéro est nouveau.</div>
                        <div id="telephoneError" class="text-danger small mt-1" style="display:none;"></div>
                    </div>
                    <button type="submit" class="btn btn-mm-primary mm-auth-submit w-100 mt-2" id="submitBtn">
                        Continuer <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="mm-auth-divider"><span>ou</span></div>
                <a href="/operateur/login" class="mm-auth-switch" data-auth-switch>
                    <span class="mm-auth-switch-icon"><i class="bi bi-shield-lock"></i></span>
                    <span><strong>Accès opérateur</strong><small>Administration et supervision</small></span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <p class="mm-auth-footer">MobileMoney Simulateur &copy; 2026</p>
            </div>
        </div>
    </section>
</div>

<script src="/assets/js/Cllient/login.js"></script>

<?= $this->endSection() ?>