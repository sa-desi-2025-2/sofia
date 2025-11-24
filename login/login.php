<?php
$mensagem_erro = isset($_GET['erro']) ? $_GET['erro'] : '';
?>

<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Três Patas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS.css">
</head>
<body>

  <div class="tela_fundo overlay d-flex align-items-center justify-content-center text-center">
    <div class="caixa_login bg-light p-4 rounded shadow-lg">
      <h2 class="fw-bold mb-4">Entrar</h2>

      <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($mensagem_erro); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form action="../app/gateway.php?acao=login" method="POST">
        <div class="mb-3 text-start">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Seu email">
        </div>

        <div class="mb-3 text-start">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha">
        </div>

        <button type="submit" class="btn btn-dark w-100">Entrar</button>

        <div class="text-center mt-3 small">
          Ainda não tem conta?
          <a href="../cadastro/cadastro.html" class="text-decoration-none fw-semibold">Cadastre-se</a>
        </div>
      </form>
    </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>