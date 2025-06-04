<?php
// Mulai session jika belum ada, untuk mengakses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_theme = 'light';
if(isset($_COOKIE['theme_preference'])) {
  $current_theme = $_COOKIE['theme_preference'] === 'dark' ? 'dark' : 'light';
}
if(isset($_SESSION['theme_preference'])) {
  $current_theme = $_SESSION['theme_preference'] === 'dark' ? 'dark' : 'light';
}


$is_dark_mode = ($current_theme === 'dark');
?>
<!DOCTYPE html>
<html lang="id" class="<?= $is_dark_mode ? 'dark' : '' ?>"> <head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistem Manajemen Event</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/JS/tailwind.config.js"></script> <script>
    // Pastikan tailwind.config.js sudah memuat darkMode: 'class'
    // contoh di tailwind.config.js:
    // module.exports = {
    //   darkMode: 'class', // atau 'media'
    //   // ...
    // }
    // Jika menggunakan file tailwind.config.js yang Anda berikan (window.tailwind),
    // pastikan darkMode: 'class' sudah dikonfigurasi di sana atau atur di sini:
    if (window.tailwind && window.tailwind.config) {
        window.tailwind.config.darkMode = 'class';
    } else if (window.tailwind) {
        window.tailwind.config = { darkMode: 'class' };
    } else {
        window.tailwind = { config: { darkMode: 'class' } };
    }
  </script>
  <style>
    /* Style dasar untuk transisi smooth antar tema */
    body {
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    /* Anda bisa menambahkan lebih banyak style spesifik di sini jika diperlukan,
       tapi idealnya gunakan utility class Tailwind */
    .dark body {
        background-color: #1f2937; /* gray-800 */
        color: #f3f4f6; /* gray-100 */
    }
    .dark nav { /* Contoh: membuat navbar sedikit lebih terang di dark mode */
        background-color: #374151; /* gray-700 */
    }
    .dark table {
        background-color: #374151; /* gray-700 untuk tabel */
    }
    .dark table thead {
        background-color: #4b5563; /* gray-600 untuk header tabel */
    }
     .dark table tr:hover {
        background-color: #4b5563 !important; /* gray-600 untuk hover baris tabel */
    }
    .dark table td, .dark table th {
        color: #d1d5db; /* gray-300 untuk teks tabel */
        border-color: #4b5563; /* gray-600 untuk border tabel */
    }
    .dark input, .dark select, .dark textarea {
        background-color: #374151; /* gray-700 */
        border-color: #4b5563; /* gray-600 */
        color: #f3f4f6; /* gray-100 */
    }
    .dark input::placeholder, .dark select::placeholder, .dark textarea::placeholder {
        color: #9ca3af; /* gray-400 */
    }
    .dark .login-card, .dark .register-card, .dark .form-card { /* Untuk form login/register */
        background-color: #374151; /* gray-700 */
    }
    .dark .login-card label, .dark .register-card label, .dark .form-card label,
    .dark .login-card h2, .dark .register-card h2, .dark .form-card h2,
    .dark .login-card div, .dark .register-card div, .dark .form-card div {
        color: #f3f4f6; /* gray-100 */
    }
    .dark .max-w-2xl, .dark .max-w-3xl, .dark .max-w-7xl { /* Ganti background container utama */
        /* background-color: #1f2937;  Sudah dihandle body */
    }
    .dark .bg-gray-50 { background-color: #1f2937 !important; /* gray-800 */ } /* Override bg-gray-50 di body */
    .dark .bg-white { background-color: #374151 !important; /* gray-700 */ }
    .dark .text-gray-800 { color: #e5e7eb !important; /* gray-200 */}
    .dark .text-gray-700 { color: #d1d5db !important; /* gray-300 */}
    .dark .text-gray-600 { color: #9ca3af !important; /* gray-400 */}
    .dark .text-gray-500 { color: #a1a1aa !important; /* gray-500 */}
    .dark .border-gray-200 { border-color: #4b5563 !important; /* gray-600 */}
    .dark .border-gray-300 { border-color: #6b7280 !important; /* gray-500 */}
    .dark .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3), 0 4px 6px -2px rgba(0,0,0,0.25); } /* Shadow lebih gelap */

  </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen dark:bg-gray-800"> <nav class="bg-green-600 shadow dark:bg-gray-700"> <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex-shrink-0">
          <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="index.php?controller=User&action=index" class="text-white text-xl font-bold dark:text-gray-100">Manajemen Event</a>
          <?php else: ?>
            <a href="index.php?controller=Auth&action=login" class="text-white text-xl font-bold dark:text-gray-100">Manajemen Event</a>
          <?php endif; ?>
        </div>
        <div class="hidden md:flex md:space-x-8 items-center">
          <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="index.php?controller=User&action=index" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium dark:text-gray-200 dark:hover:border-gray-400">Users</a>
            <a href="index.php?controller=Event&action=index" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium dark:text-gray-200 dark:hover:border-gray-400">Events</a>
            <span class="text-white text-sm dark:text-gray-200">Halo, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</span>
            <a href="index.php?controller=Auth&action=logout" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium dark:bg-red-600 dark:hover:bg-red-700">Logout</a>
          <?php else: ?>
            <a href="index.php?controller=Auth&action=login" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium dark:text-gray-200 dark:hover:border-gray-400">Login</a>
            <a href="index.php?controller=Auth&action=register" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium dark:text-gray-200 dark:hover:border-gray-400">Register</a>
          <?php endif; ?>
           <a href="index.php?controller=Auth&action=toggleTheme&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
               class="ml-4 px-3 py-2 text-sm font-medium text-white rounded-md hover:bg-green-700 dark:hover:bg-gray-600"
               title="Ganti Tema">
                <?= $is_dark_mode ? 'LIGHT MODE' : 'DARK MODE' ?>
            </a>
        </div>
        <div class="md:hidden flex items-center">
            <a href="index.php?controller=Auth&action=toggleTheme&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
               class="mr-2 p-2 text-sm font-medium text-white rounded-md hover:bg-green-700 dark:hover:bg-gray-600"
               title="Ganti Tema">
            </a>
            <?php if (isset($_SESSION['admin_id'])): ?>
              <span class="text-white text-sm mr-2 dark:text-gray-200"> <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <?php endif; ?>
          <button id="mobile-menu-button" class="text-white focus:outline-none focus:ring-2 focus:ring-white dark:text-gray-200" aria-label="Toggle menu">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-green-700 dark:bg-gray-600">
      <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="index.php?controller=User&action=index" class="block px-4 py-2 text-white hover:bg-green-800 dark:text-gray-200 dark:hover:bg-gray-500">Users</a>
        <a href="index.php?controller=Event&action=index" class="block px-4 py-2 text-white hover:bg-green-800 dark:text-gray-200 dark:hover:bg-gray-500">Events</a>
        <a href="index.php?controller=Auth&action=logout" class="block px-4 py-2 text-white bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700">Logout</a>
      <?php else: ?>
        <a href="index.php?controller=Auth&action=login" class="block px-4 py-2 text-white hover:bg-green-800 dark:text-gray-200 dark:hover:bg-gray-500">Login</a>
        <a href="index.php?controller=Auth&action=register" class="block px-4 py-2 text-white hover:bg-green-800 dark:text-gray-200 dark:hover:bg-gray-500">Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <main class="flex-grow container mx-auto px-4 py-8">