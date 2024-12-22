<?php
session_start();
require_once 'vendor/autoload.php'; // Certifique-se de incluir o autoloader do Composer
include 'classes/Database.php';
include 'classes/User.php';

use \RobThree\Auth\TwoFactorAuth;

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_data = $user->getById($_SESSION['user_id']);
$tfa = new TwoFactorAuth();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    // Validar o código 2FA
    if ($tfa->verifyCode($user_data['google_2fa_secret'], $code)) {
        // Código correto, verificar se está pendente
        if ($user_data['two_factor_pending'] == 1) {
            // Atualizar o status do 2FA
            $user->enableTwoFactorAuthentication($_SESSION['user_id']);
        }
        // Redirecionar para o dashboard após tudo concluído
        header("Location: dashboard.php");
        exit();
    } else {
        // Código incorreto, exibir mensagem de erro
        $error_message = "Código incorreto.";
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate 2FA Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Validate 2FA Code</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="verify_2fa.php">
            <div class="mb-4">
                <label for="code" class="block text-gray-700 font-medium">2FA Code</label>
                <input type="text" id="code" name="code" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">
                Validate Code
            </button>
        </form>
    </div>
</body>
</html>

