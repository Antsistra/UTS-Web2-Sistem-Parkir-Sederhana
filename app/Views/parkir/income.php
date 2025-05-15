<?= $this->extend('parkir/layout') ?>
<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="incomeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="true">Rekap Penghasilan</button>
            </li>

        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="incomeTabsContent">
            <!-- Tab Rekap Penghasilan -->
            <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                <h4 class="mb-4">Total Penghasilan: Rp<?= number_format($total_penghasilan, 0, ',', '.') ?></h4>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">Ringkasan Transaksi</div>
                            <div class="card-body">
                                <p>Total Transaksi: <?= number_format($total_transaksi ?? 0) ?></p>
                                <p>Rata-rata Pendapatan per Transaksi: Rp<?= number_format($rata_transaksi ?? 0, 0, ',', '.') ?></p>
                                <p>Transaksi Hari Ini: <?= number_format($transaksi_hari_ini ?? 0) ?></p>
                                <p>Pendapatan Hari Ini: Rp<?= number_format($pendapatan_hari_ini ?? 0, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">Statistik Pendapatan</div>
                            <div class="card-body">
                                <p>Pendapatan Tertinggi: Rp<?= number_format($pendapatan_tertinggi ?? 0, 0, ',', '.') ?></p>
                                <p>Tanggal Pendapatan Tertinggi: <?= $tanggal_tertinggi ?? '-' ?></p>
                                <p>Pendapatan Bulan Ini: Rp<?= number_format($pendapatan_bulan_ini ?? 0, 0, ',', '.') ?></p>
                                <p>Pendapatan Tahun Ini: Rp<?= number_format($pendapatan_tahun_ini ?? 0, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($penghasilan_harian) && count($penghasilan_harian) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header">Penghasilan 7 Hari Terakhir</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Total Transaksi</th>
                                            <th>Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($penghasilan_harian as $penghasilan): ?>
                                            <tr>
                                                <td><?= $penghasilan['tanggal'] ?></td>
                                                <td><?= number_format($penghasilan['total_transaksi']) ?></td>
                                                <td>Rp<?= number_format($penghasilan['total_pendapatan'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($penghasilan_per_jenis) && count($penghasilan_per_jenis) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header">Penghasilan Berdasarkan Jenis Kendaraan</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Jenis Kendaraan</th>
                                            <th>Total Transaksi</th>
                                            <th>Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($penghasilan_per_jenis as $jenis): ?>
                                            <tr>
                                                <td><?= $jenis['nama_jenis'] ?></td>
                                                <td><?= number_format($jenis['total_transaksi']) ?></td>
                                                <td>Rp<?= number_format($jenis['total_pendapatan'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bagian Riwayat Kendaraan -->
                <div class="card">
                    <div class="card-header">Riwayat Keluar Masuk Kendaraan</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Daftar Kendaraan</h5>
                            <form action="/parkir/income" method="get" class="d-flex">
                                <input type="hidden" name="tab" value="summary">
                                <input type="text" name="search" class="form-control me-2" placeholder="Cari nomor polisi..." value="<?= $search ?? '' ?>">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </form>
                        </div>

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
                        <?php if (isset($pager)): ?>
                            <div class="mt-4">
                                <?= $pager->links('transaksi', 'bootstrap_pagination') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Kosongkan Tab Riwayat karena sudah dipindahkan -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="alert alert-info">
                    Untuk melihat riwayat kendaraan, silakan lihat bagian bawah halaman rekap penghasilan.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aktivasi tab sesuai parameter URL
        <?php if (isset($active_tab) && $active_tab == 'history'): ?>
            var historyTab = new bootstrap.Tab(document.getElementById('history-tab'));
            historyTab.show();
        <?php endif; ?>

        // Tambahkan event listener untuk tab clicks
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabEl) {
            tabEl.addEventListener('click', function(event) {
                event.preventDefault();
                var tab = new bootstrap.Tab(this);
                tab.show();
            });
        });
    });
</script>
<?= $this->endSection() ?>