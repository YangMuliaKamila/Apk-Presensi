<?php 
session_start();
require_once('../config.php');

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

if (isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Prepare query to prevent SQL injection
    $query = "SELECT * FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE username = ?";

    // Execute query with prepared statement
    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Verify the password using password_verify
            if (password_verify($password, $row["Password"])) {
                // Check if the account is active
                if ($row['Status'] === 'Aktif') {
                    // Log in successfully, set session variables and redirect
                    $_SESSION["login"] = true;
                    $_SESSION['Id'] = $row['Id'];
                    $_SESSION['Role'] = $row['Role'];
                    $_SESSION['Nama'] = $row['Nama'];
                    $_SESSION['NIP'] = $row['NIP'];
                    $_SESSION['Jabatan'] = $row['Jabatan'];
                    $_SESSION['Lokasi_Presensi'] = $row['Lokasi_Presensi'];

                    // Redirect based on role
                    $redirectUrl = ($row['Role'] === 'Admin') 
                        ? '../admin/home/home.php' 
                        : '../pegawai/home/home.php';
                    header("Location: $redirectUrl");
                    exit;
                } else {
                    $_SESSION["gagal"] = "Akun anda belum aktif";
                }
            } else {
                $_SESSION["gagal"] = "Password salah, silahkan coba lagi";
            }
        } else {
            $_SESSION["gagal"] = "Username salah, silahkan coba lagi";
        }
    } else {
        $_SESSION["gagal"] = "Database query failed";
    }
}

// Display any session errors
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Sign in with illustration - Tabler</title>
    <link href="<?= base_url('assets/css/tabler.min.css?1692870487') ?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1692870487') ?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/demo.min.css?1692870487') ?>" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>
<body class="d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="text-center mb-4">
                            <a href="." class="navbar-brand navbar-brand-autodark"><img src="<?= base_url('assets/img/logo-small.svg')?>" height="36" alt="Logo"></a>
                        </div>

                        <div class="card card-md">
                            <div class="card-body">
                                <h2 class="h2 text-center mb-4">Login to your account</h2>
                                <form action="" method="POST" autocomplete="off" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" id="username" class="form-control" name="username" placeholder="Username" autocomplete="off" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="input-group input-group-flat">
                                            <input type="password" id="password" class="form-control" name="password" placeholder="Password" autocomplete="off" required>
                                            <span class="input-group-text">
                                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                                    </svg>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-footer">
                                        <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg d-none d-lg-block">
                    <img src="<?= base_url('assets/img/undraw_secure_login_pdn4.svg')?>" height="300" class="d-block mx-auto" alt="Secure Login">
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/js/tabler.min.js?1692870487')?>" defer></script>
    <script src="<?= base_url('assets/js/demo.min.js?1692870487')?>" defer></script>

    <?php if (isset($_SESSION['gagal'])): ?>
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "<?= $_SESSION['gagal']; ?>",
            });
        </script>
        <?php unset($_SESSION['gagal']); ?>
    <?php endif; ?>
</body>
</html>
