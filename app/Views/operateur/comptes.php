<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Comptes clients</h1>
    <p>Consultation globale de la situation des comptes enregistrés.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Nombre de comptes</div>
            <div class="mm-stat-value">1 284</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Solde total cumulé</div>
            <div class="mm-stat-value accent">184 320 500 Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Solde moyen / compte</div>
            <div class="mm-stat-value">143 550 Ar</div>
        </div>
    </div>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-people"></i> Liste des comptes</div>
        <div class="input-group" style="max-width:280px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Rechercher un numéro...">
        </div>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Opérateur</th>
                <th>Date de création</th>
                <th class="text-end">Solde</th>
                <th class="text-end">Statut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>033 12 345 67</strong></td>
                <td>Orange Madagascar</td>
                <td>02/01/2026</td>
                <td class="text-end">452 300 Ar</td>
                <td class="text-end"><span class="badge badge-mm badge-depot">Actif</span></td>
            </tr>
            <tr>
                <td><strong>037 44 556 78</strong></td>
                <td>Airtel Madagascar</td>
                <td>15/01/2026</td>
                <td class="text-end">98 000 Ar</td>
                <td class="text-end"><span class="badge badge-mm badge-depot">Actif</span></td>
            </tr>
            <tr>
                <td><strong>033 98 765 43</strong></td>
                <td>Orange Madagascar</td>
                <td>03/02/2026</td>
                <td class="text-end">1 250 000 Ar</td>
                <td class="text-end"><span class="badge badge-mm badge-depot">Actif</span></td>
            </tr>
            <tr>
                <td><strong>037 22 113 90</strong></td>
                <td>Airtel Madagascar</td>
                <td>21/02/2026</td>
                <td class="text-end">0 Ar</td>
                <td class="text-end"><span class="badge badge-mm badge-retrait">Suspendu</span></td>
            </tr>
        </tbody>
    </table>

    <nav class="d-flex justify-content-end mt-3">
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
        </ul>
    </nav>
</div>

<?= $this->endSection() ?>
