<?php
session_start();
require_once 'vendor/autoload.php'; // Certifique-se de que o Composer está configurado corretamente
require_once 'classes/Database.php';
require_once 'classes/User.php'; // Ajuste o caminho aqui conforme necessário

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar login
    $user_data = $user->login($username, $password);

    if ($user_data) {
        // Verificar se o 2FA está habilitado para o utilizador
        if ($user_data['two_factor_enabled'] == 1 && $user_data['two_factor_pending'] == 1) {
            // Salvar o ID do utilizador na sessão e redirecionar para a página de verificação do 2FA
            $_SESSION['user_id'] = $user_data['id'];
            header("Location: two_factor_setup.php");
            exit();
        } elseif ($user_data['two_factor_enabled'] == 0 && $user_data['two_factor_pending'] == 1) {
            // Salvar o ID do utilizador na sessão e redirecionar para a página de verificação do 2FA
            $_SESSION['user_id'] = $user_data['id'];
            header("Location: two_factor_setup.php");
            exit();
        } elseif ($user_data['two_factor_enabled'] == 1 && $user_data['two_factor_pending'] == 0) {
            // Salvar o ID do utilizador na sessão e redirecionar para a página de verificação do 2FA
            $_SESSION['user_id'] = $user_data['id'];
            header("Location: verify_2fa.php");
            exit();
        } else {
            // Se o 2FA não estiver habilitado, redirecionar para a página principal
            $_SESSION['user_id'] = $user_data['id'];
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error_message = "Nome de utilizador ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-teal-400 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-3xl p-8 w-full max-w-sm">
        <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Welcome Back!</h2>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-6">
                <label for="username" class="block text-lg text-gray-700">User</label>
                <input type="text" id="username" name="username" 
                       class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                       required>
            </div>
            <div class="mb-6 relative">
                <label for="password" class="block text-lg text-gray-700">Password</label>
                <input type="password" id="password" name="password" 
                       class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                       required>
                <!-- Toggle Visibility Button -->
                <button type="button" id="togglePassword" 
                        class="absolute right-4 top-10 text-gray-500 hover:text-blue-500 focus:outline-none">
                    👁️
                </button>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                Continue
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                New here? 
                <a href="register.php" class="text-blue-500 hover:underline">Create Account</a>
            </p>
            <p class="text-sm text-gray-600 mt-2">
                Forgot password? 
                <a href="reset_password.php" class="text-blue-500 hover:underline">Reset password</a>
            </p>
        </div>
    </div>

    <script>
        // JavaScript to toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Change icon (optional)
            togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>

</body>
</html>

