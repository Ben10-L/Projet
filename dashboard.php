<?php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$view = $_GET['view'] ?? 'all';
$current_list_id = isset($_GET['list_id']) ? (int)$_GET['list_id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$lists = $stmt->fetchAll();

$tasks = [];
$current_list_title = '';

switch ($view) {
    case 'today':
        $current_list_title = "Today's Tasks";
        $stmt = $pdo->prepare("
            SELECT t.*, l.title as list_title 
            FROM tasks t 
            JOIN lists l ON t.list_id = l.id 
            WHERE l.user_id = ? 
            AND DATE(t.due_date) = CURDATE()
            ORDER BY t.status, t.due_date ASC
        ");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll();
        break;

    case 'all':
        $current_list_title = "All Tasks";
        $stmt = $pdo->prepare("
            SELECT t.*, l.title as list_title 
            FROM tasks t 
            JOIN lists l ON t.list_id = l.id 
            WHERE l.user_id = ? 
            ORDER BY t.status, t.due_date ASC
        ");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll();
        break;

    default:
        if ($current_list_id) {
            foreach ($lists as $list) {
                if ($list['id'] == $current_list_id) {
                    $current_list_title = $list['title'];
                    break;
                }
            }
            
            $stmt = $pdo->prepare("
                SELECT t.*, l.title as list_title 
                FROM tasks t 
                JOIN lists l ON t.list_id = l.id 
                WHERE t.list_id = ? 
                ORDER BY t.status, t.due_date ASC
            ");
            $stmt->execute([$current_list_id]);
            $tasks = $stmt->fetchAll();
        }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RemindMe - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="ios-style">
    <div class="app-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>RemindMe</h2>
                <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
            </div>
            
            <div class="lists-container">
                <h3>Quick Views</h3>
                <ul class="lists-menu">
                    <li class="<?php echo $view === 'today' ? 'active' : ''; ?>">
                        <a href="dashboard.php?view=today">
                            <i class="fas fa-calendar-day"></i>
                            Today's Tasks
                        </a>
                    </li>
                    <li class="<?php echo $view === 'all' ? 'active' : ''; ?>">
                        <a href="dashboard.php?view=all">
                            <i class="fas fa-tasks"></i>
                            All Tasks
                        </a>
                    </li>
                </ul>

                <h3>My Lists</h3>
                <ul class="lists-menu">
                    <?php foreach ($lists as $list): ?>
                        <li class="<?php echo $list['id'] == $current_list_id && $view === 'list' ? 'active' : ''; ?>">
                            <a href="dashboard.php?view=list&list_id=<?php echo $list['id']; ?>">
                                <i class="fas fa-list"></i>
                                <?php echo htmlspecialchars($list['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <button id="add-list-btn" class="ios-button secondary">
                    <i class="fas fa-plus"></i> New List
                </button>
            </div>
            
            <div class="sidebar-footer">
                <a href="includes/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="content-header">
                <h1><?php echo htmlspecialchars($current_list_title); ?></h1>
                
                <?php if ($view === 'list' && $current_list_id): ?>
                    <div class="list-actions">
                        <button class="ios-icon-button" id="edit-list-btn" data-list-id="<?php echo $current_list_id; ?>">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="ios-icon-button danger" id="delete-list-btn" data-list-id="<?php echo $current_list_id; ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tasks-container">
                <?php if (!empty($tasks)): ?>
                    <div class="tasks-list">
                        <?php foreach ($tasks as $task): ?>
                            <div class="task-item <?php echo $task['status'] === 'completed' ? 'completed' : ''; ?>" data-task-id="<?php echo $task['id']; ?>">
                                <div class="task-checkbox">
                                    <input type="checkbox" <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                                </div>
                                <div class="task-content">
                                    <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                                    <div class="task-list-name">
                                        <i class="fas fa-list"></i>
                                        <?php echo htmlspecialchars($task['list_title']); ?>
                                    </div>
                                    <?php if ($task['due_date']): ?>
                                        <div class="task-due-date">
                                            <i class="far fa-calendar-alt"></i>
                                            <?php echo date('M j, Y', strtotime($task['due_date'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="task-actions">
                                    <button class="ios-icon-button edit-task-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="ios-icon-button danger delete-task-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No tasks found.</p>
                        <?php if ($view === 'list'): ?>
                            <button id="add-task-btn" class="ios-button primary">
                                <i class="fas fa-plus"></i> Add Task
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($view === 'list'): ?>
                    <button id="add-task-btn" class="ios-button primary floating-btn">
                        <i class="fas fa-plus"></i> 
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="modal-overlay" id="modal-overlay"></div>
    
    <div class="modal" id="list-modal">
        <div class="modal-header">
            <h3 id="list-modal-title">Add New List</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <form id="list-form">
                <input type="hidden" id="list-id">
                <div class="form-group">
                    <label for="list-title">List Title</label>
                    <input type="text" id="list-title" class="ios-input" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="ios-button secondary modal-close-btn">Cancel</button>
                    <button type="submit" class="ios-button primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="modal" id="task-modal">
        <div class="modal-header">
            <h3 id="task-modal-title">Add New Task</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <form id="task-form">
                <input type="hidden" id="task-id">
                <input type="hidden" id="task-list-id" value="<?php echo $current_list_id; ?>">
                <div class="form-group">
                    <label for="task-title">Task Title</label>
                    <input type="text" id="task-title" class="ios-input" required>
                </div>
                <div class="form-group">
                    <label for="task-due-date">Due Date (optional)</label>
                    <input type="date" id="task-due-date" class="ios-input">
                </div>
                <div class="form-actions">
                    <button type="button" class="ios-button secondary modal-close-btn">Cancel</button>
                    <button type="submit" class="ios-button primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="modal" id="confirm-modal">
        <div class="modal-header">
            <h3>Confirm Action</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirm-message">Are you sure you want to delete this item?</p>
            <div class="form-actions">
                <button type="button" class="ios-button secondary modal-close-btn">Cancel</button>
                <button type="button" class="ios-button danger" id="confirm-action-btn">Delete</button>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
