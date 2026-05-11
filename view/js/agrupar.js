// Instâncias dos Modais e Toasts
const toastEl = new bootstrap.Toast(document.getElementById('liveToast'));
const modalCancel = new bootstrap.Modal(document.getElementById('modalCancelAgen'));
const modalReactivate = new bootstrap.Modal(document.getElementById('modalReactivateAgen'));
const modalEditNf = new bootstrap.Modal(document.getElementById('modalEditarNf'));

// Variáveis de controle temporário
let tempTargetName = "";

// 1. Navegação SPA
function showSection(id) {
    document.querySelectorAll('main section').forEach(s => s.classList.add('d-none'));
    document.getElementById(`sec-${id}`).classList.remove('d-none');
    
    // Atualiza classes ativas no Menu
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

// 3. Cadastrar Agendamento (Simulação)
document.getElementById('form-agendamento').onsubmit = function(e) {
    e.preventDefault();
    const forn = document.getElementById('fornecedor').value;
    if(forn) {
        triggerToast("Agendamento de " + forn + " cadastrado no sistema!", "success");
        this.reset(); // Limpa o formulário
    } else {
        triggerToast("Por favor, preencha o nome do fornecedor.", "error");
    }
};

// 4. Modais de Ação do Agendamento (Cancelar/Reativar)
function openCancelModal(nome) {
    tempTargetName = nome;
    document.getElementById('target-cancel').innerText = nome;
    modalCancel.show();
}

function executeCancel() {
    modalCancel.hide();
    triggerToast(`Agendamento de ${tempTargetName} foi CANCELADO.`, 'error');
}

function openReactivateModal(nome) {
    tempTargetName = nome;
    document.getElementById('target-reactivate').innerText = nome;
    modalReactivate.show();
}

function executeReactivate() {
    modalReactivate.hide();
    triggerToast(`Agendamento de ${tempTargetName} foi REATIVADO com sucesso.`, 'success');
}

// 5. Modal de Editar Nota Fiscal
function openEditNfModal(fornecedor, data, protocolo) {
    document.getElementById('edit-nf-fornecedor').value = fornecedor;
    document.getElementById('edit-nf-data').value = data;
    document.getElementById('edit-nf-protocolo').value = protocolo;
    modalEditNf.show();
}

function saveEditNf() {
    modalEditNf.hide();
    triggerToast("Dados da Nota Fiscal atualizados!", "success");
}

// 6. Checkbox "Selecionar Todos" na NF
document.getElementById('selectAllNf').onclick = function() {
    document.querySelectorAll('.nf-check').forEach(c => c.checked = this.checked);
};

document.getElementById('selectAllEmails').onclick = function() {
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

// 8. Gerador de Mock Data (25 linhas Agendamento, 15 linhas NF)
function populateMockData() {
    const fornecedores = ["Distribuidora Alpha", "Transportes Brasil", "Expresso Jundiaí", "Global Logistics", "Cargas Rápido Sul"];
    const statusList = ["Pendente", "Cancelado", "Entregue"];
    const badgeClasses = ["badge-pendente", "badge-cancelado", "badge-entregue"];
    
    // Povoar Agendamentos (25 linhas)
    const tbodyAgen = document.getElementById('tbody-agendamentos');
    let htmlAgen = "";
    
    for (let i = 1; i <= 25; i++) {
        const idx = Math.floor(Math.random() * 3); // Sorteia o status
        const currentStatus = statusList[idx];
        const fornNome = `${fornecedores[i % 5]} (OP-${1000 + i})`;
        const dataSolicitacao = `${String(Math.ceil(Math.random() * 30)).padStart(2, '0')}/03/2026`;
        const dataPrevista = `${String(Math.ceil(Math.random() * 30)).padStart(2, '0')}/04/2026`;
        const peso = Math.ceil(Math.random() * 1000);
        const qtdPaletes = Math.ceil(Math.random() * 30);
        
        // Define o botão baseado no status
        let btnAcao = "";
        if (currentStatus === "Pendente") {
            btnAcao = `<button class="btn btn-sm btn-outline-danger" onclick="openCancelModal('${fornNome}')"><i class="fas fa-times"></i> Cancelar</button>`;
        } else if (currentStatus === "Cancelado") {
            btnAcao = `<button class="btn btn-sm btn-outline-success" onclick="openReactivateModal('${fornNome}')"><i class="fas fa-redo"></i> Reativar</button>`;
        } else {
            btnAcao = `<span class="text-muted small"><i class="fas fa-check-double"></i> Finalizado</span>`;
        }

        htmlAgen += `
            <tr>
                <td class="text-center"><input type="checkbox" class="nf-check"></td>
                <td class="fw-medium">${fornNome}</td>
                <td>${dataSolicitacao}</td>
                <td>${dataPrevista}</td>
                <td>${peso}</td>
                <td>${qtdPaletes}</td>
                <td><a href="#" class="pdf-link"><i class="fas fa-file-pdf"></i> Visualizar PDF</a></td>
                <td><span class="badge ${badgeClasses[idx]}">${currentStatus}</span></td>
                <td>${btnAcao}</td>
            </tr>`;
    }
    tbodyAgen.innerHTML = htmlAgen;

    // Povoar Notas Fiscais (15 linhas)
    const tbodyNF = document.getElementById('tbody-nf');
    let htmlNF = "";
    
    for (let i = 1; i <= 15; i++) {
        const fornNome = fornecedores[i % 5];
        const dataAgendamento = `2026-03-${String(10 + i).padStart(2, '0')}`;
        const dataEntrega = `2026-04-${String(10 + i).padStart(2, '0')}`;
        const protocolo = `SEI-${64321200 + i}`;
        
        htmlNF += `
            <tr>
                <td class="text-center"><input type="checkbox" class="nf-check"></td>
                <td class="fw-medium">${fornNome}</td>
                <td>${dataAgendamento}</td>
                <td>${dataEntrega}</td>
                <td>${protocolo}</td>
                <td><a href="#" class="pdf-link"><i class="fas fa-file-pdf"></i> Visualizar PDF</a></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="openEditNfModal('${fornNome}', '${dataEntrega}', '${protocolo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </td>
            </tr>`;
    }
    tbodyNF.innerHTML = htmlNF;
}

// Iniciar sistema carregando os dados falsos
document.addEventListener('DOMContentLoaded', populateMockData);