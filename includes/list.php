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
            $title = trim($_POST['title']);
            
            if (empty($title)) {
                $response['message'] = 'List title cannot be empty';
                break;
            }
            
            $stmt = $pdo->prepare("INSERT INTO lists (user_id, title) VALUES (?, ?)");
            $stmt->execute([$user_id, $title]);
            
            $response['success'] = true;
            break;
            
        case 'update':
            $list_id = (int)$_POST['list_id'];
            $title = trim($_POST['title']);
            
            if (empty($title)) {
                $response['message'] = 'List title cannot be empty';
                break;
            }
            
            // Verify list belongs to user
            $stmt = $pdo->prepare("SELECT id FROM lists WHERE id = ? AND user_id = ?");
            $stmt->execute([$list_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'List not found or access denied';
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE lists SET title = ? WHERE id = ?");
            $stmt->execute([$title, $list_id]);
            
            $response['success'] = true;
            break;
            
        case 'delete':
            $list_id = (int)$_POST['list_id'];
            
            // Verify list belongs to user
            $stmt = $pdo->prepare("SELECT id FROM lists WHERE id = ? AND user_id = ?");
            $stmt->execute([$list_id, $user_id]);
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'List not found or access denied';
                break;
            }
            
            // Delete tasks first (foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE list_id = ?");
            $stmt->execute([$list_id]);
            
            // Then delete the list
            $stmt = $pdo->prepare("DELETE FROM lists WHERE id = ?");
            $stmt->execute([$list_id]);
            
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