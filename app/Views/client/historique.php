<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Historique des opérations</h1>
    <p>Retrouvez l'ensemble de vos opérations mobile money.</p>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-clock-history"></i> Toutes mes opérations</div>
        <select class="form-select form-select-sm" style="width:auto;" onchange="window.location.href='/client/historique?filter=' + this.value + '&page=1'">
            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Toutes les opérations</option>
            <option value="depot" <?= $filter === 'depot' ? 'selected' : '' ?>>Dépôts</option>
            <option value="retrait" <?= $filter === 'retrait' ? 'selected' : '' ?>>Retraits</option>
            <option value="transfert" <?= $filter === 'transfert' ? 'selected' : '' ?>>Transferts envoyés</option>
            <option value="transfert_recu" <?= $filter === 'transfert_recu' ? 'selected' : '' ?>>Transferts reçus</option>
        </select>
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

        <?php if ($totalPages > 1): ?>
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/client/historique?page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>">Précédent</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Précédent</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $currentPage): ?>
                            <li class="page-item active">
                                <a class="page-link" href="#"><?= $i ?></a>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="/client/historique?page=<?= $i ?>&filter=<?= $filter ?>"><?= $i ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="/client/historique?page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>">Suivant</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Suivant</a>
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
