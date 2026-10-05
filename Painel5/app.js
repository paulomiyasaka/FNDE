// Estado global
let parsedData = [];

/**
 * PARSER DEFINITIVO DE MOEDA E NÚMEROS PT-BR
 * Trata floats do JS, strings como "2,55", "30.600,00" ou "30.600"
 */
function parseCurrencyNumber(val) {
    if (val === null || val === undefined || val === "") return 0;
    
    // 1. Se já for tipo number (float/int do JS)
    if (typeof val === "number") return val;

    // 2. Limpeza de prefixo R$, espaços e caracteres não numéricos/separadores
    let str = String(val).replace(/R\$\s?|\s/g, "").trim();
    if (!str) return 0;

    // Se a string tem ponto e vírgula (ex: "30.600,00")
    if (str.includes(".") && str.includes(",")) {
        // Remove todos os pontos de milhar e troca a vírgula por ponto decimal
        str = str.replace(/\./g, "").replace(",", ".");
    } 
    // Se a string tem apenas vírgula (ex: "2,55" ou "30600,00")
    else if (str.includes(",")) {
        str = str.replace(",", ".");
    } 
    // Se a string tem apenas ponto (ex: "30.600" vs "2.55")
    else if (str.includes(".")) {
        const parts = str.split(".");
        // Se após o ponto houver exatamente 3 dígitos (ex: 30.600), trata como milhar
        if (parts.length > 1 && parts[parts.length - 1].length === 3) {
            str = str.replace(/\./g, "");
        }
        // Caso contrário (ex: "2.55"), mantém o ponto decimal
    }

    const num = parseFloat(str);
    return isNaN(num) ? 0 : num;
}

function formatCurrency(val) {
    const num = parseCurrencyNumber(val);
    return num.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

function formatNumber(val, decimals = 0) {
    const num = parseCurrencyNumber(val);
    return num.toLocaleString("pt-BR", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function populateSupplierSelect(data) {
    const select = document.getElementById("filterFornecedor");
    if (!select) return;
    select.innerHTML = '<option value="">Todos os Fornecedores</option>';

    const suppliers = [...new Set(data.map(item => item.fornecedor))].sort();

    suppliers.forEach(supplier => {
        if (!supplier) return;
        const option = document.createElement("option");
        option.value = supplier;
        option.textContent = supplier;
        select.appendChild(option);
    });
}

function updateCards(filteredData) {
    // 1. Total Faturado: Mantida a regra de ignorar "Não Recebido" + parser de conversão
    const totalFaturamento = filteredData.reduce((acc, cur) => {
        return cur.dataRecebimento === "Não Recebido" 
            ? acc 
            : acc + parseCurrencyNumber(cur.valorFaturamento || cur.subtotal || 0);
    }, 0);
    
    // 2. Total Tiragem: Mantida a regra de ignorar "Não Recebido"
    const totalTiragem = filteredData.reduce((acc, cur) => {
        return cur.dataRecebimento === "Não Recebido" 
            ? acc 
            : acc + parseCurrencyNumber(cur.tiragem || 0);
    }, 0);

    const totalPaletes = filteredData.reduce((acc, cur) => acc + parseCurrencyNumber(cur.qtdPaletesPorMilheiro || cur.posicoes_porta_palete || 0), 0);
    const totalDias = filteredData.reduce((acc, cur) => acc + parseCurrencyNumber(cur.diasArmazenamento || 0), 0);
    
    const recebidos = filteredData.filter(item => item.dataRecebimento !== "Não Recebido");
    const naoRecebidos = filteredData.filter(item => item.dataRecebimento === "Não Recebido");
    const qtdRecebidos = recebidos.length;

    // 3. Média de Armazenagem Palete/Dia (apenas valores informados > 0)
    let somaValoresDiarias = 0;
    let qtdValoresDiarias = 0;
    filteredData.forEach(item => {
        const vDiaria = parseCurrencyNumber(item.valor_dia_armazenagem || item.valor_armazenagem_palete_dia || item.valorDiaria || 0);
        if (vDiaria > 0) {
            somaValoresDiarias += vDiaria;
            qtdValoresDiarias++;
        }
    });
    const mediaValDiaria = qtdValoresDiarias > 0 ? (somaValoresDiarias / qtdValoresDiarias) : 0;

    // Atualização dos elementos na tela
    if (document.getElementById("cardTotalFaturamento")) document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFaturamento);
    if (document.getElementById("cardTotalTiragem")) document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTiragem);
    if (document.getElementById("cardTotalNaoRecebido")) document.getElementById("cardTotalNaoRecebido").textContent = naoRecebidos.length;
    if (document.getElementById("cardTotalRegistros")) document.getElementById("cardTotalRegistros").textContent = qtdRecebidos;
    if (document.getElementById("cardTotalPaletes")) document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPaletes);
    if (document.getElementById("cardTotalDias")) document.getElementById("cardTotalDias").textContent = formatNumber(qtdRecebidos > 0 ? totalDias / qtdRecebidos : 0);
    if (document.getElementById("cardValorDiaria")) document.getElementById("cardValorDiaria").textContent = formatCurrency(mediaValDiaria);

    if (document.getElementById("recordCounter")) document.getElementById("recordCounter").textContent = `Mostrando ${filteredData.length} registros`;
}

/**
 * Renderiza a Tabela com links clicáveis para abrir os detalhes excedentes
 */
function renderTable(data) {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    if (data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Nenhum registro encontrado para os filtros selecionados.
                </td>
            </tr>
        `;
        return;
    }

    data.forEach((item, index) => {
        const tr = document.createElement("tr");
        const statusNaoRecebido = item.dataRecebimento === 'Não Recebido';

        const btnDetalharSupplier = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-dark" onclick="abrirModalDetalhes(${index})">${item.fornecedor || '-'}</a>`;
        const btnDetalharSku = `<a href="javascript:void(0)" class="badge bg-light text-dark border text-decoration-none" onclick="abrirModalDetalhes(${index})">${item.sku || '-'}</a>`;
        const btnDetalharDesc = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-primary" onclick="abrirModalDetalhes(${index})">${item.descricao || '-'}</a>`;

        let linha = `
            <td class="fw-bold text-secondary fs-7 text-center">${index + 1}</td>
            <td>
                <div>${btnDetalharSupplier}</div>
                <small class="text-muted">CNPJ: ${item.cnpj || 'N/A'}</small>
            </td>
            <td class="text-center">${btnDetalharSku}</td>
            <td>${btnDetalharDesc}</td>
            <td class="text-center"><span class="badge bg-secondary">${item.acervo || ''}</span></td>
            <td class="text-center fw-bold">${formatNumber(item.tiragem)}</td>
            <td class="text-center">${formatNumber(item.qtdPaletesPorMilheiro)}</td>
            <td class="text-center text-muted">${formatNumber(item.diasArmazenamento)}</td>
        `;

        if (statusNaoRecebido) {
            linha += `
                <td class="text-center"><span class="badge bg-danger text-white">${item.dataRecebimento}</span></td>
                <td class="text-center fw-bold text-danger">${formatCurrency(item.valorFaturamento)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary" title="Livro não recebido" onclick="alert('O livro ${item.descricao} não foi recebido.')">
                        <i class="bi bi-exclamation-circle"></i>
                    </button>
                </td>
            `;
        } else {
            linha += `
                <td class="text-center"><span class="badge bg-info text-dark">${item.dataRecebimento}</span></td>
                <td class="text-center fw-bold text-success">${formatCurrency(item.valorFaturamento)}</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" title="Download NF" href="saida/${item.arquivoNf}">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </td>
            `;
        }

        tr.innerHTML = linha;
        tableBody.appendChild(tr);
    });
}

function abrirModalDetalhes(index) {
    const item = parsedData[index];
    if (!item) return;

    document.getElementById("modalTitulo").textContent = item.descricao || "Detalhes do Item";

    let htmlConteudo = `
        <div class="row g-3">
            <div class="col-md-6"><strong>Fornecedor:</strong> ${item.fornecedor || '-'}</div>
            <div class="col-md-6"><strong>CNPJ:</strong> ${item.cnpj || 'N/A'}</div>
            <div class="col-md-6"><strong>SKU:</strong> ${item.sku || '-'}</div>
            <div class="col-md-6"><strong>Acervo:</strong> ${item.acervo || '-'}</div>
            <div class="col-md-6"><strong>Tiragem:</strong> ${formatNumber(item.tiragem)}</div>
            <div class="col-md-6"><strong>Valor Faturamento:</strong> ${formatCurrency(item.valorFaturamento)}</div>
            <div class="col-md-6"><strong>Data Recebimento:</strong> ${item.dataRecebimento || '-'}</div>
            <div class="col-md-6"><strong>Dias Armazenamento:</strong> ${item.diasArmazenamento || 0}</div>
        </div>
        <hr>
        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-info-circle me-1"></i> Informações Adicionais (Colunas Excedentes)</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Campo</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
    `;

    let temExcedentes = false;
    const camposPadrao = ['chave','cnpj','fornecedor','sku','descricao','acervo','tiragem','qtdPaletesPorMilheiro','diasArmazenamento','dataRecebimento','valorFaturamento','arquivoNf','detalhesExcedentes'];

    for (const [key, value] of Object.entries(item)) {
        if (!camposPadrao.includes(key)) {
            temExcedentes = true;
            htmlConteudo += `
                <tr>
                    <td class="fw-semibold text-capitalize">${key.replace(/_/g, ' ')}</td>
                    <td>${value !== null && value !== '' ? value : '-'}</td>
                </tr>
            `;
        }
    }

    if (item.detalhesExcedentes) {
        for (const [key, value] of Object.entries(item.detalhesExcedentes)) {
            temExcedentes = true;
            htmlConteudo += `
                <tr>
                    <td class="fw-semibold text-capitalize">${key}</td>
                    <td>${value !== null && value !== '' ? value : '-'}</td>
                </tr>
            `;
        }
    }

    if (!temExcedentes) {
        htmlConteudo += `<tr><td colspan="2" class="text-center text-muted">Nenhuma informação adicional cadastrada.</td></tr>`;
    }

    htmlConteudo += `</tbody></table></div>`;

    document.getElementById("modalBodyContent").innerHTML = htmlConteudo;

    const modalElement = new bootstrap.Modal(document.getElementById("modalDetalhesItem"));
    modalElement.show();
}

function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const selectedSupplier = document.getElementById("filterFornecedor").value;

    const filtered = parsedData.filter(item => {
        const matchesSearch = 
            (item.descricao && item.descricao.toLowerCase().includes(searchText)) ||
            (item.sku && item.sku.toLowerCase().includes(searchText)) ||
            (item.fornecedor && item.fornecedor.toLowerCase().includes(searchText)) ||
            (item.dataRecebimento && item.dataRecebimento.toLowerCase().includes(searchText));

        const matchesSupplier = selectedSupplier === "" || item.fornecedor === selectedSupplier;

        return matchesSearch && matchesSupplier;
    });

    renderTable(filtered);
    updateCards(filtered);
}

function carregarDados() {
    try {
        if (typeof faturamentoDados !== 'undefined') {
            parsedData = faturamentoDados;
        } else {
            parsedData = [];
        }
        
        populateSupplierSelect(parsedData);
        renderTable(parsedData);
        updateCards(parsedData);

    } catch (error) {
        console.error("Erro ao carregar dados:", error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    carregarDados();

    if (document.getElementById("searchInput")) {
        document.getElementById("searchInput").addEventListener("input", applyFilters);
    }
    if (document.getElementById("filterFornecedor")) {
        document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
    }
});