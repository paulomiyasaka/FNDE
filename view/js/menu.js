export const menuBotaoManager = {

    container: document.getElementById("btns_conferencia"),  

    adicionar: function(chave) {
        const dados = configBotoes[chave];
        if (!dados || document.getElementById(dados.id)) return;

        const btn = document.createElement("button");
        btn.id = dados.id;
        btn.type = "button";
        btn.className = `btn ${dados.classe} m-1`;
        btn.textContent = dados.texto;

        Object.entries(dados.attrs).forEach(([attr, valor]) => {
            btn.setAttribute(attr, valor);
        });

        this.container.appendChild(btn);
        this.container.classList.remove("invisible");
    },

    remover: function(chave) {
        const dados = configBotoes[chave];
        const btn = document.getElementById(dados?.id);
        if (btn) btn.remove();

        if (this.container.children.length === 0) {
            this.container.classList.add("invisible");
        }
    },

    limparTudo: function() {
        this.container.innerHTML = "";
        this.container.classList.add("invisible");
    },

    renderizarBotoes: function(nomePerfil) {
        this.limparTudo(); // Remove o que estava antes
        const botoesParaCriar = perfisDeAcesso[nomePerfil];
        
        if (botoesParaCriar) {
            botoesParaCriar.forEach(chave => this.adicionar(chave));
        }
    }

};

const perfisDeAcesso = {

    administrador: ['reter', 'confirmar', 'corrigirCaixa', 'corrigirCliente'],

    gestor: ['reter', 'confirmar', 'corrigirCaixa', 'corrigirCliente'],

    usuario: ['reter', 'confirmar', 'alterarQuebra']
};



const configBotoes = {
    reter: {
        id: "btn_reter_caixa",
        texto: "Reter Caixa",
        classe: "btn-outline-danger",
        attrs: {"data-bs-toggle": "modal", "data-bs-target": "#modal_alerta" }
    },
    confirmar: {
        id: "btn_confirmar_caixa",
        texto: "Confirmar para gerar carta",
        classe: "btn-outline-primary",
        attrs: {}
    },
    alterarQuebra: {
        id: "btn_alterar_quebra_sequencia",
        texto: "Alterar Quebra de Sequência",
        classe: "btn-outline-primary",
        attrs: { "data-bs-toggle": "modal", "data-bs-target": "#modal_alterar_quebra_sequencia" }
    },
    corrigirCaixa: {
        id: "btn_corrigir_informacoes_caixa",
        texto: "Corrigir Informações da Caixa",
        classe: "btn-outline-primary",
        attrs: { "data-bs-toggle": "modal", "data-bs-target": "#modal_corrigir_caixa" }
    },
    corrigirCliente: {
        id: "btn_corrigir_informacoes_cliente",
        texto: "Corrigir Cadastro SGD",
        classe: "btn-outline-primary",
        attrs: { "data-bs-toggle": "modal", "data-bs-target": "#modal_corrigir_cliente" }
    },
    gerarCarta: {
        id: "btn_gerar_carta_devolucao",
        texto: "Gerar Carta de Devolução",
        classe: "btn-outline-primary",
        attrs: { "data-bs-toggle": "modal", "data-bs-target": "#modal_gerar_carta_devolucao" }
    }
};
