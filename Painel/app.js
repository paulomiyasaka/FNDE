// Estado global
let parsedData = [];

/**
 * Formata um número para o padrão Monetário BRL
 */
function formatCurrency(val) {
    return (val || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

/**
 * Formata um número para o padrão de milhar pt-BR
 */
function formatNumber(val, decimals = 0) {
    return (val || 0).toLocaleString("pt-BR", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Popula o select com fornecedores únicos
 */
function populateSupplierSelect(data) {
    const select = document.getElementById("filterFornecedor");
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

/**
 * Recalcula os Cards de Resumo
 */
function updateCards(filteredData) {
    const totalFaturamento = filteredData.reduce((acc, cur) => acc + (cur.valorFaturamento || 0), 0);
    const totalTiragem = filteredData.reduce((acc, cur) => acc + (cur.tiragem || 0), 0);
    const totalPeso = filteredData.reduce((acc, cur) => acc + (cur.pesoTiragem || 0), 0);

    document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFaturamento);
    document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTiragem);
    document.getElementById("cardTotalPeso").textContent = `${formatNumber(totalPeso, 2)} kg`;
    document.getElementById("cardTotalRegistros").textContent = filteredData.length;

    document.getElementById("recordCounter").textContent = `Mostrando ${filteredData.length} registros`;
}

/**
 * Renderiza os dados na Tabela
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

    data.forEach(item => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td class="fw-bold text-secondary fs-7">${item.chave}</td>
            <td>
                <div class="fw-semibold text-dark">${item.fornecedor}</div>
                <small class="text-muted">${item.cnpj}</small>
            </td>
            <td><span class="badge bg-light text-dark border">${item.sku}</span></td>
            <td class="fw-semibold">${item.descricao}</td>
            <td class="text-center"><span class="badge bg-secondary">${item.acervo}</span></td>
            <td class="text-end fw-bold">${formatNumber(item.tiragem)}</td>
            <td class="text-end">${formatNumber(item.pesoTiragem, 3)}</td>
            <td class="text-end text-muted">${formatCurrency(item.subtotal)}</td>
            <td class="text-center"><span class="badge bg-info text-dark">${item.dataRecebimento}</span></td>
            <td class="text-end fw-bold text-success">${formatCurrency(item.valorFaturamento)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary" title="Download NF: ${item.arquivoNf}" onclick="alert('NF: ${item.arquivoNf}')">
                    <i class="bi bi-file-earmark-pdf"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

/**
 * Aplica os Filtros de Busca Texto e Fornecedor
 */
function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const selectedSupplier = document.getElementById("filterFornecedor").value;

    const filtered = parsedData.filter(item => {
        const matchesSearch = 
            (item.descricao && item.descricao.toLowerCase().includes(searchText)) ||
            (item.sku && item.sku.toLowerCase().includes(searchText)) ||
            (item.fornecedor && item.fornecedor.toLowerCase().includes(searchText)) ||
            (item.chave && item.chave.toLowerCase().includes(searchText));

        const matchesSupplier = selectedSupplier === "" || item.fornecedor === selectedSupplier;

        return matchesSearch && matchesSupplier;
    });

    renderTable(filtered);
    updateCards(filtered);
}

/**
 * Carrega o arquivo JSON e inicializa a interface
 */
async function loadData() {
    try {
        // Altere 'dados.json' para o caminho relativo ou URL da sua API
        const response = await fetch('faturamento_dados.json');
        
        if (!response.ok) {
            throw new Error(`Erro de rede ao carregar JSON: ${response.statusText}`);
        }

        parsedData = await response.json();
        
        // Inicializa a interface
        populateSupplierSelect(parsedData);
        renderTable(parsedData);
        updateCards(parsedData);

    } catch (error) {
        console.error("Erro ao carregar dados:", error);
        document.getElementById("tableBody").innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-4 text-danger">
                    Erro ao carregar o arquivo JSON. Certifique-se de que o arquivo existe e o servidor local está rodando.
                </td>
            </tr>
        `;
    }
}

// Evento de inicialização do DOM
document.addEventListener("DOMContentLoaded", () => {
    loadData();

    document.getElementById("searchInput").addEventListener("input", applyFilters);
    document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
});