<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Parkir</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/js/sweetalert2.all.min.js"></script>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--secondary) 0%, #34495e 100%);
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .sidebar-header {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }

        .sidebar-header h3 {
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .sidebar-logo {
            font-size: 24px;
            margin-right: 10px;
            color: var(--primary);
        }

        .main-content {
            margin-left: 25%;
            padding: 30px;
            transition: all 0.3s;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 5px 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--primary);
            color: white;
        }

        .nav-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .content-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 25px;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-icon {
            color: var(--accent);
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-car-side sidebar-logo"></i> UBJ Parking</h3>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'parkir' ? 'active' : '' ?>" href="/parkir">
                            <i class="fas fa-tachometer-alt nav-icon"></i> DASHBOARD
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'parkir/income' ? 'active' : '' ?>" href="/parkir/income">
                            <i class="fas fa-money-bill-wave nav-icon"></i> PENGHASILAN
                        </a>
                    </li>

                </ul>
                <footer>
                    <i class="fas fa-copyright footer-icon"></i>
                    <small>UBJ Parking © <?= date('Y') ?></small>
                </footer>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 offset-md-3 main-content">
                <div class="content-card">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk menampilkan harga karcis secara otomatis saat jenis kendaraan dipilih
        document.addEventListener('DOMContentLoaded', function() {
            const kendaraanSelect = document.getElementById('kendaraan_id');
            const hargaKarcisInput = document.getElementById('harga_karcis');

            if (kendaraanSelect && hargaKarcisInput) {
                kendaraanSelect.addEventListener('change', function() {
                    const selectedValue = this.value;

                    if (selectedValue) {
                        // Kirim request AJAX ke server untuk mendapatkan tarif
                        fetch(`/parkir/getTarif?kendaraan_id=${selectedValue}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Format tarif sebagai mata uang
                                    const formattedTarif = new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(data.tarif);

                                    hargaKarcisInput.value = formattedTarif;
                                } else {
                                    hargaKarcisInput.value = '-';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                hargaKarcisInput.value = '-';
                            });
                    } else {
                        hargaKarcisInput.value = '';
                        hargaKarcisInput.placeholder = 'Pilih jenis kendaraan';
                    }
                });
            }
        });
    </script>
</body>

</html>