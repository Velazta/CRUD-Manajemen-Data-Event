<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-3xl mx-auto mt-10 px-4">

    <?php if (isset($_SESSION['success_message_event_show'])): ?>
        <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg shadow">
            <?= htmlspecialchars($_SESSION['success_message_event_show']) ?>
        </div>
        <?php unset($_SESSION['success_message_event_show']);?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message_event_show'])): ?>
        <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow">
            <?= htmlspecialchars($_SESSION['error_message_event_show']) ?>
        </div>
        <?php unset($_SESSION['error_message_event_show']);?>
    <?php endif; ?>

    <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-6 border-b pb-4"><?= htmlspecialchars($event['name']) ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500">Tanggal:</p>
                <p class="text-lg text-gray-700 font-semibold"><?= htmlspecialchars(date('l, d F Y', strtotime($event['date']))) // Format tanggal lebih lengkap ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Lokasi:</p>
                <p class="text-lg text-gray-700 font-semibold"><?= htmlspecialchars($event['location']) ?></p>
            </div>
        </div>

        <h2 class="mt-10 text-2xl font-semibold text-gray-700 mb-4">Daftar Peserta</h2>

        <?php if (!empty($participants) && is_array($participants)): ?>
            <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-700">
                <?php foreach ($participants as $p): ?>
                    <li class="bg-gray-50 p-3 rounded-md shadow-sm">
                        <span class="font-medium"><?= htmlspecialchars($p['name']) ?></span> (<?= htmlspecialchars($p['email']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500 italic mt-4">Belum ada peserta yang terdaftar untuk event ini.</p>
        <?php endif; ?>

        <h3 class="mt-10 text-xl font-semibold text-gray-700 mb-3">Tambah Peserta ke Event</h3>
        <p class="text-sm text-gray-600 mb-4">Pilih user dari daftar di bawah untuk ditambahkan sebagai peserta event ini.</p>

        <form action="index.php?controller=Event&action=addParticipant&id=<?= $event['id'] ?>" method="post" class="mt-4 flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
            <select name="user_id" required class="border border-gray-300 rounded px-4 py-2 flex-grow focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                <option value="">-- Pilih Peserta --</option>
                <?php if (!empty($allUsers) && is_array($allUsers)): ?>
                    <?php foreach ($allUsers as $user): ?>
                        <?php
                        // Cek apakah user ini sudah menjadi peserta
                        $isParticipant = false;
                        if (!empty($participants) && is_array($participants)) {
                            foreach ($participants as $participant) {
                                if ($participant['id'] == $user['id']) {
                                    $isParticipant = true;
                                    break;
                                }
                            }
                        }
                        // Untuk saat ini, kita hanya cek apakah sudah jadi peserta atau belum.
                        if (!$isParticipant):
                        ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 shadow hover:shadow-lg transition duration-150">Tambah Peserta</button>
        </form>
        <?php 
            $availableUsersToAdd = false;
            if (!empty($allUsers) && is_array($allUsers)) {
                foreach ($allUsers as $user) {
                    $isParticipant = false;
                    if (!empty($participants) && is_array($participants)) {
                        foreach ($participants as $participant) {
                            if ($participant['id'] == $user['id']) {
                                $isParticipant = true;
                                break;
                            }
                        }
                    }
                    if (!$isParticipant) {
                        $availableUsersToAdd = true;
                        break;
                    }
                }
            }
            if (!$availableUsersToAdd && !empty($allUsers)) :
        ?>
            <p class="text-sm text-yellow-700 bg-yellow-50 p-2 rounded-md mt-3 italic">Semua user yang tersedia sudah menjadi peserta event ini atau tidak ada user yang bisa ditambahkan.</p>
        <?php elseif (empty($allUsers)): ?>
             <p class="text-sm text-gray-500 mt-3 italic">Tidak ada user tersedia di sistem untuk ditambahkan.</p>
        <?php endif; ?>


    </div>
    <div class="mt-8 text-center">
        <a href="index.php?controller=Event&action=index" class="inline-block text-green-600 hover:text-green-800 hover:underline font-medium py-2 px-4 rounded-lg border border-green-500 hover:bg-green-50 transition duration-150">
            &larr; Kembali ke Daftar Event
        </a>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
