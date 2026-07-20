<?= $this->extend('layouts/layout_operateur') ?>

<?php
$stats = $stats ?? [
    'interne' => ['total' => 0, 'retrait' => 0, 'transfert' => 0],
    'externe' => ['total' => 0, 'transfert' => 0, 'details' => []],
    'total_cumule' => 0,
];
$filter = $filter ?? 'all';
$transactions = $transactions ?? [];
$pager = $pager ?? '';
?>

<?= $this->section('content') ?>

<div class="mm-page-header mb-4">
    <h1 class="h2 fw-bold text-dark">Situation des gains</h1>
    <p class="text-muted">Vue d'ensemble des revenus générés par les frais de barème et les commissions inter-opérateurs.</p>
</div>

<!-- Cartes Statistiques Principales -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-green p-3 h-100 rounded-3 shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="mm-stat-icon me-2 px-2 py-1 bg-white bg-opacity-25 rounded text-white">
                    <i class="bi bi-building"></i>
                </div>
                <div class="mm-stat-label text-white-50 fw-semibold">Gains internes</div>
            </div>
            <div class="mm-stat-value positive text-white h3 fw-bold mb-1"><?= number_format($stats['interne']['total'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle text-white-50 small">Mon opérateur</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-purple p-3 h-100 rounded-3 shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="mm-stat-icon me-2 px-2 py-1 bg-white bg-opacity-25 rounded text-white">
                    <i class="bi bi-globe"></i>
                </div>
                <div class="mm-stat-label text-white-50 fw-semibold">Gains externes</div>
            </div>
            <div class="mm-stat-value accent text-white h3 fw-bold mb-1"><?= number_format($stats['externe']['total'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle text-white-50 small">Commissions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-blue p-3 h-100 rounded-3 shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="mm-stat-icon me-2 px-2 py-1 bg-white bg-opacity-25 rounded text-white">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="mm-stat-label text-white-50 fw-semibold">Total cumulé</div>
            </div>
            <div class="mm-stat-value text-white h3 fw-bold mb-1"><?= number_format($stats['total_cumule'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle text-white-50 small">Revenus totaux</div>
        </div>
    </div>
</div>

<!-- Détails Symétriques des Tableaux de Bord -->
<div class="row g-3 mb-4">
    <!-- Section Gains Internes -->
    <div class="col-md-6">
        <div class="mm-detail-section p-3 bg-white rounded-3 shadow-sm border h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="mm-card-title mb-3 fw-bold text-secondary"><i class="bi bi-building me-2"></i>Gains internes (mon opérateur)</div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="mm-stat-card-sm p-2 border rounded bg-light text-center">
                            <div class="mm-stat-label-sm text-muted small">Retrait</div>
                            <div class="mm-stat-value-sm fw-bold text-dark mt-1"><?= number_format($stats['interne']['retrait'], 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mm-stat-card-sm p-2 border rounded bg-light text-center">
                            <div class="mm-stat-label-sm text-muted small">Transfert</div>
                            <div class="mm-stat-value-sm fw-bold text-dark mt-1"><?= number_format($stats['interne']['transfert'], 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top text-muted small">
                Gains issus de l'activité exclusive sur votre infrastructure réseau.
            </div>
        </div>
    </div>

    <!-- Section Gains Externes -->
    <div class="col-md-6">
        <div class="mm-detail-section p-3 bg-white rounded-3 shadow-sm border h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="mm-card-title mb-3 fw-bold text-secondary"><i class="bi bi-globe me-2"></i>Gains externes (commissions)</div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="mm-stat-card-sm p-2 border rounded bg-light text-center">
                            <div class="mm-stat-label-sm text-muted small">Transfert</div>
                            <div class="mm-stat-value-sm fw-bold text-dark mt-1"><?= number_format($stats['externe']['transfert'], 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mm-stat-card-sm p-2 border rounded bg-light text-center">
                            <div class="mm-stat-label-sm text-muted small">Total commissions</div>
                            <div class="mm-stat-value-sm fw-bold text-dark mt-1"><?= number_format($stats['externe']['total'], 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($stats['externe']['details'])): ?>
                <div class="mm-detail-section-inner border-top pt-2">
                    <div class="mm-detail-label mb-2 fw-semibold text-muted small">Détail par opérateur de destination :</div>
                    <?php foreach ($stats['externe']['details'] as $detail): ?>
                        <div class="mm-detail-item d-flex justify-content-between small py-1 border-bottom border-light">
                            <span class="text-secondary"><?= esc($detail['operateur']) ?></span>
                            <span class="fw-bold text-dark"><?= number_format($detail['montant'], 0, ',', ' ') ?> Ar</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="mt-3 pt-3 border-top text-muted small">
                    Aucun frais collecté via l'interconnexion réseau tiers pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tableau principal -->
<div class="mm-card bg-white p-3 rounded-3 shadow-sm border">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0 fw-bold text-dark"><i class="bi bi-cash-stack me-2"></i>Détail des frais perçus</div>
        <div>
            <select class="form-select form-select-sm" style="width:auto;" onchange="window.location.href='<?= base_url('operateur/gains') ?>?filter=' + this.value">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Toutes les opérations</option>
                <option value="RETRAIT" <?= $filter === 'RETRAIT' ? 'selected' : '' ?>>Retrait uniquement</option>
                <option value="TRANSFERT" <?= $filter === 'TRANSFERT' ? 'selected' : '' ?>>Transfert uniquement</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mm-table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Numéro client</th>
                    <th>Numéro destinataire</th>
                    <th>Réseau concerné</th>
                    <th>Opération</th>
                    <th>Montant</th>
                    <th class="text-end">Frais barème</th>
                    <th class="text-end">Commission</th>
                    <th class="text-end">Total frais</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($transaction['effectue_le'])) ?></td>
                            <td class="font-monospace fw-semibold"><?= esc($transaction['compte_source_numero'] ?? 'N/A') ?></td>
                            <td class="font-monospace"><?= esc($transaction['numero_destinataire_affiche'] ?? '-') ?></td>
                            <td><span class="text-secondary small"><?= esc($transaction['reseau_concerne'] ?? 'Non défini') ?></span></td>
                            <td>
                                <span class="badge rounded-pill p-2 <?= $transaction['type_code'] === 'RETRAIT' ? 'bg-success text-white' : 'bg-primary text-white' ?>">
                                    <?= esc($transaction['type_nom']) ?>
                                </span>
                            </td>
                            <td class="fw-semibold"><?= number_format($transaction['montant'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-end text-success font-monospace"><?= number_format($transaction['frais_bareme'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td class="text-end text-purple font-monospace" style="color: var(--bs-purple, #6f42c1);"><?= number_format($transaction['frais_commission'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td class="text-end fw-bold text-dark font-monospace"><?= number_format($transaction['frais_percus'] ?? 0, 0, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Aucune transaction enregistrée sous ce filtre.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Alignement Centré et Symétrique de la Pagination -->
    <?php if (!empty($pager)): ?>
        <nav class="d-flex justify-content-center align-items-center mt-4 pt-2 border-top w-100">
            <div class="pagination-clean-wrapper">
                <?= $pager ?>
            </div>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>