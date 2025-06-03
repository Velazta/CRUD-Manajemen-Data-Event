<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Tambah Event Baru</h1>

    <form action="index.php?controller=Event&action=create" method="post" class="space-y-4">
        <div>
            <label for="name" class="block font-semibold mb-1">Nama Event</label>
            <input type="text" name="name" id="name" required
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="date" class="block font-semibold mb-1">Tanggal</label>
            <input type="date" name="date" id="date" required
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="location" class="block font-semibold mb-1">Lokasi</label>
            <input type="text" name="location" id="location" required
                class="w-full px-4 py-2 border rounded" />
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan</button>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>