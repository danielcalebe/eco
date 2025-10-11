const tipoEntrega = document.getElementById("tipoEntrega");
    const enderecoInput = document.getElementById("endereco");
    const campoRegiao = document.getElementById("campoRegiao");
    const regiaoBH = document.getElementById("regiaoBH");
    const form = document.getElementById("formDoacao");
    const quantidadeInput = document.getElementById("quantidade");
    const alertContainer = document.getElementById("alertContainer");

    // Função para mostrar alertas Bootstrap
    function showAlert(message, type = "info", timeout = 7000) {
      const alertDiv = document.createElement("div");
      alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
      alertDiv.role = "alert";
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      `;
      alertContainer.appendChild(alertDiv);
      setTimeout(() => alertDiv.classList.remove("show"), timeout - 500);
      setTimeout(() => alertDiv.remove(), timeout);
    }

    // Mostrar/esconder campos conforme tipo de entrega
    tipoEntrega.addEventListener("change", () => {
      const valor = tipoEntrega.value;
      enderecoInput.disabled = false;
      campoRegiao.classList.add("d-none");

      if (valor === "entrega") {
        enderecoInput.value = "Rua ABC 123, Belo Horizonte - MG";
        enderecoInput.disabled = true;
      } else if (valor === "ponto_encontro") {
        enderecoInput.placeholder = "Digite o local de encontro combinado";
      } else if (valor === "coleta") {
        campoRegiao.classList.remove("d-none");
        enderecoInput.disabled = false;
        enderecoInput.placeholder = "Selecione a região acima";
      }
    });

    // Mostrar cronograma conforme região
    regiaoBH.addEventListener("change", () => {
      const regiao = regiaoBH.value;
      let dia = "";

      switch (regiao) {
        case "norte":
        case "venda-nova":
          dia = "Terça-feira"; break;
        case "noroeste":
        case "pampulha":
          dia = "Quarta-feira"; break;
        case "oeste":
        case "barreiro":
          dia = "Quinta-feira"; break;
        case "leste":
        case "nordeste":
        case "centro-sul":
          dia = "Sexta-feira"; break;
      }

      if (dia) {
                scrollTo(0, 0)

        showAlert(`🗓️ A coleta para a região selecionada ocorre na <strong>${dia}</strong>.`, "info");
      }
    });

    // Validação completa no envio
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const tipo = tipoEntrega.value;
      const quantidade = parseInt(quantidadeInput.value);

      if ((tipo === "coleta" || tipo === "ponto_encontro") && quantidade < 15) {
        scrollTo(0, 0)

        showAlert("⚠️ As opções <strong>Por coleta</strong> e <strong>Ponto de encontro</strong> estão disponíveis apenas para doações a partir de <strong>15 kg</strong>.", "warning");
        showAlert("⚠️ Tente alguma outra opção de doação ou junte um pouco mais de lixo orgânico!", "success");

        return;
      }

      if (tipo === "coleta" && quantidade >= 15) {
        const cronograma = `
          📅 <strong>Cronograma de Coletas:</strong><br>
          • Norte e Venda Nova → Terça-feira<br>
          • Noroeste e Pampulha → Quarta-feira<br>
          • Oeste e Barreiro → Quinta-feira<br>
          • Leste e Nordeste → Sexta-feira<br>
          • Centro-Sul → Sexta-feira
        `;
                scrollTo(0, 0)

        showAlert(cronograma, "info", 10000);
      }
      scrollTo(0, 0)

      showAlert("✅ Sua doação foi registrada com sucesso! A equipe EcoRaiz entrará em contato por telefone em breve.  💚", "success");
      this.reset();
      campoRegiao.classList.add("d-none");
      enderecoInput.disabled = false;
      enderecoInput.value = "";
    });