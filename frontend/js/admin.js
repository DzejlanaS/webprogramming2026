window.Admin = (function () {
  function getIds() {
    return (window.Home && window.Home.getFeaturedIds()) || [];
  }

  function saveIds(ids) {
    localStorage.setItem(
      window.Home.FEATURED_KEY,
      JSON.stringify(ids.slice(0, window.Home.MAX_FEATURED))
    );
  }

  function updateCountLabel(count) {
    const label = document.getElementById("featuredCount");
    if (label) {
      label.textContent = `${count}/${window.Home.MAX_FEATURED} featured`;
      label.classList.toggle("text-petly-secondary", count > 0);
    }
  }

  function syncCheckboxState() {
    const checkboxes = document.querySelectorAll(".js-featured-toggle");
    const ids = getIds();

    checkboxes.forEach((box) => {
      box.checked = ids.includes(box.dataset.id);
    });
    updateCountLabel(ids.length);

    const atCap = ids.length >= window.Home.MAX_FEATURED;
    checkboxes.forEach((box) => {
      box.disabled = atCap && !box.checked;
    });
  }

  function bindFeaturedToggles() {
    const checkboxes = document.querySelectorAll(".js-featured-toggle");
    if (!checkboxes.length || !window.Home) return;

    syncCheckboxState();

    checkboxes.forEach((box) => {
      if (box.dataset.bound) return;
      box.dataset.bound = "true";
      box.addEventListener("change", () => {
        let ids = getIds();
        if (box.checked) {
          if (ids.length >= window.Home.MAX_FEATURED) {
            box.checked = false;
            return;
          }
          ids = [...ids, box.dataset.id];
        } else {
          ids = ids.filter((id) => id !== box.dataset.id);
        }
        saveIds(ids);
        syncCheckboxState();
      });
    });
  }

  function onRoute(view) {
    if (view === "admin") bindFeaturedToggles();
  }

  return { onRoute };
})();
