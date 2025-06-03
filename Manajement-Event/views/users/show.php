<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Detail User</h1>

    <div class="border p-6 rounded shadow">
        <p><strong>ID:</strong> <?= $user['id'] ?></p>
        <p><strong>Nama:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
    </div>

    <a href="index.php?controller=User&action=index" class="inline-block mt-6 text-green-600 hover:underline">Kembali ke Daftar User</a>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>