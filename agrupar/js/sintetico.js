const tbl = document.getElementById("tblSintetico");
const btnPesquisar = document.getElementById("btnPesquisar");
const btnExportar = document.getElementById("btnExportar");

// Toast
const toastEl = document.getElementById("toastMsg");
const toastBody = toastEl.querySelector(".toast-body");
const toast = new bootstrap.Toast(toastEl);

// Dados simulados (já consolidados)
let dados = [
    {
        data: "2026-04-02",
        estado: "DF",
        central: "Central A",
        agrupamentos: 1,
        paletes: 4,
        encomendas: 10,
        peso: 450.75
    },
    {
        data: "2026-04-01",
        estado: "GO",
        central: "Central B",
        agrupamentos: 1,
        paletes: 3,
        encomendas: 6,
        peso: 320.40
    }
];

let ordemAtual = { coluna: null, asc: true };

btnPesquisar.onclick = () => {
    aplicarFiltros();
    mostrarToast("Relatório carregado", "sucesso");
};

function aplicarFiltros() {

    const dtInicio = document.getElementById("dtInicio").value;
    const dtFim = document.getElementById("dtFim").value;
    const estado = document.getElementById("estado").value;
    const central = document.getElementById("centralizadora").value;

    const filtrados = dados.filter(d =>
        (!dtInicio || d.data >= dtInicio) &&
        (!dtFim || d.data <= dtFim) &&
        (!estado || d.estado === estado) &&
        (!central || d.central === central)
    );

    renderTabela(filtrados);
}

function renderTabela(lista) {
    tbl.innerHTML = "";

    if (lista.length === 0) {
        mostrarToast("Nenhum registro encontrado", "erro");
        return;
    }

    lista.forEach(d => {
        tbl.innerHTML += `
            <tr>
                <td>${d.data}</td>
                <td>${d.estado}</td>
                <td>${d.central}</td>
                <td>${d.agrupamentos}</td>
                <td>${d.paletes}</td>
                <td>${d.encomendas}</td>
                <td>${d.peso.toFixed(3)}</td>
            </tr>
        `;
    });
}

// Ordenação por coluna
document.querySelectorAll(".sortable").forEach(th => {
    th.addEventListener("click", () => {

        const col = th.dataset.col;
        ordemAtual.asc = ordemAtual.coluna === col ? !ordemAtual.asc : true;
        ordemAtual.coluna = col;

        dados.sort((a, b) => {
            if (a[col] < b[col]) return ordemAtual.asc ? -1 : 1;
            if (a[col] > b[col]) return ordemAtual.asc ? 1 : -1;
            return 0;
        });

        renderTabela(dados);
        mostrarToast("Tabela ordenada", "sucesso");
    });
});

// Exportação CSV
btnExportar.onclick = () => {

    let csv = "Data;Estado;Centralizadora;Agrupamentos;Paletes;Encomendas;Peso\n";

    [...tbl.querySelectorAll("tr")].forEach(tr => {
        const cols = [...tr.children].map(td => td.innerText);
        csv += cols.join(";") + "\n";
    });

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "relatorio_sintetico.csv";
    a.click();

    mostrarToast("Arquivo exportado com sucesso", "sucesso");
};

// Toast
function mostrarToast(msg, tipo) {
    toastEl.classList.remove("text-bg-success", "text-bg-danger");
    toastEl.classList.add(tipo === "erro" ? "text-bg-danger" : "text-bg-success");
    toastBody.textContent = msg;
    toast.show();
}