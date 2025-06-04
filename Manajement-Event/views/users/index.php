<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 class="text-4xl font-bold text-gray-800">Daftar Users</h1>
    <a href="index.php?controller=User&action=create" class="inline-block rounded bg-green-600 px-5 py-2 text-white font-medium hover:bg-green-700 transition duration-200 mt-4 sm:mt-0">+ Tambah User</a>
  </div>

  <!-- Search bar -->
  <form method="GET" action="index.php" class="mb-4">
    <input type="hidden" name="controller" value="User" />
    <input type="hidden" name="action" value="index" />
    <input id="searchInput" name="search" type="text" placeholder="Cari nama user..."
      value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
      class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
    <button type="submit" class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Cari</button>
  </form>


  <!-- Table -->
  <div class="overflow-x-auto shadow-lg rounded-lg border border-gray-200">
    <table class="min-w-full bg-white">
      <thead class="bg-green-100">
        <tr>
          <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">ID</th>
          <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Nama</th>
          <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Email</th>
          <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Role</th>
          <th class="py-3 px-6 text-center text-xs font-semibold text-green-800 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody id="userTableBody" class="divide-y divide-gray-200">
        <?php if (!empty($users) && is_array($users)): ?>
          <?php foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50">
              <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= $user['id'] ?></td>
              <td class="py-4 px-6 whitespace-nowrap text-sm font-semibold text-gray-900"><?= htmlspecialchars($user['name']) ?></td>
              <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
              <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($user['role']) ?></td>
              <td class="py-4 px-6 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <a href="index.php?controller=User&action=show&id=<?= $user['id'] ?>" class="text-green-600 hover:text-green-900 font-semibold">Lihat</a>
                <a href="index.php?controller=User&action=edit&id=<?= $user['id'] ?>" class="text-yellow-500 hover:text-yellow-700 font-semibold">Edit</a>
                <a href="index.php?controller=User&action=delete&id=<?= $user['id'] ?>" class="text-red-600 hover:text-red-800 font-semibold" onclick="return confirm('Hapus user ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center p-4">Data user tidak ditemukan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-between items-center mt-4">
    <button id="prevBtn" class="bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50" disabled>Previous</button>
    <div class="text-gray-700 font-semibold" id="pageIndicator">Page 1</div>
    <button id="nextBtn" class="bg-green-600 text-white px-4 py-2 rounded">Next</button>
  </div>

</div>

<script>
  // Ambil semua baris tr pada tbody
  const tbody = document.getElementById('userTableBody');
  const rows = Array.from(tbody.getElementsByTagName('tr'));
  const rowsPerPage = 5;
  let currentPage = 1;

  // Fungsi render pagination
  function renderTablePage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
      if (index >= start && index < end) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    document.getElementById('pageIndicator').innerText = `Page ${page}`;
    document.getElementById('prevBtn').disabled = page === 1;
    document.getElementById('nextBtn').disabled = end >= rows.length;
  }

  // Inisialisasi halaman pertama
  renderTablePage(currentPage);

  // Tombol pagination event
  document.getElementById('prevBtn').addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      renderTablePage(currentPage);
    }
  });

  document.getElementById('nextBtn').addEventListener('click', () => {
    if (currentPage * rowsPerPage < rows.length) {
      currentPage++;
      renderTablePage(currentPage);
    }
  });

  // Search input event
  document.getElementById('searchInput').addEventListener('input', (e) => {
    const filter = e.target.value.toLowerCase();
    rows.forEach(row => {
      const nameCell = row.cells[1].textContent.toLowerCase();
      if (nameCell.includes(filter)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    // Reset pagination setelah filter
    currentPage = 1;
    renderTablePage(currentPage);
  });
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>