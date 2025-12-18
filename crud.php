<?php
require_once 'functions.php';

$todo = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $todo = getTodoById($_GET['id']);
    if ($todo) {
        $isEdit = true;
    }
}
ob_start();
?>

<div class="max-w-xl mx-auto">
    
    <div class="mb-6">
        <a href="index.php" onclick="showLoader()" class="text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-slate-800">
                <?= $isEdit ? 'Edit Tugas' : 'Tugas Baru' ?>
            </h2>
        </div>
        
        <form action="process.php" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="action" value="save">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $todo['id'] ?>">
            <?php endif; ?>

            <div>
                <label for="task" class="block text-sm font-medium text-slate-700 mb-1">Judul Tugas</label>
                <input type="text" id="task" name="task" required 
                    value="<?= $isEdit ? htmlspecialchars($todo['task']) : '' ?>" 
                    placeholder="Apa yang perlu diselesaikan?"
                    class="block w-full rounded-lg border-gray-300 focus:border-slate-500 focus:ring-slate-500 text-sm py-2.5 px-3 shadow-sm placeholder:text-gray-400">
            </div>

            <div>
                <label for="desc" class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                <textarea id="desc" name="desc" rows="3" 
                    placeholder="Detail tambahan (opsional)"
                    class="block w-full rounded-lg border-gray-300 focus:border-slate-500 focus:ring-slate-500 text-sm py-2 px-3 shadow-sm placeholder:text-gray-400"><?= $isEdit ? htmlspecialchars($todo['desc']) : '' ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="deadline" class="block text-sm font-medium text-slate-700 mb-1">Deadline</label>
                    <input type="datetime-local" id="deadline" name="deadline" 
                        value="<?= $isEdit ? htmlspecialchars($todo['deadline']) : '' ?>"
                        class="block w-full rounded-lg border-gray-300 focus:border-slate-500 focus:ring-slate-500 text-sm py-2.5 px-3 shadow-sm text-slate-600">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Notifikasi WA</label>
                    <input type="text" id="phone" name="phone" 
                        placeholder="Contoh: 628123..." 
                        value="<?= $isEdit ? htmlspecialchars($todo['phone'] ?? '') : '' ?>"
                        class="block w-full rounded-lg border-gray-300 focus:border-slate-500 focus:ring-slate-500 text-sm py-2.5 px-3 shadow-sm placeholder:text-gray-400">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="index.php" onclick="showLoader()" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" onclick="showLoader()" class="px-4 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
