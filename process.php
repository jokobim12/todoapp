<?php
require_once 'functions.php';

// Prevent Caching (Relaxed for Offline Support)
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


// Simple router logic
$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $task = $_POST['task'] ?? '';
        $desc = $_POST['desc'] ?? '';
        $deadline = $_POST['deadline'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $todos = getTodos();

        if ($id) {
            // Edit Data
            foreach ($todos as &$todo) {
                if ($todo['id'] === $id) {
                    $todo['task'] = $task;
                    $todo['desc'] = $desc;
                    $todo['deadline'] = $deadline;
                    $todo['phone'] = $phone;
                    // Reset status notifikasi jika deadline berubah? 
                    // Untuk sederhananya, kita reset jika disimpan ulang.
                    $todo['notified'] = false; 
                    break;
                }
            }
        } else {
            // Data Baru
            $newTodo = [
                'id' => uniqid(),
                'task' => $task,
                'desc' => $desc,
                'deadline' => $deadline,
                'phone' => $phone,
                'status' => 'pending',
                'notified' => false,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $todos[] = $newTodo;
        }

        saveTodos($todos);
        header('Location: index.php?t=' . time());
        exit;
    } 
    elseif ($action === 'toggle_status') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $todos = getTodos();
            foreach ($todos as &$todo) {
                if ($todo['id'] === $id) {
                    $todo['status'] = ($todo['status'] === 'pending') ? 'completed' : 'pending';
                    break;
                }
            }
            saveTodos($todos);
        }
        header('Location: index.php?t=' . time());
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'delete') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $todos = getTodos();
            $todos = array_filter($todos, function($t) use ($id) {
                return $t['id'] !== $id;
            });
            // Re-index array not strictly needed for JSON but good practice
            $todos = array_values($todos);
            saveTodos($todos);
        }
        header('Location: index.php?t=' . time());
        exit;
    }
}

// Fallback
header('Location: index.php?t=' . time());
exit;
