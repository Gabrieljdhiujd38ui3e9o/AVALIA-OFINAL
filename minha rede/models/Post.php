<?php
require_once '../config/conexao.php';
class Post {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function criar($idUsuario, $conteudo) {
        
        $sql = "INSERT INTO posts (usuario_id, conteudo) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$idUsuario, $conteudo]);
    }
    public function listarFeed($idUsuario) {
        
        $sql = "SELECT p.*, u.nome, u.username, u.foto_perfil 
                FROM posts p 
                JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.usuario_id = ? 
                OR p.usuario_id IN (SELECT id_seguido FROM seguidores WHERE id_seguidor = ?) 
                ORDER BY p.data_postagem DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario, $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function curtir($idPost) {
        $sql = "UPDATE posts SET curtidas = curtidas + 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$idPost]);
    }
}
?>