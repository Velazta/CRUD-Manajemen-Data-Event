<?php include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($event['name']) ?></h1>

    <p><strong>Tanggal:</strong> <?= htmlspecialchars($event['date']) ?></p>
    <p><strong>Lokasi:</strong> <?= htmlspecialchars($event['location']) ?></p>

    <h2 class="mt-8 text-2xl font-semibold">Daftar Peserta</h2>

    <?php if (!empty($participants) && is_array($participants)): ?>
        <ul class="list-disc pl-6 mt-4">
            <?php foreach ($participants as $p): ?>
                <li><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Belum ada peserta yang terdaftar.</p>
    <?php endif; ?>

    <h3 class="mt-8 text-xl font-semibold">Tambah Peserta</h3>

    <form action="index.php?controller=Event&action=addParticipant&id=<?= $event['id'] ?>" method="post" class="mt-4 flex space-x-2">
        <select name="user_id" required class="border rounded px-4 py-2 flex-grow">
            <option value="">-- Pilih Peserta --</option>
            <?php foreach ($allUsers as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Tambah</button>
    </form>

    <a href="index.php?controller=Event&action=index" class="inline-block mt-6 text-green-600 hover:underline">Kembali ke Daftar Event</a>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
