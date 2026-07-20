<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Mon solde</h1>
    <p>Consultez votre solde actuel et accédez rapidement à vos opérations.</p>
</div>
<div class="row g-3">
    <!-- Colonne Solde disponible -->
    <div class="col-lg-5">
        <div class="mm-card text-center py-4 h-100 d-flex flex-column justify-content-center">
            <div class="mm-stat-label mb-2">Solde disponible</div>
            <div class="display-6 fw-semibold" style="color:#5c677d;"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</div>
            <p class="text-muted small mt-2 mb-0">Compte n&deg; <?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?></p>
        </div>
    </div>

    <!-- Colonne Actions rapides -->
    <div class="col-lg-7">
        <div class="mm-card h-100">
            <div class="mm-card-title"><i class="bi bi-lightning-charge"></i> Actions rapides</div>
            <div class="row g-3">
                <div class="col-4">
                    <a href="/client/depot" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-download d-block mb-1" style="font-size:1.3rem;"></i>
                        Dépôt
                    </a>
                </div>
                <div class="col-4">
                    <a href="/client/retrait" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-upload d-block mb-1" style="font-size:1.3rem;"></i>
                        Retrait
                    </a>
                </div>
                <div class="col-4">
                    <a href="/client/transfert" class="btn btn-mm-outline w-100 py-3">
                        <i class="bi bi-arrow-left-right d-block mb-1" style="font-size:1.3rem;"></i>
                        Transfert
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mm-card mt-2">
    <div class="mm-card-title d-flex justify-content-between align-items-center">
        <div><i class="bi bi-clock-history"></i> Dernières opérations</div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href='/client/solde?filter=' + this.value + '&page=1'">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Toutes</option>
                <option value="depot" <?= $filter === 'depot' ? 'selected' : '' ?>>Dépôts</option>
                <option value="retrait" <?= $filter === 'retrait' ? 'selected' : '' ?>>Retraits</option>
                <option value="transfert" <?= $filter === 'transfert' ? 'selected' : '' ?>>Transferts envoyés</option>
                <option value="transfert_recu" <?= $filter === 'transfert_recu' ? 'selected' : '' ?>>Transferts reçus</option>
            </select>
            <?php if (!$showAll): ?>
                <a href="/client/solde?filter=<?= $filter ?>&show_all=1" class="btn btn-mm-outline btn-sm">
                    <i class="bi bi-list-ul"></i> Voir tout
                </a>
            <?php else: ?>
                <a href="/client/solde?filter=<?= $filter ?>&show_all=0" class="btn btn-mm-outline btn-sm">
                    <i class="bi bi-list-check"></i> Pagination
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (empty($historique)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">Aucune transaction trouvée</p>
        </div>
    <?php else: ?>
        <table class="table mm-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Opération</th>
                    <th>Tiers</th>
                    <th class="text-end">Montant</th>
                    <th class="text-end">Frais</th>
                    <th class="text-end">Impact</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $transaction): ?>
                    <?php 
                        $badgeClass = 'badge-' . strtolower($transaction['type_code']);
                        $sign = $transaction['sens_mouvement'];
                        $isDebit = $sign === '-';
                        $colorClass = $isDebit ? 'text-danger' : 'text-success';
                    ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($transaction['effectue_le'])) ?></td>
                        <td><span class="badge badge-mm <?= $badgeClass ?>"><?= esc($transaction['type_nom']) ?></span></td>
                        <td class="text-muted small">
                            <?php if ($transaction['telephone_tiers']): ?>
                                <?= esc($transaction['telephone_tiers']) ?>
                            <?php else: ?>
                                <em>Compte propre</em>
                            <?php endif; ?>
                        </td>
                        <td class="text-end"><?= number_format($transaction['montant_brut'], 0, '.', ' ') ?> Ar</td>
                        <td class="text-end text-muted"><?= number_format($transaction['frais_appliques'], 0, '.', ' ') ?> Ar</td>
                        <td class="text-end fw-bold <?= $colorClass ?>"><?= $sign ?> <?= number_format($transaction['impact_solde'], 0, '.', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (!$showAll && $totalPages > 1): ?>
            <nav aria-label="Pagination">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/client/solde?page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>">Précédent</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="/client/solde?page=<?= $i ?>&filter=<?= $filter ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="/client/solde?page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>">Suivant</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
        <div class="text-center small text-muted mt-2">
            Affichage de <?= count($historique) ?> sur <?= $totalTransactions ?> transaction(s)
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
