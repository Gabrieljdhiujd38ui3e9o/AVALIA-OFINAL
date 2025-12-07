<?php
session_start();
require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $userModel = new Usuario($pdo);
    $usuario = $userModel->login($email, $senha);

    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_user'] = $usuario['username'];
        $_SESSION['usuario_foto'] = $usuario['foto_perfil'];
        header("Location: feed.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Rede Social</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="container" style="justify-content:center; align-items:center; height:100vh;">
        <main class="form-cadastro">
            <h2 style="text-align:center;">Bem-vindo a rede social</h2>
            <?php if($erro): ?><p style="color:red; text-align:center;"><?= $erro ?></p><?php endif; ?>
            
            <form method="POST">
                <label>E-mail</label>
                <input type="email" name="email" required>
                
                <label>Senha</label>
                <input type="password" name="senha" required>
                
                <button type="submit" class="post-btn" style="width:100%;">Entrar</button>
            </form>
            <p style="text-align:center; margin-top:10px;">
                 <a href="cadastro.php">Criar conta</a>
            </p>
        </main>
    </div>
</body>
</html>