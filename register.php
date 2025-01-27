<?php
session_start();
include 'classes/Database.php'; // Arquivo de configuração com o banco de dados
include 'classes/User.php';
require_once 'vendor/autoload.php'; // Incluir a biblioteca do Google Authenticator
$db = (new Database())->getConnection();

use PragmaRX\Google2FA\Google2FA;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dados do formulário
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $security_question = trim($_POST['security_question']);
    $security_answer = trim($_POST['security_answer']);
    $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0; // Checkbox para ativar 2FA

    // Verificação de campos obrigatórios
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($security_question) || empty($security_answer)) {
        $error_message = "Todos os campos são obrigatórios.";
    } 
    // Regex para validação
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error_message = "The password must have at least 8 characters, one uppercase letter, one number, and one special character.";
    } 
    // Verificar se as senhas coincidem
    elseif ($password !== $confirm_password) {
        $error_message = "As senhas não coincidem.";
    } 
    // Validar e-mail
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "O e-mail informado não é válido.";
    } 
    // Verificar se o nome de utilizador já existe
    else {
        $user = new User($db);
        if ($user->getByUsername($username)) {
            $error_message = "Nome de utilizador já existe. Tente outro.";
        } 
        // Criptografar a senha
        else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Gerar a chave secreta para o Google Authenticator
            
            $googleAuthenticator = new Google2FA();
            $secret = $googleAuthenticator->generateSecretKey();
           

            // Registrar o utilizador no banco de dados
            if ($user->register($username, $email, $hashed_password, $security_question, $security_answer, $enable_2fa, $secret)) {
                // Enviar para a página de configuração do 2FA, se ativado
                if ($enable_2fa) {
                    $_SESSION['user_id'] = $db->lastInsertId(); // Salvar o ID do utilizador na sessão
                    header('Location: two_factor_setup.php');
                    exit();
                } else {
                    // Redirecionar para o login se 2FA não estiver ativado
                    header('Location: login.php');
                    exit();
                }
            } else {
                $error_message = "Erro ao registrar o utilizador. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-white mb-6">Create a New Account</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">E-mail</label>
                <input type="email" id="email" name="email" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
                <!-- Toggle Visibility Button -->
                <button type="button" id="togglePassword" 
                        class="absolute right-4 top-10 text-gray-500 hover:text-blue-500 focus:outline-none">
                    👁️
                </button>
            </div>
            <div class="mb-4 relative">
                <label for="confirm_password" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>                
            </div>
            <div class="mb-4">
                <label for="security_question" class="block text-sm font-medium text-gray-300">Security Question</label>
                <input type="text" id="security_question" name="security_question" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4">
                <label for="security_answer" class="block text-sm font-medium text-gray-300">Security Answer</label>
                <input type="text" id="security_answer" name="security_answer" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="enable_2fa" name="enable_2fa" 
                       class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500 bg-gray-700">
                <label for="enable_2fa" class="ml-2 text-sm text-gray-300">Enable 2FA</label>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                Continue
            </button>
        </form>
        <p class="text-center text-sm text-gray-400 mt-4">
            Already have an account? <a href="login.php" class="text-blue-400 hover:underline">Sign In</a>
        </p>
    </div>

    <script>
        // JavaScript to toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const passwordField1 = document.getElementById('confirm_password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            passwordField1.setAttribute('type', type);

            // Change icon (optional)
            togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
