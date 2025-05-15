<?php

namespace App\Controllers;

use App\Models\KendaraanModel;
use App\Models\TransaksiModel;
use App\Models\PenghasilanModel;
use CodeIgniter\Controller;

class Parkir extends Controller
{
    protected $kendaraanModel;
    protected $transaksiModel;
    protected $penghasilanModel;

    public function __construct()
    {
        $this->kendaraanModel = new KendaraanModel();
        $this->transaksiModel = new TransaksiModel();
        $this->penghasilanModel = new PenghasilanModel();
    }

    public function index()
    {
        $kendaraan = $this->kendaraanModel->findAll();
        $transaksi = $this->transaksiModel->select('transaksi.*, kendaraan.jenis_kendaraan')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('parkir/index', ['transaksi' => $transaksi, 'kendaraan' => $kendaraan]);
    }

    public function create()
    {
        $kendaraan = $this->kendaraanModel->findAll();
        $transaksi = $this->transaksiModel->select('transaksi.*, kendaraan.jenis_kendaraan')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->orderBy('created_at', 'DESC')
            ->findAll();
        return view('parkir/create', ['kendaraan' => $kendaraan, 'transaksi' => $transaksi]);
    }

    public function store()
    {
        $nomorPolisi = strtoupper($this->request->getPost('nomor_polisi'));

        // Check if there's an existing vehicle with the same license plate that's still in the parking lot
        $existingTransaksi = $this->transaksiModel->select('transaksi.*, kendaraan.tarif_perjam')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->where('nomor_polisi', $nomorPolisi)
            ->where('jam_keluar IS NULL')
            ->where('biaya IS NULL')
            ->first();

        if ($existingTransaksi) {
            // Vehicle is exiting - calculate parking fee
            $jamMasuk = strtotime($existingTransaksi['jam_masuk']);
            $jamKeluar = time();

            $lamaParkir = ceil(($jamKeluar - $jamMasuk) / 3600);  // calculate in hours
            $biaya = $existingTransaksi['tarif_perjam'];
            if ($lamaParkir > 1) {
                $biaya += ($lamaParkir - 1) * 2000;
            }

            // Update transaction
            $this->transaksiModel->update($existingTransaksi['id'], [
                'jam_keluar' => date('Y-m-d H:i:s'),
                'biaya' => $biaya,
            ]);

            // Update penghasilan
            $penghasilan = $this->penghasilanModel->first();
            if ($penghasilan) {
                $this->penghasilanModel->update($penghasilan['id'], [
                    'total_penghasilan' => $penghasilan['total_penghasilan'] + $biaya,
                ]);
            } else {
                $this->penghasilanModel->insert(['total_penghasilan' => $biaya]);
            }

            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Berhasil Keluar Parkir!');
            session()->setFlashdata('swal_text', 'Kendaraan berhasil checkout dengan biaya Rp' . number_format($biaya, 0, ',', '.'));
            return redirect()->to('/parkir');
        } else {
            // New vehicle entering
            $data = [
                'kendaraan_id' => $this->request->getPost('kendaraan_id'),
                'nomor_polisi' => $nomorPolisi,
                'jam_masuk' => date('Y-m-d H:i:s'),
            ];

            $this->transaksiModel->insert($data);

            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Data Tersimpan!');
            session()->setFlashdata('swal_text', 'Data parkir berhasil ditambahkan!');
            return redirect()->to('/parkir');
        }
    }

    public function checkout($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Error!');
            session()->setFlashdata('swal_text', 'Transaksi tidak ditemukan!');
            return redirect()->back();
        }

        $kendaraan = $this->kendaraanModel->find($transaksi['kendaraan_id']);
        $jamMasuk = strtotime($transaksi['jam_masuk']);
        $jamKeluar = time();

        $lamaParkir = ceil(($jamKeluar - $jamMasuk) / 3600);  // hitung dalam jam
        $biaya = $kendaraan['tarif_perjam'];
        if ($lamaParkir > 1) {
            $biaya += ($lamaParkir - 1) * 2000;
        }

        // Update transaksi
        $this->transaksiModel->update($id, [
            'jam_keluar' => date('Y-m-d H:i:s'),
            'biaya' => $biaya,
        ]);

        // Update penghasilan
        $penghasilan = $this->penghasilanModel->first();
        if ($penghasilan) {
            $this->penghasilanModel->update($penghasilan['id'], [
                'total_penghasilan' => $penghasilan['total_penghasilan'] + $biaya,
            ]);
        } else {
            $this->penghasilanModel->insert(['total_penghasilan' => $biaya]);
        }

        session()->setFlashdata('swal_icon', 'success');
        session()->setFlashdata('swal_title', 'Berhasil Keluar Parkir!');
        session()->setFlashdata('swal_text', 'Kendaraan berhasil checkout dengan biaya Rp' . number_format($biaya, 0, ',', '.'));
        return redirect()->to('/parkir');
    }

    public function income()
    {
        // Cek tab aktif dan parameter pencarian
        $active_tab = $this->request->getGet('tab') ?? 'summary';
        $search = $this->request->getGet('search');

        // Mendapatkan total penghasilan
        $penghasilan = $this->penghasilanModel->first();
        $total_penghasilan = $penghasilan ? $penghasilan['total_penghasilan'] : 0;

        // Query untuk statistik transaksi
        $total_transaksi = $this->transaksiModel->where('biaya IS NOT NULL')->countAllResults();
        $rata_transaksi = $total_transaksi > 0 ? ($total_penghasilan / $total_transaksi) : 0;

        // Transaksi hari ini
        $today = date('Y-m-d');
        $transaksi_hari_ini = $this->transaksiModel
            ->where('DATE(jam_keluar)', $today)
            ->where('biaya IS NOT NULL')
            ->countAllResults();

        // Pendapatan hari ini
        $pendapatan_hari_ini = $this->transaksiModel
            ->where('DATE(jam_keluar)', $today)
            ->where('biaya IS NOT NULL')
            ->selectSum('biaya')
            ->get()
            ->getRow()
            ->biaya ?? 0;

        // Pendapatan tertinggi berdasarkan hari
        $pendapatan_per_hari = $this->transaksiModel
            ->select('DATE(jam_keluar) as tanggal, SUM(biaya) as total')
            ->where('biaya IS NOT NULL')
            ->groupBy('DATE(jam_keluar)')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        $pendapatan_tertinggi = !empty($pendapatan_per_hari) ? $pendapatan_per_hari[0]['total'] : 0;
        $tanggal_tertinggi = !empty($pendapatan_per_hari) ? date('d M Y', strtotime($pendapatan_per_hari[0]['tanggal'])) : '-';

        // Pendapatan bulan ini
        $current_month = date('Y-m');
        $pendapatan_bulan_ini = $this->transaksiModel
            ->where('DATE_FORMAT(jam_keluar, "%Y-%m")', $current_month)
            ->where('biaya IS NOT NULL')
            ->selectSum('biaya')
            ->get()
            ->getRow()
            ->biaya ?? 0;

        // Pendapatan tahun ini
        $current_year = date('Y');
        $pendapatan_tahun_ini = $this->transaksiModel
            ->where('YEAR(jam_keluar)', $current_year)
            ->where('biaya IS NOT NULL')
            ->selectSum('biaya')
            ->get()
            ->getRow()
            ->biaya ?? 0;

        // Penghasilan 7 hari terakhir
        $penghasilan_harian = $this->transaksiModel
            ->select('DATE(jam_keluar) as tanggal, COUNT(*) as total_transaksi, SUM(biaya) as total_pendapatan')
            ->where('jam_keluar IS NOT NULL')
            ->where('biaya IS NOT NULL')
            ->where('jam_keluar >=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy('DATE(jam_keluar)')
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($penghasilan_harian as &$item) {
            $item['tanggal'] = date('d M Y', strtotime($item['tanggal']));
        }

        // Penghasilan berdasarkan jenis kendaraan
        $penghasilan_per_jenis = $this->transaksiModel
            ->select('kendaraan.jenis_kendaraan as nama_jenis, COUNT(*) as total_transaksi, SUM(transaksi.biaya) as total_pendapatan')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->where('transaksi.biaya IS NOT NULL')
            ->groupBy('kendaraan.jenis_kendaraan')
            ->orderBy('total_pendapatan', 'DESC')
            ->get()
            ->getResultArray();

        // Data untuk riwayat kendaraan - diambil selalu terlepas dari tab yang aktif
        $search = $this->request->getGet('search');
        $builder = $this->transaksiModel->select('transaksi.*, kendaraan.jenis_kendaraan')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->orderBy('transaksi.created_at', 'DESC');

        // Tambahkan pencarian jika ada
        if ($search) {
            $builder->like('nomor_polisi', $search);
        }

        // Pagination
        $perPage = 10; // Jumlah data per halaman
        $transaksi = $builder->paginate($perPage, 'transaksi');
        $pager = $this->transaksiModel->pager;

        // Nomor urut untuk tampilan
        $currentPage = $this->request->getGet('page_transaksi') ? (int) $this->request->getGet('page_transaksi') : 1;
        $nomor_urut = ($currentPage - 1) * $perPage + 1;

        return view('parkir/income', [
            'total_penghasilan' => $total_penghasilan,
            'total_transaksi' => $total_transaksi,
            'rata_transaksi' => $rata_transaksi,
            'transaksi_hari_ini' => $transaksi_hari_ini,
            'pendapatan_hari_ini' => $pendapatan_hari_ini,
            'pendapatan_tertinggi' => $pendapatan_tertinggi,
            'tanggal_tertinggi' => $tanggal_tertinggi,
            'pendapatan_bulan_ini' => $pendapatan_bulan_ini,
            'pendapatan_tahun_ini' => $pendapatan_tahun_ini,
            'penghasilan_harian' => $penghasilan_harian,
            'penghasilan_per_jenis' => $penghasilan_per_jenis,
            // Data untuk tab riwayat kendaraan
            'transaksi' => $transaksi,
            'pager' => $pager,
            'search' => $search,
            'nomor_urut' => $nomor_urut,
            'active_tab' => $active_tab
        ]);
    }

    public function history()
    {
        $search = $this->request->getGet('search');

        // Query dasar
        $builder = $this->transaksiModel->select('transaksi.*, kendaraan.jenis_kendaraan')
            ->join('kendaraan', 'transaksi.kendaraan_id = kendaraan.id')
            ->orderBy('transaksi.created_at', 'DESC');

        // Tambahkan pencarian jika ada
        if ($search) {
            $builder->like('nomor_polisi', $search);
        }

        // Pagination
        $perPage = 10; // Jumlah data per halaman
        $transaksi = $builder->paginate($perPage, 'transaksi');
        $pager = $this->transaksiModel->pager;

        // Nomor urut untuk tampilan
        $currentPage = $this->request->getGet('page_transaksi') ? (int) $this->request->getGet('page_transaksi') : 1;
        $nomor_urut = ($currentPage - 1) * $perPage + 1;

        return view('parkir/history', [
            'transaksi' => $transaksi,
            'pager' => $pager,
            'search' => $search,
            'nomor_urut' => $nomor_urut
        ]);
    }

    public function getTarif()
    {
        if ($this->request->isAJAX()) {
            $kendaraan_id = $this->request->getVar('kendaraan_id');
            $kendaraan = $this->kendaraanModel->find($kendaraan_id);

            if ($kendaraan) {
                return $this->response->setJSON([
                    'success' => true,
                    'tarif' => $kendaraan['tarif_perjam']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kendaraan tidak ditemukan'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request'
        ]);
    }
}
