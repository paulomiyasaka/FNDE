import { getPaletesAgrupados } from './getPaletesAgrupados.js';
// ELEMENTOS
const codigoPalete = document.getElementById("codigoPalete");
let idAgrupamento = 0;
const btnPesquisar = document.getElementById("btnPesquisar");
const btnImprimir = document.getElementById("btnImprimir");
const btnImprimirQR = document.getElementById("btnImprimirQR");
const btnImprimirQRUnificado = document.getElementById("btnImprimirQRUnificado");

const areaResultado = document.getElementById("areaResultado");
const rEstado = document.getElementById("rEstado");
const rIdAgrupamento = document.getElementById("rIdAgrupamento");
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
    if (paletesAgrupados.resultado == false) {
        areaResultado.classList.add("d-none");
        btnImprimir.disabled = true;
        btnImprimirQR.disabled = true;
        btnImprimirQRUnificado.disabled = true;
        mostrarModal(`O palete número <strong>${codigo}</strong> não foi agrupado.`);
        return;
    }

    preencherResultado(paletesAgrupados);
    mostrarToast("Agrupamento localizado com sucesso", "sucesso");
}

function preencherResultado(dados) {

    idAgrupamento = dados.dadosGerais[0].idAgrupamento;
    areaResultado.classList.remove("d-none");
    console.log(dados.dadosGerais[0]);
    //console.log(dados.paletes);
    rEstado.textContent = dados.dadosGerais[0].siglaSe;
    rIdAgrupamento.textContent = idAgrupamento;
    
    rCentral.textContent = `${dados.dadosGerais[0].siglaCentralizadora} - ${dados.dadosGerais[0].nomeCentralizadora}`;
    rQtd.textContent = dados.agrupamento.length;

    const pesoTotal = dados.agrupamento.reduce((s, p) => s + p.pesoPrevisto, 0);
    rPeso.textContent = pesoTotal.toFixed(3).replace('.', ',');

    tabelaPaletes.innerHTML = "";
    dados.agrupamento.forEach((p, i) => {
        tabelaPaletes.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${p.numeroPalete}</td>
                <td>${p.pesoPrevisto.toFixed(3).replace('.', ',')}</td>
            </tr>
        `;
    });

    btnImprimir.disabled = false;
    btnImprimirQR.disabled = false;
    btnImprimirQRUnificado.disabled = false;
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
    mostrarToast("Abrindo rótulo DataMatrix para impressão...", "sucesso");
    window.open(`gerar_rotulo_agrupamento.php?id=${idAgrupamento}`, "_blank");
};

btnImprimirQR.onclick = () => {
    mostrarToast("Abrindo rótulo QR Code para impressão...", "sucesso");
    window.open(`gerar_rotulo_qr_code.php?id=${idAgrupamento}`, "_blank");
};

btnImprimirQRUnificado.onclick = () => {
    mostrarToast("Abrindo rótulo QR Code Unificado para impressão...", "sucesso");
    window.open(`rotulo_qr_unico.php?id=${idAgrupamento}`, "_blank");
};