<?php
require_once 'models/Task.php';

class TaskRepository {
    protected $pdo;

    public function __construct() {
        require 'config/config.php';
        $this->pdo = $pdo;
    }

    // Récupérer toutes les tâches avec filtres, pagination, et recherche
    public function getAllTasks($categoryId = null, $status = null, $limit = 5, $offset = 0, $search = null) {
        $sql = 'SELECT tasks.*, categories.name as category_name
                FROM tasks
                LEFT JOIN categories ON tasks.category_id = categories.id
                WHERE 1=1';

        $params = [];
        if ($categoryId) {
            $sql .= ' AND tasks.category_id = ?';
            $params[] = $categoryId;
        }

        if ($status === 'completed') {
            $sql .= ' AND tasks.is_completed = 1';
        } elseif ($status === 'not_completed') {
            $sql .= ' AND tasks.is_completed = 0';
        }

        if ($search) {
            $sql .= ' AND (tasks.title LIKE ? OR tasks.description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Limiter les résultats et définir l'offset (pagination)
        $sql .= ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);

        // Préparer et exécuter la requête
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $task = new Task(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['is_completed'],
                $row['created_at']
            );
            $task->category = $row['category_name'];
            $tasks[] = $task;
        }

        return $tasks;
    }

    // Compter toutes les tâches (pour la pagination et recherche)
    public function countAllTasks($categoryId = null, $status = null, $search = null) {
        $sql = 'SELECT COUNT(*) as task_count FROM tasks WHERE 1=1';

        $params = [];
        if ($categoryId) {
            $sql .= ' AND tasks.category_id = ?';
            $params[] = $categoryId;
        }

        if ($status === 'completed') {
            $sql .= ' AND tasks.is_completed = 1';
        } elseif ($status === 'not_completed') {
            $sql .= ' AND tasks.is_completed = 0';
        }

        if ($search) {
            $sql .= ' AND (tasks.title LIKE ? OR tasks.description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['task_count'];
    }

    // Ajouter une nouvelle tâche
    public function addTask($title, $description, $categoryId = null) {
        $stmt = $this->pdo->prepare('
            INSERT INTO tasks (title, description, is_completed, created_at, category_id)
            VALUES (?, ?, 0, NOW(), ?)
        ');
        return $stmt->execute([$title, $description, $categoryId]);
    }

    // Supprimer une tâche
    public function deleteTask($id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Marquer une tâche comme terminée
    public function markAsCompleted($id) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = 1 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Récupérer une tâche par son ID
    public function getTaskById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Task(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['is_completed'],
                $row['created_at']
            );
        }

        return null;
    }

    // Mettre à jour une tâche
    public function updateTask($id, $title, $description, $categoryId = null) {
        $stmt = $this->pdo->prepare('
            UPDATE tasks SET title = ?, description = ?, category_id = ?
            WHERE id = ?
        ');
        return $stmt->execute([$title, $description, $categoryId, $id]);
    }

    // Ajouter une nouvelle catégorie
    public function addCategory($name) {
        // Vérifier si la catégorie existe déjà
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([$name]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            return $category['id'];
        }

        // Ajouter une nouvelle catégorie
        $stmt = $this->pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);

        return $this->pdo->lastInsertId();
    }

    // Récupérer toutes les catégories
    public function getAllCategories() {
        $stmt = $this->pdo->query('SELECT * FROM categories');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Supprimer une catégorie
    public function deleteCategory($id) {
        // Mettre à jour les tâches pour les catégories supprimées
        $stmt = $this->pdo->prepare('UPDATE tasks SET category_id = NULL WHERE category_id = ?');
        $stmt->execute([$id]);

        // Supprimer la catégorie
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
