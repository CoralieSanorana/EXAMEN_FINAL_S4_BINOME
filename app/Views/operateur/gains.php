<?= $this->extend('layouts/layout_operateur') ?>

<?php
$stats = $stats ?? [
    'gains_retrait' => '0',
    'gains_transfert' => '0',
    'total_cumule' => '0',
];
$filter = $filter ?? 'all';
$transactions = $transactions ?? [];
$pager = $pager ?? '';
?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Situation des gains</h1>
    <p>Vue d'ensemble des revenus générés par les frais de barème et les commissions inter-opérateurs.</p>
</div>

<!-- CONSERVATION STRICTE DES COULEURS ET DEGRADES D'ORIGINE -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-green">
            <div class="mm-stat-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="mm-stat-label">Gains internes</div>
            <div class="mm-stat-value positive"><?= number_format($stats['interne']['total'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle">Mon opérateur</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-purple">
            <div class="mm-stat-icon">
                <i class="bi bi-globe"></i>
            </div>
            <div class="mm-stat-label">Gains externes</div>
            <div class="mm-stat-value accent"><?= number_format($stats['externe']['total'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle">Commissions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card mm-stat-card-gradient-blue">
            <div class="mm-stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="mm-stat-label">Total cumulé</div>
            <div class="mm-stat-value"><?= number_format($stats['total_cumule'], 0, ',', ' ') ?> Ar</div>
            <div class="mm-stat-subtitle">Revenus totaux</div>
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

<!-- SECTION TABLEAU AVEC CORRECTION INTEGRALE DE LA PAGINATION -->
<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-cash-stack"></i> Détail des frais perçus</div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width:auto;" onchange="window.location.href='<?= base_url('operateur/gains') ?>?filter=' + this.value">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="RETRAIT" <?= $filter === 'RETRAIT' ? 'selected' : '' ?>>Retrait</option>
                <option value="TRANSFERT" <?= $filter === 'TRANSFERT' ? 'selected' : '' ?>>Transfert</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mm-table">
            <thead>
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
                            <td><?= date('d/m/Y H:i', strtotime($transaction['effectue_le'])) ?></td>
                            <td><?= esc($transaction['compte_source_numero'] ?? 'N/A') ?></td>
                            <td><?= esc($transaction['numero_destinataire_affiche'] ?? '-') ?></td>
                            <td><?= esc($transaction['reseau_concerne'] ?? 'Non défini') ?></td>
                            <td>
                                <span class="badge badge-mm <?= $transaction['type_code'] === 'RETRAIT' ? 'badge-retrait' : 'badge-transfert' ?>">
                                    <?= esc($transaction['type_nom']) ?>
                                </span>
                            </td>
                            <td><?= number_format($transaction['montant'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-end"><?= number_format($transaction['frais_bareme'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td class="text-end"><?= number_format($transaction['frais_commission'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td class="text-end"><?= number_format($transaction['frais_percus'] ?? 0, 0, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucune transaction trouvée</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- RESTRUCTURATION COMPLÈTE ET CENTRAGE DE LA PAGINATION POUR SUPPRIMER LE RENDU INESTHÉTIQUE -->
    <?php if (!empty($pager)): ?>
        <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top w-100 pagination-container-fixed">
            <style>
                /* Injection de styles ciblés pour neutraliser les puces ou blocs natifs bruts et forcer la symétrie Bootstrap */
                .pagination-container-fixed ul {
                    display: flex !important;
                    padding-left: 0 !important;
                    list-style: none !important;
                    margin: 0 !important;
                    gap: 5px;
                }
                .pagination-container-fixed ul li {
                    display: inline-block !important;
                }
                .pagination-container-fixed ul li a, 
                .pagination-container-fixed ul li span {
                    position: relative;
                    display: block;
                    padding: 0.5rem 0.75rem;
                    color: #6f42c1; /* Rappel harmonieux de ta couleur Accent/Purple */
                    background-color: #fff;
                    border: 1px solid #dee2e6;
                    text-decoration: none;
                    border-radius: 4px;
                    transition: all 0.2s ease-in-out;
                }
                .pagination-container-fixed ul li.active span,
                .pagination-container-fixed ul li a:hover {
                    z-index: 3;
                    color: #fff;
                    background-color: #6f42c1;
                    border-color: #6f42c1;
                }
                .pagination-container-fixed ul li.disabled span {
                    color: #6c757d;
                    pointer-events: none;
                    background-color: #fff;
                    border-color: #dee2e6;
                    opacity: 0.6;
                }
            </style>
            <?= $pager ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>