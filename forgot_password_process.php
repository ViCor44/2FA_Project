<?php
include 'config.php';
include 'classes/Database.php';
include 'classes/User.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $security_answer = $_POST['security_answer'];

    // Buscar o usuário no banco
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data && password_verify($security_answer, $user_data['security_answer'])) {
        // Se a resposta de segurança estiver correta
        echo "<div class='alert alert-success'>Resposta correta! Você pode agora redefinir sua senha.</div>";
        // Redirecionar para a página de redefinição de senha
        header("Location: reset_password.php?user_id=" . $user_data['id']);
        exit();
    } else {
        echo "<div class='alert alert-danger'>Usuário ou resposta de segurança incorretos.</div>";
    }
}
?>
