<?php
require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioModel = new Usuario($pdo);

    $nome = $_POST['nome'];
    $user = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];
    $nasc = $_POST['nascimento'];
    $genero = $_POST['genero'];

    if ($senha !== $confirmar) {
        $erro = "As senhas não coincidem!";
    } elseif (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $erro = "A senha deve ter 6 caracteres, 1 maiúscula e 1 número.";
    } else {
        if ($usuarioModel->criar($nome, $user, $email, $senha, $nasc, $genero)) {
            header("Location: index.php");
            exit;
        } else {
            $erro = "E-mail ou Usuário já cadastrados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro </title>

    
    <link rel="stylesheet" href="../css/estilo.css">

    
</head>

<body>

    <div class="card-cadastro">
        <h2>Cria  Conta</h2>
       

        <?php if($erro): ?>
            <p style="color: red; margin-bottom: 10px;">
                <?php echo $erro; ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <label>Nome completo</label>
            <input type="text" name="nome" required value="<?= isset($_POST['nome']) ? $_POST['nome'] : '' ?>">

            <label>Usuário </label>
            <input type="text" name="usuario" required value="<?= isset($_POST['usuario']) ? $_POST['usuario'] : '' ?>">

            <label>E-mail</label>
            <input type="email" name="email" required value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">

            <label>Data de Nascimento</label>
            <input type="date" name="nascimento" required>

            <label>Gênero</label>
            <select name="genero">
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <label>Confirmar Senha</label>
            <input type="password" name="confirmar" required>

            <button type="submit" class="btn-submit">Cadastrar</button>
        </form>

        <a class="link-voltar" href="index.php">Voltar</a>

    </div>

</body>
</html>
