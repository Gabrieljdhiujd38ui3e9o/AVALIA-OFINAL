<?php
session_start();
if (!isset($_SESSION['usuario_id'])) header("Location: index.php");

require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$userModel = new Usuario($pdo);
$resultados = [];

if (isset($_GET['busca'])) {
    $resultados = $userModel->pesquisar($_GET['busca'], $_SESSION['usuario_id']);
}

// seguir
if (isset($_GET['acao']) && isset($_GET['id'])) {
    if ($_GET['acao'] == 'seguir') {
        $userModel->seguir($_SESSION['usuario_id'], $_GET['id']);
    } elseif ($_GET['acao'] == 'unfollow') {
        $userModel->deixarDeSeguir($_SESSION['usuario_id'], $_GET['id']);
    }
    // Redireciona 
    header("Location: pesquisa.php?busca=" . $_GET['busca']); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pesquisa</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <a href="feed.php" class="sidebar-icon"><i class="fas fa-home"></i></a>
            <a href="pesquisa.php" class="sidebar-icon"><i class="fas fa-search"></i></a>
            <a href="perfil.php" class="sidebar-icon"><i class="fas fa-user"></i></a>
        </aside>

        <main class="content">
            <div style="margin-bottom: 20px;">
                <a href="feed.php" class="post-btn" style="background-color: #6c757d; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Voltar ao Feed
                </a>
            </div>

            <h2>Pesquisar Usuários</h2>
            <form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" name="busca" class="post-input" style="min-height:40px;" placeholder="Nome ou usuário..." required>
                <button type="submit" class="post-btn">Buscar</button>
            </form>

            <div class="post-feed">
                <?php foreach ($resultados as $user): 
                    $segue = $userModel->jaSegue($_SESSION['usuario_id'], $user['id']);
                ?>
                <div class="post" style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="../img/<?= $user['foto_perfil'] ?: 'default.jpg' ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        <div>
                            <strong><?= $user['nome'] ?></strong><br>
                            <span style="color:gray; font-size:13px;">@<?= $user['username'] ?></span>
                        </div>
                    </div>
                    
                    <?php if ($segue): ?>
                        <a href="pesquisa.php?busca=<?= $_GET['busca'] ?>&acao=unfollow&id=<?= $user['id'] ?>" class="edit-profile-btn" style="text-decoration:none; background-color:#ccc;">Seguindo</a>
                    <?php else: ?>
                        <a href="pesquisa.php?busca=<?= $_GET['busca'] ?>&acao=seguir&id=<?= $user['id'] ?>" class="post-btn" style="text-decoration:none;">Seguir</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <?php if (isset($_GET['busca']) && empty($resultados)): ?>
                    <p>Nenhum usuário encontrado.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>