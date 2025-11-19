<?php

require_once __DIR__ . '/classes/Conexao.php';
require_once __DIR__ . '/classes/Usuario.php';
require_once __DIR__ . '/classes/Voluntario.php';
require_once __DIR__ . '/classes/Ong.php';

session_start();
$acao = $_GET['acao'];

if ($acao == "conectar") {
    $conexao = new Conexao();
} else if ($acao == "cadastrar_voluntario") {
    $usuario = new Usuario();

    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);
    $usuario->setTipo('voluntario');

    $id_usuario = $usuario->cadastrar();

    $diretorio = '../uploads/voluntarios/';
    $nomeArquivo = $id_usuario . '.png';
    $caminhoCompleto = $diretorio . $nomeArquivo;

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    if (!empty($_FILES['imagem']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto);
    }

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
    $voluntario->setImagem('uploads/voluntarios/' . $id_usuario . '.png');
    $voluntario->cadastrar();

    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['tipo'] = 'voluntario';

    header("Location: ../home.php");
} else if ($acao == "conectar") {
    $conexao = new Conexao();
} else if ($acao == "cadastrar_ong") {
    $usuario = new Usuario();

    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);
    $usuario->setTipo('ong');

    $id_usuario = $usuario->cadastrar();

    $diretorio = '../uploads/ongs/';
    $nomeArquivo = $id_usuario . '.png';
    $caminhoCompleto = $diretorio . $nomeArquivo;

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    if (!empty($_FILES['imagem']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto);
    }

    $ong = new ONG();
    $ong->setIdUsuario($id_usuario);
    $ong->setNome($_POST['nome']);
    $ong->setCNPJ($_POST['cnpj']);
    $ong->setEmail($_POST['email']);
    $ong->setTelefone($_POST['telefone']);
    $ong->setEndereco($_POST['endereco']);
    $ong->setCEP($_POST['cep']);
    $ong->setDescricao($_POST['descricao']);
    $ong->setImagem('uploads/ongs/' . $nomeArquivo);
    $ong->cadastrar();

    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['tipo'] = 'ong';

    header("Location: ../home.php");

} else if ($acao == "login") {
    $usuario = new Usuario();

    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);

    $login = $usuario->login();
    if ($login) {
        $_SESSION['id_usuario'] = $login['id_usuario'];
        $_SESSION['email'] = $login['email'];
        $_SESSION['tipo'] = $login['tipo'];

        header("Location: ../home.php");
    } else {
        header("Location: login.php?erro=1");
    }
}
