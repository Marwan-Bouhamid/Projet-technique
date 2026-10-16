const API = "../../backend/admin/api/GestionGroupeMusculaire.php";
const table = document.getElementById("table-groupe-musculaire");
const form = document.getElementById("form-groupe-musculaire");
const btnAjouter = document.getElementById("btn-nouveau");
const btnAnnuler = document.getElementById("btn-annuler");
const sectionForm = document.getElementById("section_form");
const nameInput = document.getElementById("name");
const descriptionInput = document.getElementById("description");

function afficherGroupesMusculaires() {

    fetch(API).then(response => response.json())
        .then(groupes => {
            if (table) {
                table.innerHTML = "";
                groupes.forEach(groupe => {
                    table.insertAdjacentHTML(
                        "beforeend",
                        `
                        <tr class="border-b">

                            <td class="p-4">
                                ${groupe.id_group}
                            </td>

                            <td class="p-4">
                                ${groupe.name}
                            </td>

                            <td class="p-4">
                                ${groupe.description}
                            </td>

                        </tr>
                        `
                    );

                });
            }

        })
        .catch(error => {
            console.error(error);
        });
}

function ajouterGroupeMusculaire() {

    const name = nameInput.value.trim();
    const description = descriptionInput.value.trim();


    fetch(API, {
        method: "POST",
        headers: {
            "content-type": "application/json"
        },
        body: JSON.stringify({
            name: name,
            description: description
        })
    }).then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                console.log(data);
                form.reset();
                sectionForm.classList.add("hidden");
                afficherGroupesMusculaires();
            }
        })
        .catch(error => console.error(error));
}




btnAjouter.addEventListener("click", () => {
    sectionForm.classList.remove("hidden")
})

btnAnnuler.addEventListener("click", () => {
    form.reset();
    sectionForm.classList.add("hidden")
})

document.addEventListener("DOMContentLoaded", () => {
    afficherGroupesMusculaires();
});

if (form) {
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        ajouterGroupeMusculaire();
    })
}


