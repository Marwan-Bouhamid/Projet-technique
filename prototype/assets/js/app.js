const form=document.getElementById("form-groupe")
const sectionForm=document.getElementById("section-form")
const btnAjouter=document.getElementById("btn-show-form")
const btnAnnuler=document.getElementById("btn-cancel-form")
const tbody=document.getElementById("table-categories-body")
const btnEnregister=document.getElementById("enregister")
const api="backend/api/api.php"


function Loading(isloading){
    if(isloading){
        btnEnregister.disabled=true
        btnEnregister.textContent="En cours ..."
    }
    else{
        btnEnregister.disabled=false
        btnEnregister.textContent="Enregistrer"
    }
}

function Toast(message,type="success"){
    const toastContainer=document.getElementById("toast-container")
    const toast=document.createElement("div")
    const color=type=="success"?'bg-green-500':'bg-red-500'

    toast.className=`
        ${color} px-5 py-4 text-white rounded shadow-lg transition-opacity duration-300
    `

    toast.textContent=`${message}`
    toastContainer.appendChild(toast)
    setTimeout(()=>{
        toast.style.opacity=0
        setTimeout(()=>{
            toast.remove()
        },500)
    },3000)
}

function Afficher(){
    tbody.innerHTML=""
    fetch(api).then(response=>
        response.json()
    )
    .then(data=>{
        console.log(data)
        data.forEach(groupe => {
            Loading(false)
            const tr=document.createElement("tr")
            tr.innerHTML=`
                <td class="px-4 py-3">${groupe["id"]}</td>
                <td class="px-4 py-3">${groupe["nom"]}</td>
                <td class="px-4 py-3">${groupe["description"]}</td>
            `
            tbody.appendChild(tr)
        })
    })
    .catch(error=>{
        Loading(false)
        console.error(error)
    })
}

document.addEventListener("DOMContentLoaded",()=>{
    Afficher()
    form.addEventListener("submit",(e)=>{
        e.preventDefault()
        Loading(true)
        fetch(api,{
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body:JSON.stringify({
                nom:document.getElementById("nom").value,
                description:document.getElementById("description").value
            })
        }).then(response=>response.json())
        .then(data=>{
            Loading(false)
            Toast("Groupe musculaire bien ajouté")
            form.reset()
            sectionForm.classList.add("hidden")
            Afficher()
        })
        .catch(err=>{
            Loading(false)
            Toast("Erreur lors de l'ajout", "error")
            console.error(err)
        })
    })
})

btnAjouter.addEventListener("click",()=>sectionForm.classList.remove("hidden"))
btnAnnuler.addEventListener("click",()=>sectionForm.classList.add("hidden"))
