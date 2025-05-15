<?= $this->extend('parkir/layout') ?>
<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Keluar Masuk Kendaraan</h5>
        <form action="/parkir/history" method="get" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor polisi..." value="<?= $search ?? '' ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor Polisi</th>
                        <th>Jenis Kendaraan</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Durasi</th>
                        <th>Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksi)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transaksi as $index => $t): ?>
                            <tr>
                                <td><?= $nomor_urut++ ?></td>
                                <td><?= $t['nomor_polisi'] ?></td>
                                <td><?= $t['jenis_kendaraan'] ?></td>
                                <td><?= date('d M Y - H:i:s', strtotime($t['jam_masuk'])) ?></td>
                                <td>
                                    <?= $t['jam_keluar'] ? date('d M Y - H:i:s', strtotime($t['jam_keluar'])) : '<span class="badge bg-primary">Masih di dalam</span>' ?>
                                </td>
                                <td>
                                    <?php if ($t['jam_keluar']): ?>
                                        <?php
                                        $masuk = strtotime($t['jam_masuk']);
                                        $keluar = strtotime($t['jam_keluar']);
                                        $durasi = $keluar - $masuk;
                                        $jam = floor($durasi / 3600);
                                        $menit = floor(($durasi % 3600) / 60);
                                        echo "$jam jam $menit menit";
                                        ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t['biaya'] ? 'Rp' . number_format($t['biaya'], 0, ',', '.') : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <?= $pager->links('transaksi', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>