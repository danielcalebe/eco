document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".contador");
  const speed = 150; // controla a velocidade da contagem (menor = mais rápido)
  let started = false;

  // Função para animar os números
  function animateCounters() {
    counters.forEach(counter => {
      const target = +counter.getAttribute("data-target");
      const updateCount = () => {
        const current = +counter.innerText.replace(/\D/g, '');
        const increment = target / speed;

        if (current < target) {
          counter.innerText = Math.ceil(current + increment).toLocaleString("pt-BR");
          setTimeout(updateCount, 20);
        } else {
          counter.innerText = target.toLocaleString("pt-BR");
        }
      };
      updateCount();
    });
  }

  // Detecta quando a seção "indicadores" entra na tela
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !started) {
        started = true; // garante que roda só uma vez
        animateCounters();
      }
    });
  }, { threshold: 0.4 }); // 40% visível já ativa a animação

  observer.observe(document.querySelector("#indicadores"));
});