<?= $this->extend('parkir/layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-3 bg-light vh-100">
        <h3 class="mt-4">Your Company</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/parkir">DASHBOARD</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/parkir/income">PENGHASILAN</a>
            </li>
        </ul>
        <footer class="position-absolute bottom-0 mb-4">
            <small>CopyRight - 2025</small>
        </footer>
    </div>
    <div class="col-md-9">
        <div class="card mt-4">
            <div class="card-body">
                <form action="/parkir/store" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kendaraan_id" class="form-label">Jenis Kendaraan</label>
                            <select name="kendaraan_id" id="kendaraan_id" class="form-select" required>
                                <option value="">Pilih Kendaraan</option>
                                <?php foreach ($kendaraan as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= $k['jenis_kendaraan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="harga_karcis" class="form-label">Harga Karcis</label>
                            <input type="text" id="harga_karcis" class="form-control" disabled placeholder="Pilih jenis kendaraan">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_polisi" class="form-label">No Polisi</label>
                        <input type="text" name="nomor_polisi" id="nomor_polisi" class="form-control text-center fs-1" placeholder="A 5641 BT" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Simpan</button>
                </form>
            </div>
        </div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Polisi</th>
                    <th>Jenis Kendaraan</th>
                    <th>Tanggal & Jam</th>
                    <th>Status Kendaraan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $index => $t): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $t['nomor_polisi'] ?></td>
                        <td><?= $t['jenis_kendaraan'] ?></td>
                        <td><?= date('d M Y - H:i:s', strtotime($t['jam_masuk'])) ?></td>
                        <td><?= $t['jam_keluar'] ? 'KELUAR' : 'MASUK' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>