<?php
session_start();
include 'classes/Database.php';
include 'classes/User.php';
require_once 'vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->getConnection();
$user = new User($db);
$user_id = $_SESSION['user_id'];

// Buscar os detalhes do usuário logado
$user_data = $user->getById($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar formulário
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;

    // Validar entrada
    if (empty($username) || empty($email)) {
        $error_message = "O nome de usuário e o e-mail são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "O e-mail informado não é válido.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error_message = "As senhas não coincidem.";
    } else {
        // Atualizar informações do usuário
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

        if ($user->updateProfile($user_id, $username, $email, $hashed_password, $enable_2fa)) {
            $success_message = "Perfil atualizado com sucesso!";
            // Atualizar dados exibidos
            $user_data = $user->getById($user_id);
        } else {
            $error_message = "Erro ao atualizar o perfil. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Editar Perfil</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?= htmlspecialchars($success_message) ?>
            </div>
            <script>
                // Atrasar o redirecionamento por 2 segundos
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 2000); // 2000 milissegundos (2 segundos)
            </script>
        <?php endif; ?>

        <form method="POST">
            <!-- Formulário de edição de perfil -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-medium">Nome de Usuário</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium">Nova Senha</label>
                <input type="password" id="password" name="password"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-700 font-medium">Confirmar Nova Senha</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="enable_2fa" name="enable_2fa"
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       <?= $user_data['two_factor_enabled'] ? 'checked' : '' ?>>
                <label for="enable_2fa" class="ml-2 text-gray-700 font-medium">Ativar 2FA</label>
            </div>
            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">
                Salvar Alterações
            </button>
        </form>
    </div>
</body>
</html>