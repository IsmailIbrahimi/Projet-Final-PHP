<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des tâches</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="theme-switch-wrapper">
    <label class="theme-switch" for="checkbox">
        <input type="checkbox" id="checkbox" />
        <div class="slider round"></div>
    </label>
    <span id="theme-text">Activer le mode sombre</span>
</div>

<h1>Liste des tâches</h1>

<form method="GET" action="index.php" id="filterForm">
    <input type="hidden" name="controller" value="task">
    <input type="hidden" name="action" value="index">

    <label for="search">Rechercher une tâche :</label>
    <input type="text" name="search" id="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Rechercher par titre ou description">
    
    <label for="category_filter">Filtrer par catégorie :</label>
    <select name="category_filter" id="category_filter" onchange="submitFilterForm()">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= isset($_GET['category_filter']) && $_GET['category_filter'] == $category['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="status_filter">Filtrer par statut :</label>
    <select name="status_filter" id="status_filter" onchange="submitFilterForm()">
        <option value="">Tous les statuts</option>
        <option value="completed" <?= isset($_GET['status_filter']) && $_GET['status_filter'] == 'completed' ? 'selected' : '' ?>>Terminées</option>
        <option value="not_completed" <?= isset($_GET['status_filter']) && $_GET['status_filter'] == 'not_completed' ? 'selected' : '' ?>>Non terminées</option>
    </select>

    <div class="button-container">
    <a href="index.php?controller=task&action=add" class="btn-add-task">Ajouter une nouvelle tâche</a>
</form>

<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            <?= htmlspecialchars($task->title) ?> - 
            <?= htmlspecialchars($task->description) ?> 
            <?php if ($task->category): ?>
                (Catégorie : <?= htmlspecialchars($task->category) ?>)
            <?php endif; ?>
            <?php if (!$task->isCompleted): ?>
                <a href="index.php?controller=task&action=complete&id=<?= $task->id ?>">
                    <img src="images/check.svg" alt="Marquer comme terminée" width="20" height="20">
                </a>
            <?php else: ?>
                <span>(Terminée)</span>
            <?php endif; ?>
            <a href="index.php?controller=task&action=edit&id=<?= $task->id ?>">
                <img src="images/pen.svg" alt="Modifier" width="20" height="20">
            </a>
            <a href="index.php?controller=task&action=delete&id=<?= $task->id ?>">
                <img src="images/trash.svg" alt="Supprimer" width="20" height="20">
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Gérer les catégories</h2>
<ul>
    <?php foreach ($categories as $category): ?>
        <li>
            <?= htmlspecialchars($category['name']) ?>
            <a href="index.php?controller=task&action=deleteCategory&id=<?= $category['id'] ?>">
                <img src="images/trash.svg" alt="Supprimer cette catégorie" width="20" height="20">
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a href="index.php?controller=task&action=index&page=<?= $currentPage - 1 ?>">Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="index.php?controller=task&action=index&page=<?= $i ?>" <?= $i == $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="index.php?controller=task&action=index&page=<?= $currentPage + 1 ?>">Suivant</a>
    <?php endif; ?>
</div>
    
    <?php if ($_GET['action'] !== 'index' || isset($_GET['id'])): ?>
        <a href="index.php?controller=task&action=index" class="btn-back-list">Retour à la liste des tâches</a>
    <?php endif; ?>
</div>

<script>
    const toggleSwitch = document.querySelector('#checkbox');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        toggleSwitch.checked = true;
    }

    toggleSwitch.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });

    function submitFilterForm() {
        document.getElementById('filterForm').submit();
    }
</script>

</body>
</html>
