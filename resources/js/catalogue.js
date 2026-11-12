const API = '/api';
const PER_PAGE = 30;
const MAX_PAGES = 60;
const TOKEN_KEY = 'fr_api_token';

const state = {
    categories: [],
    allProducts: [],
    products: [],
    users: [],
    page: 1,
    loading: false,
    auth: {
        token: window.localStorage.getItem(TOKEN_KEY),
        user: null,
    },
    admin: {
        tab: 'produits',
        productId: null,
        categoryId: null,
    },
};

let requestId = 0;
let confirmResolver = null;

const els = {};

const money = new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
});

function el(id) {
    return document.getElementById(id);
}

function cacheElements() {
    els.adminNav = el('admin-nav');
    els.authGuest = el('auth-guest');
    els.authUser = el('auth-user');
    els.userMenuButton = el('user-menu-button');
    els.userMenu = el('user-menu');
    els.userName = el('user-name');
    els.userEmail = el('user-email');
    els.userRole = el('user-role');
    els.userAvatar = el('user-avatar');
    els.adminLink = el('admin-link');
    els.logoutButton = el('logout-button');
    els.navbar = el('navbar');
    els.mobileMenuButton = el('mobile-menu-button');
    els.mobileMenu = el('mobile-menu');
    els.mobileMenuIconOpen = el('mobile-menu-icon-open');
    els.mobileMenuIconClose = el('mobile-menu-icon-close');
    els.adminNavMobile = el('admin-nav-mobile');
    els.mobileAuthGuest = el('mobile-auth-guest');

    els.authModal = el('auth-modal');
    els.authTitle = el('auth-title');
    els.authError = el('auth-error');
    els.loginForm = el('login-form');
    els.loginEmail = el('login-email');
    els.loginPassword = el('login-password');
    els.registerForm = el('register-form');
    els.registerName = el('register-name');
    els.registerEmail = el('register-email');
    els.registerPassword = el('register-password');
    els.registerPasswordConfirmation = el('register-password-confirmation');

    els.search = el('search');
    els.categorie = el('categorie');
    els.prixMin = el('prix-min');
    els.prixMax = el('prix-max');
    els.stockMin = el('stock-min');
    els.promo = el('promo');
    els.sort = el('sort');
    els.reset = el('reset');
    els.chips = el('category-chips');
    els.grid = el('product-grid');
    els.loading = el('loading');
    els.empty = el('empty');
    els.error = el('error');
    els.pagination = el('pagination');
    els.pageInfo = el('page-info');
    els.resultCount = el('result-count');
    els.statProduits = el('stat-produits');
    els.statCategories = el('stat-categories');
    els.statPrix = el('stat-prix');
    els.statPromo = el('stat-promo');

    els.admin = el('admin');
    els.adminClose = el('admin-close');
    els.productForm = el('product-form');
    els.productId = el('product-id');
    els.productDesignation = el('product-designation');
    els.productCategorie = el('product-categorie');
    els.productPrix = el('product-prix');
    els.productPromo = el('product-promo');
    els.productStock = el('product-stock');
    els.productImage = el('product-image');
    els.productSubmit = el('product-submit');
    els.productCancel = el('product-cancel');
    els.categoryForm = el('category-form');
    els.categoryId = el('category-id');
    els.categoryDesignation = el('category-designation');
    els.categorySubmit = el('category-submit');
    els.categoryCancel = el('category-cancel');
    els.userForm = el('user-form');
    els.userNameInput = el('user-name-input');
    els.userEmailInput = el('user-email-input');
    els.userPasswordInput = el('user-password-input');
    els.userRoleInput = el('user-role-input');
    els.userSubmit = el('user-submit');
    els.adminProductsBody = el('admin-products-body');
    els.adminCategoriesBody = el('admin-categories-body');
    els.adminUsersBody = el('admin-users-body');

    els.confirmModal = el('confirm-modal');
    els.confirmMessage = el('confirm-message');
    els.confirmCancel = el('confirm-cancel');
    els.confirmOk = el('confirm-ok');
    els.toasts = el('toasts');
}

function esc(value) {
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function categoryId(iri) {
    if (!iri) return null;
    const parts = String(iri).split('/');
    const id = Number(parts[parts.length - 1]);
    return Number.isFinite(id) ? id : null;
}

function categoryName(iri) {
    const id = categoryId(iri);
    const found = state.categories.find((categorie) => Number(categorie.id) === id);
    return found ? found.designationC : 'Non classé';
}

function isAdmin() {
    return Boolean(state.auth.user && state.auth.user.role === 'admin');
}

async function api(path, options = {}) {
    const { method = 'GET', body, skipAuth = false } = options;
    const headers = { Accept: 'application/json' };

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
    }

    if (!skipAuth && state.auth.token) {
        headers.Authorization = `Bearer ${state.auth.token}`;
    }

    const response = await fetch(API + path, {
        method,
        headers,
        body: body === undefined ? undefined : JSON.stringify(body),
    });

    const text = await response.text();
    let data = null;

    if (text) {
        try {
            data = JSON.parse(text);
        } catch {
            data = text;
        }
    }

    if (!response.ok) {
        const error = new Error(`Erreur HTTP ${response.status}`);
        error.status = response.status;
        error.data = data;
        throw error;
    }

    return data;
}

function errorMessage(error) {
    const data = error && error.data;

    if (data && Array.isArray(data.violations) && data.violations.length) {
        return data.violations.map((violation) => violation.message).join(' ');
    }

    if (data && (data.detail || data.description || data.message)) {
        return data.detail || data.description || data.message;
    }

    return (error && error.message) || 'Une erreur est survenue.';
}

function debounce(fn, delay) {
    let timer = null;
    return function (...args) {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => fn.apply(null, args), delay);
    };
}

function buildParams() {
    const params = new URLSearchParams();
    const search = els.search.value.trim();

    if (search) params.set('designationP', search);
    if (els.categorie.value) params.set('categorieId', els.categorie.value);

    const min = els.prixMin.value.trim();
    if (min !== '') params.set('prixHt[gte]', min);

    const max = els.prixMax.value.trim();
    if (max !== '') params.set('prixHt[lte]', max);

    const stock = els.stockMin.value.trim();
    if (stock !== '') params.set('stockP[gte]', stock);

    if (els.promo.checked) params.set('promotionP[gt]', '0');

    if (els.sort.value) {
        const [property, direction] = els.sort.value.split(':');
        params.set(`sort[${property}]`, direction);
    }

    return params;
}

async function fetchAllProducts(params = new URLSearchParams()) {
    const results = [];

    for (let page = 1; page <= MAX_PAGES; page++) {
        const query = new URLSearchParams(params);
        query.set('page', page);

        const data = await api(`/produits?${query.toString()}`);

        if (!Array.isArray(data) || data.length === 0) break;

        results.push(...data);

        if (data.length < PER_PAGE) break;
    }

    return results;
}

async function fetchUsers() {
    const results = [];

    for (let page = 1; page <= MAX_PAGES; page++) {
        const data = await api(`/users?page=${page}`);

        if (!Array.isArray(data) || data.length === 0) break;

        results.push(...data);

        if (data.length < PER_PAGE) break;
    }

    return results;
}

function populateCategoryOptions() {
    const filterValue = els.categorie.value;
    const adminValue = els.productCategorie.value;

    const filterOptions = state.categories
        .map((categorie) => `<option value="${categorie.id}">${esc(categorie.designationC)}</option>`)
        .join('');

    els.categorie.innerHTML = `<option value="">Toutes les catégories</option>${filterOptions}`;
    els.categorie.value = filterValue;

    els.productCategorie.innerHTML = `<option value="">Choisir une catégorie</option>${filterOptions}`;
    els.productCategorie.value = adminValue;
}

async function refreshCatalogue() {
    state.categories = await api('/categories');
    populateCategoryOptions();
    state.allProducts = await fetchAllProducts(new URLSearchParams());
    await applyFilters();
    renderChips();
}

async function refreshAll() {
    await refreshCatalogue();

    if (isAdmin()) {
        await loadAdmin();
    }
}

function setLoading(loading) {
    state.loading = loading;
    els.loading.classList.toggle('hidden', !loading);

    if (loading) {
        els.grid.classList.add('hidden');
        els.empty.classList.add('hidden');
        els.error.classList.add('hidden');
    }
}

function showError(message) {
    els.error.textContent = message;
    els.error.classList.remove('hidden');
}

async function applyFilters(resetPage = true) {
    const current = ++requestId;

    if (resetPage) state.page = 1;

    setLoading(true);

    try {
        const products = await fetchAllProducts(buildParams());

        if (current !== requestId) return;

        state.products = products;
        render();
    } catch (error) {
        if (current === requestId) {
            showError(errorMessage(error));
        }
    } finally {
        if (current === requestId) setLoading(false);
    }
}

function animateValue(element, target, formatter) {
    const from = Number(element.dataset.value || 0);
    const to = Number(target) || 0;

    element.dataset.value = String(to);

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        element.textContent = formatter(to);
        return;
    }

    const duration = 550;
    const start = performance.now();

    function frame(now) {
        const progress = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - progress, 3);

        element.textContent = formatter(from + (to - from) * eased);

        if (progress < 1) requestAnimationFrame(frame);
    }

    requestAnimationFrame(frame);
}

function renderStats() {
    const products = state.products;
    const average = products.length
        ? products.reduce((sum, product) => sum + Number(product.prixHt || 0), 0) / products.length
        : 0;

    animateValue(els.statProduits, products.length, (value) => String(Math.round(value)));
    animateValue(els.statCategories, state.categories.length, (value) => String(Math.round(value)));
    animateValue(els.statPrix, average, (value) => money.format(value));
    animateValue(
        els.statPromo,
        products.filter((product) => Number(product.promotionP) > 0).length,
        (value) => String(Math.round(value))
    );
}

function renderChips() {
    if (!els.chips) return;

    const counts = new Map();

    state.allProducts.forEach((product) => {
        const id = categoryId(product.categorie);
        counts.set(id, (counts.get(id) || 0) + 1);
    });

    const chips = [chipHtml(null, 'Toutes', state.allProducts.length)];

    state.categories.forEach((categorie) => {
        chips.push(chipHtml(categorie.id, categorie.designationC, counts.get(categorie.id) || 0));
    });

    els.chips.innerHTML = chips.join('');
}

function chipHtml(id, label, count) {
    const active = String(els.categorie.value) === String(id === null ? '' : id);
    const classes = active
        ? 'border-indigo-400/60 bg-gradient-to-r from-indigo-500/30 to-fuchsia-500/25 text-white shadow-lg shadow-indigo-500/20'
        : 'border-white/10 bg-white/[0.03] text-slate-400 hover:border-indigo-400/40 hover:text-white';

    return `
        <button type="button" data-category="${id === null ? '' : id}"
            class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium backdrop-blur transition ${classes}">
            <span>${esc(label)}</span>
            <span class="rounded-full bg-black/20 px-1.5 py-0.5 text-[10px] font-semibold">${count}</span>
        </button>`;
}

function productCard(product, index) {
    const promo = Number(product.promotionP) || 0;
    const stock = Number(product.stockP) || 0;
    const initial = String(product.designationP || '?').trim().charAt(0).toUpperCase();

    const image = product.imageP
        ? `<img src="${esc(product.imageP)}" alt="" loading="lazy" referrerpolicy="no-referrer"
                class="absolute inset-0 h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-110 group-hover:opacity-100"
                onerror="this.style.opacity='0'">`
        : '';

    const promoBadge = promo > 0
        ? `<span class="rounded-full border border-rose-400/30 bg-rose-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-rose-300 backdrop-blur-md">-${promo}%</span>`
        : '';

    const stockClass = stock <= 0
        ? 'text-rose-300'
        : stock < 10
            ? 'text-amber-300'
            : 'text-emerald-300';

    return `
        <article class="group glass card-hover reveal overflow-hidden rounded-3xl" style="animation-delay:${Math.min(index * 45, 400)}ms">
            <div class="relative aspect-[4/3] overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/25 via-violet-500/15 to-cyan-400/20"></div>
                <span class="absolute inset-0 grid place-items-center font-display text-5xl font-semibold text-white/15">${esc(initial)}</span>
                ${image}
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
                <div class="absolute left-4 right-4 top-4 flex items-start justify-between gap-2">
                    <span class="rounded-full border border-white/15 bg-black/30 px-2.5 py-1 text-[10px] font-medium text-slate-200 backdrop-blur-md">${esc(categoryName(product.categorie))}</span>
                    ${promoBadge}
                </div>
                <div class="absolute inset-x-4 bottom-4">
                    <h3 class="font-display text-base font-semibold leading-snug text-white">${esc(product.designationP)}</h3>
                    <p class="mt-0.5 text-[11px] text-slate-400">Réf. #${esc(product.id)}</p>
                </div>
            </div>
            <div class="flex items-end justify-between gap-3 p-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">Prix HT</p>
                    <p class="mt-0.5 font-display text-xl font-semibold text-white">${money.format(Number(product.prixHt) || 0)}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">Stock</p>
                    <p class="mt-0.5 text-sm font-semibold ${stockClass}">${stock <= 0 ? 'Rupture' : `${stock} unités`}</p>
                </div>
            </div>
        </article>`;
}

function renderProducts() {
    const start = (state.page - 1) * PER_PAGE;
    const items = state.products.slice(start, start + PER_PAGE);

    els.resultCount.textContent = `${state.products.length} produit${state.products.length > 1 ? 's' : ''}`;

    if (items.length === 0) {
        els.grid.innerHTML = '';
        els.grid.classList.add('hidden');
        els.empty.classList.remove('hidden');
        return;
    }

    els.empty.classList.add('hidden');
    els.grid.classList.remove('hidden');
    els.grid.classList.add('grid');
    els.grid.innerHTML = items.map((product, index) => productCard(product, index)).join('');
}

function renderPagination() {
    const totalPages = Math.max(1, Math.ceil(state.products.length / PER_PAGE));

    els.pageInfo.textContent = state.products.length
        ? `Page ${state.page} sur ${totalPages} · ${PER_PAGE} par page`
        : '';

    if (state.products.length === 0) {
        els.pagination.innerHTML = '';
        return;
    }

    const base = 'inline-flex h-9 min-w-9 items-center justify-center rounded-xl border px-3 text-xs font-semibold transition';
    const enabled = 'border-white/10 bg-white/5 text-slate-300 hover:border-indigo-400/40 hover:text-white';
    const disabled = 'cursor-not-allowed border-white/5 bg-white/[0.02] text-slate-600';
    const current = 'border-indigo-400/50 bg-gradient-to-br from-indigo-500/40 to-fuchsia-500/30 text-white shadow-lg shadow-indigo-500/20';

    const buttons = [];
    const windowStart = Math.max(1, state.page - 2);
    const windowEnd = Math.min(totalPages, windowStart + 4);

    buttons.push(`
        <button type="button" data-page="${state.page - 1}" class="${base} ${state.page <= 1 ? disabled : enabled}"
            ${state.page <= 1 ? 'disabled' : ''}>Précédent</button>`);

    if (windowStart > 1) {
        buttons.push(`<button type="button" data-page="1" class="${base} ${enabled}">1</button>`);
        if (windowStart > 2) buttons.push('<span class="px-1 text-slate-500">…</span>');
    }

    for (let page = windowStart; page <= windowEnd; page++) {
        buttons.push(`
            <button type="button" data-page="${page}" class="${base} ${page === state.page ? current : enabled}">${page}</button>`);
    }

    if (windowEnd < totalPages) {
        if (windowEnd < totalPages - 1) buttons.push('<span class="px-1 text-slate-500">…</span>');
        buttons.push(`<button type="button" data-page="${totalPages}" class="${base} ${enabled}">${totalPages}</button>`);
    }

    buttons.push(`
        <button type="button" data-page="${state.page + 1}" class="${base} ${state.page >= totalPages ? disabled : enabled}"
            ${state.page >= totalPages ? 'disabled' : ''}>Suivant</button>`);

    els.pagination.innerHTML = buttons.join('');
}

function render() {
    renderStats();
    renderProducts();
    renderPagination();
    renderChips();
}

function renderAuth() {
    const user = state.auth.user;
    const logged = Boolean(user);
    const admin = logged && user.role === 'admin';

    els.authGuest.classList.toggle('hidden', logged);
    els.authUser.classList.toggle('hidden', !logged);
    els.adminNav.classList.toggle('hidden', !admin);
    els.adminNav.classList.toggle('inline-flex', admin);
    els.adminNavMobile.classList.toggle('hidden', !admin);
    els.adminLink.classList.toggle('hidden', !admin);
    els.mobileAuthGuest.classList.toggle('hidden', logged);

    if (logged) {
        els.userName.textContent = user.name || 'Utilisateur';
        els.userEmail.textContent = user.email || '';
        els.userRole.textContent = user.role === 'admin' ? 'Administrateur' : 'Membre';
        els.userAvatar.textContent = String(user.name || '?').trim().charAt(0).toUpperCase();
    } else {
        els.userMenu.classList.add('hidden');
        els.admin.classList.add('hidden');
        closeMobileMenu();
    }
}

function setAuth(token, user) {
    state.auth.token = token || null;
    state.auth.user = user || null;

    if (token) {
        window.localStorage.setItem(TOKEN_KEY, token);
    } else {
        window.localStorage.removeItem(TOKEN_KEY);
    }

    renderAuth();
}

async function restoreAuth() {
    if (!state.auth.token) {
        renderAuth();
        return;
    }

    try {
        state.auth.user = await api('/me');
        renderAuth();
    } catch (error) {
        if (error.status === 401 || error.status === 403) {
            setAuth(null, null);
            return;
        }

        throw error;
    }
}

function openAuth(mode = 'login') {
    els.authModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    els.authError.classList.add('hidden');
    switchAuthTab(mode);

    window.setTimeout(() => {
        (mode === 'register' ? els.registerName : els.loginEmail).focus();
    }, 60);
}

function closeAuth() {
    els.authModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    els.authError.classList.add('hidden');
}

function setNavbarScrolled() {
    if (!els.navbar) return;
    els.navbar.classList.toggle('is-scrolled', window.scrollY > 6);
}

function setMobileMenu(open) {
    els.mobileMenu.classList.toggle('hidden', !open);
    els.mobileMenuIconOpen.classList.toggle('hidden', open);
    els.mobileMenuIconClose.classList.toggle('hidden', !open);
    els.mobileMenuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
    els.mobileMenuButton.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
}

function closeMobileMenu() {
    if (!els.mobileMenu) return;
    setMobileMenu(false);
}

function toggleMobileMenu() {
    setMobileMenu(els.mobileMenu.classList.contains('hidden'));
}

function switchAuthTab(mode) {
    els.loginForm.classList.toggle('hidden', mode !== 'login');
    els.registerForm.classList.toggle('hidden', mode !== 'register');
    els.authTitle.textContent = mode === 'login' ? 'Connexion' : 'Créer un compte';

    document.querySelectorAll('[data-auth-tab]').forEach((button) => {
        button.classList.toggle('is-active', button.dataset.authTab === mode);
    });
}

async function handleLogin(event) {
    event.preventDefault();

    els.authError.classList.add('hidden');
    const submit = els.loginForm.querySelector('button[type="submit"]');
    submit.disabled = true;

    try {
        const data = await api('/login', {
            method: 'POST',
            skipAuth: true,
            body: {
                email: els.loginEmail.value.trim(),
                password: els.loginPassword.value,
            },
        });

        setAuth(data.token, data.user);
        els.loginForm.reset();
        closeAuth();
        toast(`Bienvenue, ${data.user.name || ''}`.trim());

        if (data.user.role === 'admin') {
            await openAdmin();
        }
    } catch (error) {
        els.authError.textContent = errorMessage(error);
        els.authError.classList.remove('hidden');
    } finally {
        submit.disabled = false;
    }
}

async function handleRegister(event) {
    event.preventDefault();

    els.authError.classList.add('hidden');
    const submit = els.registerForm.querySelector('button[type="submit"]');
    submit.disabled = true;

    try {
        const data = await api('/register', {
            method: 'POST',
            skipAuth: true,
            body: {
                name: els.registerName.value.trim(),
                email: els.registerEmail.value.trim(),
                password: els.registerPassword.value,
                password_confirmation: els.registerPasswordConfirmation.value,
            },
        });

        setAuth(data.token, data.user);
        els.registerForm.reset();
        closeAuth();
        toast(`Compte créé, bienvenue ${data.user.name || ''}`.trim());
    } catch (error) {
        els.authError.textContent = errorMessage(error);
        els.authError.classList.remove('hidden');
    } finally {
        submit.disabled = false;
    }
}

async function handleLogout() {
    try {
        await api('/logout', { method: 'POST' });
    } catch {
        // Le token est peut-être déjà expiré : on nettoie quand même la session.
    }

    setAuth(null, null);
    closeAdmin();
    toast('Vous êtes déconnecté.');
}

async function openAdmin() {
    if (!isAdmin()) {
        toast('Accès réservé aux administrateurs.', 'error');
        return;
    }

    els.admin.classList.remove('hidden');
    setAdminTab(state.admin.tab);
    await loadAdmin();
    els.admin.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function closeAdmin() {
    els.admin.classList.add('hidden');
    document.getElementById('catalogue').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function setAdminTab(tab) {
    state.admin.tab = tab;

    document.querySelectorAll('[data-admin-tab]').forEach((button) => {
        button.classList.toggle('is-active', button.dataset.adminTab === tab);
    });

    document.querySelectorAll('[data-admin-panel]').forEach((panel) => {
        panel.classList.toggle('hidden', panel.dataset.adminPanel !== tab);
    });
}

async function loadAdmin() {
    if (!isAdmin()) return;

    const [categories, products, users] = await Promise.all([
        api('/categories'),
        fetchAllProducts(new URLSearchParams()),
        fetchUsers(),
    ]);

    state.categories = categories;
    state.allProducts = products;
    state.users = users;

    populateCategoryOptions();
    renderAdminProducts();
    renderAdminCategories();
    renderAdminUsers();
    renderChips();
}

function renderAdminProducts() {
    els.adminProductsBody.innerHTML = state.allProducts.map((product) => {
        const stock = Number(product.stockP) || 0;

        return `
            <tr class="transition hover:bg-white/[0.03]">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-indigo-500/30 to-fuchsia-500/20 text-xs font-bold text-indigo-200">
                            ${esc(String(product.designationP || '?').charAt(0).toUpperCase())}
                        </span>
                        <div>
                            <p class="font-medium text-slate-100">${esc(product.designationP)}</p>
                            <p class="text-[11px] text-slate-500">#${esc(product.id)}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-slate-400">${esc(categoryName(product.categorie))}</td>
                <td class="px-4 py-3 text-slate-200">${money.format(Number(product.prixHt) || 0)}</td>
                <td class="px-4 py-3 text-slate-300">${stock}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <button type="button" data-edit-product="${product.id}"
                            class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-slate-200 transition hover:border-indigo-400/40 hover:text-white">
                            Modifier
                        </button>
                        <button type="button" data-delete-product="${product.id}"
                            class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-2.5 py-1 text-[11px] font-medium text-rose-300 transition hover:bg-rose-500/20">
                            Supprimer
                        </button>
                    </div>
                </td>
            </tr>`;
    }).join('') || '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Aucun produit.</td></tr>';
}

function renderAdminCategories() {
    els.adminCategoriesBody.innerHTML = state.categories.map((categorie) => {
        const count = state.allProducts.filter(
            (product) => categoryId(product.categorie) === Number(categorie.id)
        ).length;

        return `
            <tr class="transition hover:bg-white/[0.03]">
                <td class="px-4 py-3">
                    <p class="font-medium text-slate-100">${esc(categorie.designationC)}</p>
                    <p class="text-[11px] text-slate-500">#${esc(categorie.id)}</p>
                </td>
                <td class="px-4 py-3 text-slate-300">${count}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <button type="button" data-edit-category="${categorie.id}"
                            class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-slate-200 transition hover:border-indigo-400/40 hover:text-white">
                            Modifier
                        </button>
                        <button type="button" data-delete-category="${categorie.id}"
                            class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-2.5 py-1 text-[11px] font-medium text-rose-300 transition hover:bg-rose-500/20">
                            Supprimer
                        </button>
                    </div>
                </td>
            </tr>`;
    }).join('') || '<tr><td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Aucune catégorie.</td></tr>';
}

function renderAdminUsers() {
    els.adminUsersBody.innerHTML = state.users.map((user) => {
        const isSelf = state.auth.user && Number(user.id) === Number(state.auth.user.id);
        const roleBadge = user.role === 'admin'
            ? 'border-indigo-400/30 bg-indigo-500/15 text-indigo-200'
            : 'border-white/10 bg-white/5 text-slate-300';

        return `
            <tr class="transition hover:bg-white/[0.03]">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-indigo-500/30 to-fuchsia-500/20 text-xs font-bold text-indigo-200">
                            ${esc(String(user.name || '?').charAt(0).toUpperCase())}
                        </span>
                        <div>
                            <p class="font-medium text-slate-100">${esc(user.name)} ${isSelf ? '<span class="text-[10px] text-slate-500">(vous)</span>' : ''}</p>
                            <p class="text-[11px] text-slate-500">${esc(user.email)}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide ${roleBadge}">${user.role === 'admin' ? 'Admin' : 'Utilisateur'}</span>
                </td>
                <td class="px-4 py-3">
                    <select data-user-role="${user.id}" ${isSelf ? 'disabled' : ''} class="input-dark input-compact">
                        <option value="user" ${user.role === 'user' ? 'selected' : ''}>Utilisateur</option>
                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Administrateur</option>
                    </select>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end">
                        <button type="button" data-delete-user="${user.id}" ${isSelf ? 'disabled' : ''}
                            class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-2.5 py-1 text-[11px] font-medium text-rose-300 transition hover:bg-rose-500/20 disabled:cursor-not-allowed disabled:opacity-40">
                            Supprimer
                        </button>
                    </div>
                </td>
            </tr>`;
    }).join('') || '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Aucun utilisateur.</td></tr>';
}

function editProduct(id) {
    const product = state.allProducts.find((item) => Number(item.id) === Number(id));
    if (!product) return;

    state.admin.productId = product.id;
    els.productId.value = product.id;
    els.productDesignation.value = product.designationP || '';
    els.productCategorie.value = categoryId(product.categorie) || '';
    els.productPrix.value = product.prixHt ?? '';
    els.productPromo.value = product.promotionP ?? 0;
    els.productStock.value = product.stockP ?? 0;
    els.productImage.value = product.imageP || '';
    els.productSubmit.textContent = 'Enregistrer les modifications';
    els.productCancel.classList.remove('hidden');
    els.productForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function resetProductForm() {
    els.productForm.reset();
    els.productId.value = '';
    els.productSubmit.textContent = 'Ajouter le produit';
    els.productCancel.classList.add('hidden');
    state.admin.productId = null;
}

function editCategory(id) {
    const categorie = state.categories.find((item) => Number(item.id) === Number(id));
    if (!categorie) return;

    state.admin.categoryId = categorie.id;
    els.categoryId.value = categorie.id;
    els.categoryDesignation.value = categorie.designationC || '';
    els.categorySubmit.textContent = 'Enregistrer les modifications';
    els.categoryCancel.classList.remove('hidden');
    els.categoryForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function resetCategoryForm() {
    els.categoryForm.reset();
    els.categoryId.value = '';
    els.categorySubmit.textContent = 'Ajouter la catégorie';
    els.categoryCancel.classList.add('hidden');
    state.admin.categoryId = null;
}

async function handleProductSubmit(event) {
    event.preventDefault();

    const id = els.productId.value;
    const categorieId = els.productCategorie.value;

    if (!categorieId) {
        toast('Choisissez une catégorie.', 'error');
        return;
    }

    const body = {
        designationP: els.productDesignation.value.trim(),
        categorie: `/api/categories/${categorieId}`,
        prixHt: Number(els.productPrix.value),
        promotionP: els.productPromo.value === '' ? 0 : Number(els.productPromo.value),
        stockP: Number(els.productStock.value),
        imageP: els.productImage.value.trim() || null,
    };

    try {
        if (id) {
            await api(`/produits/${id}`, { method: 'PATCH', body });
            toast('Produit mis à jour.');
        } else {
            await api('/produits', { method: 'POST', body });
            toast('Produit ajouté.');
        }

        resetProductForm();
        await refreshAll();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

async function handleCategorySubmit(event) {
    event.preventDefault();

    const id = els.categoryId.value;
    const designationC = els.categoryDesignation.value.trim();

    if (!designationC) {
        toast('La désignation est obligatoire.', 'error');
        return;
    }

    try {
        if (id) {
            await api(`/categories/${id}`, { method: 'PATCH', body: { designationC } });
            toast('Catégorie mise à jour.');
        } else {
            await api('/categories', { method: 'POST', body: { designationC } });
            toast('Catégorie ajoutée.');
        }

        resetCategoryForm();
        await refreshAll();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

async function handleUserSubmit(event) {
    event.preventDefault();

    const body = {
        name: els.userNameInput.value.trim(),
        email: els.userEmailInput.value.trim(),
        password: els.userPasswordInput.value,
        role: els.userRoleInput.value,
    };

    try {
        await api('/admin/users', { method: 'POST', body });
        els.userForm.reset();
        toast('Utilisateur créé.');
        await loadAdmin();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

async function handleUserRoleChange(select) {
    try {
        await api(`/admin/users/${select.dataset.userRole}`, {
            method: 'PATCH',
            body: { role: select.value },
        });
        toast('Rôle mis à jour.');
        await loadAdmin();
    } catch (error) {
        toast(errorMessage(error), 'error');
        await loadAdmin();
    }
}

async function deleteProduct(id) {
    const product = state.allProducts.find((item) => Number(item.id) === Number(id));
    const label = product ? product.designationP : `#${id}`;

    if (!(await confirmAction(`Supprimer le produit « ${label} » ?`))) return;

    try {
        await api(`/produits/${id}`, { method: 'DELETE' });
        toast('Produit supprimé.');
        await refreshAll();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

async function deleteCategory(id) {
    const categorie = state.categories.find((item) => Number(item.id) === Number(id));
    const label = categorie ? categorie.designationC : `#${id}`;

    if (!(await confirmAction(`Supprimer la catégorie « ${label} » ? Les produits associés seront détachés.`))) return;

    try {
        await api(`/categories/${id}`, { method: 'DELETE' });
        toast('Catégorie supprimée.');

        if (String(els.categorie.value) === String(id)) {
            els.categorie.value = '';
        }

        await refreshAll();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

async function deleteUser(id) {
    const user = state.users.find((item) => Number(item.id) === Number(id));
    const label = user ? `${user.name} (${user.email})` : `#${id}`;

    if (!(await confirmAction(`Supprimer l'utilisateur « ${label} » ?`))) return;

    try {
        await api(`/admin/users/${id}`, { method: 'DELETE' });
        toast('Utilisateur supprimé.');
        await loadAdmin();
    } catch (error) {
        toast(errorMessage(error), 'error');
    }
}

function toast(message, type = 'success') {
    if (!els.toasts) return;

    const node = document.createElement('div');
    const tone = type === 'error'
        ? 'border-rose-400/30 bg-rose-500/10 text-rose-200'
        : 'border-emerald-400/30 bg-emerald-500/10 text-emerald-200';
    const dot = type === 'error' ? 'bg-rose-400' : 'bg-emerald-400';

    node.className = `toast glass-strong flex items-start gap-2 rounded-2xl border px-4 py-3 text-sm ${tone}`;
    node.innerHTML = `<span class="mt-1.5 h-2 w-2 shrink-0 rounded-full ${dot}"></span><span class="flex-1">${esc(message)}</span>`;

    els.toasts.appendChild(node);

    window.setTimeout(() => {
        node.style.opacity = '0';
        node.style.transform = 'translateX(16px)';
        node.style.transition = 'all .3s ease';
        window.setTimeout(() => node.remove(), 320);
    }, 3600);
}

function confirmAction(message) {
    els.confirmMessage.textContent = message;
    els.confirmModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    return new Promise((resolve) => {
        confirmResolver = resolve;
    });
}

function closeConfirm(result) {
    els.confirmModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    if (confirmResolver) {
        confirmResolver(result);
        confirmResolver = null;
    }
}

function bindEvents() {
    els.search.addEventListener('input', debounce(() => applyFilters(), 350));
    els.prixMin.addEventListener('input', debounce(() => applyFilters(), 400));
    els.prixMax.addEventListener('input', debounce(() => applyFilters(), 400));
    els.stockMin.addEventListener('input', debounce(() => applyFilters(), 400));
    els.categorie.addEventListener('change', () => applyFilters());
    els.promo.addEventListener('change', () => applyFilters());
    els.sort.addEventListener('change', () => applyFilters());

    els.reset.addEventListener('click', () => {
        els.search.value = '';
        els.categorie.value = '';
        els.prixMin.value = '';
        els.prixMax.value = '';
        els.stockMin.value = '';
        els.promo.checked = false;
        els.sort.value = '';
        applyFilters();
    });

    if (els.chips) {
        els.chips.addEventListener('click', (event) => {
            const button = event.target.closest('[data-category]');
            if (!button) return;

            els.categorie.value = button.dataset.category;
            applyFilters();
        });
    }

    els.pagination.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');
        if (!button || button.disabled) return;

        const totalPages = Math.max(1, Math.ceil(state.products.length / PER_PAGE));
        const page = Number(button.dataset.page);

        if (!Number.isFinite(page) || page < 1 || page > totalPages || page === state.page) return;

        state.page = page;
        render();
        els.grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    els.adminNav.addEventListener('click', openAdmin);
    els.adminLink.addEventListener('click', openAdmin);
    els.adminClose.addEventListener('click', closeAdmin);
    els.adminNavMobile.addEventListener('click', () => {
        closeMobileMenu();
        openAdmin();
    });

    window.addEventListener('scroll', setNavbarScrolled, { passive: true });
    setNavbarScrolled();

    els.mobileMenuButton.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleMobileMenu();
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) closeMobileMenu();
    });

    document.querySelectorAll('[data-admin-tab]').forEach((button) => {
        button.addEventListener('click', () => setAdminTab(button.dataset.adminTab));
    });

    els.productForm.addEventListener('submit', handleProductSubmit);
    els.productCancel.addEventListener('click', resetProductForm);
    els.categoryForm.addEventListener('submit', handleCategorySubmit);
    els.categoryCancel.addEventListener('click', resetCategoryForm);
    els.userForm.addEventListener('submit', handleUserSubmit);

    els.adminProductsBody.addEventListener('click', (event) => {
        const edit = event.target.closest('[data-edit-product]');
        if (edit) return editProduct(edit.dataset.editProduct);

        const remove = event.target.closest('[data-delete-product]');
        if (remove) return deleteProduct(remove.dataset.deleteProduct);
    });

    els.adminCategoriesBody.addEventListener('click', (event) => {
        const edit = event.target.closest('[data-edit-category]');
        if (edit) return editCategory(edit.dataset.editCategory);

        const remove = event.target.closest('[data-delete-category]');
        if (remove) return deleteCategory(remove.dataset.deleteCategory);
    });

    els.adminUsersBody.addEventListener('change', (event) => {
        const select = event.target.closest('[data-user-role]');
        if (select) handleUserRoleChange(select);
    });

    els.adminUsersBody.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-delete-user]');
        if (remove) deleteUser(remove.dataset.deleteUser);
    });

    document.querySelectorAll('[data-open-auth]').forEach((button) => {
        button.addEventListener('click', () => openAuth(button.dataset.openAuth));
    });

    document.querySelectorAll('[data-close-auth]').forEach((button) => {
        button.addEventListener('click', closeAuth);
    });

    document.querySelectorAll('[data-auth-tab]').forEach((button) => {
        button.addEventListener('click', () => switchAuthTab(button.dataset.authTab));
    });

    els.loginForm.addEventListener('submit', handleLogin);
    els.registerForm.addEventListener('submit', handleRegister);
    els.logoutButton.addEventListener('click', handleLogout);

    els.userMenuButton.addEventListener('click', (event) => {
        event.stopPropagation();
        els.userMenu.classList.toggle('hidden');
    });

    els.userMenu.addEventListener('click', (event) => event.stopPropagation());
    document.addEventListener('click', () => {
        els.userMenu.classList.add('hidden');
        closeMobileMenu();
    });

    els.confirmOk.addEventListener('click', () => closeConfirm(true));
    els.confirmCancel.addEventListener('click', () => closeConfirm(false));

    document.querySelectorAll('[data-close-confirm]').forEach((element) => {
        element.addEventListener('click', () => closeConfirm(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        if (!els.authModal.classList.contains('hidden')) closeAuth();
        if (!els.confirmModal.classList.contains('hidden')) closeConfirm(false);
        els.userMenu.classList.add('hidden');
        closeMobileMenu();
    });
}

async function init() {
    cacheElements();
    bindEvents();
    setAdminTab('produits');
    setLoading(true);

    try {
        await restoreAuth();
        await refreshCatalogue();

        if (isAdmin() && window.location.hash === '#admin') {
            await openAdmin();
        }
    } catch (error) {
        showError(errorMessage(error));
        setLoading(false);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
