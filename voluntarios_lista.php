<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'ong') {
    header("Location: ../login/login.php?erro=permissao");
    exit;
}

require_once __DIR__ . '/app/classes/Ong.php';
require_once __DIR__ . '/app/classes/Voluntario.php';
require_once __DIR__ . '/app/classes/Conexao.php';

$id_usuario = $_SESSION['id_usuario'];

$ong = new Ong();
$ongData = $ong->buscarPorIdUsuario($id_usuario);
if (!$ongData) {
    die("Erro: ONG não encontrada.");
}

$id_ong = $ongData['id_ong'];

$mostrar_todos = !isset($_GET['todos']) || (isset($_GET['todos']) && $_GET['todos'] == '1');

$con = new Conexao();

$sql_todos = "
    SELECT v.id_voluntario, v.nome, v.email, v.telefone, v.imagem, v.cidade, v.estado
    FROM voluntarios v
    ORDER BY v.nome ASC
";

$sql_ong = "
    SELECT v.id_voluntario, v.nome, v.email, v.telefone, v.imagem, v.cidade, v.estado
    FROM ongs_voluntarios ov
    INNER JOIN voluntarios v ON v.id_voluntario = ov.id_voluntario
    WHERE ov.id_ong = ?
    ORDER BY v.nome ASC
";

if ($mostrar_todos) {
    $stmt = $con->prepare($sql_todos);
    $stmt->execute();
} else {
    $stmt = $con->prepare($sql_ong);
    $stmt->execute([$id_ong]);
}

$voluntarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voluntários Inscritos</title>
    <link rel="stylesheet" href="CSS.css">
</head>
<body>

<header>
    <div class="nav" style="display: flex; justify-content: space-between; align-items: center;">
        
        <div class="nav-esquerda">
            <a href="index.php">Início</a>
            <a href="sobre.html">Sobre</a>
            <a href="contato.html">Contato</a>
        </div>

        <div class="nav-direita">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<div class="container-voluntarios">
    <h1>Gerenciar Voluntários</h1>

    <!-- Filtros -->
    <div class="filtros">
        <form method="GET" action="">
            <button type="submit" name="todos" value="0" 
                    class="btn-filtro <?= !$mostrar_todos ? 'ativo' : '' ?>">
                Voluntários da Minha ONG
            </button>
            <button type="submit" name="todos" value="1" 
                    class="btn-filtro <?= $mostrar_todos ? 'ativo' : '' ?>">
                Todos os Voluntários
            </button>
        </form>
    </div>

    <!-- Resultados -->
    <?php if (count($voluntarios) == 0): ?>
        <p>Nenhum voluntário encontrado.</p>
    <?php else: ?>

        <div class="vol-grid">
            <?php foreach ($voluntarios as $v): 
                $stmt_check = $con->prepare("SELECT 1 FROM ongs_voluntarios WHERE id_ong = ? AND id_voluntario = ?");
                $stmt_check->execute([$id_ong, $v['id_voluntario']]);
                $esta_na_ong = $stmt_check->fetch() !== false;
            ?>
            <div class="vol-card">
                <img src="<?= $v['imagem'] ?: 'assets/sem-foto.jpg' ?>" 
                     alt="<?= $v['nome'] ?>">

                <h3><?= $v['nome'] ?></h3>

                <p><strong>Email:</strong> <?= $v['email'] ?></p>
                <p><strong>Telefone:</strong> <?= $v['telefone'] ?></p>
                <p><strong>Localização:</strong> <?= $v['cidade'] ?> - <?= $v['estado'] ?></p>

                <?php if ($esta_na_ong): ?>
                    <span class="badge-ong">Já está na sua ONG</span>
                <?php endif; ?>

                <div class="acoes">
                    <?php if (!$esta_na_ong): ?>
                        <button class="btn-acao btn-adicionar" 
                                onclick="adicionarVoluntario(<?= $v['id_voluntario'] ?>)">
                            Convidar
                        </button>
                    <?php else: ?>
                        <button class="btn-acao btn-remover" 
                                onclick="removerVoluntario(<?= $v['id_voluntario'] ?>)">
                            Remover da ONG
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
function adicionarVoluntario(idVoluntario) {
    if (confirm('Deseja adicionar este voluntário à sua ONG?')) {
        fetch('app/gateway.php?acao=adicionar_voluntario', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_voluntario=' + idVoluntario
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Voluntário adicionado com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao adicionar voluntário');
        });
    }
}

function removerVoluntario(idVoluntario) {
    if (confirm('Deseja remover este voluntário da sua ONG?')) {
        fetch('app/gateway.php?acao=remover_voluntario', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_voluntario=' + idVoluntario
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Voluntário removido com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover voluntário');
        });
    }
}
</script>

</body>
</html>
