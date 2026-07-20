<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Situation des gains</h1>
    <p>Vue d'ensemble des revenus générés par les frais de retrait et de transfert.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Gains totaux (retrait)</div>
            <div class="mm-stat-value positive">1 245 800 Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Gains totaux (transfert)</div>
            <div class="mm-stat-value accent">832 400 Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Total cumulé</div>
            <div class="mm-stat-value">2 078 200 Ar</div>
        </div>
    </div>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-cash-stack"></i> Détail des frais perçus</div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width:auto;">
                <option>Ce mois-ci</option>
                <option>Semaine dernière</option>
                <option>Aujourd'hui</option>
            </select>
        </div>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Numéro client</th>
                <th>Opération</th>
                <th>Montant</th>
                <th class="text-end">Frais perçus</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>19/07/2026 14:32</td>
                <td>033 12 345 67</td>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td>75 000 Ar</td>
                <td class="text-end">1 125 Ar</td>
            </tr>
            <tr>
                <td>19/07/2026 11:08</td>
                <td>037 44 556 78</td>
                <td><span class="badge badge-mm badge-transfert">Transfert</span></td>
                <td>150 000 Ar</td>
                <td class="text-end">2 250 Ar</td>
            </tr>
            <tr>
                <td>18/07/2026 17:45</td>
                <td>033 98 765 43</td>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td>15 000 Ar</td>
                <td class="text-end">500 Ar</td>
            </tr>
            <tr>
                <td>18/07/2026 09:21</td>
                <td>037 22 113 90</td>
                <td><span class="badge badge-mm badge-transfert">Transfert</span></td>
                <td>320 000 Ar</td>
                <td class="text-end">4 800 Ar</td>
            </tr>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
