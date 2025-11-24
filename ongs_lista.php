<?php
require_once __DIR__ . '/app/classes/Ong.php';
require_once __DIR__ . '/app/classes/Voluntario.php';
require_once __DIR__ . '/app/classes/Conexao.php';

session_start();

// Verificar se é voluntário
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'voluntario') {
    header("Location: login.php?erro=permissao");
    exit;
}

$volClass = new Voluntario();
$volClass->setIdUsuario($_SESSION['id_usuario']);
$dadosVol = $volClass->buscarPorIdUsuario();
if (!$dadosVol) {
    die("Erro: Voluntário não encontrado.");
}

$id_voluntario_logado = $dadosVol['id_voluntario'];

// Parâmetro para mostrar todas as ONGs ou apenas convites
$mostrar_todas = !isset($_GET['todas']) || (isset($_GET['todas']) && $_GET['todas'] == '1');

$con = new Conexao();

// Query para todas as ONGs
$sql_todas = "
    SELECT o.id_ong, o.nome, o.email, o.telefone, o.endereco, o.descricao, o.imagem
    FROM ongs o
    LEFT JOIN convites_ongs_voluntarios cov ON o.id_ong = cov.id_ong AND cov.id_voluntario = ? AND cov.status = 'pendente'
    LEFT JOIN ongs_voluntarios ov ON o.id_ong = ov.id_ong AND ov.id_voluntario = ?
    ORDER BY o.nome ASC
";

// Query apenas para convites pendentes
$sql_convites = "
    SELECT o.id_ong, o.nome, o.email, o.telefone, o.endereco, o.descricao, o.imagem,
           cov.id_convite, cov.status as status_relacao, cov.data_convite
    FROM convites_ongs_voluntarios cov
    INNER JOIN ongs o ON o.id_ong = cov.id_ong
    WHERE cov.id_voluntario = ? AND cov.status = 'pendente'
    ORDER BY cov.data_convite DESC
";

if ($mostrar_todas) {
    $stmt = $con->prepare($sql_todas);
    $stmt->execute([$id_voluntario_logado, $id_voluntario_logado]);
} else {
    $stmt = $con->prepare($sql_convites);
    $stmt->execute([$id_voluntario_logado]);
}

$ongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONGs e Convites</title>
    <link rel="stylesheet" href="CSS.css">
    <style>
        .filtros {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .btn-filtro {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            font-size: 16px;
        }
        .btn-filtro:hover {
            background: #0056b3;
        }
        .btn-filtro.ativo {
            background: #28a745;
        }
        .ong-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .ong-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }
        .ong-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            margin-top: 10px;
            display: inline-block;
        }
        .status-pendente {
            background: #ffc107;
            color: black;
        }
        .status-inscrito {
            background: #28a745;
            color: white;
        }
        .status-disponivel {
            background: #6c757d;
            color: white;
        }
        .acoes {
            margin-top: 15px;
        }
        .btn-acao {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }
        .btn-aceitar {
            background: #28a745;
            color: white;
        }
        .btn-recusar {
            background: #dc3545;
            color: white;
        }
        .btn-candidatar {
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
        }
        .convite-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            font-size: 0.9em;
        }
    </style>
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

<div class="ong-lista-container">
    <h1 class="ong-lista-titulo">ONGs e Convites</h1>

    <!-- Filtros -->
    <div class="filtros">
        <form method="GET" action="">
            <button type="submit" name="todas" value="0" 
                    class="btn-filtro <?= !$mostrar_todas ? 'ativo' : '' ?>">
                Meus Convites
            </button>
            <button type="submit" name="todas" value="1" 
                    class="btn-filtro <?= $mostrar_todas ? 'ativo' : '' ?>">
                Todas as ONGs
            </button>
        </form>
    </div>

    <!-- Resultados -->
    <?php if (count($ongs) == 0): ?>
        <p style="text-align: center;">
            <?php if ($mostrar_todas): ?>
                Nenhuma ONG encontrada.
            <?php else: ?>
                Nenhum convite pendente no momento.
            <?php endif; ?>
        </p>
    <?php else: ?>

        <div class="ong-grid">
            <?php foreach ($ongs as $ong): ?>
            <?php $ong['status_relacao'] =isset($ong['status_relacao']) ? $ong['status_relacao'] : 'disponivel'; ?>
            <div class="ong-card">
                <img src="<?= $ong['imagem'] ?: 'assets/sem-foto-ong.jpg' ?>" 
                     alt="<?= $ong['nome'] ?>">

                <h3><?= $ong['nome'] ?></h3>

                <p><?= $ong['descricao'] ?></p>

                <!-- Informações de contato -->
                <p><strong>Email:</strong> <?= $ong['email'] ?></p>
                <p><strong>Telefone:</strong> <?= $ong['telefone'] ?></p>
                <p><strong>Endereço:</strong> <?= $ong['endereco'] ?></p>

                <!-- Status e ações -->
                <?php if ($mostrar_todas): ?>
                    <!-- Modo "Todas as ONGs" -->
                    <div class="status-badge status-<?= $ong['status_relacao'] ?>">
                        <?php 
                            switch($ong['status_relacao']) {
                                case 'pendente': echo 'Convite Pendente'; break;
                                case 'inscrito': echo 'Já Inscrito'; break;
                                case 'disponivel': echo 'Disponível'; break;
                                default: echo $ong['status_relacao'];
                            }
                        ?>
                    </div>

                    <div class="acoes">
                        <?php if ($ong['status_relacao'] == 'pendente'): ?>
                            <button class="btn-acao btn-aceitar" 
                                    onclick="responderConvite(<?= $ong['id_ong'] ?>, 'aceito')">
                                Aceitar
                            </button>
                            <button class="btn-acao btn-recusar" 
                                    onclick="responderConvite(<?= $ong['id_ong'] ?>, 'recusado')">
                                Recusar
                            </button>
                        <?php elseif ($ong['status_relacao'] == 'disponivel'): ?>
                            <a href="app/gateway.php?acao=candidatar&id_ong=<?= $ong['id_ong'] ?>" 
                               class="btn-candidatar">
                                Candidatar-se
                            </a>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <!-- Modo "Meus Convites" (apenas convites pendentes) -->
                    <div class="convite-info">
                        <strong>Convite recebido em:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($ong['data_convite'])) ?>
                    </div>

                    <div class="status-badge status-pendente">
                        Convite Pendente
                    </div>

                    <div class="acoes">
                        <button class="btn-acao btn-aceitar" 
                                onclick="responderConvite(<?= $ong['id_ong'] ?>, 'aceito')">
                            Aceitar Convite
                        </button>
                        <button class="btn-acao btn-recusar" 
                                onclick="responderConvite(<?= $ong['id_ong'] ?>, 'recusado')">
                            Recusar Convite
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
function responderConvite(idOng, acao) {
    const mensagem = acao === 'aceito' 
        ? 'Deseja aceitar o convite desta ONG?' 
        : 'Deseja recusar o convite desta ONG?';

    if (confirm(mensagem)) {
        fetch('app/gateway.php?acao=responder_convite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_ong=' + idOng + '&resposta=' + acao
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Convite ' + (acao === 'aceito' ? 'aceito' : 'recusado') + ' com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar convite');
        });
    }
}
</script>

</body>
</html>