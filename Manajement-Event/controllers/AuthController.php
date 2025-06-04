<?php
require_once __DIR__ . '/../models/Admin.php'; 

class AuthController {
    private $adminModel;
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->adminModel = new Admin();
    }
     
    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
     
    private function verifyCsrfToken($submittedToken) {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $submittedToken)) {
            // Regenerasi token setelah verifikasi berhasil untuk form berikutnya
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return true;
        }
        return false;
    }

    
    // Menampilkan halaman login.
     
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
                
                setcookie('remember_me_token', '', time() - 3600, "/");
            }
        }
        
        $csrf_token = $this->generateCsrfToken(); 
        require_once __DIR__ . '/../views/auth/login.php'; 
    }

    
    // Memproses permintaan login (authenticate).
     
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($submittedToken)) {
                $_SESSION['error_message'] = "Permintaan tidak valid atau sesi telah berakhir (CSRF Token Error). Silakan coba lagi.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? ''; 
            $remember_me = isset($_POST['remember_me']);

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

            $admin = $this->adminModel->findByEmail($email);

            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];

                unset($_SESSION['error_message']);

                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    if ($this->adminModel->setRememberToken($admin['id'], $token)) {
                        setcookie('remember_me_token', $token, time() + (86400 * 30), "/", "", false, true); // Path, Domain, Secure, HttpOnly
                    }
                } else {
                    $this->adminModel->setRememberToken($admin['id'], null); 
                    setcookie('remember_me_token', '', time() - 3600, "/"); 
                }

                $redirect_url = $_SESSION['redirect_url'] ?? 'index.php?controller=User&action=index';
                unset($_SESSION['redirect_url']);
                header("Location: " . $redirect_url);
                exit;
            } else {
                $_SESSION['error_message'] = "Email atau password salah.";
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }
        } else {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
    }

    
    //  Menampilkan halaman registrasi.
     
    public function register() {
         // Jika sudah login, arahkan ke dashboard
        if ($this->isLoggedIn()) {
            header("Location: index.php?controller=User&action=index"); // Ganti dengan dashboard admin jika berbeda
            exit;
        }
        $csrf_token = $this->generateCsrfToken(); // Hasilkan token CSRF untuk form registrasi
        require_once __DIR__ . '/../views/auth/register.php'; // Pastikan path ini benar
    }

    
    //  Memproses permintaan registrasi (doRegister).
     
    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifikasi CSRF Token terlebih dahulu
            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!$this->verifyCsrfToken($submittedToken)) {
                $_SESSION['error_message'] = "Permintaan tidak valid atau sesi telah berakhir (CSRF Token Error). Silakan coba lagi.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }

            // Ambil dan sanitasi input (contoh sederhana)
            $name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            //Validasi input
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

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Buat data admin
            $adminData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ];

            // Simpan admin baru
            if ($this->adminModel->create($adminData)) {
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
                // Hapus pesan error dan input lama jika ada
                unset($_SESSION['form_errors_register']);
                unset($_SESSION['old_input_register']);
                header("Location: index.php?controller=Auth&action=login");
                exit;
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan saat registrasi. Silakan coba lagi nanti.";
                header("Location: index.php?controller=Auth&action=register");
                exit;
            }
        } else {
            header("Location: index.php?controller=Auth&action=register");
            exit;
        }
    }
    
    
    // Proses Logout.

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
        
        // Mulai session baru untuk pesan setelah logout 
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['success_message'] = "Anda telah berhasil logout.";
        header("Location: index.php?controller=Auth&action=login");
        exit;
    }

    
    //  Mengecek apakah admin sudah login.
    //  @return bool True jika sudah login, false jika belum.
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    //  Fungsi untuk mengamankan halaman. Panggil di awal controller yang perlu proteksi.
    public static function protectPage() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            if (isset($_COOKIE['remember_me_token'])) {
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
            if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'action=login') === false && strpos($_SERVER['REQUEST_URI'], 'action=authenticate') === false) {
                $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            }
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
    }

    public function toggleTheme() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $current_theme = $_SESSION['theme_preference'] ?? $_COOKIE['theme_preference'] ?? 'light';
        $new_theme = ($current_theme === 'light') ? 'dark' : 'light';
        $_SESSION['theme_preference'] = $new_theme;

        setcookie('theme_preference', $new_theme, time() + (86400 * 30), "/", "", false, false);
        
        $redirect_url = $_GET['redirect'] ?? 'index.php';
        if(empty($redirect_url) || !filter_var($redirect_url, FILTER_VALIDATE_URL) === false && strpos($redirect_url, 'index.php') !== 0) {
             if (strpos($redirect_url, 'index.php?') !== 0 && strpos($redirect_url, 'index.php') !== 0 && strpos($redirect_url, '/') !== 0) {
                $redirect_url = 'index.php';
             }
        }

        header("Location: " . $redirect_url);
        exit;
    }
}
?>