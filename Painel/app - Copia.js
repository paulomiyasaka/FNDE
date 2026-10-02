// Estado da Aplicação
let parsedData = [];

/**
 * Converte valores em string de moeda/número (ex: "R$ 30.477,60", "4162,400") em Number puro
 */
function parseCurrencyToNumber(value) {
    if (typeof value === "number") return value;
    if (!value) return 0;
    const cleanStr = String(value)
        .replace("R$", "")
        .replace(/\./g, "")
        .replace(",", ".")
        .trim();
    return parseFloat(cleanStr) || 0;
}

/**
 * Formata um número para o padrão Monetário BRL
 */
function formatCurrency(val) {
    return val.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

/**
 * Formata número para o padrão de milhar pt-BR
 */
function formatNumber(val, decimals = 0) {
    return val.toLocaleString("pt-BR", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Mapeia/Normaliza a estrutura dos objetos recebidos do JSON
 * garantindo que números e moedas fiquem no tipo Number para o calculo dos cards
 */
function normalizeJSONData(jsonData) {
    return jsonData.map(item => ({
        chave: item.chave || "",
        contem: item.contem || "",
        cnpj: item.cnpj || item.CNPJ || "",
        fornecedor: item.fornecedor || item["Nome do Fornecedor"] || "",
        sku: item.sku || item.SKU || "",
        descricao: item.descricao || item["Descrição Item"] || "",
        acervo: parseInt(item.acervo || item.Acervo) || 0,
        tiragem: parseInt(item.tiragem || item.Tiragem) || 0,
        peso: parseCurrencyToNumber(item.peso || item["Peso Tiragem"]),
        subtotal: parseCurrencyToNumber(item.subtotal || item.Subtotal),
        dataRecebimento: item.dataRecebimento || item["Data recebimento"] || "",
        valorFaturamento: parseCurrencyToNumber(item.valorFaturamento || item["Valor Faturamento"]),
        arquivoNf: item.arquivoNf || item["Arquivo NF"] || "",
        linkNf: item.linkNf || item["Link NF"] || ""
    }));
}

/**
 * Popula o select com fornecedores únicos
 */
function populateSupplierSelect(data) {
    const select = document.getElementById("filterFornecedor");
    
    // Limpa opções antigas mantendo apenas o placeholder
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
 * Recalcula e atualiza os Cards de Resumo
 */
function updateCards(filteredData) {
    const totalFaturamento = filteredData.reduce((acc, cur) => acc + cur.valorFaturamento, 0);
    const totalTiragem = filteredData.reduce((acc, cur) => acc + cur.tiragem, 0);
    const totalPeso = filteredData.reduce((acc, cur) => acc + cur.peso, 0);

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
            <td class="text-end">${formatNumber(item.peso, 2)}</td>
            <td class="text-end text-muted">${formatCurrency(item.subtotal)}</td>
            <td class="text-center"><span class="badge bg-info text-dark">${item.dataRecebimento}</span></td>
            <td class="text-end fw-bold text-success">${formatCurrency(item.valorFaturamento)}</td>
            <td class="text-center">
                <a class="btn btn-sm btn-outline-primary" title="Abrir NF: ${item.arquivoNf}" href="saida/${item.arquivoNf}">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

/**
 * Aplica os Filtros de Busca Texto e Seleção de Fornecedor
 */
function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const selectedSupplier = document.getElementById("filterFornecedor").value;

    const filtered = parsedData.filter(item => {
        const matchesSearch = 
            item.descricao.toLowerCase().includes(searchText) ||
            item.sku.toLowerCase().includes(searchText) ||
            item.fornecedor.toLowerCase().includes(searchText) ||
            item.chave.toLowerCase().includes(searchText);

        const matchesSupplier = selectedSupplier === "" || item.fornecedor === selectedSupplier;

        return matchesSearch && matchesSupplier;
    });

    renderTable(filtered);
    updateCards(filtered);
}

/**
 * Carrega o arquivo JSON e inicializa a aplicação
 */
async function loadData() {
    try {
        // Altere 'dados.json' para o caminho do seu arquivo JSON
        const response = await fetch('faturamento_dados.json');
        
        if (!response.ok) {
            throw new Error(`Erro ao carregar o arquivo JSON: ${response.statusText}`);
        }

        const rawJson = await response.json();
        
        // Normaliza e faz o parse dos dados de moeda e peso
        parsedData = normalizeJSONData(rawJson);
        
        // Inicializa a interface
        populateSupplierSelect(parsedData);
        renderTable(parsedData);
        updateCards(parsedData);

    } catch (error) {
        console.error("Falha ao inicializar os dados:", error);
        document.getElementById("tableBody").innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-4 text-danger">
                    Ocorreu um erro ao carregar os dados JSON. Verifique o console.
                </td>
            </tr>
        `;
    }
}

// Inicialização da Aplicação após o carregamento do DOM
document.addEventListener("DOMContentLoaded", () => {
    loadData();

    // Event Listeners
    document.getElementById("searchInput").addEventListener("input", applyFilters);
    document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
});