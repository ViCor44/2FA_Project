<?php
session_start();
include 'classes/Database.php'; // Conexão com o banco de dados
include 'classes/User.php';

$db = (new Database())->getConnection();
$user = new User($db);

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $security_question = trim($_POST['security_question']);
    $security_answer = trim($_POST['security_answer']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificação de campos obrigatórios
    if (empty($email) || empty($security_question) || empty($security_answer) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Todos os campos são obrigatórios.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "As senhas não coincidem.";
    } else {
        // Buscar o utilizador pelo e-mail e verificar a pergunta e resposta de segurança
        $user_data = $user->getByEmail($email);

        if ($user_data && $user_data['security_question'] === $security_question && $user_data['security_answer'] === $security_answer) {
            // Atualizar a senha do utilizador
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            if ($user->updatePassword($email, $hashed_password)) {
                $success_message = "Senha redefinida com sucesso. Faça login novamente.";
            } else {
                $error_message = "Erro ao atualizar a senha. Tente novamente.";
            }
        } else {
            $error_message = "Informações de segurança incorretas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Redefinir Password</h2>

        <?php if ($error_message): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php else: ?>
            <form method="POST" action="reset_password.php">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium">E-mail</label>
                    <input type="email" id="email" name="email" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="security_question" class="block text-gray-700 font-medium">Pergunta de Segurança</label>
                    <input type="text" id="security_question" name="security_question" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="security_answer" class="block text-gray-700 font-medium">Resposta de Segurança</label>
                    <input type="text" id="security_answer" name="security_answer" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 font-medium">Nova Password</label>
                    <input type="password" id="new_password" name="new_password" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-gray-700 font-medium">Confirme a Nova Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">
                    Redefinir Password
                </button>
            </form>
        <?php endif; ?>

        <p class="text-center text-sm text-gray-600 mt-4">
            <a href="login.php" class="text-blue-500 hover:underline">Voltar ao Login</a>
        </p>
    </div>
</body>
</html>
