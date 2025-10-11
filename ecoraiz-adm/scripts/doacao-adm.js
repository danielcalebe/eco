const searchInput = document.getElementById("searchInput");
const tbody = document.getElementById("doacoesTableBody");

searchInput.addEventListener("input", () => {
  const filtro = searchInput.value.toLowerCase();

  Array.from(tbody.rows).forEach(row => {
    const id = row.cells[0].innerText.toLowerCase();
    const doador = row.cells[1].innerText.toLowerCase();
    const tipo = row.cells[2].innerText.toLowerCase();
    const quantidade = row.cells[3].innerText.toLowerCase();
    const local = row.cells[4].innerText.toLowerCase();
    const status = row.cells[5].querySelector("span").innerText.toLowerCase();

    if (
      id.includes(filtro) ||
      doador.includes(filtro) ||
      tipo.includes(filtro) ||
      quantidade.includes(filtro) ||
      local.includes(filtro) ||
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

// Atualizar IDs (apenas visual, se necessário)
function updateIDs() {
  Array.from(tbody.rows).forEach((row, i) => {
    row.cells[1].innerText = i + 1; // atualiza número da linha
  });
}

// Adicionar / Editar Doação
document.getElementById('addDoacaoBtn').addEventListener('click', () => {
  const doador = document.getElementById('addDoador').value;
  const tipo = document.getElementById('addTipoResiduos').value;
  const quantidade = document.getElementById('addQuantidade').value;
  const local = document.getElementById('addLocal').value;
  const status = document.getElementById('addStatus').value;

  if (!doador || !tipo || !quantidade || !local || !status) {
    alert("Preencha todos os campos!");
    return;
  }

  if (editingRow) {
    editingRow.cells[2].innerText = doador;
    editingRow.cells[3].innerText = tipo;
    editingRow.cells[4].innerText = quantidade;
    editingRow.cells[5].innerText = local;
    editingRow.cells[6].innerHTML = `<span class="${status === 'Recebido' ? 'status-recebido' : 'status-cancelado'}">${status}</span>`;
    editingRow = null;
  } else {
    const newRow = document.createElement('tr');
    const rowNumber = tbody.rows.length + 1;
    newRow.innerHTML = `
      <td>${rowNumber}</td>
      <td>${doador}</td>
      <td>${tipo}</td>
      <td>${quantidade}</td>
      <td>${local}</td>
      <td><span class="${status === 'Recebido' ? 'status-recebido' : 'status-cancelado'}">${status}</span></td>
      <td class="text-end">
        <button class="btn btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-excluir"><i class="bi bi-trash"></i></button>
      </td>
    `;
    tbody.appendChild(newRow);
  }

  bootstrap.Modal.getInstance(document.getElementById('addDoacaoModal')).hide();
  document.getElementById('addDoacaoForm').reset();
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
    document.getElementById('addDoador').value = row.cells[1].innerText;
    document.getElementById('addTipoResiduos').value = row.cells[2].innerText;
    document.getElementById('addQuantidade').value = row.cells[3].innerText;
    document.getElementById('addLocal').value = row.cells[4].innerText;
    document.getElementById('addStatus').value = row.cells[5].innerText;
    new bootstrap.Modal(document.getElementById('addDoacaoModal')).show();
  }
});

// Seleção e exclusão múltipla
deleteBtn.addEventListener('click', () => {
  if (!selecting) {
    selecting = true;
    deleteBtn.innerHTML = '<i class="bi bi-check-lg"></i> Confirmar Exclusão';

    // Criar botão cancelar
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

    // Adicionar checkboxes
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

// Ordenar por ID (primeira célula)
let idDescending = true;
document.getElementById('idHeader').addEventListener('click', () => {
  const rows = Array.from(tbody.rows);
  rows.sort((a, b) => {
    const idA = parseInt(a.cells[0].innerText); // ID real
    const idB = parseInt(b.cells[0].innerText);
    return idDescending ? idB - idA : idA - idB;
  });
  rows.forEach(r => tbody.appendChild(r));
  idDescending = !idDescending;
});