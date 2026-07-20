<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Historique des opérations</h1>
    <p>Retrouvez l'ensemble de vos opérations mobile money.</p>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-clock-history"></i> Toutes mes opérations</div>
        <select class="form-select form-select-sm" style="width:auto;">
            <option>Toutes les opérations</option>
            <option>Dépôts</option>
            <option>Retraits</option>
            <option>Transferts</option>
        </select>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Opération</th>
                <th>Destinataire / Source</th>
                <th class="text-end">Montant</th>
                <th class="text-end">Frais</th>
                <th class="text-end">Solde après</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>19/07/2026 14:32</td>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td>Agent partenaire</td>
                <td class="text-end">- 75 000 Ar</td>
                <td class="text-end">1 125 Ar</td>
                <td class="text-end">452 300 Ar</td>
            </tr>
            <tr>
                <td>17/07/2026 10:05</td>
                <td><span class="badge badge-mm badge-depot">Dépôt</span></td>
                <td>Agent partenaire</td>
                <td class="text-end">+ 200 000 Ar</td>
                <td class="text-end">0 Ar</td>
                <td class="text-end">527 300 Ar</td>
            </tr>
            <tr>
                <td>15/07/2026 18:40</td>
                <td><span class="badge badge-mm badge-transfert">Transfert</span></td>
                <td>037 44 556 78</td>
                <td class="text-end">- 50 000 Ar</td>
                <td class="text-end">500 Ar</td>
                <td class="text-end">327 300 Ar</td>
            </tr>
            <tr>
                <td>10/07/2026 09:12</td>
                <td><span class="badge badge-mm badge-depot">Dépôt</span></td>
                <td>Virement bancaire</td>
                <td class="text-end">+ 150 000 Ar</td>
                <td class="text-end">0 Ar</td>
                <td class="text-end">377 800 Ar</td>
            </tr>
            <tr>
                <td>03/07/2026 16:55</td>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td>Guichet automatique</td>
                <td class="text-end">- 20 000 Ar</td>
                <td class="text-end">500 Ar</td>
                <td class="text-end">227 800 Ar</td>
            </tr>
        </tbody>
    </table>

    <nav class="d-flex justify-content-end mt-3">
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
        </ul>
    </nav>
</div>

<?= $this->endSection() ?>
