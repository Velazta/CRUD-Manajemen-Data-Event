<?php
// Pastikan session sudah dimulai (sebaiknya sudah di index.php utama atau header.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definisikan $phpRowsPerPage untuk digunakan dalam logika PHP
$phpRowsPerPage = 5; // Sesuaikan dengan jumlah item per halaman yang Anda inginkan untuk pagination

include __DIR__ . '/../components/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

    <?php if (isset($_SESSION['success_message_global'])): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded shadow">
            <?= htmlspecialchars($_SESSION['success_message_global']) ?>
        </div>
        <?php unset($_SESSION['success_message_global']); // Hapus pesan setelah ditampilkan ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message_global'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded shadow">
            <?= htmlspecialchars($_SESSION['error_message_global']) ?>
        </div>
        <?php unset($_SESSION['error_message_global']); // Hapus pesan setelah ditampilkan ?>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-4xl font-bold text-gray-800">Daftar Event</h1>
        <a href="index.php?controller=Event&action=create" class="inline-block rounded bg-green-600 px-5 py-2 text-white font-medium hover:bg-green-700 transition duration-200 mt-4 sm:mt-0 shadow hover:shadow-lg">+ Tambah Event</a>
    </div>

    <form method="GET" action="index.php" class="mb-4">
    <input type="hidden" name="controller" value="Event" />
    <input type="hidden" name="action" value="index" />
    <input id="searchInput" name="search" type="text" placeholder="Cari nama event..."
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
        class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
    <button type="submit" class="mt-2 sm:mt-0 sm:ml-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow hover:shadow-lg">Cari</button>
</form>

    <div class="overflow-x-auto shadow-lg rounded-lg border border-gray-200">
        <table class="min-w-full bg-white">
            <thead class="bg-green-100">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Nama Event</th>
                    <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Tanggal</th>
                    <th class="py-3 px-6 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Lokasi</th>
                    <th class="py-3 px-6 text-center text-xs font-semibold text-green-800 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="eventTableBody" class="divide-y divide-gray-200">
                <?php if (!empty($events) && is_array($events)): ?>
                    <?php foreach ($events as $event): ?>
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= $event['id'] ?></td>
                        <td class="py-4 px-6 whitespace-nowrap text-sm font-semibold text-gray-900"><?= htmlspecialchars($event['name']) ?></td>
                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars(date('d M Y', strtotime($event['date']))) // Format tanggal ?></td>
                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($event['location']) ?></td>
                        <td class="py-4 px-6 whitespace-nowrap text-center text-sm font-medium space-x-2">
                            <a href="index.php?controller=Event&action=show&id=<?= $event['id'] ?>" class="text-green-600 hover:text-green-900 font-semibold">Lihat</a>
                            <a href="index.php?controller=Event&action=edit&id=<?= $event['id'] ?>" class="text-yellow-500 hover:text-yellow-700 font-semibold">Edit</a>
                            <a href="index.php?controller=Event&action=delete&id=<?= $event['id'] ?>" class="text-red-600 hover:text-red-800 font-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus event ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center p-4 text-gray-500">Data event tidak ditemukan. <?= isset($_GET['search']) && !empty($_GET['search']) ? 'Coba kata kunci lain atau tampilkan semua.' : 'Silakan tambahkan event baru.' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    if (empty($searchQuery) && !empty($events) && count($events) > $phpRowsPerPage ):
    ?>
    <div class="flex justify-between items-center mt-6">
        <button id="prevBtn" class="bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 shadow hover:shadow-lg transition duration-150" disabled>Sebelumnya</button>
        <div class="text-gray-700 font-semibold" id="pageIndicator">Halaman 1</div>
        <button id="nextBtn" class="bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 shadow hover:shadow-lg transition duration-150">Berikutnya</button>
    </div>
    <?php endif; ?>
</div>

<script>
  const eventTbody = document.getElementById('eventTableBody');
  // Gunakan nama variabel yang berbeda untuk baris di JavaScript untuk menghindari kebingungan
  const jsEventRows = eventTbody ? Array.from(eventTbody.getElementsByTagName('tr')) : [];
  // Gunakan variabel yang sama dengan yang didefinisikan di PHP untuk konsistensi, atau definisikan secara independen.
  const jsRowsPerPage = <?= $phpRowsPerPage; ?>; // Mengambil nilai dari PHP
  let jsCurrentPage = 1;

  const pageIndicator = document.getElementById('pageIndicator');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  function renderEventTablePage(page) {
    if (!eventTbody || jsEventRows.length === 0) {
        if (pageIndicator) pageIndicator.innerText = 'Halaman 1';
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        return;
    }
    const start = (page - 1) * jsRowsPerPage;
    const end = start + jsRowsPerPage;

    jsEventRows.forEach((row, index) => {
      if(index >= start && index < end) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    if (pageIndicator) pageIndicator.innerText = `Halaman ${page}`;
    if (prevBtn) prevBtn.disabled = page === 1;
    if (nextBtn) nextBtn.disabled = end >= jsEventRows.length;
  }

  
  const currentUrlParams = new URLSearchParams(window.location.search);
  const currentSearchQuery = currentUrlParams.get('search');

  if (!currentSearchQuery && jsEventRows.length > 0 && prevBtn && nextBtn && pageIndicator) {
    renderEventTablePage(jsCurrentPage);

    prevBtn.addEventListener('click', () => {
      if (jsCurrentPage > 1) {
        jsCurrentPage--;
        renderEventTablePage(jsCurrentPage);
      }
    });

    nextBtn.addEventListener('click', () => {
      if (jsCurrentPage * jsRowsPerPage < jsEventRows.length) {
        jsCurrentPage++;
        renderEventTablePage(jsCurrentPage);
      }
    });
  } else if (prevBtn && nextBtn && pageIndicator) {
    // Jika sedang search atau tidak ada baris yang cukup untuk pagination, disable tombol
    prevBtn.disabled = true;
    nextBtn.disabled = true;
    
    if (jsEventRows.length === 0) {
        pageIndicator.innerText = 'Halaman 1';
    } else if (currentSearchQuery && jsEventRows.length > 0) {
        // Jika ada hasil dari search, semua ditampilkan, jadi pagination client-side tidak relevan
        pageIndicator.innerText = 'Halaman 1 (Hasil Pencarian)';
    }
  }

 
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>