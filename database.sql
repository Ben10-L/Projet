CREATE DATABASE IF NOT EXISTS remindme;
USE remindme;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    list_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    due_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_user_id ON lists(user_id);
CREATE INDEX idx_list_id ON tasks(list_id);
CREATE INDEX idx_task_status ON tasks(status);
CREATE INDEX idx_task_due_date ON tasks(due_date);


INSERT INTO users (username, password) VALUES 
('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO lists (user_id, title) VALUES 
(1, 'Work Tasks'),
(1, 'Personal Tasks'),
(1, 'Shopping List');

INSERT INTO tasks (list_id, title, status, due_date) VALUES 
(1, 'Complete project proposal', 'pending', DATE_ADD(NOW(), INTERVAL 2 DAY)),
(1, 'Review team code', 'completed', DATE_ADD(NOW(), INTERVAL 1 DAY)),
(2, 'Gym workout', 'pending', DATE_ADD(NOW(), INTERVAL 1 DAY)),
(2, 'Read book', 'pending', NULL),
(3, 'Buy groceries', 'pending', DATE_ADD(NOW(), INTERVAL 3 DAY)),
(3, 'Get new shoes', 'pending', NULL);

COMMENT ON TABLE users IS 'Stores user account information';
COMMENT ON TABLE lists IS 'Stores task lists created by users';
COMMENT ON TABLE tasks IS 'Stores individual tasks within lists';

-- Add helpful views
CREATE OR REPLACE VIEW user_tasks AS
SELECT 
    u.username,
    l.title as list_title,
    t.title as task_title,
    t.status,
    t.due_date,
    t.created_at
FROM users u
JOIN lists l ON u.id = l.user_id
JOIN tasks t ON l.id = t.list_id
ORDER BY t.due_date ASC;

DELIMITER //
CREATE PROCEDURE GetUserTasks(IN user_id_param INT)
BEGIN
    SELECT 
        l.title as list_title,
        t.title as task_title,
        t.status,
        t.due_date,
        t.created_at
    FROM lists l
    JOIN tasks t ON l.id = t.list_id
    WHERE l.user_id = user_id_param
    ORDER BY t.due_date ASC;
END //
DELIMITER ; 