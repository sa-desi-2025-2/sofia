<?php
require_once __DIR__ . '/Conexao.php';

class Voluntario {

    private $id;
    private $id_usuario;
    private $conexao;

    private $nome;
    private $cpf;
    private $email;
    private $telefone;
    private $endereco;
    private $complemento;
    private $cidade;
    private $estado;
    private $cep;
    private $qualificacoes;
    private $imagem;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getCPF() { return $this->cpf; }
    public function setCPF($cpf) { $this->cpf = $cpf; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function getEndereco() { return $this->endereco; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }

    public function getComplemento() { return $this->complemento; }
    public function setComplemento($complemento) { $this->complemento = $complemento; }

    public function getCidade() { return $this->cidade; }
    public function setCidade($cidade) { $this->cidade = $cidade; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }

    public function getCEP() { return $this->cep; }
    public function setCEP($cep) { $this->cep = $cep; }

    public function getQualificacoes() { return $this->qualificacoes; }
    public function setQualificacoes($qualificacoes) { $this->qualificacoes = $qualificacoes; }

    public function getImagem() { return $this->imagem; }
    public function setImagem($imagem) { $this->imagem = $imagem; }

    public function __construct() {
        $this->conexao = new Conexao();
    }

    public function cadastrar(){
        $sql = "INSERT INTO voluntarios 
                (id_usuario, nome, cpf, email, telefone, endereco, complemento, cidade, estado, cep, qualificacoes, imagem)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $consulta = $this->conexao->prepare($sql);
        $consulta->execute([$this->id_usuario, $this->nome, $this->cpf, $this->email, $this->telefone, $this->endereco, $this->complemento, $this->cidade, $this->estado, $this->cep, $this->qualificacoes, $this->imagem]);
    }

    public function listar(){
        $consulta = $this->conexao->prepare("SELECT * FROM voluntarios");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(){
        $consulta = $this->conexao->prepare("SELECT * FROM voluntarios WHERE id_voluntario = ?");
        $consulta->execute([$this->id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar(){
        $sql = "UPDATE voluntarios SET nome = ?, cpf = ?, email = ?, telefone = ?, endereco = ?, complemento = ?, cidade = ?, estado = ?, cep = ?, qualificacoes = ?, imagem = ?
                WHERE id_voluntario = ?";

        $consulta = $this->conexao->prepare($sql);

        return $consulta->execute([$this->nome, $this->cpf, $this->email, $this->telefone, $this->endereco, $this->complemento, $this->cidade, $this->estado, $this->cep, $this->qualificacoes, $this->imagem, $this->id]);
    }

    public function excluir(){
        $consulta = $this->conexao->prepare("DELETE FROM voluntarios WHERE id_voluntario = ?");
        return $consulta->execute([$this->id]);
    }

    public function buscarPorEmail(){
        $consulta = $this->conexao->prepare("SELECT * FROM voluntarios WHERE email = ?");
        $consulta->execute([$this->email]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function inscreverEmOng($id_ong){
        $consulta = $this->conexao->prepare("
            INSERT IGNORE INTO ongs_voluntarios (id_ong, id_voluntario)
            VALUES (?, ?)
        ");
        return $consulta->execute([$id_ong, $this->id]);
    }

    public function listarOngs(){
        $consulta = $this->conexao->prepare("
            SELECT o.* 
            FROM ongs o
            JOIN ongs_voluntarios ov ON o.id_ong = ov.id_ong
            WHERE ov.id_voluntario = ?
        ");
        $consulta->execute([$this->id]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorIdUsuario() {
        $sql = "SELECT * FROM voluntarios WHERE id_usuario = ?";
        $consulta = $this->conexao->prepare($sql);
        $consulta->execute([$this->id_usuario]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }    
}
?>
