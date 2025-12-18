<?php

define('DATA_FILE', __DIR__ . '/data.json');

function getTodos() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $json = file_get_contents(DATA_FILE);
    return json_decode($json, true) ?? [];
}

function saveTodos($todos) {
    file_put_contents(DATA_FILE, json_encode($todos, JSON_PRETTY_PRINT));
}

function getTodoById($id) {
    $todos = getTodos();
    foreach ($todos as $todo) {
        if ($todo['id'] === $id) {
            return $todo;
        }
    }
    return null;
}

// Integrasi WhatsApp Fonnte (Placeholder)
function sendWhatsAppNotification($target, $message) {
    // Anda harus memasukkan token Fonnte Anda di sini
    // Dapatkan token dari https://fonnte.com/
    $token = "bpGgRM9SKw8vBvuBCp39"; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send', // URL API Fonnte Default
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $message,
        'countryCode' => '62', // Opsional, default 62
    ),
      CURLOPT_HTTPHEADER => array(
        "Authorization: $token"
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function checkDeadlinesAndNotify() {
    $todos = getTodos();
    $updated = false;
    $now = new DateTime();
    
    foreach ($todos as &$todo) {
        if (!empty($todo['deadline']) && $todo['status'] !== 'completed') {
            $deadline = new DateTime($todo['deadline']);
            $diff = $now->diff($deadline);
            
            // Cek apakah deadline dalam waktu 1 hari (dan belum lewat jauh/lampau)
            // Logika: jika deadline di masa depan DAN selisih hari adalah 0 (artinya kurang dari 24 jam).
            if ($deadline > $now && $diff->days == 0 && empty($todo['notified'])) {
                 if (!empty($todo['phone'])) {
                     $msg = "Pengingat: Tugas Anda '{$todo['task']}' harus selesai pada {$todo['deadline']}!";
                     // MENGIRIM PESAN ASLI
                     sendWhatsAppNotification($todo['phone'], $msg);
                     
                     // Tandai sudah dinotifikasi agar tidak spam
                     $todo['notified'] = true;
                     $updated = true;
                 }
            }
        }
    }
    
    if ($updated) {
        saveTodos($todos);
    }
}
