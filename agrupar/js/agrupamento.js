let paletes = [];
let pesoTotal = 0;
let limiteAtingido = false;

const estado = document.getElementById("estado");
const centralizadora = document.getElementById("centralizadora");
const codigoPalete = document.getElementById("codigoPalete");

const totalPaletesEl = document.getElementById("totalPaletes");
const pesoTotalEl = document.getElementById("pesoTotal");
const tabelaPaletes = document.getElementById("tabelaPaletes");

const btnAbrir = document.getElementById("btnAbrir");
const btnCancelar = document.getElementById("btnCancelar");
const btnFechar = document.getElementById("btnFechar");
const btnInserir = document.getElementById("btnInserir");

const areaLancamento = document.getElementById("areaLancamento");

const toastEl = document.getElementById("toastMsg");
const toastBody = toastEl.querySelector(".toast-body");
const toast = new bootstrap.Toast(toastEl);

const modal = new bootstrap.Modal(document.getElementById("modalConfirm"));
const modalTitulo = document.getElementById("modalTitulo");
const modalMensagem = document.getElementById("modalMensagem");
const modalConfirmar = document.getElementById("modalConfirmar");

btnAbrir.onclick = () => {
    if (!estado.value || !centralizadora.value) {
        mostrarToast("Preencha Estado e Centralizadora", "erro");
        return;
    }

    estado.disabled = centralizadora.disabled = true;
    btnAbrir.classList.add("d-none");
    btnCancelar.classList.remove("d-none");
    btnFechar.classList.remove("d-none");
    areaLancamento.classList.remove("d-none");
};

codigoPalete.addEventListener("keypress", e => {
    if (e.key === "Enter") {
        e.preventDefault();
        inserirPalete();
    }
});

btnInserir.onclick = inserirPalete;

function inserirPalete() {
    if (limiteAtingido) return;

    const codigo = codigoPalete.value.trim();
    if (codigo.length < 22) return;

    const numero = codigo.substring(0, 11);
    const peso = parseFloat(codigo.substring(11, 22));

    const existente = paletes.find(p => p.numero === numero);
    if (existente) {
        abrirModal(
            "Palete duplicado",
            `O palete ${numero} já está registrado.<br>Deseja removê-lo?`,
            () => removerPalete(numero)
        );
        return;
    }

    if ((pesoTotal + peso) > 950) {
        limiteAtingido = true;
        codigoPalete.disabled = true;

        abrirModal(
            "Limite de Peso Atingido",
            `Palete ${numero} desconsiderado.<br>
             Peso total atingiu ou ultrapassou 950 kg.<br>
             Feche ou cancele o agrupamento.`
        );
        return;
    }

    paletes.push({ numero, peso });
    pesoTotal += peso;

    codigoPalete.value = "";
    atualizarTela();
    mostrarToast("Palete inserido", "sucesso");
}

function removerPalete(numero) {
    const p = paletes.find(p => p.numero === numero);
    paletes = paletes.filter(p => p.numero !== numero);
    pesoTotal -= p.peso;
    atualizarTela();
    mostrarToast("Palete removido", "sucesso");
}

function atualizarTela() {
    totalPaletesEl.textContent = paletes.length;
    pesoTotalEl.textContent = pesoTotal.toFixed(3);

    tabelaPaletes.innerHTML = "";

    [...paletes].reverse().forEach((p, index) => {
        const registro = paletes.length - index;
        tabelaPaletes.innerHTML += `
            <tr>
                <td>${registro}</td>
                <td>${p.numero}</td>
                <td>${p.peso.toFixed(3)}</td>
            </tr>`;
    });
}

btnFechar.onclick = () => {
    abrirModal(
        "Fechar Agrupamento",
        `Confirma o fechamento do agrupamento?<br><br>
         <strong>Paletes:</strong> ${paletes.length}<br>
         <strong>Peso Total:</strong> ${pesoTotal.toFixed(3)} kg`,
        () => {
            mostrarToast("Agrupamento fechado", "sucesso");
            location.reload();
        }
    );
};

btnCancelar.onclick = () => {
    abrirModal(
        "Cancelar Agrupamento",
        "Todos os paletes registrados serão descartados.",
        () => location.reload()
    );
};

function abrirModal(titulo, mensagem, confirmar) {
    // Reseta o input do código do palete
    codigoPalete.value = "";

    modalTitulo.innerHTML = titulo;
    modalMensagem.innerHTML = mensagem;

    modalConfirmar.onclick = () => {
        modal.hide();
        if (confirmar) confirmar();
    };

    modal.show();
}

function mostrarToast(msg, tipo) {
    toastEl.classList.remove("text-bg-success", "text-bg-danger");
    toastEl.classList.add(tipo === "erro" ? "text-bg-danger" : "text-bg-success");
    toastBody.textContent = msg;
    toast.show();
}
