<?php
session_start();
include_once 'conexão.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Pega o ID da sessão
$usuario_id = $_SESSION['usuario_id'];

// Busca o NOME do usuário pelo ID
$sql = "SELECT nome FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $nome = $usuario['nome'];
} else {
    $nome = "Usuário";
}

$stmt->close();

// Buscar agendamentos do usuário
$sql_agendamentos = "SELECT id, servico, data_agendamento, hora_agendamento, status 
                     FROM agendamentos 
                     WHERE usuario_id = ? 
                     ORDER BY data_agendamento DESC, hora_agendamento DESC";
$stmt_agendamentos = $conn->prepare($sql_agendamentos);
$stmt_agendamentos->bind_param("i", $usuario_id);
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página do Usuário</title>
    <link rel="stylesheet" href="css/usuario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    Bem-vindo, <?php echo htmlspecialchars($nome); ?> 👋
</header>

<div class="container">
    <!-- Sidebar com opções do usuário -->
    <div class="sidebar">
        <h3>Minhas Opções</h3>
        <ul>
            <li><a href="editar_dados.php">Editar Perfil</a></li>
            <li><a href="agendamento.php">Agendar Corte</a></li>
            <li><a href="enviar_mensagem.php">Enviar Mensagem</a></li>
            <li><a href="/logout.php">Sair</a></li>
        </ul>
    </div>

    <!-- Conteúdo principal -->
    <div class="content">
        <h1>Meus Agendamentos</h1>
        <div class="card-container">
            <?php
            if ($result_agendamentos->num_rows > 0) {
                while ($agendamento = $result_agendamentos->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<i class="fas fa-calendar-alt icon"></i>';
                    echo '<h2>' . htmlspecialchars($agendamento['servico']) . '</h2>';
                    echo '<p>Data: ' . date("d/m/Y", strtotime($agendamento['data_agendamento'])) . '</p>';
                    echo '<p>Hora: ' . htmlspecialchars($agendamento['hora_agendamento']) . '</p>';
                    echo '<p>Status: ' . ucfirst(htmlspecialchars($agendamento['status'])) . '</p>';
                    echo '<a href="detalhes_agendamento.php?id=' . $agendamento['id'] . '" class="btn">Detalhes</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>Você ainda não fez nenhum agendamento.</p>';
            }

            $stmt_agendamentos->close();
            ?>
        </div>
    </div>
</div>

<footer>
    © 2025 Barbearia XYZ
</footer>

</body>
</html>

<?php
$conn->close(); // Fechar a conexão com o banco após todas as operações
?>
