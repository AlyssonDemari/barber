<?php
session_start();
include_once 'conexão.php'; // Inclua a conexão com o banco
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Buscar barbeiros disponíveis no banco
$sql_barbeiros = "SELECT id, nome FROM barbeiros";
$stmt_barbeiros = $conn->prepare($sql_barbeiros);
$stmt_barbeiros->execute();
$barbeiros_result = $stmt_barbeiros->get_result();

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_corte = $_POST['data'];
    $hora_corte = $_POST['hora'];
    $barbeiro_id = $_POST['barbeiro'];
    $servico = $_POST['servico'];
    $observacoes = !empty($_POST['observacoes']) ? $_POST['observacoes'] : null;

    // Verificar se o horário já foi agendado
    $sql_verificar = "SELECT * FROM agendamentos WHERE data_agendamento = ? AND hora_agendamento = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ss", $data_corte, $hora_corte);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        echo "Esse horário já está agendado. Tente outro horário.";
    } else {
        // Inserir o agendamento no banco de dados
        $sql_agendar = "INSERT INTO agendamentos (usuario_id, barbeiro_id, servico, data_agendamento, hora_agendamento, observacoes, status) VALUES (?, ?, ?, ?, ?, ?, 'pendente')";
        $stmt_agendar = $conn->prepare($sql_agendar);
        $stmt_agendar->bind_param("iissss", $usuario_id, $barbeiro_id, $servico, $data_corte, $hora_corte, $observacoes);

        if ($stmt_agendar->execute()) {
            echo "Corte agendado com sucesso!";
        } else {
            echo "Erro ao agendar o corte.";
        }

        $stmt_agendar->close();
    }

    $stmt_verificar->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agendar Corte</title>
    <link rel="stylesheet" href="css/agendamentos.css">
</head>
<body>

<header>
    Agendar Corte
</header>

<div class="container">
    <form action="agendamento.php" method="POST">
        <div class="form-group">
            <label for="data">Data do Corte</label>
            <input type="date" name="data" id="data" required>
        </div>

        <div class="form-group">
            <label for="hora">Hora do Corte</label>
            <input type="time" name="hora" id="hora" required>
        </div>

        <div class="form-group">
            <label for="barbeiro">Escolha o Barbeiro</label>
            <select name="barbeiro" id="barbeiro" required>
                <option value="">Selecione um barbeiro</option>
                <?php while ($row = $barbeiros_result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="servico">Tipo de Serviço</label>
            <select name="servico" id="servico" required>
                <option value="">Selecione...</option>
                <option value="Corte de Cabelo">Corte de Cabelo</option>
                <option value="Barba">Barba</option>
                <option value="Cabelo + Barba">Cabelo + Barba</option>
                <option value="Cabelo + Sobrancelha">Cabelo + Sobrancelha</option>
                <option value="Completo">Completo</option>
            </select>
        </div>

        <div class="form-group">
            <label for="observacoes">Observações (opcional)</label>
            <textarea name="observacoes" id="observacoes" rows="3"></textarea>
        </div>

        <button type="submit" class="btn">Agendar Corte</button>
       

    </form>
    <a href="/implentar_cadUser/usuario.php"><button>Voltar a página d agendamentos</button></a>
</div>

<footer>
    © 2025 Barbearia XYZ
</footer>

</body>
</html>
