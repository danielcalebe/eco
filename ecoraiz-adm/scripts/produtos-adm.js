document.addEventListener("DOMContentLoaded", () => {
  const addBtn = document.getElementById("addProdutoBtn");
  const tbody = document.getElementById("produtosTableBody");

  // Adicionar produto
  addBtn.addEventListener("click", () => {
    const data = new FormData();
    data.append("action", "add");
    data.append("nome", document.getElementById("addNome").value);
    data.append("descricao", document.getElementById("addDescricao").value);
    data.append("qtd", document.getElementById("addQuantidade").value);
    data.append("preco", document.getElementById("addPreco").value);
    data.append("status", document.getElementById("addStatus").value);
    data.append("categoria", document.getElementById("addCategoria").value);
    data.append("imagem", document.getElementById("addCaminhoImagem").value);
    data.append("unidade", document.getElementById("addUnidadeMedida").value);

    fetch("produtos_actions.php", { method: "POST", body: data })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === "success") {
          alert("Produto adicionado com sucesso!");
          location.reload();
        } else {
          alert("Erro ao adicionar produto.");
        }
      });
  });

  // Excluir produto
  tbody.addEventListener("click", e => {
    if (e.target.closest(".btn-excluir")) {
      const id = e.target.closest(".btn-excluir").dataset.id;
      if (confirm("Deseja excluir este produto?")) {
        const data = new FormData();
        data.append("action", "delete");
        data.append("id", id);

        fetch("produtos_actions.php", { method: "POST", body: data })
          .then(res => res.text())
          .then(response => {
            if (response.trim() === "success") {
              alert("Produto excluído!");
              location.reload();
            } else {
              alert("Erro ao excluir produto.");
            }
          });
      }
    }
  });
});
