<?php

require_once __DIR__ . '/classes/Conexao.php';
require_once __DIR__ . '/classes/Usuario.php';
require_once __DIR__ . '/classes/Voluntario.php';
require_once __DIR__ . '/classes/Ong.php';

session_start();
$acao = $_GET['acao'];

if($acao == "conectar"){
    $conexao = new Conexao();
}else if($acao == "cadastrar_voluntario"){
    $usuario = new Usuario();

    // $usuario->setNome($_POST['nome']);
    // $usuario->setCNPJ($_POST['cpf']);
    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);
    $usuario->setTipo('voluntario');
    // $usuario->setTelefone($_POST['telefone']);
    // $usuario->setEndereco($_POST['endereco']);
    // $usuario->setComplemento($_POST['complemento']);
    // $usuario->setCidade($_POST['cidade']);
    // $usuario->setEstado($_POST['estado']);
    // $usuario->setCEP($_POST['cep']);

    $id_usuario = $usuario->cadastrar();

    $voluntario = new Voluntario();
    $voluntario->setIdUsuario($id_usuario);
    $voluntario->setNome($_POST['nome']);
    $voluntario->setCPF($_POST['cpf']);
    $voluntario->setEmail($_POST['email']);
    $voluntario->setTelefone($_POST['telefone']);
    $voluntario->setEndereco($_POST['endereco']);
    $voluntario->setComplemento($_POST['complemento']);
    $voluntario->setCidade($_POST['cidade']);
    $voluntario->setEstado($_POST['estado']);
    $voluntario->setCEP($_POST['cep']);
    $voluntario->setQualificacoes($_POST['qualificacoes']);
    $voluntario->cadastrar();
}


if($acao == "conectar"){
    $conexao = new Conexao();
} else if($acao == "cadastrar_ong"){
    $usuario = new Usuario();

    // $usuario->setNome($_POST['nome']);
    // $usuario->setCNPJ($_POST['cnpj']);
    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);
    $usuario->setTipo('ong');
    // $usuario->setTelefone($_POST['telefone']);
    // $usuario->setEndereco($_POST['endereco']);
    // $usuario->setCEP($_POST['cep']);

    $id_usuario = $usuario->cadastrar();

    $voluntario = new ONG();
    $voluntario->setIdUsuario($id_usuario);
    $voluntario->setNome($_POST['nome']);
    $voluntario->setCNPJ($_POST['cnpj']);
    $voluntario->setEmail($_POST['email']);
    $voluntario->setTelefone($_POST['telefone']);
    $voluntario->setEndereco($_POST['endereco']);
    $voluntario->setCEP($_POST['cep']);
    $voluntario->setDescricao($_POST['descricao']);
    $voluntario->cadastrar();
}
