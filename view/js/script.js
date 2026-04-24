import { habilitarTooltip, habilitarDropdown } from './funcoesAutoLoad.js';
import { RenderizarToast } from './RenderizarToast.js';

document.addEventListener('DOMContentLoaded', () => {
    // Joga a inicialização para o final da fila de tarefas
    setTimeout(() => {
        habilitarTooltip();
        habilitarDropdown();

    }, 0);
});


function adicionarLinha(botaoAtual) {
    const tabela = document.getElementById("minhaTabela");
    const totalLinhas = tabela.rows.length;
    
    // 1. Transforma o botão clicado em "Remover"
    botaoAtual.parentNode.innerHTML = '<button onclick="removerLinha(this)">Remover</button>';
    
    // 2. Cria a nova linha
    const novaLinha = tabela.insertRow(-1); 
    const celulaTexto = novaLinha.insertCell(0);
    const celulaBotao = novaLinha.insertCell(1);
    
    // 3. Define o conteúdo da nova linha com o botão "Adicionar"
    celulaTexto.innerHTML = "Linha " + (totalLinhas + 1);
    celulaBotao.innerHTML = '<button onclick="adicionarLinha(this)">Adicionar</button>';
}

function removerLinha(botao) {
    // Remove a linha correspondente ao botão clicado
    const linha = botao.parentNode.parentNode;
    linha.parentNode.removeChild(linha);
}







