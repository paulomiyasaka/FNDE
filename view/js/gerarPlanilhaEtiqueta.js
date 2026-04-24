import { focusInput, bloquearSubmit, atualizarPagina } from './funcoesModal.js';
import { RenderizarToast } from './RenderizarToast.js';
import { getSession } from './getSession.js';

const formGerarCarta = document.getElementById('form_gerar_plantilha_etiqueta');
const aguarde = document.getElementById('aguarde');
const notificacao = new RenderizarToast();

formGerarCarta.addEventListener('submit', async function(e) {
    bloquearSubmit(e);
    aguarde.classList.remove('invisible');
    aguarde.classList.add('visible'); 
    const url = 'src/controller/gerarPlanilhaEtiqueta.php';
    
    try{
        const session = await getSession();
        
        if(session.perfil === "GESTOR" || session.perfil === "ADMINISTRADOR"){
            const tabela = document.getElementById('tabela_etiqueta');
            const linhas = tabela.querySelectorAll('tbody tr');
            const resultado = [];

            linhas.forEach(linha => {
                const destinatarios = linha.querySelector('select[name="destinatarios[]"]');
                const embalagem = linha.querySelector('select[name="tipo_embalagem[]"]');
                const quantEtiquetas = linha.querySelector('input[name="quantidade_etiqueta[]"]');

                if (destinatarios && quantEtiquetas && embalagem) {
                    resultado.push({
                        destinatario: destinatarios.value,
                        quantidade: quantEtiquetas.value,
                        embalagem: embalagem.value
                    });
                }
            });

            const formData = new FormData();
            //formData.append('etiqueta', resultado);
            formData.append('etiqueta', JSON.stringify(resultado));
            formData.append('origem', session.se);
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if(data.resultado){
                notificacao.exibir(`Planilha gerada com sucesso!`, "success");
                
            }else{
                notificacao.exibir(`Erro ao tentar gerar a planilha!`, "danger");
                console.log(data);
                //notificacao.exibir(data, "danger");

            }//if data.resultado


        }else{
            notificacao.exibir(`Perfil sem permissão para gerar a planilha!`, "danger");
        }//if perfil getSession


    }catch (error) {
        console.error('Erro na requisição:', error);
        notificacao.exibir(`Erro ao gerar planilha!`, "danger");

    } finally {
        aguarde.classList.remove('visible');
        aguarde.classList.add('invisible');
    }//try, catch, finally

    

});