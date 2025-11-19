<?php 
require_once __DIR__ . '/Conexao.php';

Class Ong {
    private $id;
    private $id_usuario;
    private $conexao;
    private $nome;
    private $email;
    private $telefone;
    private $cnpj;
    private $endereco;
    private $cep;
    private $descricao;
    private $imagem;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getCNPJ() { return $this->cnpj; }
    public function setCNPJ($cnpj) { $this->cnpj = $cnpj; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function getEndereco() { return $this->endereco; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }
    public function getCEP() { return $this->cep; }
    public function setCEP($cep) { $this->cep = $cep; }
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function getImagem() { return $this->imagem; }
    public function setImagem($imagem) { $this->imagem = $imagem; }

    public function __construct() {
        $this->conexao = new Conexao();
    }

    
    public function cadastrar(){
        $consulta = $this->conexao->prepare("INSERT INTO ongs(id_usuario, nome, cnpj, email, telefone, endereco, cep, descricao, imagem) VALUES(?,?,?,?,?,?,?,?,?)");
        $consulta->execute([$this->id_usuario, $this->nome, $this->cnpj, $this->email, $this->telefone, $this->endereco, $this->cep, $this->descricao, $this->imagem]);
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

?>
