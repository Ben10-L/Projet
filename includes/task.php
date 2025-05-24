<?php
session_start();
require 'db.php';
require 'auth.php';

redirectIfNotLoggedIn();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    switch ($action) {
        case 'create':
            $list_id = (int)$_POST['list_id'];
            $title = trim($_POST['title']);
            $due_date = $_POST['due_date'] ?: null;
            
            if (empty($title)) {
                $response['message'] = 'Task title cannot be empty';
                break;
            }
            
            // Verify list belongs to user
            $stmt = $pdo->prepare("SELECT id FROM lists WHERE id = ? AND user_id = ?");
            $stmt->execute([$list_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'List not found or access denied';
                break;
            }
            
            $stmt = $pdo->prepare("INSERT INTO tasks (list_id, title, due_date) VALUES (?, ?, ?)");
            $stmt->execute([$list_id, $title, $due_date]);
            
            $response['success'] = true;
            break;
            
        case 'update':
            $task_id = (int)$_POST['task_id'];
            $title = trim($_POST['title']);
            $due_date = $_POST['due_date'] ?: null;
            
            if (empty($title)) {
                $response['message'] = 'Task title cannot be empty';
                break;
            }
            
            // Verify task belongs to user
            $stmt = $pdo->prepare("SELECT t.id FROM tasks t JOIN lists l ON t.list_id = l.id WHERE t.id = ? AND l.user_id = ?");
            $stmt->execute([$task_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'Task not found or access denied';
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, due_date = ? WHERE id = ?");
            $stmt->execute([$title, $due_date, $task_id]);
            
            $response['success'] = true;
            break;
            
        case 'update-status':
            $task_id = (int)$_POST['task_id'];
            $status = $_POST['status'] === 'completed' ? 'completed' : 'pending';
            
            // Verify task belongs to user
            $stmt = $pdo->prepare("SELECT t.id FROM tasks t JOIN lists l ON t.list_id = l.id WHERE t.id = ? AND l.user_id = ?");
            $stmt->execute([$task_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'Task not found or access denied';
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
            $stmt->execute([$status, $task_id]);
            
            $response['success'] = true;
            break;
            
        case 'delete':
            $task_id = (int)$_POST['task_id'];
            
            // Verify task belongs to user
            $stmt = $pdo->prepare("SELECT t.id FROM tasks t JOIN lists l ON t.list_id = l.id WHERE t.id = ? AND l.user_id = ?");
            $stmt->execute([$task_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'Task not found or access denied';
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            
            $response['success'] = true;
            break;
            
        default:
            $response['message'] = 'Invalid action';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>