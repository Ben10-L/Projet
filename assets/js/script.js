document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('modal-overlay');
    const listModal = document.getElementById('list-modal');
    const taskModal = document.getElementById('task-modal');
    const confirmModal = document.getElementById('confirm-modal');
    
    const addListBtn = document.getElementById('add-list-btn');
    const addTaskBtn = document.getElementById('add-task-btn');
    const editListBtn = document.getElementById('edit-list-btn');
    const deleteListBtn = document.getElementById('delete-list-btn');
    
    const listForm = document.getElementById('list-form');
    const taskForm = document.getElementById('task-form');
    
    document.querySelectorAll('.modal-close-btn').forEach(btn => {
        btn.addEventListener('click', closeAllModals);
    });
    
    modalOverlay.addEventListener('click', closeAllModals);
    
    addListBtn?.addEventListener('click', () => {
        document.getElementById('list-modal-title').textContent = 'Add New List';
        document.getElementById('list-id').value = '';
        document.getElementById('list-title').value = '';
        openModal(listModal);
    });
    
    editListBtn?.addEventListener('click', () => {
        const listId = editListBtn.dataset.listId;
        const listTitle = document.querySelector('.content-header h1').textContent;
        
        document.getElementById('list-modal-title').textContent = 'Edit List';
        document.getElementById('list-id').value = listId;
        document.getElementById('list-title').value = listTitle;
        openModal(listModal);
    });
    
    deleteListBtn?.addEventListener('click', () => {
        const listId = deleteListBtn.dataset.listId;
        document.getElementById('confirm-message').textContent = 'Are you sure you want to delete this list? All tasks in this list will also be deleted.';
        document.getElementById('confirm-action-btn').onclick = () => deleteList(listId);
        openModal(confirmModal);
    });
    
    addTaskBtn?.addEventListener('click', () => {
        document.getElementById('task-modal-title').textContent = 'Add New Task';
        document.getElementById('task-id').value = '';
        document.getElementById('task-title').value = '';
        document.getElementById('task-due-date').value = '';
        openModal(taskModal);
    });
    
    document.querySelectorAll('.edit-task-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskItem = this.closest('.task-item');
            const taskId = taskItem.dataset.taskId;
            const taskTitle = taskItem.querySelector('.task-title').textContent;
            const dueDate = taskItem.querySelector('.task-due-date')?.textContent.trim();
            
            document.getElementById('task-modal-title').textContent = 'Edit Task';
            document.getElementById('task-id').value = taskId;
            document.getElementById('task-title').value = taskTitle;
            if (dueDate) {
                const date = new Date(dueDate);
                document.getElementById('task-due-date').value = date.toISOString().split('T')[0];
            }
            openModal(taskModal);
        });
    });
    
    document.querySelectorAll('.delete-task-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.closest('.task-item').dataset.taskId;
            document.getElementById('confirm-message').textContent = 'Are you sure you want to delete this task?';
            document.getElementById('confirm-action-btn').onclick = () => deleteTask(taskId);
            openModal(confirmModal);
        });
    });
    
    document.querySelectorAll('.task-checkbox input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.closest('.task-item').dataset.taskId;
            const status = this.checked ? 'completed' : 'pending';
            updateTaskStatus(taskId, status);
        });
    });
    
    listForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        const listId = document.getElementById('list-id').value;
        const title = document.getElementById('list-title').value;
        
        if (listId) {
            updateList(listId, title);
        } else {
            createList(title);
        }
    });
    
    taskForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        const taskId = document.getElementById('task-id').value;
        const listId = document.getElementById('task-list-id').value;
        const title = document.getElementById('task-title').value;
        const dueDate = document.getElementById('task-due-date').value;
        
        if (taskId) {
            updateTask(taskId, title, dueDate);
        } else {
            createTask(listId, title, dueDate);
        }
    });
});

function openModal(modal) {
    document.getElementById('modal-overlay').classList.add('active');
    modal.classList.add('active');
}

function closeAllModals() {
    document.getElementById('modal-overlay').classList.remove('active');
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.remove('active');
    });
}

async function createList(title) {
    try {
        const response = await fetch('includes/list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=create&title=${encodeURIComponent(title)}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error creating list');
        }
    } catch (error) {
        alert('Error creating list');
    }
}

async function updateList(listId, title) {
    try {
        const response = await fetch('includes/list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update&list_id=${listId}&title=${encodeURIComponent(title)}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating list');
        }
    } catch (error) {
        alert('Error updating list');
    }
}

async function deleteList(listId) {
    try {
        const response = await fetch('includes/list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&list_id=${listId}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.href = 'dashboard.php?view=all';
        } else {
            alert(data.message || 'Error deleting list');
        }
    } catch (error) {
        alert('Error deleting list');
    }
}

async function createTask(listId, title, dueDate) {
    try {
        const response = await fetch('includes/task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=create&list_id=${listId}&title=${encodeURIComponent(title)}&due_date=${dueDate}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error creating task');
        }
    } catch (error) {
        alert('Error creating task');
    }
}

async function updateTask(taskId, title, dueDate) {
    try {
        const response = await fetch('includes/task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update&task_id=${taskId}&title=${encodeURIComponent(title)}&due_date=${dueDate}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating task');
        }
    } catch (error) {
        alert('Error updating task');
    }
}

async function deleteTask(taskId) {
    try {
        const response = await fetch('includes/task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&task_id=${taskId}`
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error deleting task');
        }
    } catch (error) {
        alert('Error deleting task');
    }
}

async function updateTaskStatus(taskId, status) {
    try {
        const response = await fetch('includes/task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update-status&task_id=${taskId}&status=${status}`
        });
        
        const data = await response.json();
        if (data.success) {
            const taskItem = document.querySelector(`[data-task-id="${taskId}"]`);
            if (status === 'completed') {
                taskItem.classList.add('completed');
            } else {
                taskItem.classList.remove('completed');
            }
        } else {
            alert(data.message || 'Error updating task status');
        }
    } catch (error) {
        alert('Error updating task status');
    }
}