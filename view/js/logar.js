import { bloquearSubmit, formReset, focusInput, url } from './funcoesModal.js';
import { RenderizarToast } from './RenderizarToast.js';

const form = document.querySelector('form');
const notificacao = new RenderizarToast();
focusInput('matricula');

form.addEventListener('submit', async function(e) {
    bloquearSubmit(e);

    const matricula = document.getElementById('matricula');
    const formData = new FormData();
    //console.log(codigo);
    // Adiciona o arquivo ao objeto FormData
    formData.append('matricula', matricula.value);

        await fetch('src/controller/logar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
            //throw new Error('Erro na rede ou o arquivo não foi encontrado');
            console.error('Erro no response');
        }
        return response.text();
        }).then(data => {
                
                let objetoData = data;
                objetoData = (typeof data === 'string') ? JSON.parse(data) : data;
                console.log(objetoData);
                //console.log(data);

                if (objetoData.resultado) {
                    
                      url('index.php');                    
                    
                } else {                 
                    formReset();                                        
                    notificacao.exibir('Matrícula inválida.', "danger");
                    focusInput('matricula');
                    //modalResposta('modal_falso', 'show', 'msg_erro', 'Caixa não encontrada!');
                    //hiddenModal('modal_falso', 'codigo_caixa');
                }
            })
            .catch(error => {
                //console.error('Erro:', error);
                //notificacao.exibir(error, "danger");
                notificacao.exibir('Erro ao consultar banco de dados.', "danger");
            });

});