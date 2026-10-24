window.Home = (function () {
  const FEATURED_KEY = "petly_featured_products";
  const MAX_FEATURED = 4;
  const DEFAULT_FEATURED = ["1", "2", "6", "8"];

  function getFeaturedIds() {
    try {
      const stored = JSON.parse(localStorage.getItem(FEATURED_KEY));
      if (Array.isArray(stored) && stored.length > 0) {
        return stored.slice(0, MAX_FEATURED);
      }
    } catch {
    }
    return DEFAULT_FEATURED;
  }

  function renderFeaturedProducts() {
    const grid = document.getElementById("featuredProductsGrid");
    if (!grid) return;

    const catalog = window.Products || [];
    const ids = getFeaturedIds();
    const items = ids
      .map((id) => catalog.find((p) => p.id === id))
      .filter(Boolean);

    if (items.length === 0) {
      grid.innerHTML =
        '<p class="text-muted">No featured products selected yet.</p>';
      return;
    }

    grid.innerHTML = items
      .map((p, i) => {
        const tilt = i % 2 === 0 ? "photo-tilt-a" : "photo-tilt-b";
        const img = window.productImageUrl(p.image, 500);
        return `
        <div class="col-sm-6 col-lg-3">
          <div class="card card-petly card-hover h-100 p-3">
            <div class="photo-frame ${tilt} mb-3">
              <img src="${img}" alt="${p.name}" loading="lazy" />
            </div>
            <h3 class="h6 mb-1">${p.name}</h3>
            <p class="text-muted small mb-2">${p.description}</p>
            <div class="fw-bold text-petly-primary">${p.price.toFixed(2).replace(".", ",")} KM</div>
          </div>
        </div>`;
      })
      .join("");
  }

  function onRoute(view) {
    if (view === "home") renderFeaturedProducts();
  }

  return { onRoute, getFeaturedIds, FEATURED_KEY, MAX_FEATURED };
})();
