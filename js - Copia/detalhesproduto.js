const mainImg = document.getElementById("mainProductImage");
  const zoomResult = document.getElementById("zoomResult");

  // Ativar lupa
  mainImg.addEventListener("mousemove", moveZoom);
  mainImg.addEventListener("mouseenter", () => {
    zoomResult.style.display = "block";
    zoomResult.style.backgroundImage = `url(${mainImg.src})`;
  });
  mainImg.addEventListener("mouseleave", () => {
    zoomResult.style.display = "none";
  });

  function moveZoom(e) {
    const rect = mainImg.getBoundingClientRect();
    const x = ((e.pageX - rect.left - window.scrollX) / mainImg.width) * 100;
    const y = ((e.pageY - rect.top - window.scrollY) / mainImg.height) * 100;

    zoomResult.style.backgroundPosition = `${x}% ${y}%`;
  }

  // Trocar imagem principal ao clicar na miniatura
  function changeImage(el) {
    mainImg.src = el.src;
    zoomResult.style.backgroundImage = `url(${el.src})`;
  }