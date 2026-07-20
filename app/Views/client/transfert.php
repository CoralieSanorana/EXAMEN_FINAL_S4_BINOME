<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un transfert</h1>
    <p>Envoyez de l'argent vers un ou plusieurs numéros mobile money.</p>
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
                    <input type="text" class="form-control" id="numeroEmetteur" value="<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="numerosDestinataires">Numéros destinataires</label>
                    <div class="input-group">
                        <span class="input-group-text align-items-start"><i class="bi bi-phone"></i></span>
                        <textarea
                            name="numero_destinataires"
                            class="form-control"
                            id="numerosDestinataires"
                            rows="5"
                            placeholder="Ex : 0374455678, 0331234567"
                            required
                        ><?= esc(old('numero_destinataires')) ?></textarea>
                    </div>
                    <div class="form-text">
                        Saisissez un ou plusieurs numéros séparés par des virgules. Les retours à la ligne et points-virgules sont aussi acceptés.
                    </div>
                    <div id="destinatairesInfo" class="mt-2" style="display:none;">
                        <div class="alert alert-success py-2 px-3 mb-0">
                            <div><i class="bi bi-people-fill"></i> <span id="destinatairesResume"></span></div>
                            <div class="small mt-1" id="destinatairesPreview"></div>
                        </div>
                    </div>
                    <div id="destinatairesError" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant total à répartir (Ar)</label>
                    <div class="input-group">
                        <input type="number" name="montant" class="form-control" id="montantInput" placeholder="Ex : 50000" min="100" step="0.01" value="<?= esc(old('montant')) ?>" required>
                        <span class="input-group-text">Ar</span>
                    </div>
                    <div class="form-text">Solde disponible : <span id="soldeDisponible"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?></span> Ar</div>
                    <div id="montantError" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetrait" value="1" <?= old('inclure_frais_retrait') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="inclureFraisRetrait">
                            Inclure les frais de retrait dans le montant envoyé
                        </label>
                    </div>
                    <div class="form-text">
                        Si activé, chaque destinataire reçoit sa part de base augmentée des frais théoriques de retrait, puis les frais de transfert sont calculés individuellement sur ce sous-total.
                    </div>
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
                    <span class="text-muted">Nombre de destinataires</span>
                    <span id="nombreDestinataires">0</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant de base total</span>
                    <span id="montantBase">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Part brute par destinataire</span>
                    <span id="partParDestinataire">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de retrait cumulés</span>
                    <span id="fraisRetrait">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant réellement envoyé cumulé</span>
                    <span id="montantTransfert">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de transfert cumulés</span>
                    <span id="fraisApplicables">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Débit total</span>
                    <span id="totalDebit">0 Ar</span>
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
