<?php
require_once '../config/conexao.php';
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function criar($nome, $user, $email, $senha, $nasc, $genero) {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR username = ?");
        $stmt->execute([$email, $user]);
        if ($stmt->rowCount() > 0) return false;

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, username, email, senha, data_nascimento, genero) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $user, $email, $hash, $nasc, $genero]);
    }

    public function login($email, $senha) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($senha, $user['senha'])) return $user;
        return false;
    }
    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $nome, $user, $fotoNome) {
        
        if ($fotoNome) {
            $sql = "UPDATE usuarios SET nome = ?, username = ?, foto_perfil = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $user, $fotoNome, $id]);
        } else {
            $sql = "UPDATE usuarios SET nome = ?, username = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $user, $id]);
        }
    }
    public function pesquisar($termo, $meuId) {
        $termo = "%$termo%";
        // Traz usuario semm ser eu 
        $sql = "SELECT * FROM usuarios WHERE (nome LIKE ? OR username LIKE ?) AND id != ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termo, $termo, $meuId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function seguir($idSeguidor, $idSeguido) {
        $sql = "INSERT INTO seguidores (id_seguidor, id_seguido) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        try { return $stmt->execute([$idSeguidor, $idSeguido]); } catch(Exception $e) { return false; }
    }
    public function deixarDeSeguir($idSeguidor, $idSeguido) {
        $sql = "DELETE FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$idSeguidor, $idSeguido]);
    }
    public function jaSegue($idSeguidor, $idSeguido) {
        $stmt = $this->pdo->prepare("SELECT id FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?");
        $stmt->execute([$idSeguidor, $idSeguido]);
        return $stmt->rowCount() > 0;
    }
}
?>