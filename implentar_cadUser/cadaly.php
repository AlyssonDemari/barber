<?php
// ⚠️ Coloque isso no topo, sem espaços ou linhas em branco acima
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Evita erro de session_start() após saída de dados
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'conexãoTeste.php';

// Verifica se o script está sendo executado via navegador (requisição POST)
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
        die("Por favor, preencha todos os campos.");
    }

    // Verifica se o e-mail já existe
    $sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        die("Este e-mail já está cadastrado.");
    }

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o novo usuário
    $sql = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $telefone, $senha_hash);

    if ($stmt->execute()) {
        header("Location: /sucesso_cad.php");
        exit;
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt_verifica->close();
    $stmt->close();
    $conn->close();
}
?>
