<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Edit User</h1>

    <form action="index.php?controller=User&action=edit&id=<?= $user['id'] ?>" method="post" class="space-y-4">
        <div>
            <label for="name" class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name" id="name" required value="<?= htmlspecialchars($user['name']) ?>"
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>"
                class="w-full px-4 py-2 border rounded" />
        </div>
        <div>
            <label for="role" class="block font-semibold mb-1">Role</label>
            <select name="role" id="role" required class="w-full px-4 py-2 border rounded">
                <option value="Peserta" <?= $user['role'] == 'Peserta' ? 'selected' : '' ?>>Peserta</option>
                <option value="Panitia" <?= $user['role'] == 'Panitia' ? 'selected' : '' ?>>Panitia</option>
            </select>
        </div>
        <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">Update</button>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
