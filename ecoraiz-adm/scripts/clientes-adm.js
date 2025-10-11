const searchInput = document.getElementById("searchInput");
const tbody = document.getElementById("clientesTableBody");

searchInput.addEventListener("input", () => {
  const filtro = searchInput.value.toLowerCase();

  Array.from(tbody.rows).forEach(row => {
    const id = row.cells[0].innerText.toLowerCase();
    const nome = row.cells[1].innerText.toLowerCase();
    const cpf = row.cells[2].innerText.toLowerCase();
    const nascimento = row.cells[3].innerText.toLowerCase();
    const email = row.cells[4].innerText.toLowerCase();
    const telefone = row.cells[5].innerText.toLowerCase();
    const atividade = row.cells[6].innerText.toLowerCase();
    const status = row.cells[7].querySelector("span").innerText.toLowerCase();

    if (
      id.includes(filtro) ||
      nome.includes(filtro) ||
      cpf.includes(filtro) ||
      nascimento.includes(filtro) ||
      email.includes(filtro) ||
      telefone.includes(filtro) ||
      atividade.includes(filtro) ||
      status.includes(filtro)
    ) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
});

const deleteBtn = document.getElementById('deleteSelectedBtn');
let selecting = false;
let rowsToDelete = [];
let editingRow = null;

// Atualizar IDs (apenas visual)
function updateIDs() {
  Array.from(tbody.rows).forEach((row, i) => {
    row.cells[0].innerText = i + 1; // atualiza número da linha
  });
}

// Adicionar / Editar Cliente
document.getElementById('addClienteBtn').addEventListener('click', () => {
  const nome = document.getElementById('addNome').value;
  const cpf = document.getElementById('addCPF').value;
  const nascimento = document.getElementById('addDataNascimento').value;
  const email = document.getElementById('addEmail').value;
  const telefone = document.getElementById('addTelefone').value;
  const atividade = document.getElementById('addAtividade').value;
  const status = document.getElementById('addStatus').value;

  if (!nome || !cpf || !nascimento || !email || !telefone || !atividade || !status) {
    alert("Preencha todos os campos!");
    return;
  }

  if (editingRow) {
    editingRow.cells[1].innerText = nome;
    editingRow.cells[2].innerText = cpf;
    editingRow.cells[3].innerText = nascimento;
    editingRow.cells[4].innerText = email;
    editingRow.cells[5].innerText = telefone;
    editingRow.cells[6].innerText = atividade;
    editingRow.cells[7].innerHTML = `<span class="${status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${status}</span>`;
    editingRow = null;
  } else {
    const newRow = document.createElement('tr');
    const rowNumber = tbody.rows.length + 1;
    newRow.innerHTML = `
      <td>${rowNumber}</td>
      <td>${nome}</td>
      <td>${cpf}</td>
      <td>${nascimento}</td>
      <td>${email}</td>
      <td>${telefone}</td>
      <td>${atividade}</td>
      <td><span class="${status === 'Ativo' ? 'status-ativo' : 'status-inativo'}">${status}</span></td>
      <td class="text-end">
        <button class="btn btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-excluir"><i class="bi bi-trash"></i></button>
      </td>
    `;
    tbody.appendChild(newRow);
  }

  bootstrap.Modal.getInstance(document.getElementById('addClienteModal')).hide();
  document.getElementById('addClienteForm').reset();
});

// Editar / Excluir individual
tbody.addEventListener('click', (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const row = btn.closest('tr');

  if (btn.classList.contains('btn-excluir')) {
    rowsToDelete = [row];
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
  }

  if (btn.classList.contains('btn-editar')) {
    editingRow = row;
    document.getElementById('addNome').value = row.cells[1].innerText;
    document.getElementById('addCPF').value = row.cells[2].innerText;
    document.getElementById('addDataNascimento').value = row.cells[3].innerText;
    document.getElementById('addEmail').value = row.cells[4].innerText;
    document.getElementById('addTelefone').value = row.cells[5].innerText;
    document.getElementById('addAtividade').value = row.cells[6].innerText;
    document.getElementById('addStatus').value = row.cells[7].innerText;
    new bootstrap.Modal(document.getElementById('addClienteModal')).show();
  }
});

// Seleção e exclusão múltipla
deleteBtn.addEventListener('click', () => {
  if (!selecting) {
    selecting = true;
    deleteBtn.innerHTML = '<i class="bi bi-check-lg"></i> Confirmar Exclusão';

    if (!document.getElementById('cancelSelectionBtn')) {
      const cancelBtn = document.createElement('button');
      cancelBtn.id = 'cancelSelectionBtn';
      cancelBtn.className = 'btn btn-outline-secondary btn-sm ms-2';
      cancelBtn.innerText = 'Cancelar';
      deleteBtn.after(cancelBtn);

      cancelBtn.addEventListener('click', () => {
        selecting = false;
        deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Apagar';
        cancelBtn.remove();
        Array.from(tbody.rows).forEach(row => {
          const cb = row.querySelector('.row-checkbox');
          if (cb) cb.remove();
        });
      });
    }

    Array.from(tbody.rows).forEach(row => {
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.className = 'row-checkbox';
      cb.style.marginRight = '10px';
      row.insertBefore(cb, row.cells[0]);
    });

  } else {
    rowsToDelete = Array.from(tbody.querySelectorAll('.row-checkbox:checked')).map(cb => cb.closest('tr'));
    if (rowsToDelete.length === 0) {
      alert('Selecione pelo menos uma linha para excluir!');
      return;
    }
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
  }
});

// Confirmar exclusão
document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
  rowsToDelete.forEach(r => r.remove());
  rowsToDelete = [];
  selecting = false;
  deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Apagar';
  const cancelBtn = document.getElementById('cancelSelectionBtn');
  if (cancelBtn) cancelBtn.remove();
  Array.from(tbody.rows).forEach(row => {
    const cb = row.querySelector('.row-checkbox');
    if (cb) cb.remove();
  });
  updateIDs();
  bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
});

// Ordenar por ID
let idDescending = true;
document.getElementById('idHeader').addEventListener('click', () => {
  const rows = Array.from(tbody.rows);
  rows.sort((a, b) => {
    const idA = parseInt(a.cells[0].innerText);
    const idB = parseInt(b.cells[0].innerText);
    return idDescending ? idB - idA : idA - idB;
  });
  rows.forEach(r => tbody.appendChild(r));
  idDescending = !idDescending;
});
