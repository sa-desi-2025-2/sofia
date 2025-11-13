<?php
require_once __DIR__ . '/Conexao.php';

Class Usuario {
    private $id;
    private $conexao;
    private $tipo;
    private $email;
    private $senha;    

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getSenha() { return $this->senha; }
    public function setSenha($senha) { $this->senha = $senha; }
    public function getTipo() { return $this->tipo; }
    public function setTipo($tipo) { $this->tipo = $tipo; }

    public function __construct() {
        $this->conexao = new Conexao();
    }

    public function cadastrar(){
        $this->senha = hash('sha512', $this->senha);
        $consulta = $this->conexao->prepare("INSERT INTO usuarios(email, senha, tipo) VALUES(?,?,?)");
        $consulta->execute([$this->email, $this->senha, $this->tipo]);
        $this->id = $this->conexao->lastInsertId();
        
        return $this->id;
    }
    
    public function listar(){
        $consulta = $this->conexao->prepare("SELECT pk_usuario, email_usuario, eh_adm_usuario FROM usuario");      
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscar(){
        $consulta = $this->conexao->prepare("SELECT pk_usuario, nome, cnpj, email_usuario, telefone, endereco, complemento, cidade, estado, cep, eh_adm_usuario FROM usuario WHERE pk_usuario = ?");      
        $consulta->execute([$this->id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar(){
        try {
            
            if (!empty($this->senha)) {
                $this->senha = hash('sha512', $this->senha);
                $consulta = $this->conexao->prepare("UPDATE usuario SET email_usuario = ?, senha_usuario = ? WHERE pk_usuario = ?");
                $consulta->execute([$this->email, $this->senha, $this->id]);
            } else {
                
                $consulta = $this->conexao->prepare("UPDATE usuario SET email_usuario = ? WHERE pk_usuario = ?");
                $consulta->execute([$this->email, $this->id]);
            }
            return true;
        } catch (PDOException $e) {
            echo "Erro ao atualizar usuário: " . $e->getMessage();
            return false;
        }
    }
    
    public function excluir(){
        try {
            $consulta = $this->conexao->prepare("DELETE FROM usuario WHERE pk_usuario = ?");
            $consulta->execute([$this->id]);
            return true;
        } catch (PDOException $e) {
            echo "Erro ao excluir usuário: " . $e->getMessage();
            return false;
        }
    }
    
    public function buscarPorEmail(): PDOStatement  {
        $consulta = $this->conexao->prepare("SELECT pk_usuario, senha_usuario, eh_adm_usuario FROM usuario WHERE email_usuario = ?");
        $consulta->execute([$this->email]);
        return $consulta;
    }
}