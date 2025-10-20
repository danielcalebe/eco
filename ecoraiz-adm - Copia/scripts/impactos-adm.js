const searchInput = document.getElementById("searchInput");
const tbody = document.getElementById("impactosTableBody"); // atualizado

searchInput.addEventListener("input", () => {
  const filtro = searchInput.value.toLowerCase();

  Array.from(tbody.rows).forEach(row => {
    const id = row.cells[0].innerText.toLowerCase();
    const doador = row.cells[1].innerText.toLowerCase();
    const quantidade = row.cells[2].innerText.toLowerCase();
    const fertilizante = row.cells[3].innerText.toLowerCase();
    const impacto = row.cells[4].innerText.toLowerCase();
    const obs = row.cells[5].innerText.toLowerCase();

    if (
      id.includes(filtro) ||
      doador.includes(filtro) ||
      quantidade.includes(filtro) ||
      fertilizante.includes(filtro) ||
      impacto.includes(filtro) ||
      obs.includes(filtro)
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
    row.cells[0].innerText = i + 1; // atualiza número da linha
  });
}

// Adicionar / Editar Impacto
document.getElementById('addImpactoBtn').addEventListener('click', () => {
  const doador = document.getElementById('addIdDoador').value;
  const quantidade = document.getElementById('addQuantidadeTotal').value;
  const fertilizante = document.getElementById('addFertilizante').value;
  const impacto = document.getElementById('addImpacto').value;
  const obs = document.getElementById('addObservacoes').value;

  if (!doador || !quantidade || !fertilizante || !impacto || !obs) {
    alert("Preencha todos os campos!");
    return;
  }

  if (editingRow) {
    editingRow.cells[1].innerText = doador;
    editingRow.cells[2].innerText = quantidade;
    editingRow.cells[3].innerText = fertilizante;
    editingRow.cells[4].innerText = impacto;
    editingRow.cells[5].innerText = obs;
    editingRow = null;
  } else {
    const newRow = document.createElement('tr');
    const rowNumber = tbody.rows.length + 1;
    newRow.innerHTML = `
      <td>${rowNumber}</td>
      <td>${doador}</td>
      <td>${quantidade}</td>
      <td>${fertilizante}</td>
      <td>${impacto}</td>
      <td>${obs}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-excluir"><i class="bi bi-trash"></i></button>
      </td>
    `;
    tbody.appendChild(newRow);
  }

  bootstrap.Modal.getInstance(document.getElementById('addImpactoModal')).hide();
  document.getElementById('addImpactoForm').reset();
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
    document.getElementById('addIdDoador').value = row.cells[1].innerText;
    document.getElementById('addQuantidadeTotal').value = row.cells[2].innerText;
    document.getElementById('addFertilizante').value = row.cells[3].innerText;
    document.getElementById('addImpacto').value = row.cells[4].innerText;
    document.getElementById('addObservacoes').value = row.cells[5].innerText;
    new bootstrap.Modal(document.getElementById('addImpactoModal')).show();
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

// Ordenar por ID (primeira célula)
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
