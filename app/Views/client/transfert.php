<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un transfert</h1>
    <p>Envoyez de l'argent vers un autre numéro mobile money.</p>
</div>

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

<div class="row">
    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-arrow-left-right"></i> Formulaire de transfert</div>

            <form action="/client/processTransfert" method="post" id="transfertForm">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Numéro émetteur</label>
                    <input type="text" class="form-control" value="<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Numéro destinataire</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="text" name="numero_destinataire" class="form-control" id="numeroDestinataire" placeholder="Ex : 037 44 556 78" required>
                    </div>
                    <div id="destinataireInfo" class="mt-2" style="display:none;">
                        <div class="alert alert-success py-2 px-3 mb-0">
                            <i class="bi bi-person-check"></i> 
                            <span id="destinataireNom"></span>
                        </div>
                    </div>
                    <div id="destinataireError" class="text-danger small mt-1" style="display:none;"></div>
                    <input type="hidden" name="destinataire_id" id="destinataireId">
                </div>

                <div class="mb-4">
                    <label class="form-label">Montant à transférer (Ar)</label>
                    <div class="input-group">
                        <input type="number" name="montant" class="form-control" id="montantInput" placeholder="Ex : 50000" min="100" required>
                        <span class="input-group-text">Ar</span>
                    </div>
                    <div class="form-text">Solde disponible : <span id="soldeDisponible"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?></span> Ar</div>
                    <div id="montantError" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                <button type="submit" class="btn btn-mm-primary w-100" id="submitBtn" disabled>
                    <i class="bi bi-check-lg"></i> Confirmer le transfert
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-info-circle"></i> Résumé des frais</div>
            <ul class="list-unstyled mb-0" style="font-size:.9rem;">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Solde actuel</span>
                    <span id="soldeActuel"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant à transférer</span>
                    <span id="montantTransfert">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais applicables</span>
                    <span id="fraisApplicables">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span class="text-muted">Nouveau solde estimé</span>
                    <span class="fw-semibold" id="nouveauSolde"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script src="/assets/js/Cllient/transfert.js"></script>

<?= $this->endSection() ?>
