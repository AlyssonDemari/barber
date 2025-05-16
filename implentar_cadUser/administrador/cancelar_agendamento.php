<?php
session_start();
include_once 'conexão.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginAdm.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Agendamento inválido.";
    exit();
}

$id = intval($_GET['id']);
$erro = '';
$sucesso = '';
$mensagem_whatsapp = '';
$link_whatsapp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo = trim($_POST['motivo']);

    if (empty($motivo)) {
        $erro = "Por favor, preencha o motivo do cancelamento.";
    } else {
        // Busca os dados do agendamento antes de excluir
        $stmt = $conn->prepare("SELECT a.*, u.nome AS cliente, u.telefone, a.servico, a.hora_agendamento, a.data_agendamento 
                                FROM agendamentos a 
                                JOIN usuarios u ON a.usuario_id = u.id 
                                WHERE a.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "Agendamento não encontrado.";
            exit();
        }

        $agendamento = $result->fetch_assoc();
        $stmt->close();

        // Exclui o agendamento
        $stmt = $conn->prepare("DELETE FROM agendamentos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $sucesso = "Agendamento cancelado e removido com sucesso!";

        // Gera link de WhatsApp
        $mensagem = "Olá, infelizmente tive um imprevisto agora .";
        $mensagem .= "Motivo: *{$motivo}";
        $mensagem .= "*Serviço:* {$agendamento['servico']}";
        $mensagem .= "*Data:* {$agendamento['data_agendamento']}";
        $mensagem .= "*Hora:* {$agendamento['hora_agendamento']}";
        $mensagem .= "Gostaria de reagendar para outro horário? ";

        $numero_formatado = '55' . preg_replace('/\D/', '', $agendamento['telefone']);
        $link_whatsapp = "https://wa.me/{$numero_formatado}?text=" . urlencode($mensagem);
    }
}

// Se ainda não buscou os dados
if (!isset($agendamento)) {
    $stmt = $conn->prepare("SELECT a.*, u.nome AS cliente, a.servico, a.hora_agendamento 
                            FROM agendamentos a 
                            JOIN usuarios u ON a.usuario_id = u.id 
                            WHERE a.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Agendamento não encontrado.";
        exit();
    }

    $agendamento = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cancelar Agendamento</title>
    <link rel="stylesheet" href="css/cancelar.css">
</head>
<body>

<div class="container">
    <h2>Cancelar Agendamento</h2>

    <?php if ($sucesso): ?>
        <div class="success"><?php echo $sucesso; ?></div>
        <a href="admdash.php" class="btn">Voltar ao Painel</a>
        <?php if ($link_whatsapp): ?>
            <a href="<?php echo $link_whatsapp; ?>" target="_blank" class="btn whatsapp">Enviar Mensagem pelo WhatsApp</a>
        <?php endif; ?>
    <?php else: ?>
        <p><strong>Título:</strong> Cancelamento</p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($agendamento['cliente']); ?></p>
        <p><strong>Serviço:</strong> <?php echo htmlspecialchars($agendamento['servico']); ?></p>
        <p><strong>Hora:</strong> <?php echo htmlspecialchars($agendamento['hora_agendamento']); ?></p>

        <?php if ($erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="motivo">Motivo do cancelamento:</label><br>
            <textarea name="motivo" id="motivo" rows="4" required></textarea><br>
            <button type="submit" class="btn cancelar">Confirmar Cancelamento</button>
            <a href="admdash.php" class="btn voltar">Voltar</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
