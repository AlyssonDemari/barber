<?php
include_once 'conexão.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nome, $senha_hash);
            $stmt->fetch();

            if (password_verify($senha, $senha_hash)) {
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nome'] = $nome;
                header("Location: usuario.php");
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <form action="login.php" method="POST">
        <h2><i class="fas fa-sign-in-alt"></i> Login do Usuário</h2>

        <?php if (!empty($erro)) : ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>

        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
        <input type="email" name="email" required>

        <label for="senha"><i class="fas fa-lock"></i> Senha:</label>
        <input type="password" name="senha" required>

        <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
    </form>
</body>
</html>
