<?php 
$form_errors = $form_errors ?? []; 

include __DIR__ . '/../components/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">Edit Event</h1>

    <?php if (isset($form_errors['general'])): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">
            <?= htmlspecialchars($form_errors['general']) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=Event&action=edit&id=<?= htmlspecialchars($event['id'] ?? '') ?>" method="post" class="space-y-4">
        <div>
            <label for="name" class="block font-semibold mb-1">Nama Event</label>
            <input type="text" name="name" id="name" required value="<?= htmlspecialchars($event['name'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded <?php echo isset($form_errors['name']) ? 'border-red-500' : 'border-gray-300'; ?>" />
            <?php if (isset($form_errors['name'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['name']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="date" class="block font-semibold mb-1">Tanggal</label>
            <input type="date" name="date" id="date" required value="<?= htmlspecialchars($event['date'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded <?php echo isset($form_errors['date']) ? 'border-red-500' : 'border-gray-300'; ?>" />
            <?php if (isset($form_errors['date'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['date']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="location" class="block font-semibold mb-1">Lokasi</label>
            <input type="text" name="location" id="location" required value="<?= htmlspecialchars($event['location'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded <?php echo isset($form_errors['location']) ? 'border-red-500' : 'border-gray-300'; ?>" />
            <?php if (isset($form_errors['location'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($form_errors['location']) ?></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">Update</button>
        <a href="index.php?controller=Event&action=index" class="inline-block ml-2 text-gray-600 hover:underline">Batal</a>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
