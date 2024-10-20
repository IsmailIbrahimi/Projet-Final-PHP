<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de tâche</title>
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

<h1><?= isset($task) ? 'Modifier la tâche' : 'Ajouter une nouvelle tâche' ?></h1>

<form method="POST" action="<?= isset($task) ? 'index.php?controller=task&action=update&id=' . $task->id : 'index.php?controller=task&action=add' ?>">
    <label for="title">Titre :</label>
    <input type="text" name="title" id="title" value="<?= isset($task) ? htmlspecialchars($task->title) : '' ?>" required><br>

    <label for="description">Description :</label>
    <textarea name="description" id="description" required><?= isset($task) ? htmlspecialchars($task->description) : '' ?></textarea><br>

    <label for="category">Catégorie :</label>
    <select name="category" id="category">
        <option value="">Aucune</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= isset($task) && $task->category == $category['name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="new_category">Ou ajouter une nouvelle catégorie :</label>
    <input type="text" name="new_category" id="new_category"><br>

    <button type="submit"><?= isset($task) ? 'Mettre à jour' : 'Ajouter' ?></button>
</form>

<div class="button-container">
    <a href="index.php?controller=task&action=index" class="btn-back-list">Retour à la liste des tâches</a>
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
</script>



</body>
</html>
