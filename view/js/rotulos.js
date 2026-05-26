import { getPaletesAgrupados } from './getPaletesAgrupados.js';
// ELEMENTOS
const codigoPalete = document.getElementById("codigoPalete");
let idAgrupamento = 0;
const btnPesquisar = document.getElementById("btnPesquisar");
const btnImprimir = document.getElementById("btnImprimir");

const areaResultado = document.getElementById("areaResultado");
const rEstado = document.getElementById("rEstado");
const rCentral = document.getElementById("rCentral");
const rQtd = document.getElementById("rQtd");
const rPeso = document.getElementById("rPeso");
const tabelaPaletes = document.getElementById("tabelaPaletes");

// TOAST
const toastEl = document.getElementById("toastMsg");
const toastBody = toastEl.querySelector(".toast-body");
const toast = new bootstrap.Toast(toastEl);

// MODAL
const modal = new bootstrap.Modal(document.getElementById("modalInfo"));
const modalMensagem = document.getElementById("modalMensagem");

// ENTER
codigoPalete.addEventListener("keypress", e => {
    if (e.key === "Enter") {
        e.preventDefault();
        pesquisar();
    }
});

// CLICK
btnPesquisar.onclick = pesquisar;

async function pesquisar() {

    let codigo = codigoPalete.value;
    codigo = codigo.substring(0, 11).toUpperCase();
    

    if (!codigo) {
        mostrarToast("Informe o número do palete", "erro");
        return;
    }
    //alert(codigo);
    const paletesAgrupados = await getPaletesAgrupados(codigo); 
    console.log(paletesAgrupados.resultado);
    if (!paletesAgrupados.resultado) {
        areaResultado.classList.add("d-none");
        btnImprimir.disabled = true;
        mostrarModal(`O palete número <strong>${codigo}</strong> não foi agrupado.`);
        return;
    }

    preencherResultado(paletesAgrupados);
    mostrarToast("Agrupamento localizado com sucesso", "sucesso");
}

function preencherResultado(dados) {

    idAgrupamento = dados.paletes.idAgrupamento;
    areaResultado.classList.remove("d-none");
    //console.log(dados);
    console.log(dados.paletes);
    rEstado.textContent = dados.paletes.idAgrupamento;
    
    rCentral.textContent = dados.numeroPalete;
    //rQtd.textContent = dados.pesoPrevisto.length;
    rQtd.textContent = dados.pesoPrevisto;

    const pesoTotal = dados.paletes.reduce((s, p) => s + p.pesoPrevisto, 0);
    rPeso.textContent = pesoTotal.toFixed(3);

    tabelaPaletes.innerHTML = "";
    dados.paletes.forEach((p, i) => {
        tabelaPaletes.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${p.numeroPalete}</td>
                <td>${p.pesoPrevisto.toFixed(3)}</td>
            </tr>
        `;
    });

    btnImprimir.disabled = false;
}

// PADRÕES
function mostrarModal(msg) {
    modalMensagem.innerHTML = msg;
    modal.show();
}

function mostrarToast(msg, tipo) {
    toastEl.classList.remove("text-bg-success", "text-bg-danger");
    toastEl.classList.add(tipo === "erro" ? "text-bg-danger" : "text-bg-success");
    toastBody.textContent = msg;
    toast.show();
}

// IMPRIMIR
btnImprimir.onclick = () => {
    mostrarToast("Abrindo rótulo para impressão...", "sucesso");
    window.open(`gerar_rotulo_agrupamento.php?id=${idAgrupamento}`, "_blank");
};