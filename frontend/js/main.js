(function () {
  const routeMap = {
    "": "home",
    home: "home",
    shop: "shop",
    product: "product-detail",
    cart: "cart",
    checkout: "checkout",
    "order-confirmation": "order-confirmation",
    login: "login",
    register: "register",
    profile: "profile",
    about: "about",
    contact: "contact",
    admin: "admin",
  };

  const pageTitles = {
    home: "Petly – Everything Your Pet Needs",
    shop: "Shop · Petly",
    "product-detail": "Product Details · Petly",
    cart: "Your Cart · Petly",
    checkout: "Checkout · Petly",
    "order-confirmation": "Order Confirmed · Petly",
    login: "Login · Petly",
    register: "Register · Petly",
    profile: "My Profile · Petly",
    about: "About Us · Petly",
    contact: "Contact Us · Petly",
    admin: "Admin Dashboard · Petly",
    "not-found": "Page Not Found · Petly",
  };

  const app = document.getElementById("app");

  function closeOpenModals() {
    if (!window.bootstrap) return;
    document.querySelectorAll(".modal.show").forEach((modalEl) => {
      window.bootstrap.Modal.getInstance(modalEl)?.hide();
    });
    document.body.classList.remove("modal-open");
    document.body.style.removeProperty("padding-right");
    document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
  }

  async function render() {
    closeOpenModals();
    const hash = window.location.hash.replace(/^#\/?/, "");
    const [key, ...params] = hash.split("/").filter(Boolean);
    const view = routeMap[key || ""] || "not-found";

    try {
      const res = await fetch(`views/${view}.html`);
      app.innerHTML = res.ok
        ? await res.text()
        : '<p class="p-5 text-center text-muted">Could not load this page.</p>';
    } catch (err) {
      app.innerHTML =
        '<p class="p-5 text-center text-muted">Could not load this page.</p>';
    }

    document.title = pageTitles[view] || "Petly";
    window.scrollTo({ top: 0, behavior: "auto" });
    updateActiveNav(key || "home");
    bindPlaceholderForms();
    closeMobileNav();

    if (window.Shop && typeof window.Shop.onRoute === "function") {
      window.Shop.onRoute(view, params);
    }
    if (window.Cart && typeof window.Cart.onRoute === "function") {
      window.Cart.onRoute(view, params);
    }
    if (window.Home && typeof window.Home.onRoute === "function") {
      window.Home.onRoute(view, params);
    }
    if (window.Admin && typeof window.Admin.onRoute === "function") {
      window.Admin.onRoute(view, params);
    }
  }

  function updateActiveNav(key) {
    document.querySelectorAll("[data-route]").forEach((link) => {
      link.classList.toggle("active", link.dataset.route === key);
    });
  }

  function bindPlaceholderForms() {
    document.querySelectorAll(".js-placeholder-form").forEach((form) => {
      if (form.dataset.bound) return;
      form.dataset.bound = "true";
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        const note = form.querySelector(".js-placeholder-note");
        if (note) {
          note.classList.remove("d-none");
          note.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      });
    });
  }

  function closeMobileNav() {
    const navEl = document.getElementById("mainNav");
    if (navEl && navEl.classList.contains("show") && window.bootstrap) {
      window.bootstrap.Collapse.getOrCreateInstance(navEl).hide();
    }
  }

  window.addEventListener("hashchange", render);
  render();
})();
