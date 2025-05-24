<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Tambah User Baru</h1>

    <form action="index.php?controller=User&action=create" method="post" class="space-y-4">
        <div>
            <label for="name" class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name" id="name" required
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" id="email" required
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="role" class="block font-semibold mb-1">Role</label>
            <select name="role" id="role" required class="w-full px-4 py-2 border rounded">
                <option value="Peserta">Peserta</option>
                <option value="Panitia">Panitia</option>
            </select>
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan</button>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
