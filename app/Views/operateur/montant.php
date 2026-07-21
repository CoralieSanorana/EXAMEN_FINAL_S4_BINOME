<?= $this->extend('layouts/layout_operateur') ?>

<?php
$montants = $montants ?? [];
$total_global = $total_global ?? 0;
?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Situation des montants à envoyer</h1>
    <p>Table de compensation : total cumulé de l'argent transféré vers chaque opérateur concurrent.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="mm-stat-card mm-stat-card-gradient-blue">
            <div class="mm-stat-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="mm-stat-label">Total global à envoyer</div>
            <div class="mm-stat-value"><?= number_format($total_global, 2, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle">Ensemble des opérateurs externes</div>
        </div>
    </div>
</div>

<div class="mm-card">
    <div class="mm-card-title"><i class="bi bi-table"></i> Montants par opérateur</div>

    <div class="table-responsive">
        <table class="table mm-table">
            <thead>
                <tr>
                    <th>Opérateur</th>
                    <th>Nombre de transferts</th>
                    <th class="text-end">Total à envoyer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($montants)): ?>
                    <?php foreach ($montants as $montant): ?>
                        <tr>
                            <td>
                                <strong><?= esc($montant['operateur_nom'] ?? 'Non défini') ?></strong>
                                <span class="badge badge-mm badge-transfert ms-2">Externe</span>
                            </td>
                            <td><?= $montant['nombre_transfers'] ?? 0 ?></td>
                            <td class="text-end">
                                <strong><?= number_format($montant['total_a_envoyer'] ?? 0, 2, ',', ' ') ?> Ar</strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Aucun transfert vers les opérateurs externes</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
