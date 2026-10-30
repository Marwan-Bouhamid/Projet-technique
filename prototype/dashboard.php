<?php

require_once 'conf/db.php';

$sql = 'SELECT workouts.*, users.first_name, muscle_groups.name AS muscle_name
        FROM workouts 
        INNER JOIN users ON workouts.id_coach = users.id 
        INNER JOIN muscle_groups ON workouts.id_group = muscle_groups.id_group
        ORDER BY workouts.created_at DESC';
        
$stmt = $conn->prepare($sql);
$stmt->execute();

$results = $stmt->fetchAll();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            <a href="dashboard.php" class="active">Tableau de bord</a>
            <a href="add_workout.php">Ajouter un Workout</a>
            <a href="index.html">Groupes Musculaires</a>
        </nav>
    </aside>

    <div class="flex-1">
        <header>
            <div class="flex items-center gap-3">
                <button id="btn-toggle-sidebar" class="md:hidden text-gray-600 hover:text-gray-900 bg-transparent border-none cursor-pointer p-1">
                    <svg class="w-6 h-6" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div style="font-weight: 500; color: #4b5563;">
                    Tableau de bord - Workouts
                </div>
            </div>
            <div class="user-info">
                <span>Admin</span>
            </div>
        </header>

        <main>
            <div class="container">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Liste des Workouts</h1>
                    </div>
                    <a href="add_workout.php" class="btn-primary">
                        + Nouveau Workout
                    </a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Workout</th>
                                <th>Description</th>
                                <th>Niveau</th>
                                <th>Durée</th>
                                <th>Muscle Cible</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $workout): ?>
                                <tr>
                                    <td><?= htmlspecialchars($workout['title']) ?></td>
                                    <td><?= htmlspecialchars($workout['description']) ?></td>
                                    <td><?= htmlspecialchars($workout['difficulty_level']) ?></td>
                                    <td><?= htmlspecialchars($workout['duration_min']) ?> min</td>
                                    <td><?= htmlspecialchars($workout['muscle_name']) ?></td>
                                    <td><?= htmlspecialchars($workout['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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