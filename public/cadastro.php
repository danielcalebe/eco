<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro - EcoRaiz</title>
    <?php include '../elements/head.php'; ?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/cadastro.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <?php include '../elements/navbar.php'; ?>

<section class="background-radial-gradient overflow-hidden">
  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center mb-5">
      <div class="col-lg-6 mb-6 mb-lg-0 text-left-adjust" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: #f7f5ec">
          Cadastre-se agora na<br><span style="color: #78ac4d">EcoRaiz</span>
        </h1>
        <p class="mb-4 opacity-70" style="color: #f7f5ec">     A EcoRaiz é uma empresa comprometida com a sustentabilidade.
            Nossa plataforma permite que pessoas físicas e jurídicas realizem cadastro,
            doem matéria orgânica, façam compras de fertilizantes naturais
            e compartilhem dados de maneira segura. Junte-se a nós e contribua para
            práticas ambientais conscientes enquanto gerencia seu negócio com eficiência.</p>
      </div>

      <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
        <div class="card bg-glass shadow-lg py-2">
          <div class="card-body px-4 py-5 px-md-5">


            <form method="POST" action="processa_cadastro.php" >
              <div class="text-center mb-4"><img src="../img/logo.png" alt="Logo EcoRaiz" width="100"></div>

              <div class="mb-4">
                <label class="form-label fw-bold d-block mb-2">Tipo de cadastro</label>
                <div class="btn-group tipo-pessoa-group" role="group">
                  <input type="radio" class="btn-check" name="tipoPessoa" id="pf" value="pf" checked>
                  <label class="btn btn-outline-primary" for="pf">Pessoa Física</label>
                  <input type="radio" class="btn-check" name="tipoPessoa" id="pj" value="pj">
                  <label class="btn btn-outline-primary" for="pj">Pessoa Jurídica</label>
                </div>
              </div>

              <div id="pfFields">
                <div class="mb-3"><label class="form-label">Nome completo</label><input type="text" class="form-control" name="nome" required></div>
                <div class="mb-3"><label class="form-label">Telefone</label><input type="text" class="form-control" name="telefone" required></div>
                <div class="mb-3"><label class="form-label">CPF</label><input type="text" class="form-control" name="cpf" required></div>
                <div class="mb-3"><label class="form-label">Data de nascimento</label><input type="date" class="form-control" name="data_nascimento" required></div>
                <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="endereco_email" required></div>
                <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
              </div>

              <div id="pjFields" style="display:none;">
                <div class="mb-3"><label class="form-label">Razão social</label><input type="text" class="form-control" name="nome"></div>
                <div class="mb-3"><label class="form-label">CNPJ</label><input type="text" class="form-control" name="cnpj"></div>
                <div class="mb-3"><label class="form-label">Telefone</label><input type="text" class="form-control" name="telefone"></div>
                <div class="mb-3"><label class="form-label">Responsável</label><input type="text" class="form-control" name="responsavel"></div>
                <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="endereco_email"></div>
                <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha"></div>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <a href="login.php" class="text-decoration-none" style="color: #1E5E2E; font-weight: 500;">Já possui conta?</a>
                <button type="submit" class="btn btn-register">Cadastrar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
const pfRadio = document.getElementById('pf');
const pjRadio = document.getElementById('pj');
const pfFields = document.getElementById('pfFields');
const pjFields = document.getElementById('pjFields');

// função auxiliar para habilitar/desabilitar inputs
function toggleFields(show, hide) {
  show.style.display = 'block';
  hide.style.display = 'none';
  show.querySelectorAll('input').forEach(inp => inp.disabled = false);
  hide.querySelectorAll('input').forEach(inp => inp.disabled = true);
}

// estado inicial
toggleFields(pfFields, pjFields);

// troca dinâmica
pfRadio.addEventListener('change', () => toggleFields(pfFields, pjFields));
pjRadio.addEventListener('change', () => toggleFields(pjFields, pfFields));
</script>

<script>
const pfRadio=document.getElementById('pf');
const pjRadio=document.getElementById('pj');
const pfFields=document.getElementById('pfFields');
const pjFields=document.getElementById('pjFields');
pfRadio.addEventListener('change',()=>{pfFields.style.display='block';pjFields.style.display='none';});
pjRadio.addEventListener('change',()=>{pfFields.style.display='none';pjFields.style.display='block';});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
