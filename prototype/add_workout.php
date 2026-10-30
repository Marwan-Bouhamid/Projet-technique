<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require "conf/db.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title         = trim($_POST['title']);
    $description   = trim($_POST['description']);
    $duration_min  = $_POST['duration_min'];
    $level         = $_POST['level'];
    $muscle_name   = $_POST['muscel'];
    $id_coach      = 1;

    if (empty($title) || empty($description) || empty($duration_min)) {
        die("Merci de remplir tous les champs obligatoires.");
    }

    $stmt = $conn->prepare("SELECT id_group FROM muscle_groups WHERE name = ?");
    $stmt->execute([$muscle_name]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);


    $id_group = $group['id_group'];

    $sql = "INSERT INTO workouts 
                (title, description, duration_min, difficulty_level, id_coach, id_group) 
            VALUES 
                (:title, :description, :duration_min, :difficulty_level, :id_coach, :id_group)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':title'            => $title,
        ':description'      => $description,
        ':duration_min'     => $duration_min,
        ':difficulty_level' => $level,
        ':id_coach'         => $id_coach,
        ':id_group'         => $id_group,
    ]);


} 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add article</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div id="sidebar-backdrop"></div>
    <aside>
        <div class="aside-header flex justify-between items-center">
            <h2>Admin</h2>
            <button id="btn-close-sidebar" class="md:hidden text-gray-400 hover:text-white bg-transparent border-none cursor-pointer text-xl font-bold">&times;</button>
        </div>
        <nav>
            <a href="dashboard.php">Tableau de bord</a>
            <a href="add_workout.php" class="active">Ajouter un Workout</a>
            <a href="index.html">Groupes Musculaires</a>
        </nav>
    </aside>
    <div class="flex-1">
        <header>
            <div class="flex items-center gap-3">
                <button id="btn-toggle-sidebar" class="md:hidden text-gray-600 hover:text-gray-900 bg-transparent border-none cursor-pointer p-1">
                    <svg class="w-6 h-6" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span>New Workout</span>
            </div>
            <div class="user-info">
                <span>Admin</span>
            </div>
        </header>
        <main>
            <div class="container">
                <div class="card">
                    <form action="add_workout.php" method="POST">
                        <div>
                            <label>Title</label>
                            <input type="text" name="title" id="title" required>
                        </div>
                        <div>
                            <label>Description</label>
                            <input type="text" name="description" id="description" required>
                        </div>
                        <div>
                            <label>duration(min)</label>
                            <input type="number" name="duration_min" id="duration_min" required>
                        </div>
                        <div>
                            <label>Difficulty level</label>
                            <select name="level" id="level">
                                <option value="beginner">beginner</option>
                                <option value="intermediate">intermediate</option>
                                <option value="advanced">advanced</option>
                            </select>
                        </div>
                        <div>
                            <label>Target muscle</label>
                            <select name="muscel" id="muscel">
                                <option value="chest">chest</option>
                                <option value="legs">legs</option>
                                <option value="back">back</option>
                                <option value="core">core</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save workout</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
        const sidebar = document.querySelector('aside');
        const backdrop = document.getElementById('sidebar-backdrop');
        const btnToggle = document.getElementById('btn-toggle-sidebar');
        const btnClose = document.getElementById('btn-close-sidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('open');
        }

        if (btnToggle) btnToggle.addEventListener('click', toggleSidebar);
        if (btnClose) btnClose.addEventListener('click', toggleSidebar);
        if (backdrop) backdrop.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>