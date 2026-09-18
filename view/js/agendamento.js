// Instâncias dos Modais e Toasts
const toastEl = new bootstrap.Toast(document.getElementById('liveToast'));
const modalCancel = new bootstrap.Modal(document.getElementById('modalCancelAgen'));
const modalReactivate = new bootstrap.Modal(document.getElementById('modalReactivateAgen'));
const modalEditNf = new bootstrap.Modal(document.getElementById('modalEditarNf'));

let tempTargetName = "";

// 1. Navegação SPA
function showSection(id) {
    document.querySelectorAll('main section').forEach(s => s.classList.add('d-none'));
    document.getElementById(`sec-${id}`).classList.remove('d-none');
    
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    if(id.includes('nf')) document.getElementById('nav-nf').classList.add('active');
    else document.getElementById('nav-agen').classList.add('active');
}

// 2. Feedback Visual (Toasts)
function triggerToast(msg, type) {
    const toastDOM = document.getElementById('liveToast');
    toastDOM.classList.remove('toast-success', 'toast-error');
    toastDOM.classList.add(type === 'success' ? 'toast-success' : 'toast-error');
    document.getElementById('toast-text').textContent = msg;
    toastEl.show();
}

// 3. Submissão do Formulário de Agendamento
document.getElementById('form-agendamento').onsubmit = function(e) {
    e.preventDefault();
    const sku = document.getElementById('sku-agen').value;
    if(sku) {
        triggerToast("Agendamento para o SKU (" + sku + ") registrado!", "success");
        this.reset();
    } else {
        triggerToast("Por favor, informe o SKU ou livro.", "error");
    }
};

// 4. Modais de Ação (Cancelar/Reativar)
function openCancelModal(nome) {
    tempTargetName = nome;
    document.getElementById('target-cancel').innerText = nome;
    modalCancel.show();
}

function executeCancel() {
    modalCancel.hide();
    triggerToast(`Agendamento do SKU ${tempTargetName} foi CANCELADO.`, 'error');
}

function openReactivateModal(nome) {
    tempTargetName = nome;
    document.getElementById('target-reactivate').innerText = nome;
    modalReactivate.show();
}

function executeReactivate() {
    modalReactivate.hide();
    triggerToast(`Agendamento do SKU ${tempTargetName} foi REATIVADO.`, 'success');
}

// 5. Modal Editar Nota Fiscal
function openEditNfModal(sku, protocolo, numero, data, peso, paletes) {
    document.getElementById('edit-nf-sku').value = sku;
    document.getElementById('edit-nf-protocolo').value = protocolo;
    document.getElementById('edit-nf-numero').value = numero;
    document.getElementById('edit-nf-data').value = data;
    document.getElementById('edit-nf-peso').value = peso;
    document.getElementById('edit-nf-paletes').value = paletes;
    modalEditNf.show();
}

function saveEditNf() {
    modalEditNf.hide();
    triggerToast("Dados da Nota Fiscal atualizados!", "success");
}

// 6. Checkbox "Selecionar Todos"
document.getElementById('selectAllNf').onclick = function() {
    document.querySelectorAll('.nf-check').forEach(c => c.checked = this.checked);
};

// 7. Ordenação de Tabelas
function sortTable(n, tableId) {
    let table = document.getElementById(tableId);
    let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    switching = true;
    dir = "asc";
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) { shouldSwitch = true; break; }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount ++;
        } else {
            if (switchcount == 0 && dir == "asc") { dir = "desc"; switching = true; }
        }
    }
}

// 8. População dos Dados Baseados na Modelagem de tb_agendamento
function populateMockData() {
    const statusList = ["Pendente", "Cancelado", "Entregue"];
    const badgeClasses = ["badge-pendente", "badge-cancelado", "badge-entregue"];
    
    // Tabela Agendamentos
    const tbodyAgen = document.getElementById('tbody-agendamentos');
    const selectAgendamentoNf = document.getElementById('select-agendamento-nf');
    
    let htmlAgen = "";
    let optionsNf = "<option value=''>Selecione o agendamento pendente...</option>";

    for (let i = 1; i <= 20; i++) {
        const idx = Math.floor(Math.random() * 3);
        const currentStatus = statusList[idx];
        const idAgen = i;
        const skuStr = `SKU-2026-${1000 + i}`;
        const dataSolic = `2026-04-${String(Math.ceil(Math.random() * 15)).padStart(2, '0')}`;
        const dataPrev = `2026-04-${String(15 + Math.ceil(Math.random() * 15)).padStart(2, '0')}`;
        const pesoPrev = (Math.random() * 500 + 100).toFixed(2);
        const paletesPrev = Math.floor(Math.random() * 10) + 1;

        let btnAcao = "";
        if (currentStatus === "Pendente") {
            btnAcao = `<button class="btn btn-sm btn-outline-danger" onclick="openCancelModal('${skuStr}')"><i class="fas fa-times"></i> Cancelar</button>`;
            optionsNf += `<option value="${idAgen}">${idAgen} - ${skuStr} (Prev: ${dataPrev})</option>`;
        } else if (currentStatus === "Cancelado") {
            btnAcao = `<button class="btn btn-sm btn-outline-success" onclick="openReactivateModal('${skuStr}')"><i class="fas fa-redo"></i> Reativar</button>`;
        } else {
            btnAcao = `<span class="text-muted small"><i class="fas fa-check-double"></i> Entregue</span>`;
        }

        htmlAgen += `
            <tr>
                <td><strong>#${idAgen}</strong></td>
                <td class="fw-medium">${skuStr}</td>
                <td>${dataSolic}</td>
                <td>${dataPrev}</td>
                <td>${pesoPrev} kg</td>
                <td>${paletesPrev} UN</td>
                <td><span class="badge ${badgeClasses[idx]}">${currentStatus}</span></td>
                <td>${btnAcao}</td>
            </tr>`;
    }
    tbodyAgen.innerHTML = htmlAgen;
    selectAgendamentoNf.innerHTML = optionsNf;

    // Tabela Notas Fiscais / Recebimento
    const tbodyNF = document.getElementById('tbody-nf');
    let htmlNF = "";
    
    for (let i = 1; i <= 15; i++) {
        const skuStr = `SKU-2026-${1000 + i}`;
        const protocolo = `SEI-${8000 + i}`;
        const numNf = `NF-${45000 + i}`;
        const dataReal = `2026-04-${String(Math.ceil(Math.random() * 20)).padStart(2, '0')}`;
        const pesoPrev = (Math.random() * 500 + 100).toFixed(2);
        const pesoReal = (parseFloat(pesoPrev) + (Math.random() * 10 - 5)).toFixed(2);
        const paletesPrev = Math.floor(Math.random() * 8) + 1;
        const paletesReal = paletesPrev;

        htmlNF += `
            <tr>
                <td class="text-center"><input type="checkbox" class="nf-check"></td>
                <td class="fw-medium">${protocolo}</td>
                <td>${skuStr}</td>
                <td>${numNf}</td>
                <td>${dataReal}</td>
                <td>${pesoReal} / ${pesoPrev} kg</td>
                <td>${paletesReal} / ${paletesPrev} UN</td>
                <td><a href="#" class="pdf-link"><i class="fas fa-file-pdf"></i> Visualizar NF</a></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="openEditNfModal('${skuStr}', '${protocolo}', '${numNf}', '${dataReal}', '${pesoReal}', '${paletesReal}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </td>
            </tr>`;
    }
    tbodyNF.innerHTML = htmlNF;
}

document.addEventListener('DOMContentLoaded', populateMockData);