<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Mon solde</h1>
    <p>Consultez votre solde actuel et accédez rapidement à vos opérations.</p>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="mm-card text-center py-4">
            <div class="mm-stat-label mb-2">Solde disponible</div>
            <div class="display-6 fw-semibold" style="color:#5c677d;">452 300 Ar</div>
            <p class="text-muted small mt-2 mb-0">Compte n&deg; 033 12 345 67</p>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-lightning-charge"></i> Actions rapides</div>
            <div class="row g-3">
                <div class="col-4">
                    <a href="#" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-download d-block mb-1" style="font-size:1.3rem;"></i>
                        Dépôt
                    </a>
                </div>
                <div class="col-4">
                    <a href="#" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-upload d-block mb-1" style="font-size:1.3rem;"></i>
                        Retrait
                    </a>
                </div>
                <div class="col-4">
                    <a href="#" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-arrow-left-right d-block mb-1" style="font-size:1.3rem;"></i>
                        Transfert
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mm-card mt-2">
    <div class="mm-card-title"><i class="bi bi-clock-history"></i> Dernières opérations</div>
    <table class="table mm-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Opération</th>
                <th class="text-end">Montant</th>
                <th class="text-end">Solde après</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>19/07/2026 14:32</td>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td class="text-end">- 75 000 Ar</td>
                <td class="text-end">452 300 Ar</td>
            </tr>
            <tr>
                <td>17/07/2026 10:05</td>
                <td><span class="badge badge-mm badge-depot">Dépôt</span></td>
                <td class="text-end">+ 200 000 Ar</td>
                <td class="text-end">527 300 Ar</td>
            </tr>
            <tr>
                <td>15/07/2026 18:40</td>
                <td><span class="badge badge-mm badge-transfert">Transfert</span></td>
                <td class="text-end">- 50 000 Ar</td>
                <td class="text-end">327 300 Ar</td>
            </tr>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
