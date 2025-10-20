document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("funcionariosTableBody");

  // Função para listar
  function listarFuncionarios() {
    fetch('processa_funcionario.php', {
      method: 'POST',
      body: new URLSearchParams({ action: 'listar' })
    })
    .then(res => res.json())
    .then(data => {
      tableBody.innerHTML = '';
      data.forEach(func => {
        tableBody.innerHTML += `
          <tr>
            <td>${func.id_funcionario}</td>
            <td>${func.cpf}</td>
            <td>${func.cargo}</td>
            <td>${func.email}</td>
            <td>${func.senha}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-editar" data-id="${func.id_funcionario}"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-excluir" data-id="${func.id_funcionario}"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`;
      });
      attachEventButtons();
    });
  }

  listarFuncionarios();

  // Adicionar funcionário
  document.getElementById("addFuncionarioBtn").addEventListener("click", () => {
    const cpf = document.getElementById("addCpf").value;
    const cargo = document.getElementById("addCargo").value;
    const email = document.getElementById("addEmail").value;
    const senha = document.getElementById("addSenha").value;

    fetch('processa_funcionario.php', {
      method: 'POST',
      body: new URLSearchParams({ action: 'adicionar', cpf, cargo, email, senha })
    }).then(res => res.text())
      .then(resp => {
        if(resp === 'success') {
          listarFuncionarios();
          document.getElementById("addFuncionarioForm").reset();
          bootstrap.Modal.getInstance(document.getElementById("addFuncionarioModal")).hide();
        } else {
          alert("Erro ao adicionar funcionário!");
        }
      });
  });

  // Editar e excluir
  function attachEventButtons() {
    document.querySelectorAll(".btn-editar").forEach(btn => {
      btn.onclick = () => {
        const tr = btn.closest("tr");
        const id = btn.dataset.id;
        const cpf = tr.children[1].textContent;
        const cargo = tr.children[2].textContent;
        const email = tr.children[3].textContent;
        const senha = '';

        const newCpf = prompt("CPF:", cpf);
        const newCargo = prompt("Cargo:", cargo);
        const newEmail = prompt("Email:", email);
        const newSenha = prompt("Senha (vazio para não alterar):", senha);

        if (newCpf && newCargo && newEmail) {
          fetch('processa_funcionario.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'editar', id, cpf: newCpf, cargo: newCargo, email: newEmail, senha: newSenha })
          }).then(res => res.text())
            .then(resp => {
              if(resp === 'success') listarFuncionarios();
              else alert("Erro ao editar funcionário!");
            });
        }
      };
    });

    document.querySelectorAll(".btn-excluir").forEach(btn => {
      btn.onclick = () => {
        if(confirm("Deseja realmente excluir este funcionário?")) {
          const id = btn.dataset.id;
          fetch('processa_funcionario.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'excluir', ids: id }) // envia apenas 1 id
          }).then(res => res.text())
            .then(resp => {
              if(resp === 'success') listarFuncionarios();
              else alert("Erro ao excluir funcionário!");
            });
        }
      };
    });
  }
});
