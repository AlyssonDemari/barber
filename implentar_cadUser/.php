<?php
include_once 'conexão.php';

$nome = 'Barbeiro 1';
$email = 'admin@barbearia.com';
$senha = password_hash('admin', PASSWORD_DEFAULT); // senha segura

$sql = "INSERT INTO administradores (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha);
$stmt->execute();

echo "Administrador cadastrado com sucesso!";
?>
