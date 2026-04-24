import { modalResposta, atualizarPagina, formReset, bloquearSubmit } from './funcoesModal.js';


const modalSucesso = document.getElementById('modal_verdadeiro');
if (modalSucesso) {
    modalSucesso.addEventListener('hidden.bs.modal', () => {
        //window.location.reload();
        atualizarPagina();
    });
}

const modalErro = document.getElementById('modal_falso');
if (modalErro) {
    modalErro.addEventListener('hidden.bs.modal', () => {
        //console.log('Modal de erro fechado. O usuário pode tentar corrigir os dados.');
        //window.location.reload();
        atualizarPagina();
    });
}


document.querySelector('form').addEventListener('submit', async function(e) {
    bloquearSubmit(e);

    //const tabelaInput = document.getElementById('tabela');
    const arquivoInput = document.getElementById('file');
    const formData = new FormData();
    
    // Adiciona o arquivo ao objeto FormData
    formData.append('file', arquivoInput.files[0]);
    //formData.append('tabela', tabelaInput.value);


    const botao = document.querySelector('button');
    botao.disabled = true;
    //botao.innerText = "Processando...";
    botao.innerHTML = "Aguarde <img src=\"img/load.gif\" style=\"vertical-align: middle;\">";
    
    
    // Envia para o controller PHP
    await fetch('src/controller/importar.php', {
        method: 'POST',
        body: formData
    })
    //.then(response => response.json() ) // Espera um retorno JSON    
    .then(response => {
        // Verifica se o servidor respondeu com status 200-299
        if (!response.ok) {
            //throw new Error('Erro na rede ou o arquivo não foi encontrado');
            console.error('Erro no response');
        }
        return response.text(); // Primeiro pegamos como texto para depurar se necessário
    })
    .then(data => {
        console.log(data);
        let objetoData = (typeof data === 'string') ? JSON.parse(data) : data;
        console.log("Retorno: ", objetoData.resultado);


        if (objetoData.resultado) {            
            //document.getElementById('resultado').innerHTML = "✅ Upload realizado com sucesso!";
            modalResposta('modal_verdadeiro', 'show', 'msg_sucesso', objetoData.mensagem);

        } else {
            //document.getElementById('resultado').innerHTML = "❌ Erro ao enviar: " + objetoData.mensagem;
            modalResposta('modal_falso', 'show', 'msg_erro', objetoData.mensagem);

        }   

        //formReset();

    })
    .catch(error => {
        console.error('Erro:', error);
        //document.getElementById('resultado').innerHTML = "❌ Erro na comunicação com o servidor.";
        //modalResposta('modal_falso', 'show', 'msg_erro', error);
        modalResposta('modal_falso', 'show', 'msg_erro', 'Verifique o nome da tabela e dos atributos, também confira se há registros duplicados para a chave primária.');
    })
    .finally(() => {
        // Isso executa independente de dar erro ou sucesso
        botao.disabled = false;
        botao.innerText = "Salvar Arquivo";
    });
});




