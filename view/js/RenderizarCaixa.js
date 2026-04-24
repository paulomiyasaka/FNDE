export class RenderizarCaixa {
    constructor(idTabela, idCorpo) {
        this.tabela = document.getElementById(idTabela);
        this.corpo = document.getElementById(idCorpo);
    }

    exibirDados(dadosCaixa, listaClasse = null) {
        this.corpo.innerHTML = '';
        let codigo = 0;
        const tr = document.createElement('tr');
        const classe = listaClasse;
        // Defina a ordem exata das propriedades conforme o seu <thead>
        const chaves = [
            'numeroCaixa', 
            'siglaCliente', 
            'nomeCliente', 
            'codigoCliente', 
            'quantidadeLotes', 
            'quantidadeObjetos', // adicionei conforme seu HTML anterior
            'loteClienteInicial', 
            'loteClienteFinal',
            'dataMovimento',
            'quebraSequencia',
            'armazenar',
            'prazoArmazenamento',
            'fragmentar'
        ];

        chaves.forEach((chave, index) => {
            // Cria 'th' para a primeira coluna (Nº Caixa) e 'td' para as demais
            const celula = document.createElement(index === 0 ? 'th' : 'td');
            if(chave === 'numeroCaixa'){
                codigo = dadosCaixa['numeroCaixa'];
            }
            // Garante que não apareça "undefined" se o campo estiver vazio
            celula.textContent = dadosCaixa[chave] ?? ''; 
            
            if (index === 0) celula.scope = "row"; // Boa prática para acessibilidade em <th>
            if(index === 8){
                const data = new Date(dadosCaixa[chave]+'T00:00:00');
                const dataFormatada = data.toLocaleDateString('pt-BR');
                celula.textContent = dataFormatada;

            }
            if (index === 10 ? celula.classList.add('border-start') : '');
            if(classe !== null){

                celula.classList.add(classe);    
            }
            celula.classList.add('p-3');
            tr.appendChild(celula);
        });

        this.corpo.appendChild(tr);
        const titulo = document.getElementById('titulo_tabela_caixa');
        titulo.innerText = `Conferência da caixa número: ${codigo}` ;
        this.tabela.style.display = 'table';
    }   


    ocultarTabela() {
        this.tabela.style.display = 'none';
        this.corpo.innerHTML = '';
    }
}
