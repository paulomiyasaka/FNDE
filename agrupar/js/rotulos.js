// ELEMENTOS
const codigoPalete = document.getElementById("codigoPalete");
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

function pesquisar() {

    const codigo = codigoPalete.value.trim();

    if (!codigo) {
        mostrarToast("Informe o número do palete", "erro");
        return;
    }

    // REGRA DEFINIDA:
    // Paletes que começam com PE estão agrupados
    if (!codigo.toUpperCase().startsWith("PE")) {
        areaResultado.classList.add("d-none");
        btnImprimir.disabled = true;
        mostrarModal(`O palete número <strong>${codigo}</strong> não foi agrupado.`);
        return;
    }

    // DADOS SIMULADOS
    const dados = {
        estado: "DF",
        centralizadora: "Central A",
        paletes: [
            { numero: "PE000000001", peso: 120.500 },
            { numero: "PE000000002", peso: 95.300 },
            { numero: "PE000000003", peso: 110.250 }
        ]
    };

    preencherResultado(dados);
    mostrarToast("Agrupamento localizado com sucesso", "sucesso");
}

function preencherResultado(dados) {

    areaResultado.classList.remove("d-none");

    rEstado.textContent = dados.estado;
    rCentral.textContent = dados.centralizadora;
    rQtd.textContent = dados.paletes.length;

    const pesoTotal = dados.paletes.reduce((s, p) => s + p.peso, 0);
    rPeso.textContent = pesoTotal.toFixed(3);

    tabelaPaletes.innerHTML = "";
    dados.paletes.forEach((p, i) => {
        tabelaPaletes.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${p.numero}</td>
                <td>${p.peso.toFixed(3)}</td>
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
};