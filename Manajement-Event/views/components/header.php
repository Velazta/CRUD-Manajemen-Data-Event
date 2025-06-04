<?php
// Mulai session jika belum ada, untuk mengakses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistem Manajemen Event</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/JS/tailwind.config.js"></script> </head>

<body class="bg-gray-50 flex flex-col min-h-screen">

  <nav class="bg-green-600 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex-shrink-0">
          <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="index.php?controller=User&action=index" class="text-white text-xl font-bold">Manajemen Event</a>
          <?php else: ?>
            <a href="index.php?controller=Auth&action=login" class="text-white text-xl font-bold">Manajemen Event</a>
          <?php endif; ?>
        </div>
        <div class="hidden md:flex md:space-x-8 items-center">
          <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="index.php?controller=User&action=index" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium">Users</a>
            <a href="index.php?controller=Event&action=index" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium">Events</a>
            <span class="text-white text-sm">Halo, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</span>
            <a href="index.php?controller=Auth&action=logout" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium">Logout</a>
          <?php else: ?>
            <a href="index.php?controller=Auth&action=login" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium">Login</a>
            <a href="index.php?controller=Auth&action=register" class="inline-flex items-center px-3 py-2 border-b-2 border-transparent text-white hover:border-green-300 hover:text-white text-sm font-medium">Register</a>
          <?php endif; ?>
        </div>
        <div class="md:hidden flex items-center">
            <?php if (isset($_SESSION['admin_id'])): ?>
              <span class="text-white text-sm mr-2"> <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <?php endif; ?>
          <button id="mobile-menu-button" class="text-white focus:outline-none focus:ring-2 focus:ring-white" aria-label="Toggle menu">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-green-700">
      <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="index.php?controller=User&action=index" class="block px-4 py-2 text-white hover:bg-green-800">Users</a>
        <a href="index.php?controller=Event&action=index" class="block px-4 py-2 text-white hover:bg-green-800">Events</a>
        <a href="index.php?controller=Auth&action=logout" class="block px-4 py-2 text-white bg-red-500 hover:bg-red-600">Logout</a>
      <?php else: ?>
        <a href="index.php?controller=Auth&action=login" class="block px-4 py-2 text-white hover:bg-green-800">Login</a>
        <a href="index.php?controller=Auth&action=register" class="block px-4 py-2 text-white hover:bg-green-800">Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <main class="flex-grow container mx-auto px-4 py-8">