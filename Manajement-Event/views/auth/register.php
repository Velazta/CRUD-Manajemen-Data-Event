<?php
// Mulai session jika belum ada, untuk mengakses $_SESSION untuk pesan error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Manajemen Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #2ECC71;
            font-family: 'Poppins', sans-serif;
        }
        .register-card {
            background-color: #FFFFFF;
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .register-input {
            border-color: #2ECC71;
            border-width: 1px;
            border-radius: 0.5rem;
        }
        .register-button {
            background-color: #2ECC71;
            border-radius: 0.5rem;
        }
        .login-link {
            color: #2ECC71;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="register-card p-8 sm:p-10 w-full max-w-sm mx-auto">
        <h2 class="text-3xl font-bold text-center mb-10 tracking-wide">REGISTER ADMIN</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">';
            echo htmlspecialchars($_SESSION['error_message']);
            echo '</div>';
            unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
        }
        ?>

        <form action="index.php?controller=Auth&action=doRegister" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        
            <div class="mb-6">
                <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                    class="register-input shadow-sm appearance-none w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan nama lengkap Anda" required>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email"
                    class="register-input shadow-sm appearance-none w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan email Anda" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password"
                    class="register-input shadow-sm appearance-none w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan password Anda" required>
            </div>

            <div class="mb-8">
                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="register-input shadow-sm appearance-none w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Konfirmasi password Anda" required>
            </div>

            <div class="mb-6">
                <button type="submit" class="register-button w-full text-white font-bold py-3 px-4 focus:outline-none focus:shadow-outline text-lg">REGISTER</button>
            </div>
        </form>

        <div class="text-center text-sm text-gray-700 mt-6">
            Sudah punya akun? <a href="index.php?controller=Auth&action=login" class="login-link font-bold hover:underline">Login</a>
        </div>
    </div>
</body>
</html>