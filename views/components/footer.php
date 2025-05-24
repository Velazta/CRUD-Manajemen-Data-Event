</main> <!-- Tutup wrapper konten utama -->

<!-- FOOTER -->
<footer class="bg-gray-100 border-t border-gray-300">
  <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-600 text-sm">
    Sistem Pengelolaan Data Event
  </div>
</footer>

<script>
  const btn = document.getElementById('mobile-menu-button');
  const menu = document.getElementById('mobile-menu');
  if (btn && menu) {
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  }
</script>

</body>

</html>