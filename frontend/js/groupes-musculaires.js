const API = new URL("../../backend/api/groupes.php", location.href);
const table = document.getElementById("table-groupe-musculaire");
const form = document.getElementById("form-groupe-musculaire");
const btnAjouter = document.getElementById("btn-nouveau");
const btnAnnuler = document.getElementById("btn-annuler");
const btnEnregistrer = document.getElementById("btn-enregistrer");
const sectionForm = document.getElementById("section_form");
const nameInput = document.getElementById("name");
const descriptionInput = document.getElementById("description");
const formError = document.getElementById("form-error");
const toasts = document.getElementById("toasts");

let groupes = [];

/* ---------- Utilitaires ---------- */
const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
const spinner = (cls = "h-4 w-4") =>
    `<span class="inline-block ${cls} animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span>`;

function setBusy(btn, busy, text) {
    if (busy) {
        btn.dataset.html = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `${spinner()}<span>${text}</span>`;
    } else {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.html ?? btn.innerHTML;
    }
}

/* ---------- Toasts ---------- */
function toast(message, type = "success") {
    const colors = { success: "bg-green-600", error: "bg-red-600" };
    const t = document.createElement("div");
    t.setAttribute("role", type === "error" ? "alert" : "status");
    t.className = `${colors[type]} text-white px-4 py-3 shadow-lg flex gap-3 transition duration-300 opacity-0 translate-y-2`;
    const p = document.createElement("p");
    p.className = "flex-1";
    p.textContent = message;
    const x = document.createElement("button");
    x.type = "button";
    x.setAttribute("aria-label", "Fermer");
    x.textContent = "×";
    const close = () => { t.classList.add("opacity-0"); setTimeout(() => t.remove(), 300); };
    x.addEventListener("click", close);
    t.append(p, x);
    toasts.append(t);
    requestAnimationFrame(() => t.classList.remove("opacity-0", "translate-y-2"));
    setTimeout(close, type === "error" ? 6000 : 3500);
}

/* ---------- Appel API ---------- */
async function api(method = "GET", { id, body } = {}) {
    const url = new URL(API);
    if (id) url.searchParams.set("id", id);
    const page = new URLSearchParams(location.search); // démo : ?delay=1500  ?fail=1
    ["delay", "fail"].forEach((k) => page.has(k) && url.searchParams.set(k, page.get(k)));

    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), 10000);
    let res;
    try {
        res = await fetch(url, {
            method,
            headers: { "content-type": "application/json" },
            body: body ? JSON.stringify(body) : undefined,
            signal: ctrl.signal
        });
    } catch (e) {
        throw new Error(e.name === "AbortError" ? "Le serveur met trop de temps à répondre." : "Serveur injoignable. Vérifiez votre connexion.");
    } finally {
        clearTimeout(timer);
    }
    let json;
    try { json = await res.json(); } catch { throw new Error("Réponse invalide du serveur."); }
    if (!res.ok || !json.success) throw new Error(json.message || `Erreur ${res.status}`);
    return json;
}

/* ---------- Tableau (états : chargement / erreur / vide / données) ---------- */
function ligneMessage(html) {
    table.innerHTML = `<tr><td colspan="4" class="p-8 text-center">${html}</td></tr>`;
}

function afficherTableau() {
    if (groupes.length === 0) {
        return ligneMessage('<span class="text-gray-500">Aucun groupe musculaire pour le moment.</span>');
    }
    table.innerHTML = groupes.map((groupe) => `
        <tr class="border-b">

            <td class="p-4">
                ${groupe.id_group}
            </td>

            <td class="p-4">
                ${esc(groupe.name)}
            </td>

            <td class="p-4">
                ${esc(groupe.description)}
            </td>

            <td class="p-4">
                <button class="btn-supprimer bg-red-600 text-white px-3 py-1 inline-flex items-center gap-2 disabled:opacity-60" data-id="${groupe.id_group}">
                    Supprimer
                </button>
            </td>

        </tr>`).join("");
}

async function afficherGroupesMusculaires() {
    ligneMessage(`<span class="inline-flex items-center gap-3 text-gray-500">${spinner("h-6 w-6")} Chargement…</span>`);
    try {
        groupes = (await api()).data;
        afficherTableau();
    } catch (error) {
        ligneMessage(`<p class="text-red-600 mb-3">${esc(error.message)}</p>
            <button id="btn-retry" class="bg-blue-600 text-white px-4 py-2">Réessayer</button>`);
        document.getElementById("btn-retry").addEventListener("click", afficherGroupesMusculaires);
        toast(error.message, "error");
    }
}

/* ---------- Ajout ---------- */
async function ajouterGroupeMusculaire() {
    const name = nameInput.value.trim();
    const description = descriptionInput.value.trim();

    if (!name || !description) {
        formError.textContent = "Le nom et la description sont obligatoires.";
        formError.classList.remove("hidden");
        return;
    }
    formError.classList.add("hidden");

    setBusy(btnEnregistrer, true, "Enregistrement…");
    try {
        const res = await api("POST", { body: { name, description } });
        groupes = [...groupes, res.data];
        afficherTableau();
        form.reset();
        sectionForm.classList.add("hidden");
        toast(res.message, "success");
    } catch (error) {
        toast(error.message, "error"); // doublon (409), validation (422), réseau…
    } finally {
        setBusy(btnEnregistrer, false);
    }
}

/* ---------- Suppression (délégation d'événements) ---------- */
table.addEventListener("click", async (event) => {
    const btn = event.target.closest(".btn-supprimer");
    if (!btn) return;
    const id = parseInt(btn.dataset.id);
    const groupe = groupes.find((g) => g.id_group === id);
    if (!confirm(`Supprimer « ${groupe.name} » ?`)) return;

    setBusy(btn, true, "Suppression…");
    try {
        const res = await api("DELETE", { id });
        groupes = groupes.filter((g) => g.id_group !== id);
        afficherTableau();
        toast(res.message, "success");
    } catch (error) {
        setBusy(btn, false);
        toast(error.message, "error");
    }
});

btnAjouter.addEventListener("click", () => {
    sectionForm.classList.remove("hidden");
    nameInput.focus();
});

btnAnnuler.addEventListener("click", () => {
    form.reset();
    formError.classList.add("hidden");
    sectionForm.classList.add("hidden");
});

form.addEventListener("submit", (event) => {
    event.preventDefault();
    ajouterGroupeMusculaire();
});

document.addEventListener("DOMContentLoaded", () => {
    afficherGroupesMusculaires();
});
