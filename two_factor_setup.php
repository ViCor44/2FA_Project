<?php
session_start();
require_once 'vendor/autoload.php'; // Certifique-se de que o autoloader está incluído
include 'classes/Database.php';
include 'classes/User.php';

use \RobThree\Auth\TwoFactorAuth;

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

// Suponha que a sessão do usuário já esteja configurada
$user_id = $_SESSION['user_id'];

$tfa = new TwoFactorAuth();
$secret = $tfa->createSecret();
$qrCodeUrl = $tfa->getQRCodeImageAsDataUri('2FA Login System', $secret);

// Salvar o segredo no banco de dados
$user->save2FASecret($user_id, $secret);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Configuration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">2FA Configuration</h2>
        <p class="text-gray-700">Scan the QR Code with the Google Authenticator</p>
        <div class="mt-4">
            <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="mx-auto border border-gray-300 rounded-md" />
        </div>
        <p class="text-gray-700 mt-6">
            After scanning the QR Code, click the button below to validate the generated code.
        </p>
        <div class="mt-6">
            <a href="verify_2fa.php" 
               class="inline-block bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition">
                Validate Code
            </a>
        </div>
    </div>
</body>
</html>

