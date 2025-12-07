<?php
session_start();
if (!isset($_SESSION['usuario_id'])) header("Location: index.php");

require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$userModel = new Usuario($pdo);
$msg = "";
$dadosUser = $userModel->buscarPorId($_SESSION['usuario_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $user = $_POST['usuario'];
    $fotoNome = $dadosUser['foto_perfil'];

    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novoNome = "perfil_" . $_SESSION['usuario_id'] . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../img/" . $novoNome);
        $fotoNome = $novoNome;
    }

    if ($userModel->atualizar($_SESSION['usuario_id'], $nome, $user, $fotoNome)) {
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_user'] = $user;
        $_SESSION['usuario_foto'] = $fotoNome;
        $msg = "Perfil atualizado!";
        $dadosUser = $userModel->buscarPorId($_SESSION['usuario_id']); 
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
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

            <h2>Editar Perfil</h2>
            <?php if($msg): ?><p style="color:green;"><?= $msg ?></p><?php endif; ?>
            
            <form class="form-cadastro" method="POST" enctype="multipart/form-data">
                <div style="text-align:center; margin-bottom:15px;">
                    <img src="../img/<?= $dadosUser['foto_perfil'] ?: 'default.jpg' ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover;">
                </div>

                <label>Alterar Foto</label>
                <input type="file" name="foto">

                <label>Nome</label>
                <input type="text" name="nome" value="<?= $dadosUser['nome'] ?>" required>

                <label>Usuário </label>
                <input type="text" name="usuario" value="<?= $dadosUser['username'] ?>" required>

                <button type="submit" class="post-btn">Salvar Alterações</button>
            </form>
        </main>
    </div>
</body>
</html>