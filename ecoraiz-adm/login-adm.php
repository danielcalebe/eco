
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Eco Raiz</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/login-adm.css">
</head>

<body>
  <div class="container login-container d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
      <div class="col-md-4 text-center d-flex align-items-center justify-content-center flex-column">
        <div class="logo mb-4">
          <img src="./img/logo-eco-raiz.png" alt="Logo Eco Raiz">
        </div>
      </div>

      <div class="col-md-4">
        <div class="login-box">
          <div class="mb-4">
            <p class="title">Entre com seus dados corporativos</p>
          </div>

          <!-- Formulário de Login -->
          <form action="processar_login.php" method="POST">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control rounded-2" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha" class="form-label mt-3">Senha</label>
            <input type="password" class="form-control rounded-2" id="senha" name="senha" placeholder="Digite sua senha" required>

            <div class="mt-5 d-flex justify-content-end">
              <button type="submit" class="btn btn-custom">Entrar</button>
            </div>
          </form>

          <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger mt-3 text-center">Usuário ou senha incorretos!</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
