import { RegistrarAgrupamento } from './registrarAgrupamento.js';

const agrupamentoService = new RegistrarAgrupamento();

document.addEventListener('DOMContentLoaded', () => {
    // 1. Carrega os dados assim que abre a página
    carregarListaAgrupamentos();

    // 2. Ação do botão "Novo Lançamento" (Esconde a lista, mostra o form)
    document.getElementById('btnNovoLancamento').addEventListener('click', () => {
        document.getElementById('areaListagem').classList.add('d-none');
        document.getElementById('blocoFormulario').classList.remove('d-none');
    });

    // 3. Ação do botão "Voltar para a Lista"
    document.getElementById('btnVoltarLista').addEventListener('click', () => {
        document.getElementById('blocoFormulario').classList.add('d-none');
        document.getElementById('areaListagem').classList.remove('d-none');
        carregarListaAgrupamentos(); // Recarrega para trazer novos registros inseridos
    });
});

async function carregarListaAgrupamentos() {
    const tbody = document.querySelector('#tabelaAgrupamentos tbody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Buscando registros...</td></tr>';

    // Chama o método listar() da sua classe que criamos na resposta anterior
    const dados = await agrupamentoService.listar(); 

    tbody.innerHTML = '';

    if (!dados || dados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Nenhum agrupamento encontrado.</td></tr>';
        return;
    }

    dados.forEach(item => {
        const linha = document.createElement('tr');
        
        // Define uma cor bonitinha com Bootstrap dependendo do status
        let badgeColor = "";
        if(item.status === 'ABERTO'){
            badgeColor = 'bg-success';
        }else if(item.status === 'FECHADO'){
            badgeColor = 'bg-primary';
        }else if(item.status === 'CANCELADO'){
            badgeColor = 'bg-warning text-dark';
        }

        linha.innerHTML = `
            <td>${item.idAgrupamento}</td>
            <td>${item.matricula}</td>
            <td>${item.siglaSe}</td>
            <td>${item.siglaCentralizadora}</td>
            <td><span class="badge ${badgeColor}">${item.status}</span></td>
        `;
        tbody.appendChild(linha);
    });
}