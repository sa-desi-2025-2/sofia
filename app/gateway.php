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

    header("Location: ../ongs_lista.php");
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

    header("Location: ../ongs_lista.php");

} else if ($acao == "login") {
    $usuario = new Usuario();

    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']);

    $login = $usuario->login();
    if ($login) {
        $_SESSION['id_usuario'] = $login['id_usuario'];
        $_SESSION['email'] = $login['email'];
        $_SESSION['tipo'] = $login['tipo'];

        header("Location: ../index.php");
    } else {
        header("Location: ../login/login.php?erro=Email ou senha incorretos");
    }
} else if ($acao == "candidatar") {
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'voluntario') {
        header("Location: ../login/login.php?erro=permissao");
        exit;
    }

    $id_ong = $_GET['id_ong'];

    $voluntario = new Voluntario();
    $voluntario->setEmail($_SESSION['email']);
    $dados = $voluntario->buscarPorEmail();

    if (!$dados) {
        header("Location: ../ongs_lista.php?erro=vol_nao_encontrado");
        exit;
    }

    $voluntario->setId($dados['id_voluntario']);

    $voluntario->inscreverEmOng($id_ong);

    header("Location: ../ongs_lista.php?sucesso=1");
    exit;
}
elseif ($acao == 'convidar_voluntario') {
    if ($_SESSION['tipo'] != 'ong') {
        echo json_encode(['success' => false, 'message' => 'Permissão negada']);
        exit;
    }
    
    $id_voluntario = $_POST['id_voluntario'];
    
    // Buscar ID da ONG do usuário logado
    $ong = new Ong();
    $ongData = $ong->buscarPorIdUsuario($_SESSION['id_usuario']);
    if (!$ongData) {
        echo json_encode(['success' => false, 'message' => 'ONG não encontrada']);
        exit;
    }
    
    $id_ong = $ongData['id_ong'];
    
    // Verificar se já existe convite pendente
    $con = new Conexao();
    $stmt = $con->prepare("SELECT id_convite FROM convites_ongs_voluntarios WHERE id_ong = ? AND id_voluntario = ? AND status = 'pendente'");
    $stmt->execute([$id_ong, $id_voluntario]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Já existe um convite pendente para este voluntário']);
        exit;
    }
    
    // Verificar se já está na ONG
    $stmt = $con->prepare("SELECT 1 FROM ongs_voluntarios WHERE id_ong = ? AND id_voluntario = ?");
    $stmt->execute([$id_ong, $id_voluntario]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Voluntário já está na sua ONG']);
        exit;
    }
    
    // Criar convite
    $stmt = $con->prepare("INSERT INTO convites_ongs_voluntarios (id_ong, id_voluntario, status) VALUES (?, ?, 'pendente')");
    
    if ($stmt->execute([$id_ong, $id_voluntario])) {
        echo json_encode(['success' => true, 'message' => 'Convite enviado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar convite']);
    }

} elseif ($acao == 'cancelar_convite') {
    if ($_SESSION['tipo'] != 'ong') {
        echo json_encode(['success' => false, 'message' => 'Permissão negada']);
        exit;
    }
    
    $id_voluntario = $_POST['id_voluntario'];
    
    // Buscar ID da ONG do usuário logado
    $ong = new Ong();
    $ongData = $ong->buscarPorIdUsuario($_SESSION['id_usuario']);
    if (!$ongData) {
        echo json_encode(['success' => false, 'message' => 'ONG não encontrada']);
        exit;
    }
    
    $id_ong = $ongData['id_ong'];
    
    // Cancelar convite
    $con = new Conexao();
    $stmt = $con->prepare("DELETE FROM convites_ongs_voluntarios WHERE id_ong = ? AND id_voluntario = ? AND status = 'pendente'");
    
    if ($stmt->execute([$id_ong, $id_voluntario])) {
        echo json_encode(['success' => true, 'message' => 'Convite cancelado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar convite']);
    }

} elseif ($acao == 'responder_convite') {
    if ($_SESSION['tipo'] != 'voluntario') {
        echo json_encode(['success' => false, 'message' => 'Permissão negada']);
        exit;
    }
    
    $id_ong = $_POST['id_ong'];
    $resposta = $_POST['resposta']; // 'aceito' ou 'recusado'
    
    // Buscar ID do voluntário logado
    $voluntario = new Voluntario();
    $voluntario->setIdUsuario($_SESSION['id_usuario']);
    $dadosVol = $voluntario->buscarPorIdUsuario();
    if (!$dadosVol) {
        echo json_encode(['success' => false, 'message' => 'Voluntário não encontrado']);
        exit;
    }
    
    $id_voluntario = $dadosVol['id_voluntario'];
    
    $con = new Conexao();
    
    if ($resposta == 'aceito') {
        // Atualizar status do convite
        $stmt = $con->prepare("UPDATE convites_ongs_voluntarios SET status = 'aceito', data_resposta = NOW() WHERE id_ong = ? AND id_voluntario = ? AND status = 'pendente'");
        $stmt->execute([$id_ong, $id_voluntario]);
        
        // Adicionar à ONG
        $stmt = $con->prepare("INSERT IGNORE INTO ongs_voluntarios (id_ong, id_voluntario) VALUES (?, ?)");
        $stmt->execute([$id_ong, $id_voluntario]);
        
        echo json_encode(['success' => true, 'message' => 'Convite aceito com sucesso']);
    } else {
        // Recusar convite
        $stmt = $con->prepare("UPDATE convites_ongs_voluntarios SET status = 'recusado', data_resposta = NOW() WHERE id_ong = ? AND id_voluntario = ? AND status = 'pendente'");
        
        if ($stmt->execute([$id_ong, $id_voluntario])) {
            echo json_encode(['success' => true, 'message' => 'Convite recusado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao recusar convite']);
        }
    }
}
