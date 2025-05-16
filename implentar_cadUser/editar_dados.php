<?php
include_once 'conexão.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Inclua a conexão com o banco

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Buscar os dados do usuário no banco
$sql = "SELECT nome, email, telefone, foto FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nome, $email, $telefone, $foto);
$stmt->fetch();
$stmt->close();

// Atualizar os dados do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_editado = $_POST['nome'];
    $email_editado = $_POST['email'];
    $telefone_editado = $_POST['telefone'];

    // Verificar se o usuário enviou uma nova foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // Definir o diretório de upload
        $uploads_dir = 'uploads/';
        $tmp_name = $_FILES['foto']['tmp_name'];
        $name = basename($_FILES['foto']['name']);
        $foto_path = $uploads_dir . $name;

        // Mover o arquivo para o diretório de uploads
        if (move_uploaded_file($tmp_name, $foto_path)) {
            $foto = $foto_path; // Atualizar a foto no banco
        }
    }

    // Atualizar no banco de dados
    $sql_update = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, foto = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $nome_editado, $email_editado, $telefone_editado, $foto, $usuario_id);

    if ($stmt_update->execute()) {
        echo "Dados atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados.";
    }

    $stmt_update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Dados</title>
    <link rel="stylesheet" href="css/editar_dados.css">
</head>
<body>

<header>
    Editar Dados
</header>

<div class="container">
    <!-- Sidebar com opções do usuário -->
    <div class="sidebar">
        <h3>Minhas Opções</h3>
        <ul>
            <li><a href="editar_dados.php">Editar Perfil</a></li>
            <li><a href="agendar_corte.php">Agendar Corte</a></li>
            <li><a href="usuario.php">Ver Meus Agendamentos</a></li>
            <li><a href="enviar_mensagem.php">Enviar Mensagem</a></li>
        </ul>
    </div>

    <!-- Formulário de Edição -->
    <div class="content">
        <h1>Editar Dados</h1>
        <form action="editar_dados.php" method="POST" enctype="multipart/form-data">
            <!-- Foto de perfil -->
            <div class="profile-img-container">
                <img src="<?php echo $foto ? $foto : 'default-profile.jpg'; ?>" alt="Foto de perfil">
                <a href="#" class="btn-upload">Alterar Foto</a>
                <input type="file" name="foto" accept="image/*">
            </div>

            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($telefone); ?>" required>
            </div>

            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</div>

<footer>
    © 2025 Barbearia XYZ
</footer>

</body>
</html>
