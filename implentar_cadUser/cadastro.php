<?php

include_once 'conexão.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
        die("Por favor, preencha todos os campos.");
    }

    $sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        die("Este e-mail já está cadastrado.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $telefone, $senha_hash);

    if ($stmt->execute()) {
  header("Location: /sucesso_cad.php");
exit;

} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

    

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="css/cadastro.css">
 <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<form  method="POST">
    <h2>Cadastro de Usuário</h2>

    <label for="nome">
        <i class="fas fa-user"></i> Nome:
    </label>
    <input type="text" name="nome" required>

    <label for="email">
        <i class="fas fa-envelope"></i> Email:
    </label>
    <input type="email" name="email" required>

    <label for="telefone">
        <i class="fas fa-phone"></i> Telefone:
    </label>
    <input type="tel" name="telefone" required>

    <label for="senha">
        <i class="fas fa-lock"></i> Senha:
    </label>
    <input type="password" name="senha" required>

    <button type="submit">
        <i class="fas fa-user-plus"></i> Cadastrar
    </button>
    <a href="/implentar_cadUser/login.php" class="login-link">Fazer login -></a>
</form>

</body>
</html>