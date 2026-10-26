window.Cart = (function () {
  const STORAGE_KEY = "petly_cart";
  const ORDER_KEY = "petly_last_order";

  function getItems() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch {
      return [];
    }
  }

  function saveItems(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    updateBadge();
  }

  function addItem(product) {
    const items = getItems();
    const existing = items.find((i) => i.id === product.id);
    if (existing) existing.qty += 1;
    else items.push({ ...product, qty: 1 });
    saveItems(items);
  }

  function updateQty(id, qty) {
    const items = getItems();
    const item = items.find((i) => i.id === id);
    if (!item) return;
    item.qty = Math.max(1, qty);
    saveItems(items);
    renderCartPage();
  }

  function removeItem(id) {
    saveItems(getItems().filter((i) => i.id !== id));
    renderCartPage();
  }

  function clear() {
    localStorage.removeItem(STORAGE_KEY);
    updateBadge();
  }

  function total() {
    return getItems().reduce((sum, i) => sum + i.price * i.qty, 0);
  }

  function count() {
    return getItems().reduce((sum, i) => sum + i.qty, 0);
  }

  function formatKM(amount) {
    return amount.toFixed(2).replace(".", ",") + " KM";
  }

  function updateBadge() {
    const badge = document.getElementById("cartBadge");
    if (badge) badge.textContent = count();
  }

  function bindAddToCartButtons() {
    document.querySelectorAll(".js-add-to-cart").forEach((btn) => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = "true";
      btn.addEventListener("click", () => {
        const product = {
          id: btn.dataset.id,
          name: btn.dataset.name,
          price: parseFloat(btn.dataset.price),
          image: btn.dataset.image || "",
        };
        const qtyInput = document.getElementById("productQty");
        const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
        for (let i = 0; i < qty; i++) addItem(product);
        flashButton(btn);
      });
    });
  }

  function flashButton(btn) {
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2"></i> Added';
    btn.disabled = true;
    setTimeout(() => {
      btn.innerHTML = original;
      btn.disabled = false;
    }, 900);
  }

  function renderCartPage() {
    const body = document.getElementById("cartBody");
    if (!body) return;

    const items = getItems();
    const emptyState = document.getElementById("cartEmpty");
    const tableWrap = document.getElementById("cartTableWrap");

    if (items.length === 0) {
      if (emptyState) emptyState.classList.remove("d-none");
      if (tableWrap) tableWrap.classList.add("d-none");
      return;
    }
    if (emptyState) emptyState.classList.add("d-none");
    if (tableWrap) tableWrap.classList.remove("d-none");

    body.innerHTML = items
      .map(
        (item) => `
      <tr>
        <td>
          <div class="d-flex align-items-center gap-3">
            <div class="thumb-sm"><img src="${item.image}" alt="${item.name}" loading="lazy"></div>
            <span>${item.name}</span>
          </div>
        </td>
        <td>${formatKM(item.price)}</td>
        <td style="width:110px">
          <input type="number" min="1" value="${item.qty}" class="form-control form-control-sm cart-qty-input" data-id="${item.id}">
        </td>
        <td>${formatKM(item.price * item.qty)}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-danger cart-remove-btn" data-id="${item.id}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>`
      )
      .join("");

    body.querySelectorAll(".cart-qty-input").forEach((input) => {
      input.addEventListener("change", () =>
        updateQty(input.dataset.id, parseInt(input.value, 10) || 1)
      );
    });
    body.querySelectorAll(".cart-remove-btn").forEach((btn) => {
      btn.addEventListener("click", () => removeItem(btn.dataset.id));
    });

    const subtotalEl = document.getElementById("cartSubtotal");
    const totalEl = document.getElementById("cartTotal");
    if (subtotalEl) subtotalEl.textContent = formatKM(total());
    if (totalEl) totalEl.textContent = formatKM(total());
  }

  function renderCheckout() {
    const list = document.getElementById("checkoutItems");
    const totalEl = document.getElementById("checkoutTotal");
    if (!list) return;

    const items = getItems();
    if (items.length === 0) {
      list.innerHTML =
        '<p class="text-muted mb-0">Your cart is empty. <a href="#/shop">Go shopping</a>.</p>';
    } else {
      list.innerHTML = items
        .map(
          (i) => `
        <div class="d-flex justify-content-between align-items-center small mb-2">
          <span class="d-flex align-items-center gap-2">
            <span class="thumb-sm" style="width:32px;height:32px;"><img src="${i.image}" alt="${i.name}" loading="lazy"></span>
            ${i.name} <span class="text-muted">x${i.qty}</span>
          </span>
          <span>${formatKM(i.price * i.qty)}</span>
        </div>`
        )
        .join("");
    }
    if (totalEl) totalEl.textContent = formatKM(total());

    const form = document.getElementById("checkoutForm");
    if (form && !form.dataset.bound) {
      form.dataset.bound = "true";
      form.addEventListener("submit", handleCheckoutSubmit);
    }
  }

  function validateCheckoutForm(form) {
    const rules = [
      { field: "fullName", test: (v) => v.trim().length >= 2 },
      { field: "phone", test: (v) => /^[0-9+()\s-]{6,20}$/.test(v.trim()) },
      { field: "email", test: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) },
      { field: "street", test: (v) => v.trim().length >= 3 },
      { field: "city", test: (v) => v.trim().length > 0 },
      { field: "postcode", test: (v) => /^[0-9]{4,6}$/.test(v.trim()) },
    ];

    let firstInvalid = null;
    rules.forEach(({ field, test }) => {
      const input = form.elements[field];
      const isValid = test(input.value || "");
      input.classList.toggle("is-invalid", !isValid);
      input.classList.toggle("is-valid", isValid);
      if (!isValid && !firstInvalid) firstInvalid = input;
    });

    if (firstInvalid) {
      firstInvalid.focus();
      return false;
    }
    return true;
  }

  function handleCheckoutSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const items = getItems();
    if (items.length === 0) return;
    if (!validateCheckoutForm(form)) return;

    const order = {
      number: "PET-" + Math.floor(100000 + Math.random() * 900000),
      name: form.fullName.value,
      phone: form.phone.value,
      email: form.email.value,
      street: form.street.value,
      city: form.city.value,
      postcode: form.postcode.value,
      notes: form.notes.value,
      items,
      total: total(),
      date: new Date().toLocaleDateString("en-GB"),
    };

    localStorage.setItem(ORDER_KEY, JSON.stringify(order));
    clear();
    window.location.hash = "#/order-confirmation";
  }

  function renderConfirmation() {
    const box = document.getElementById("orderSummaryBox");
    if (!box) return;

    let order = null;
    try {
      order = JSON.parse(localStorage.getItem(ORDER_KEY));
    } catch {
      order = null;
    }

    if (!order) {
      box.innerHTML =
        '<p class="text-muted mb-0">No recent order found. <a href="#/shop">Go shopping</a>.</p>';
      return;
    }

    const numberEl = document.getElementById("orderNumber");
    const nameEl = document.getElementById("orderCustomerName");
    const addressEl = document.getElementById("orderAddress");
    const phoneEl = document.getElementById("orderPhone");

    if (numberEl) numberEl.textContent = order.number;
    if (nameEl) nameEl.textContent = order.name;
    if (addressEl)
      addressEl.textContent = `${order.street}, ${order.city} ${order.postcode}, Bosnia and Herzegovina`;
    if (phoneEl) phoneEl.textContent = order.phone;

    box.innerHTML =
      order.items
        .map(
          (i) => `
        <div class="d-flex justify-content-between align-items-center small mb-2">
          <span class="d-flex align-items-center gap-2">
            <span class="thumb-sm" style="width:32px;height:32px;"><img src="${i.image}" alt="${i.name}" loading="lazy"></span>
            ${i.name} <span class="text-muted">x${i.qty}</span>
          </span>
          <span>${formatKM(i.price * i.qty)}</span>
        </div>`
        )
        .join("") +
      `
      <hr>
      <div class="d-flex justify-content-between fw-bold">
        <span>Total (cash on delivery)</span><span>${formatKM(order.total)}</span>
      </div>`;
  }

  function onRoute(view) {
    updateBadge();
    bindAddToCartButtons();
    if (view === "cart") renderCartPage();
    if (view === "checkout") renderCheckout();
    if (view === "order-confirmation") renderConfirmation();
  }

  return { onRoute, updateBadge, bindAddToCartButtons, formatKM };
})();
