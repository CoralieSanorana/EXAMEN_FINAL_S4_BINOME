<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un dépôt</h1>
    <p>Alimentez votre compte mobile money (opération supposée automatique).</p>
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
            <div class="mm-card-title"><i class="bi bi-download"></i> Formulaire d'insertion</div>

            <form action="/client/epargnesInsert" method="post" id="depotForm">
                <?= csrf_field() ?>
               <input type="number" name="epargnes" id="">
                <button type="submit" class="btn btn-mm-primary w-100" id="submitBtn">
                    <i class="bi bi-check-lg"></i> confirmer l'epargne
                </button>
            </form>
        </div>
 </div>

   
</div>


<?= $this->endSection() ?>
