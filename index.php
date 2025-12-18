<?php
require_once 'functions.php';

// Prevent Caching (Relaxed for Offline Support)
// no-cache: Revalidate with server before using cache.
// If offline, SW handles the fallback to cache.
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


// Check for notifications on load
checkDeadlinesAndNotify();

$todos = getTodos();

// Sort todos
usort($todos, function($a, $b) {
    // Completed at bottom
    if ($a['status'] !== $b['status']) {
        return $a['status'] === 'completed' ? 1 : -1;
    }
    // Sort by deadline if available
    $deadlineA = !empty($a['deadline']) ? strtotime($a['deadline']) : PHP_INT_MAX;
    $deadlineB = !empty($b['deadline']) ? strtotime($b['deadline']) : PHP_INT_MAX;
    return $deadlineA - $deadlineB;
});

$total = count($todos);
$pending = count(array_filter($todos, fn($t) => $t['status'] === 'pending'));
$completed = count(array_filter($todos, fn($t) => $t['status'] === 'completed'));

ob_start();
?>

    <!-- Stats Row with Icons -->
    <div class="grid grid-cols-3 gap-3 sm:gap-6">
        <div class="bg-white p-3 sm:p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center sm:justify-between text-center sm:text-left">
            <div>
                <div class="text-slate-500 text-[10px] sm:text-xs uppercase tracking-wider font-semibold mb-1">Total</div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900"><?= $total ?></div>
            </div>
            <div class="hidden sm:block p-3 bg-slate-50 rounded-lg text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>
        <div class="bg-white p-3 sm:p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center sm:justify-between text-center sm:text-left">
             <div>
                <div class="text-slate-500 text-[10px] sm:text-xs uppercase tracking-wider font-semibold mb-1">Pending</div>
                <div class="text-xl sm:text-3xl font-bold text-indigo-600"><?= $pending ?></div>
            </div>
            <div class="hidden sm:block p-3 bg-indigo-50 rounded-lg text-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-3 sm:p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center sm:justify-between text-center sm:text-left">
             <div>
                <div class="text-slate-500 text-[10px] sm:text-xs uppercase tracking-wider font-semibold mb-1">Selesai</div>
                <div class="text-xl sm:text-3xl font-bold text-emerald-600"><?= $completed ?></div>
            </div>
             <div class="hidden sm:block p-3 bg-emerald-50 rounded-lg text-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- List Header -->
    <div class="flex items-center justify-between pt-4 pb-2">
        <h2 class="text-xl font-bold text-slate-800">Daftar Tugas</h2>
        <div class="bg-slate-100 text-slate-600 text-xs font-medium px-3 py-1.5 rounded-full">
            <?= $pending ?> Aktif / <?= $total ?> Total
        </div>
    </div>

    <?php if (empty($todos)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum ada tugas</h3>
            <p class="text-slate-500 mt-2">Waktu yang tepat untuk merencanakan sesuatu yang hebat.</p>
        </div>
    <?php else: ?>
        <!-- Spacious List Layout -->
        <div class="grid grid-cols-1 gap-4">
            <?php foreach ($todos as $todo): ?>
                <?php 
                    $isCompleted = $todo['status'] === 'completed';
                    $deadlineBadge = '';
                    if (!$isCompleted && !empty($todo['deadline'])) {
                        $deadline = new DateTime($todo['deadline']);
                        $now = new DateTime();
                        $diff = $now->diff($deadline);
                        $dateStr = $deadline->format('d M y, H:i');
                        
                        if ($deadline < $now) {
                            $deadlineBadge = "<span class='inline-flex items-center text-xs font-medium text-red-700 bg-red-50 border border-red-100 px-2.5 py-0.5 rounded-md'>🚨 Telat: $dateStr</span>";
                        } elseif ($diff->days == 0) {
                            $deadlineBadge = "<span class='inline-flex items-center text-xs font-medium text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-0.5 rounded-md'>⏰ Hari ini: $dateStr</span>";
                        } else {
                            $deadlineBadge = "<span class='inline-flex items-center text-xs font-medium text-slate-600 bg-slate-100 border border-gray-200 px-2.5 py-0.5 rounded-md'>📅 $dateStr</span>";
                        }
                    }
                ?>
                
                <div class="bg-white group rounded-xl border border-gray-200 p-4 sm:p-5 transition-all hover:border-slate-300 hover:shadow-sm">
                    <div class="flex items-start gap-4">
                        <!-- Custom Checkbox -->
                        <form action="process.php" method="POST" class="shrink-0 pt-1">
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                            <button type="submit" onclick="showLoader()" class="w-6 h-6 rounded-md border-2 <?= $isCompleted ? 'bg-slate-800 border-slate-800' : 'border-gray-300 hover:border-slate-800' ?> flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500/20">
                                <?php if ($isCompleted): ?>
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </button>
                        </form>

                        <div class="flex-1 min-w-0">
                            <!-- Top Row: Title & Actions -->
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-base font-bold text-slate-800 leading-snug break-words <?= $isCompleted ? 'text-slate-400 line-through' : '' ?>">
                                    <?= htmlspecialchars($todo['task']) ?>
                                </h3>
                                
                                <!-- Actions (Visible on Mobile, Hover on Desktop) -->
                                <div class="flex items-center gap-1 shrink-0 ml-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <a href="crud.php?id=<?= $todo['id'] ?>" onclick="showLoader()" class="text-slate-400 hover:text-indigo-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <a href="process.php?action=delete&id=<?= $todo['id'] ?>" onclick="if(!confirm('Hapus tugas ini?')) return false; showLoader()" class="text-slate-400 hover:text-red-500 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Description -->
                            <?php if (!empty($todo['desc'])): ?>
                                <p class="text-sm text-slate-500 mt-1 leading-relaxed break-words"><?= htmlspecialchars($todo['desc']) ?></p>
                            <?php endif; ?>
                            
                            <!-- Badges Row -->
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                <?php if ($deadlineBadge): ?>
                                    <?= $deadlineBadge ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($todo['phone'])): ?>
                                    <span class="inline-flex items-center text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        WhatsApp
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Mobile Floating Add Button (Visible only on small screens) -->
    <a href="crud.php" onclick="showLoader()" class="sm:hidden fixed bottom-6 right-6 w-14 h-14 bg-slate-900 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-slate-800 transition-transform hover:scale-105 active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </a>
</div>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
