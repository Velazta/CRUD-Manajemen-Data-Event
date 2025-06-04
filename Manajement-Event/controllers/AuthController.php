<?php
require_once __DIR__ . '/../models/Admin.php'; // Sesuaikan path jika perlu

class AuthController {
    private $adminModel;
    // Tambahkan properti lain jika ada (misalnya $conn untuk koneksi DB langsung jika diperlukan)

    public function __construct() {
        // Pastikan session sudah dimulai di sini atau di index.php utama
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->adminModel = new Admin();
        // Inisialisasi properti lain jika ada
    }

    /**
     * Menghasilkan token CSRF, menyimpannya di session, dan mengembalikannya.
     * @return string Token CSRF yang dihasilkan.
     */
    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Memverifikasi token CSRF yang dikirim dengan yang ada di session.
     * @param string $submittedToken Token yang dikirim dari form.
     * @return bool True jika valid, false jika tidak.
     */
    private function verifyCsrfToken($submittedToken) {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $submittedToken)) {
            // Regenerasi token setelah verifikasi berhasil untuk form berikutnya
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return true;
        }
        return false;
    }

    /**
     * Menampilkan halaman login.
     */
    public function login() {
        // Jika sudah login, arahkan ke dashboard (misal halaman User index)
        if ($this->isLoggedIn()) {
            header("Location: index.php?controller=User&action=index"); // Ganti dengan dashboard admin jika berbeda
            exit;
        }
        // Jika ada cookie remember_me, coba login otomatis
        if (isset($_COOKIE['remember_me_token'])) {
            $admin = $this->adminModel->findByRememberToken($_COOKIE['remember_me_token']);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                header("Location: index.php?controller=User&action=index"); // Ganti dengan dashboard admin jika berbeda
                exit;
            } else {
                // Token tidak valid, hapus cookie
                setcookie('remember_me_token', '', time() - 3600, "/");
            }
        }
        
        $csrf_token = $this->generateCsrfToken(); // Hasilkan token CSRF untuk form login
        require_once __DIR__ . '/../views/auth/login.php'; // Pastikan path ini benar
    }

    /**
     * Memproses permintaan login (authenticate).
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Verifikasi CSRF Token terlebih dahulu
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($submittedToken)) {
                $_SESSION['error_message'] = "Permintaan tidak valid atau sesi telah berakhir (CSRF Token Error). Silakan coba lagi.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            // 2. Ambil dan sanitasi input (meskipun validasi lebih lanjut mungkin diperlukan)
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? ''; // Password tidak disanitasi dengan cara yang sama, hanya diambil apa adanya.
            $remember_me = isset($_POST['remember_me']);

            // 3. Validasi input dasar
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = "Format email tidak valid atau email kosong.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }
            if (empty($password)) {
                $_SESSION['error_message'] = "Password harus diisi.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            // 4. Cari admin berdasarkan email
            $admin = $this->adminModel->findByEmail($email);

            // 5. Verifikasi password dan proses login
            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];

                // Hapus pesan error lama jika ada
                unset($_SESSION['error_message']);

                if ($remember_me) {
                    // Buat token unik untuk remember me
                    $token = bin2hex(random_bytes(32));
                    if ($this->adminModel->setRememberToken($admin['id'], $token)) {
                        // Set cookie untuk 1 bulan (contoh)
                        setcookie('remember_me_token', $token, time() + (86400 * 30), "/", "", false, true); // Path, Domain, Secure, HttpOnly
                    }
                } else {
                    // Jika tidak remember me, pastikan tidak ada token lama di DB & cookie
                    $this->adminModel->setRememberToken($admin['id'], null); // Set token jadi null di DB
                    setcookie('remember_me_token', '', time() - 3600, "/"); // Hapus cookie
                }

                // Arahkan ke halaman dashboard admin (misal: User index atau halaman admin khusus)
                $redirect_url = $_SESSION['redirect_url'] ?? 'index.php?controller=User&action=index';
                unset($_SESSION['redirect_url']);
                header("Location: " . $redirect_url);
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
            header("Location: index.php?controller=User&action=index"); // Ganti dengan dashboard admin jika berbeda
            exit;
        }
        $csrf_token = $this->generateCsrfToken(); // Hasilkan token CSRF untuk form registrasi
        require_once __DIR__ . '/../views/auth/register.php'; // Pastikan path ini benar
    }

    /**
     * Memproses permintaan registrasi (doRegister).
     */
    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Verifikasi CSRF Token terlebih dahulu
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($submittedToken)) {
                $_SESSION['error_message'] = "Permintaan tidak valid atau sesi telah berakhir (CSRF Token Error). Silakan coba lagi.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            // 2. Ambil dan sanitasi input (contoh sederhana)
            $name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // 3. Validasi input
            $errors = [];
            if (empty($name)) {
                $errors['name'] = "Nama lengkap harus diisi.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Format email tidak valid atau email kosong.";
            }
            if (empty($password)) {
                $errors['password'] = "Password harus diisi.";
            } elseif (strlen($password) < 6) { // Contoh validasi panjang password
                $errors['password'] = "Password minimal harus 6 karakter.";
            }
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = "Password dan konfirmasi password tidak cocok.";
            }

            // Cek apakah email sudah terdaftar
            if (empty($errors['email']) && $this->adminModel->findByEmail($email)) {
                $errors['email'] = "Email sudah terdaftar. Silakan gunakan email lain.";
            }

            if (!empty($errors)) {
                $_SESSION['form_errors_register'] = $errors; // Simpan error spesifik field
                $_SESSION['old_input_register'] = ['name' => $name, 'email' => $email]; // Simpan input lama (tanpa password)
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            // 4. Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 5. Buat data admin
            $adminData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ];

            // 6. Simpan admin baru
            if ($this->adminModel->create($adminData)) {
                // Registrasi berhasil
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
                // Hapus pesan error dan input lama jika ada
                unset($_SESSION['form_errors_register']);
                unset($_SESSION['old_input_register']);
                header("Location: index.php?controller=Auth&action=login");
                exit;
            } else {
                // Registrasi gagal karena masalah database atau lainnya
                $_SESSION['error_message'] = "Terjadi kesalahan saat registrasi. Silakan coba lagi nanti.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }
        } else {
            // Jika bukan POST, arahkan ke halaman registrasi
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
        
        // Mulai session baru untuk pesan setelah logout (opsional)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['success_message'] = "Anda telah berhasil logout.";
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
                // Perlu instance baru di static method atau cara lain untuk akses model
                // Untuk kesederhanaan, jika menggunakan ini, pastikan model bisa diakses.
                // Atau, redirect ke login() yang akan menangani cookie.
                $tempAdminModel = new Admin(); 
                $admin = $tempAdminModel->findByRememberToken($_COOKIE['remember_me_token']);
                if ($admin) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    return; // Lanjutkan ke halaman yang diminta
                } else {
                    setcookie('remember_me_token', '', time() - 3600, "/"); // Token tidak valid
                }
            }
            // Simpan URL yang diminta agar bisa redirect setelah login
            if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'action=login') === false && strpos($_SERVER['REQUEST_URI'], 'action=authenticate') === false) {
                $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            }
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
    }
}
?>