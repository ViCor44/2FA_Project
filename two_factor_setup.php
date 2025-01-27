<?php
session_start();
require_once 'vendor/autoload.php'; // Certifique-se de que o autoloader está incluído
include 'classes/Database.php';
include 'classes/User.php';

use \RobThree\Auth\TwoFactorAuth;

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

// Suponha que a sessão do utilizador já esteja configurada
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
<body class="bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md text-center transform transition hover:scale-105 duration-300">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">2FA Configuration</h2>
        <p class="text-gray-600 mb-4">Scan the QR code using Google Authenticator.</p>
        
        <div class="mt-4 p-4 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
            <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="mx-auto w-40 h-40" />
        </div>
        
        <p class="text-gray-600 mt-6">
            After scanning the QR code, click the button below to validate the generated code.
        </p>
        
        <div class="mt-6">
            <a href="verify_2fa.php" 
               class="inline-block bg-blue-500 text-white py-2 px-6 rounded-md shadow-lg hover:bg-blue-600 hover:shadow-xl transition">
               Validate Code
            </a>
        </div>
    </div>
</body>
</html>


