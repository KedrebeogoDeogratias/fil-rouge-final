<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Catalogue premium — API Laravel, API Platform et Sanctum.">
        <title>Fil Rouge — Catalogue premium</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|sora:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body id="top" class="min-h-screen overflow-x-hidden bg-[#05060a] font-sans text-slate-100 antialiased">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="animate-float-slow absolute -left-40 -top-40 h-[38rem] w-[38rem] rounded-full bg-indigo-600/25 blur-[150px]"></div>
            <div class="animate-float-slower absolute -right-48 top-1/4 h-[34rem] w-[34rem] rounded-full bg-fuchsia-600/20 blur-[150px]"></div>
            <div class="animate-float-slow absolute bottom-0 left-1/3 h-[30rem] w-[30rem] rounded-full bg-cyan-500/15 blur-[150px]"></div>
            <div class="bg-grid absolute inset-0 opacity-[0.35]"></div>
            <div class="bg-noise absolute inset-0 opacity-[0.04]"></div>
        </div>

        <header class="sticky top-0 z-40 px-3 pt-3 sm:px-4 sm:pt-4">
            <div class="relative mx-auto max-w-7xl">
                <div id="navbar" class="navbar-glass flex items-center justify-between gap-2 rounded-2xl px-3 py-2 sm:gap-3 sm:px-4 sm:py-2.5">
                    <a href="#top" class="flex min-w-0 items-center gap-2.5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-cyan-400 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">FR</span>
                        <span class="min-w-0">
                            <span class="block truncate font-display text-sm font-semibold text-white">Fil Rouge</span>
                            <span class="hidden text-[10px] uppercase tracking-[0.22em] text-slate-500 sm:block">Premium API</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-1 text-sm lg:flex" aria-label="Navigation principale">
                        <a href="#catalogue" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-white/5 hover:text-white">Catalogue</a>
                        <button id="admin-nav" type="button" class="hidden items-center gap-2 rounded-lg px-3 py-2 text-indigo-200 transition hover:bg-white/5 hover:text-white">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-400 shadow-[0_0_10px_2px_rgba(129,140,248,0.8)]"></span>
                            Administration
                        </button>
                        <a href="/api/docs" target="_blank" rel="noopener" class="rounded-lg px-3 py-2 text-slate-400 transition hover:bg-white/5 hover:text-white">API</a>
                    </nav>

                    <div class="flex shrink-0 items-center gap-2">
                        <div class="hidden items-center gap-2 lg:flex">
                            <div id="auth-guest" class="flex items-center gap-2">
                                <button type="button" data-open-auth="login" class="btn-ghost">Connexion</button>
                                <button type="button" data-open-auth="register" class="btn-primary whitespace-nowrap">Créer un compte</button>
                            </div>
                        </div>

                        <div id="auth-user" class="relative hidden">
                            <button id="user-menu-button" type="button" class="glass flex items-center gap-2 rounded-xl px-2.5 py-2 text-left">
                                <span id="user-avatar" class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-xs font-bold text-white">A</span>
                                <span class="hidden sm:block">
                                    <span id="user-name" class="block max-w-[9rem] truncate text-xs font-semibold text-white"></span>
                                    <span id="user-role" class="block text-[10px] font-medium uppercase tracking-[0.16em] text-indigo-300"></span>
                                </span>
                                <svg class="hidden h-3.5 w-3.5 text-slate-500 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>

                            <div id="user-menu" class="glass-strong absolute right-0 mt-2 hidden w-60 overflow-hidden rounded-2xl p-1.5">
                                <p id="user-email" class="truncate px-3 py-2 text-[11px] text-slate-400"></p>
                                <button id="admin-link" type="button" class="hidden w-full rounded-lg px-3 py-2 text-left text-sm text-indigo-200 transition hover:bg-white/5">
                                    Espace administrateur
                                </button>
                                <button id="logout-button" type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-rose-300 transition hover:bg-rose-500/10">
                                    Se déconnecter
                                </button>
                            </div>
                        </div>

                        <button id="mobile-menu-button" type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-white/20 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-menu">
                            <svg id="mobile-menu-icon-open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                            </svg>
                            <svg id="mobile-menu-icon-close" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="mobile-menu" class="glass-strong absolute left-0 right-0 top-full z-50 mt-2 hidden rounded-2xl p-2 lg:hidden">
                    <nav class="flex flex-col gap-0.5" aria-label="Navigation mobile">
                        <a href="#catalogue" data-close-mobile class="mobile-link">
                            <span>Catalogue</span>
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <button id="admin-nav-mobile" type="button" data-close-mobile class="mobile-link hidden">
                            <span class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400 shadow-[0_0_10px_2px_rgba(129,140,248,0.8)]"></span>
                                Administration
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <a href="/api/docs" target="_blank" rel="noopener" class="mobile-link">
                            <span>Documentation API</span>
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M14 5h5v5M19 5l-8 8M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div id="mobile-auth-guest" class="mt-1 flex flex-col gap-0.5 border-t border-white/10 pt-1">
                            <button type="button" data-open-auth="login" data-close-mobile class="mobile-link">
                                <span>Connexion</span>
                                <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" data-open-auth="register" data-close-mobile class="mobile-link font-semibold text-indigo-200">
                                <span>Créer un compte</span>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 pb-24 pt-8 sm:pt-12">
            <section class="relative mb-10">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-medium uppercase tracking-[0.22em] text-indigo-200/80 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_12px_2px_rgba(52,211,153,0.8)]"></span>
                        API en ligne · édition premium
                    </span>

                    <h1 class="mt-5 font-display text-4xl font-semibold leading-[1.05] tracking-tight sm:text-6xl">
                        <span class="text-gradient">Catalogue</span>
                        <span class="text-slate-100">d'exception</span>
                    </h1>

                    <p class="mt-5 max-w-xl text-sm leading-relaxed text-slate-400 sm:text-base">
                        Une expérience de consultation pensée comme un showroom :
                        filtres instantanés, tri élégant, pagination fluide et un espace
                        d'administration réservé aux membres autorisés.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <div class="glass reveal rounded-2xl p-4" style="animation-delay: 40ms">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Produits</p>
                        <p id="stat-produits" data-value="0" class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">—</p>
                    </div>
                    <div class="glass reveal rounded-2xl p-4" style="animation-delay: 90ms">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Catégories</p>
                        <p id="stat-categories" data-value="0" class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">—</p>
                    </div>
                    <div class="glass reveal rounded-2xl p-4" style="animation-delay: 140ms">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Prix HT moyen</p>
                        <p id="stat-prix" data-value="0" class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">—</p>
                    </div>
                    <div class="glass reveal rounded-2xl p-4" style="animation-delay: 190ms">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">En promotion</p>
                        <p id="stat-promo" data-value="0" class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">—</p>
                    </div>
                </div>
            </section>

            <section class="glass mb-8 rounded-3xl p-4 sm:p-5">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <label for="search" class="mb-1 block text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Recherche</label>
                        <input id="search" type="search" placeholder="Désignation du produit…" class="input-dark">
                    </div>
                    <div class="md:col-span-3">
                        <label for="categorie" class="mb-1 block text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Catégorie</label>
                        <select id="categorie" class="input-dark">
                            <option value="">Toutes les catégories</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="prix-min" class="mb-1 block text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Prix min (€)</label>
                        <input id="prix-min" type="number" min="0" step="0.01" placeholder="0" class="input-dark">
                    </div>
                    <div class="md:col-span-2">
                        <label for="prix-max" class="mb-1 block text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Prix max (€)</label>
                        <input id="prix-max" type="number" min="0" step="0.01" placeholder="∞" class="input-dark">
                    </div>
                    <div class="md:col-span-1">
                        <label for="stock-min" class="mb-1 block text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Stock min</label>
                        <input id="stock-min" type="number" min="0" step="1" placeholder="0" class="input-dark">
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                        <input id="promo" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-indigo-500/40">
                        En promotion uniquement
                    </label>

                    <div class="flex items-center gap-2 text-sm">
                        <label for="sort" class="text-slate-500">Trier par</label>
                        <select id="sort" class="input-dark input-compact w-auto">
                            <option value="">Pertinence</option>
                            <option value="designationP:asc">Désignation (A → Z)</option>
                            <option value="designationP:desc">Désignation (Z → A)</option>
                            <option value="prixHt:asc">Prix croissant</option>
                            <option value="prixHt:desc">Prix décroissant</option>
                            <option value="stockP:desc">Stock décroissant</option>
                            <option value="promotionP:desc">Promotion décroissante</option>
                        </select>
                    </div>

                    <button id="reset" type="button" class="btn-ghost">Réinitialiser</button>
                    <span id="result-count" class="ml-auto text-sm text-slate-500"></span>
                </div>
            </section>

            <section id="catalogue" class="scroll-mt-28">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">Collection</p>
                        <h2 class="mt-1 font-display text-2xl font-semibold text-white">Produits</h2>
                    </div>
                    <p class="text-xs text-slate-500">Cliquez sur une carte pour l'ambiance, filtrez pour le reste.</p>
                </div>

                <div id="loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="glass rounded-3xl p-3">
                            <div class="skeleton h-48 rounded-2xl"></div>
                            <div class="skeleton mt-4 h-4 w-2/3 rounded-full"></div>
                            <div class="skeleton mt-2 h-3 w-1/3 rounded-full"></div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="skeleton h-5 w-20 rounded-full"></div>
                                <div class="skeleton h-5 w-14 rounded-full"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div id="product-grid" class="hidden grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>

                <div id="empty" class="glass hidden rounded-3xl p-12 text-center">
                    <p class="font-display text-lg font-semibold text-white">Aucun produit trouvé</p>
                    <p class="mt-1 text-sm text-slate-500">Essayez d'élargir vos critères de recherche.</p>
                </div>

                <div id="error" class="hidden rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200"></div>

                <div class="mt-8 flex flex-col items-center gap-3">
                    <div id="pagination" class="flex flex-wrap items-center justify-center gap-2"></div>
                    <p id="page-info" class="text-xs text-slate-500"></p>
                </div>
            </section>
        </main>

        <section id="admin" class="mx-auto hidden max-w-7xl scroll-mt-28 px-4 pb-24">
            <div class="glass rounded-3xl p-5 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-indigo-300">Espace administrateur</p>
                        <h2 class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">Gestion du catalogue</h2>
                        <p class="mt-1 text-sm text-slate-500">Produits, catégories et utilisateurs — accès strictement réservé aux admins.</p>
                    </div>
                    <button id="admin-close" type="button" class="btn-ghost">Retour au catalogue</button>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <button type="button" data-admin-tab="produits" class="admin-tab is-active">Produits</button>
                    <button type="button" data-admin-tab="categories" class="admin-tab">Catégories</button>
                    <button type="button" data-admin-tab="utilisateurs" class="admin-tab">Utilisateurs</button>
                </div>

                <div data-admin-panel="produits" class="admin-panel mt-5">
                    <form id="product-form" class="grid grid-cols-1 gap-3 rounded-2xl border border-white/5 bg-white/[0.02] p-4 md:grid-cols-12">
                        <input type="hidden" id="product-id">

                        <div class="md:col-span-4">
                            <label for="product-designation" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Désignation</label>
                            <input id="product-designation" type="text" required class="input-dark" placeholder="Nom du produit">
                        </div>
                        <div class="md:col-span-3">
                            <label for="product-categorie" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Catégorie</label>
                            <select id="product-categorie" required class="input-dark">
                                <option value="">Choisir une catégorie</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="product-prix" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Prix HT</label>
                            <input id="product-prix" type="number" min="0" step="0.01" required class="input-dark" placeholder="0.00">
                        </div>
                        <div class="md:col-span-1">
                            <label for="product-promo" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Promo %</label>
                            <input id="product-promo" type="number" min="0" max="100" class="input-dark" placeholder="0">
                        </div>
                        <div class="md:col-span-2">
                            <label for="product-stock" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Stock</label>
                            <input id="product-stock" type="number" min="0" required class="input-dark" placeholder="0">
                        </div>
                        <div class="md:col-span-12">
                            <label for="product-image" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Image (URL)</label>
                            <input id="product-image" type="url" class="input-dark" placeholder="https://images.unsplash.com/...">
                        </div>
                        <div class="flex flex-wrap items-center gap-2 md:col-span-12">
                            <button id="product-submit" type="submit" class="btn-primary">Ajouter le produit</button>
                            <button id="product-cancel" type="button" class="btn-ghost hidden">Annuler</button>
                        </div>
                    </form>

                    <div class="mt-4 overflow-x-auto rounded-2xl border border-white/5">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/[0.03] text-left text-[10px] uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Produit</th>
                                    <th class="px-4 py-3 font-semibold">Catégorie</th>
                                    <th class="px-4 py-3 font-semibold">Prix</th>
                                    <th class="px-4 py-3 font-semibold">Stock</th>
                                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="admin-products-body" class="divide-y divide-white/5"></tbody>
                        </table>
                    </div>
                </div>

                <div data-admin-panel="categories" class="admin-panel mt-5 hidden">
                    <form id="category-form" class="flex flex-col gap-3 rounded-2xl border border-white/5 bg-white/[0.02] p-4 sm:flex-row sm:items-end">
                        <input type="hidden" id="category-id">
                        <div class="flex-1">
                            <label for="category-designation" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Désignation</label>
                            <input id="category-designation" type="text" required class="input-dark" placeholder="Nom de la catégorie">
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button id="category-submit" type="submit" class="btn-primary">Ajouter la catégorie</button>
                            <button id="category-cancel" type="button" class="btn-ghost hidden">Annuler</button>
                        </div>
                    </form>

                    <div class="mt-4 overflow-x-auto rounded-2xl border border-white/5">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/[0.03] text-left text-[10px] uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Catégorie</th>
                                    <th class="px-4 py-3 font-semibold">Produits</th>
                                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="admin-categories-body" class="divide-y divide-white/5"></tbody>
                        </table>
                    </div>
                </div>

                <div data-admin-panel="utilisateurs" class="admin-panel mt-5 hidden">
                    <form id="user-form" class="grid grid-cols-1 gap-3 rounded-2xl border border-white/5 bg-white/[0.02] p-4 md:grid-cols-12">
                        <div class="md:col-span-3">
                            <label for="user-name-input" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Nom</label>
                            <input id="user-name-input" type="text" required class="input-dark" placeholder="Nom complet">
                        </div>
                        <div class="md:col-span-4">
                            <label for="user-email-input" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Email</label>
                            <input id="user-email-input" type="email" required class="input-dark" placeholder="membre@exemple.com">
                        </div>
                        <div class="md:col-span-3">
                            <label for="user-password-input" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Mot de passe</label>
                            <input id="user-password-input" type="password" required minlength="8" class="input-dark" placeholder="8 caractères minimum">
                        </div>
                        <div class="md:col-span-2">
                            <label for="user-role-input" class="mb-1 block text-[11px] uppercase tracking-[0.14em] text-slate-500">Rôle</label>
                            <select id="user-role-input" class="input-dark">
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="md:col-span-12">
                            <button id="user-submit" type="submit" class="btn-primary">Créer l'utilisateur</button>
                        </div>
                    </form>

                    <div class="mt-4 overflow-x-auto rounded-2xl border border-white/5">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/[0.03] text-left text-[10px] uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Utilisateur</th>
                                    <th class="px-4 py-3 font-semibold">Rôle</th>
                                    <th class="px-4 py-3 font-semibold">Changer le rôle</th>
                                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="admin-users-body" class="divide-y divide-white/5"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div id="auth-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md" data-close-auth></div>

            <div class="modal-panel glass-strong relative mx-auto mt-20 w-[92%] max-w-md rounded-3xl p-6 sm:mt-28">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-indigo-300">Accès membre</p>
                        <h2 id="auth-title" class="mt-1 font-display text-2xl font-semibold text-white">Connexion</h2>
                    </div>
                    <button type="button" data-close-auth class="rounded-lg p-2 text-slate-400 transition hover:bg-white/5 hover:text-white" aria-label="Fermer">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-1 rounded-xl border border-white/5 bg-white/5 p-1">
                    <button type="button" data-auth-tab="login" class="auth-tab is-active">Connexion</button>
                    <button type="button" data-auth-tab="register" class="auth-tab">Inscription</button>
                </div>

                <div id="auth-error" class="mt-4 hidden rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-xs text-rose-200"></div>

                <form id="login-form" class="mt-4 space-y-3">
                    <div>
                        <label for="login-email" class="mb-1 block text-xs text-slate-400">Email</label>
                        <input id="login-email" type="email" autocomplete="email" required class="input-dark" placeholder="admin@filrouge.test">
                    </div>
                    <div>
                        <label for="login-password" class="mb-1 block text-xs text-slate-400">Mot de passe</label>
                        <input id="login-password" type="password" autocomplete="current-password" required class="input-dark" placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn-primary w-full">Se connecter</button>
                    <p class="text-center text-[11px] text-slate-500">Démo admin : admin@filrouge.test / password</p>
                </form>

                <form id="register-form" class="mt-4 hidden space-y-3">
                    <div>
                        <label for="register-name" class="mb-1 block text-xs text-slate-400">Nom</label>
                        <input id="register-name" type="text" autocomplete="name" required class="input-dark" placeholder="Votre nom">
                    </div>
                    <div>
                        <label for="register-email" class="mb-1 block text-xs text-slate-400">Email</label>
                        <input id="register-email" type="email" autocomplete="email" required class="input-dark" placeholder="vous@exemple.com">
                    </div>
                    <div>
                        <label for="register-password" class="mb-1 block text-xs text-slate-400">Mot de passe</label>
                        <input id="register-password" type="password" autocomplete="new-password" required minlength="8" class="input-dark" placeholder="8 caractères minimum">
                    </div>
                    <div>
                        <label for="register-password-confirmation" class="mb-1 block text-xs text-slate-400">Confirmation</label>
                        <input id="register-password-confirmation" type="password" autocomplete="new-password" required minlength="8" class="input-dark" placeholder="Répétez le mot de passe">
                    </div>
                    <button type="submit" class="btn-primary w-full">Créer mon compte</button>
                </form>
            </div>
        </div>

        <div id="confirm-modal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-black/75 backdrop-blur-sm" data-close-confirm></div>

            <div class="modal-panel glass-strong relative mx-auto mt-32 w-[92%] max-w-sm rounded-3xl p-6">
                <h3 class="font-display text-lg font-semibold text-white">Confirmer l'action</h3>
                <p id="confirm-message" class="mt-2 text-sm leading-relaxed text-slate-400"></p>
                <div class="mt-5 flex justify-end gap-2">
                    <button id="confirm-cancel" type="button" class="btn-ghost">Annuler</button>
                    <button id="confirm-ok" type="button" class="btn-primary">Confirmer</button>
                </div>
            </div>
        </div>

        <div id="toasts" class="pointer-events-none fixed bottom-4 right-4 z-[70] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2"></div>

        <footer class="mx-auto max-w-7xl px-4 pb-10 text-center text-[11px] text-slate-600">
            Lecture publique · écriture et gestion réservées aux administrateurs · pagination serveur 30 par page
        </footer>
    </body>
</html>
