<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once '../config/conexao.php';
require_once '../models/Post.php';

$postModel = new Post($pdo);

// Postar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
    $conteudo = trim($_POST['conteudo']);
    if (!empty($conteudo)) {
        $postModel->criar($_SESSION['usuario_id'], $conteudo);
        header("Location: feed.php"); // limpar o formulário
        exit;
    }
}

// Curtir
if (isset($_GET['curtir'])) {
    $postModel->curtir($_GET['curtir']);
    header("Location: feed.php");
    exit;
}

$posts = $postModel->listarFeed($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Feed </title>
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head> 
<body>
    <div class="container">
        <aside class="sidebar">
            <a href="feed.php" class="sidebar-icon" title="Feed">
                <i class="fas fa-home"></i>
            </a>
            
            <a href="pesquisa.php" class="sidebar-icon" title="Pesquisar">
                <i class="fas fa-search"></i>
            </a>
            
            <a href="perfil.php" class="sidebar-icon" title="Meu Perfil">
                <i class="fas fa-user"></i>
            </a>
            
            <a href="logout.php" class="sidebar-icon" style="color:red;" title="Sair">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </aside>

        <main class="content">
            <div class="user-profile-header">
                <a href="perfil.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px; width: 100%;">
                    <div class="user-info">
                        <img src="../img/<?= $_SESSION['usuario_foto'] ?: 'default.jpg' ?>" class="profile-pic">
                        <div class="user-details">
                            <span class="user-name"><?= $_SESSION['usuario_nome'] ?></span>
                            <span class="user-handle">@<?= $_SESSION['usuario_user'] ?></span>
                        </div>
                    </div>
                </a>
                <a href="perfil.php" class="edit-profile-btn">Editar</a>
            </div>

            <form class="create-post-section" method="POST">
                <textarea class="post-input" name="conteudo" placeholder="Escrever seu post" required></textarea>
                <button type="submit" class="post-btn">Postar</button>
            </form>

            <div class="post-feed">
                <?php if (empty($posts)): ?>
                    <p style="text-align:center; color:gray; margin-top:20px;">Nenhum post ainda. Siga alguém ou faça o primeiro post!</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <img src="../img/<?= $post['foto_perfil'] ?: 'default.jpg' ?>" class="post-author-pic">
                            <div class="post-author-info">
                                <span class="post-author-name"><?= $post['nome'] ?></span>
                                <span class="post-author-handle">@<?= $post['username'] ?></span>
                            </div>
                        </div>
                        <div class="post-body">
                            <p><?= htmlspecialchars($post['conteudo']) ?></p>
                        </div>
                        <div class="post-actions">
                            <a href="feed.php?curtir=<?= $post['id'] ?>" class="action-item" style="text-decoration:none; color:gray;">
                                <i class="fas fa-heart <?= $post['curtidas'] > 0 ? 'red-heart' : '' ?>"></i>
                                <span><?= $post['curtidas'] ?> curtidas</span>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>
