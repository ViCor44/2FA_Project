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
    // Verificar se as senhas coincidem
    elseif ($password !== $confirm_password) {
        $error_message = "As senhas não coincidem.";
    } 
    // Validar e-mail
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "O e-mail informado não é válido.";
    } 
    // Verificar se o nome de usuário já existe
    else {
        $user = new User($db);
        if ($user->getByUsername($username)) {
            $error_message = "Nome de usuário já existe. Tente outro.";
        } 
        // Criptografar a senha
        else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Gerar a chave secreta para o Google Authenticator
            
            $googleAuthenticator = new Google2FA();
            $secret = $googleAuthenticator->generateSecretKey();
           

            // Registrar o usuário no banco de dados
            if ($user->register($username, $email, $hashed_password, $security_question, $security_answer, $enable_2fa, $secret)) {
                // Enviar para a página de configuração do 2FA, se ativado
                if ($enable_2fa) {
                    $_SESSION['user_id'] = $db->lastInsertId(); // Salvar o ID do usuário na sessão
                    header('Location: two_factor_setup.php');
                    exit();
                } else {
                    // Redirecionar para o login se 2FA não estiver ativado
                    header('Location: login.php');
                    exit();
                }
            } else {
                $error_message = "Erro ao registrar o usuário. Tente novamente.";
            }
        }
    }
}
?>
