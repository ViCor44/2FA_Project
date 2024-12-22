<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Recuperação de Senha</h2>
    <form method="POST" action="forgot_password_process.php">
        <div class="mb-3">
            <label for="username" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="security_answer" class="form-label">Resposta de Segurança</label>
            <input type="text" class="form-control" id="security_answer" name="security_answer" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Recuperar Senha</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
