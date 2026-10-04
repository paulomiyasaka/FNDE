/**
 * Motor Javascript para Renderização do Painel Dinâmico
 */

let datasetGlobal = { colunas: [], dados: [] };

document.addEventListener("DOMContentLoaded", async () => {
    await carregarDados();
    configurarFiltros();
});

async function carregarDados() {
    try {
        const response = await fetch('faturamento_dados.json');
        if (!response.ok) throw new Error("Não foi possível carregar o dados.json");

        datasetGlobal = await response.json();

        renderizarCabecalhoDinamico(datasetGlobal.colunas);
        renderizarTabelaDinamica(datasetGlobal.dados, datasetGlobal.colunas);
        atualizarKPIs(datasetGlobal.dados);
        popularFiltroFornecedores(datasetGlobal.dados);

    } catch (err) {
        console.error("Erro ao carregar dados do painel:", err);
    }
}

/**
 * 1. Monta o <thead> aplicando a cor exata lida do Excel
 */
function renderizarCabecalhoDinamico(colunas) {
    const thead = document.getElementById("tableHeader");
    let html = "<tr>";

    colunas.forEach(col => {
        const bg = col.bg_header || '#212529';
        const color = col.color_header || '#FFFFFF';
        const align = col.align ? `text-${col.align}` : 'text-start';

        // Estilo inline garante que a cor do Excel prevaleça sobre classes nativas
        html += `<th class="${align}" style="background-color: ${bg} !important; color: ${color} !important; vertical-align: middle;">
                    ${col.rotulo}
                 </th>`;
    });

    html += "</tr>";
    thead.innerHTML = html;
}

/**
 * 2. Formata células e aplica Badges coloridas de acordo com os valores e tipos
 */
function formatarCelula(valor, col) {
    if (valor === null || valor === undefined || valor === '') return '-';

    const fmtMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    const fmtNum = new Intl.NumberFormat('pt-BR');

    // Botão de PDF
    if (col.tipo === 'pdf' || (typeof valor === 'string' && valor.endsWith('.pdf'))) {
        return `<a href="${valor}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>`;
    }

    // Valores em Moeda
    if (col.tipo === 'moeda') {
        const num = parseFloat(String(valor).replace(',', '.'));
        return `<span class="fw-bold text-success">${fmtMoeda.format(isNaN(num) ? 0 : num)}</span>`;
    }

    // Status / Datas de Recebimento
    if (col.chave.includes('recebimento') || col.chave.includes('status')) {
        if (valor === 'Não Recebido' || valor === 'Pendente') {
            return `<span class="badge bg-danger-subtle text-danger border border-danger">${valor}</span>`;
        }
        return `<span class="badge bg-success-subtle text-success border border-success">${valor}</span>`;
    }

    // Números Inteiros / Formatados
    if (col.tipo === 'numero' && !isNaN(valor)) {
        return fmtNum.format(Number(valor));
    }

    return valor;
}

/**
 * 3. Renderiza o <tbody> da Tabela
 */
function renderizarTabelaDinamica(dados, colunas) {
    const tbody = document.getElementById("tableBody");
    tbody.innerHTML = "";

    dados.forEach(row => {
        const tr = document.createElement("tr");

        colunas.forEach(col => {
            const td = document.createElement("td");
            if (col.align) {
                td.className = `text-${col.align}`;
            }

            const valor = row[col.chave];
            td.innerHTML = formatarCelula(valor, col);
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    document.getElementById("recordCounter").innerText = `Mostrando ${dados.length} registros`;
}

/**
 * 4. Recalcula os Cards de Resumo Executivo (KPIs)
 */
function atualizarKPIs(dados) {
    let totalFaturamento = 0;
    let totalTiragem = 0;
    let totalPaletes = 0;
    let recebidos = 0;
    let naoRecebidos = 0;
    let somaDias = 0;
    let valorDiaria = 2.55;
    let dataUltimoFiltro = "--/--/----";

    dados.forEach(item => {
        // Busca flexível de propriedades
        const faturamento = item.valor_faturamento || item.faturamento || 0;
        const tiragem = item.tiragem || item.quantidade || 0;
        const paletes = item.posicoes_porta_palete || item.unidade_de_armazenamento_palete_com_1000_livros || 0;
        const statusData = item.data_recebimento || item.data_rec || '';
        const dias = item.dias_armazenados || item.dias_armazenamento || 0;

        totalFaturamento += parseFloat(String(faturamento).replace(',', '.')) || 0;
        totalTiragem += parseInt(tiragem) || 0;
        totalPaletes += parseInt(paletes) || 0;

        if (statusData && statusData !== 'Não Recebido') {
            recebidos++;
            somaDias += parseInt(dias) || 0;
            dataUltimoFiltro = statusData; 
        } else {
            naoRecebidos++;
        }
    });

    const mediaDias = recebidos > 0 ? Math.round(somaDias / recebidos) : 0;
    const fmtMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    const fmtNum = new Intl.NumberFormat('pt-BR');

    document.getElementById('cardTotalFaturamento').innerText = fmtMoeda.format(totalFaturamento);
    document.getElementById('cardTotalTiragem').innerText = fmtNum.format(totalTiragem);
    document.getElementById('cardTotalRegistros').innerText = fmtNum.format(recebidos);
    document.getElementById('cardTotalNaoRecebido').innerText = fmtNum.format(naoRecebidos);
    document.getElementById('cardTotalPaletes').innerText = fmtNum.format(totalPaletes);
    document.getElementById('cardTotalDias').innerText = `${mediaDias} dias`;
    document.getElementById('cardValorDiaria').innerText = fmtMoeda.format(valorDiaria);
    document.getElementById('cardFaturadoAte').innerText = dataUltimoFiltro;
}

/**
 * 5. Filtros de Busca e Select
 */
function configurarFiltros() {
    const searchInput = document.getElementById("searchInput");
    const filterFornecedor = document.getElementById("filterFornecedor");

    const aplicarFiltros = () => {
        const query = searchInput.value.toLowerCase();
        const fornecedorSel = filterFornecedor.value;

        const dadosFiltrados = datasetGlobal.dados.filter(row => {
            const combinaFornecedor = !fornecedorSel || (row.fornecedor_cnpj || row.fornecedor || '').includes(fornecedorSel);
            
            const combinaBusca = Object.values(row).some(val => 
                String(val).toLowerCase().includes(query)
            );

            return combinaFornecedor && combinaBusca;
        });

        renderizarTabelaDinamica(dadosFiltrados, datasetGlobal.colunas);
        atualizarKPIs(dadosFiltrados);
    };

    searchInput.addEventListener("input", aplicarFiltros);
    filterFornecedor.addEventListener("change", aplicarFiltros);
}

function popularFiltroFornecedores(dados) {
    const select = document.getElementById("filterFornecedor");
    const fornecedores = new Set();

    dados.forEach(row => {
        const f = row.fornecedor_cnpj || row.fornecedor;
        if (f) fornecedores.add(f);
    });

    select.innerHTML = '<option value="">Todos os Fornecedores</option>';
    fornecedores.forEach(f => {
        const opt = document.createElement("option");
        opt.value = f;
        opt.textContent = f;
        select.appendChild(opt);
    });
}