window.Shop = (function () {
  function bindFilters() {
    const buttons = document.querySelectorAll(".category-filter-btn");
    const search = document.getElementById("productSearch");
    const cards = document.querySelectorAll(".product-card-col");
    if (!buttons.length && !search) return;

    function apply() {
      const activeBtn = document.querySelector(".category-filter-btn.active");
      const category = activeBtn ? activeBtn.dataset.category : "all";
      const term = (search ? search.value : "").trim().toLowerCase();

      cards.forEach((card) => {
        const matchesCategory =
          category === "all" || card.dataset.category === category;
        const matchesTerm =
          !term || card.dataset.name.toLowerCase().includes(term);
        card.classList.toggle("d-none", !(matchesCategory && matchesTerm));
      });
    }

    buttons.forEach((btn) => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = "true";
      btn.addEventListener("click", () => {
        buttons.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        apply();
      });
    });

    if (search && !search.dataset.bound) {
      search.dataset.bound = "true";
      search.addEventListener("input", apply);
    }
  }

  function onRoute(view) {
    if (view === "shop") bindFilters();
  }

  return { onRoute, bindFilters };
})();
