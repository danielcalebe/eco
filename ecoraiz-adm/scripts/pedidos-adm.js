const searchInput = document.getElementById("searchInput");
const tbody = document.getElementById("pedidosTableBody");

searchInput.addEventListener("input", () => {
  const filtro = searchInput.value.toLowerCase();

  Array.from(tbody.rows).forEach(row => {
    const id = row.cells[0].innerText.toLowerCase();
    const idDoador = row.cells[1].innerText.toLowerCase();
    const quantidadeTotal = row.cells[2].innerText.toLowerCase();
    const fertilizante = row.cells[3].innerText.toLowerCase();
    const impacto = row.cells[4].innerText.toLowerCase();
    const observacoes = row.cells[5].innerText.toLowerCase();

    if (
      id.includes(filtro) ||
      idDoador.includes(filtro) ||
      quantidadeTotal.includes(filtro) ||
      fertilizante.includes(filtro) ||
      impacto.includes(filtro) ||
      observacoes.includes(filtro)
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

// Atualizar IDs (visual)
function updateIDs() {
  Array.from(tbody.rows).forEach((row, i) => {
    row.cells[0].innerText = i + 1; // atualiza número da linha
  });
}

// Adicionar / Editar Pedido
document.getElementById('addPedidoBtn').addEventListener('click', () => {
  const idDoador = document.getElementById('addIdDoador').value;
  const quantidadeTotal = document.getElementById('addQuantidadeTotal').value;
  const fertilizante = document.getElementById('addFertilizante').value;
  const impacto = document.getElementById('addImpacto').value;
  const observacoes = document.getElementById('addObservacoes').value;

  if (!idDoador || !quantidadeTotal || !fertilizante || !impacto || !observacoes) {
    alert("Preencha todos os campos!");
    return;
  }

  if (editingRow) {
    editingRow.cells[1].innerText = idDoador;
    editingRow.cells[2].innerText = quantidadeTotal;
    editingRow.cells[3].innerText = fertilizante;
    editingRow.cells[4].innerText = impacto;
    editingRow.cells[5].innerText = observacoes;
    editingRow = null;
  } else {
    const newRow = document.createElement('tr');
    const rowNumber = tbody.rows.length + 1;
    newRow.innerHTML = `
      <td>${rowNumber}</td>
      <td>${idDoador}</td>
      <td>${quantidadeTotal}</td>
      <td>${fertilizante}</td>
      <td>${impacto}</td>
      <td>${observacoes}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-excluir"><i class="bi bi-trash"></i></button>
      </td>
    `;
    tbody.appendChild(newRow);
  }

  bootstrap.Modal.getInstance(document.getElementById('addPedidoModal')).hide();
  document.getElementById('addPedidoForm').reset();
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
    new bootstrap.Modal(document.getElementById('addPedidoModal')).show();
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
