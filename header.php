<?php
include 'classes/User.php';
include 'classes/Database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->getConnection();
$user = new User($db);

// Buscar os detalhes do usuário logado
$user_id = $_SESSION['user_id'];
$user_data = $user->getById($user_id);

if (!$user_data) {
    // Se o usuário não for encontrado, forçar logout
    header("Location: logout.php");
    exit();
}

// Saudação personalizada
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$username = htmlspecialchars($user_data['username']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-blue-600 text-white p-4 shadow-md flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <h1 class="text-xl font-bold">Dashboard</h1>
            <span class="text-sm font-medium"><?= $greeting ?>, <?= $username ?>!</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="edit_profile.php" class="text-sm underline hover:text-gray-200">Edit Profile</a>
            <a href="logout.php" class="text-sm underline hover:text-gray-200">Logout</a>
        </div>
    </header>
</body>
</html>
