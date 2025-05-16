<?php
session_start();
include_once 'conexão.php';

// Verifica se o admin está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Consulta os agendamentos com nome do cliente, barbeiro e telefone
$sql = "SELECT 
            a.id, u.nome AS cliente, u.telefone, b.nome AS barbeiro, 
            a.data_agendamento, a.hora_agendamento, a.servico, a.status
        FROM agendamentos a
        JOIN usuarios u ON a.usuario_id = u.id
        LEFT JOIN barbeiros b ON a.barbeiro_id = b.id
        ORDER BY a.barbeiro_id, a.data_agendamento, a.hora_agendamento";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="css/painel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <h1>Painel do Administrador</h1>
    <a href="/implentar_cadUser/administrador/logout.php" class="logout-btn">Sair</a>
</header>

<div class="container">
    <h2>Agendamentos por Barbeiro</h2>

    <div class="card-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3>Cliente: <?php echo htmlspecialchars($row['cliente']); ?></h3>
                    <p><strong>Barbeiro:</strong> <?php echo htmlspecialchars($row['barbeiro']); ?></p>
                    <p><strong>Serviço:</strong> <?php echo htmlspecialchars($row['servico']); ?></p>
                    <p><strong>Data:</strong> <?php echo htmlspecialchars($row['data_agendamento']); ?></p>
                    <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_agendamento']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>

                    <div class="btn-group">
                         <a href="cancelar_agendamento.php?id=<?php echo $row['id']; ?>" class="btn cancelar">Cancelar</a>
                         <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $row['telefone']); ?>" target="_blank" class="btn mensagem">Enviar Mensagem</a>
                         <a href="concluir_agendamento.php?id=<?php echo $row['id']; ?>" class="btn concluir">Concluir</a>
</div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum agendamento encontrado.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
