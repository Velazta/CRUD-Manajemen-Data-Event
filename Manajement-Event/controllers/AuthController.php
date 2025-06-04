<?php
require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
        // Mulai session jika belum ada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menampilkan halaman login.
     */
    public function login() {
        // Jika sudah login, arahkan ke dashboard (misal halaman User index)
        if ($this->isLoggedIn()) {
            header("Location: index.php?controller=User&action=index");
            exit;
        }
        // Jika ada cookie remember_me, coba login otomatis
        if (isset($_COOKIE['remember_me_token'])) {
            $admin = $this->adminModel->findByRememberToken($_COOKIE['remember_me_token']);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                header("Location: index.php?controller=User&action=index");
                exit;
            } else {
                // Token tidak valid, hapus cookie
                setcookie('remember_me_token', '', time() - 3600, "/"); // Hapus cookie
            }
        }
        require_once __DIR__ . '/../views/auth/login.php'; // Sesuaikan path jika perlu
    }

    /**
     * Memproses permintaan login.
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember_me = isset($_POST['remember_me']);

            if (empty($email) || empty($password)) {
                // Handle error: email atau password kosong
                $_SESSION['error_message'] = "Email dan password harus diisi.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $admin = $this->adminModel->findByEmail($email);

            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];

                if ($remember_me) {
                    // Buat token unik untuk remember me
                    $token = bin2hex(random_bytes(32));
                    $this->adminModel->setRememberToken($admin['id'], $token);
                    // Set cookie untuk 1 bulan
                    setcookie('remember_me_token', $token, time() + (86400 * 30), "/"); // 86400 = 1 hari
                } else {
                    // Jika tidak remember me, pastikan tidak ada token lama di DB & cookie
                    $this->adminModel->setRememberToken($admin['id'], null);
                    setcookie('remember_me_token', '', time() - 3600, "/");
                }

                // Arahkan ke halaman dashboard admin (misal: User index)
                header("Location: index.php?controller=User&action=index");
                exit;
            } else {
                // Login gagal
                $_SESSION['error_message'] = "Email atau password salah.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }
        } else {
            // Jika bukan POST, arahkan ke halaman login
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
    }

    /**
     * Menampilkan halaman registrasi.
     */
    public function register() {
         // Jika sudah login, arahkan ke dashboard
        if ($this->isLoggedIn()) {
            header("Location: index.php?controller=User&action=index");
            exit;
        }
        require_once __DIR__ . '/../views/auth/register.php'; // Sesuaikan path jika perlu
    }

    /**
     * Memproses permintaan registrasi.
     */
    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validasi sederhana
            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                $_SESSION['error_message'] = "Semua field harus diisi.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            // Cek apakah email sudah terdaftar
            if ($this->adminModel->findByEmail($email)) {
                $_SESSION['error_message'] = "Email sudah terdaftar.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ];

            if ($this->adminModel->create($data)) {
                // Registrasi berhasil, arahkan ke login
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            } else {
                // Registrasi gagal
                $_SESSION['error_message'] = "Terjadi kesalahan saat registrasi.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }
        } else {
            header("Location: index.php?controller=Auth&action=register");
            exit;
        }
    }

    /**
     * Proses Logout.
     */
    public function logout() {
        // Hapus remember_me token dari database jika ada
        if (isset($_SESSION['admin_id'])) {
            $this->adminModel->setRememberToken($_SESSION['admin_id'], null);
        }

        // Hapus cookie remember_me
        setcookie('remember_me_token', '', time() - 3600, "/");

        // Hancurkan semua data session
        session_unset();
        session_destroy();


        // Arahkan ke halaman login
        header("Location: index.php?controller=Auth&action=login");
        exit;
    }

    /**
     * Mengecek apakah admin sudah login.
     * @return bool True jika sudah login, false jika belum.
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    /**
     * Fungsi untuk mengamankan halaman. Panggil di awal controller yang perlu proteksi.
     */
    public static function protectPage() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Jika belum login DAN tidak ada cookie remember_me yang valid
        if (!isset($_SESSION['admin_id'])) {
            if (isset($_COOKIE['remember_me_token'])) {
                $adminModel = new Admin(); // Perlu instance baru di static method
                $admin = $adminModel->findByRememberToken($_COOKIE['remember_me_token']);
                if ($admin) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    // Lanjutkan ke halaman yang diminta
                    return;
                } else {
                     // Token tidak valid, hapus cookie
                    setcookie('remember_me_token', '', time() - 3600, "/");
                }
            }
            // Jika tetap tidak bisa login, arahkan ke halaman login
            header("Location: index.php?controller=Auth&action=login&redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }
}
?>