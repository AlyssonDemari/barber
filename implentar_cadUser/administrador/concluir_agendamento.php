<?php
session_start();
include_once 'conexão.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica se o admin está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    echo "Agendamento inválido.";
    exit();
}

$id = intval($_GET['id']);
$sucesso = '';
$erro = '';

// Atualiza o status do agendamento
$stmt = $conn->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
$status = "Concluído";
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    $sucesso = "Agendamento marcado como concluído com sucesso!";
} else {
    $erro = "Erro ao concluir o agendamento.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Concluir Agendamento</title>
    <link rel="stylesheet" href="css/cancelar.css"> <!-- Reutilize o CSS do cancelamento -->
</head>
<body>

<div class="container">
    <h2>Concluir Agendamento</h2>

    <?php if ($sucesso): ?>
        <div class="success"><?php echo $sucesso; ?></div>
    <?php elseif ($erro): ?>
        <div class="error"><?php echo $erro; ?></div>
    <?php endif; ?>

    <a href="admdash.php" class="btn voltar">Voltar ao Painel</a>
</div>

</body>
</html>
