<?php
require_once 'models/TaskRepository.php';

class TaskController {
    // Afficher la liste des tâches avec filtres, pagination et recherche
    public function index() {
        $taskRepo = new TaskRepository();

        $categoryId = $_GET['category_filter'] ?? null;
        $status = $_GET['status_filter'] ?? null;
        $search = $_GET['search'] ?? null; 

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $tasksPerPage = 5; // Nombre de tâches par page
        $offset = ($currentPage - 1) * $tasksPerPage;

        $tasks = $taskRepo->getAllTasks($categoryId, $status, $tasksPerPage, $offset, $search);

        $totalTasks = $taskRepo->countAllTasks($categoryId, $status, $search);
        $totalPages = ceil($totalTasks / $tasksPerPage);

        $categories = $taskRepo->getAllCategories(); // Récupérer les catégories pour le filtre
        require 'views/tasks.php'; // Vue pour afficher la liste des tâches
    }

    // Ajouter une nouvelle tâche (avec catégorie)
    public function add() {
        $taskRepo = new TaskRepository();
        $categories = $taskRepo->getAllCategories(); // Récupérer les catégories

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];

            $newCategory = $_POST['new_category'] ?? null;
            if ($newCategory) {
                $categoryId = $taskRepo->addCategory($newCategory);
            } else {
                $categoryId = $_POST['category'] !== '' ? $_POST['category'] : null; // Si catégorie vide, mettre NULL
            }

            $taskRepo->addTask($title, $description, $categoryId);
            header('Location: index.php?controller=task&action=index');
        } else {
            require 'views/form.php'; // Vue pour le formulaire d'ajout
        }
    }

    // Supprimer une tâche
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $taskRepo = new TaskRepository();
            $taskRepo->deleteTask($id);
        }
        header('Location: index.php?controller=task&action=index');
    }

    // Marquer une tâche comme terminée
    public function complete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $taskRepo = new TaskRepository();
            $taskRepo->markAsCompleted($id);
        }
        header('Location: index.php?controller=task&action=index');
    }

    // Modifier une tâche
    public function edit() {
        $id = $_GET['id'] ?? null;
        $taskRepo = new TaskRepository();
        $categories = $taskRepo->getAllCategories(); // Récupérer les catégories

        if ($id) {
            $task = $taskRepo->getTaskById($id);
            require 'views/form.php'; // Vue pour le formulaire de modification
        }
    }

    // Mettre à jour une tâche
    public function update() {
        $taskRepo = new TaskRepository();
        $categories = $taskRepo->getAllCategories(); // Récupérer les catégories

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_GET['id'];
            $title = $_POST['title'];
            $description = $_POST['description'];

            // Vérifier si une nouvelle catégorie a été entrée
            $newCategory = $_POST['new_category'] ?? null;
            if ($newCategory) {
                // Ajouter la nouvelle catégorie et obtenir son ID
                $categoryId = $taskRepo->addCategory($newCategory);
            } else {
                $categoryId = $_POST['category'] !== '' ? $_POST['category'] : null; // Si catégorie vide, mettre NULL
            }

            $taskRepo->updateTask($id, $title, $description, $categoryId);
            header('Location: index.php?controller=task&action=index');
        } else {
            require 'views/form.php'; // Vue pour le formulaire de modification
        }
    }

    // Supprimer une catégorie
    public function deleteCategory() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $taskRepo = new TaskRepository();
            $taskRepo->deleteCategory($id);
        }
        header('Location: index.php?controller=task&action=index');
    }
}
