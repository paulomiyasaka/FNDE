import { RenderizarToast } from './RenderizarToast.js';

const TEMPO_LIMITE = 10 * 60 * 1000; 
const AVISO_ANTECIPADO = 2 * 60 * 1000;

let timeoutSessao;
let avisoSessao;

function iniciarCronometroSessao() {
    clearTimeout(timeoutSessao);
    clearTimeout(avisoSessao);

    // 1. Agenda o aviso visual (Toast)
    avisoSessao = setTimeout(() => {
        const notificacao = new RenderizarToast();
        notificacao.exibir("Sua sessão expirará em 2 minutos por inatividade. Atualize a página para continuar logado.", "warning", 10000);
    }, TEMPO_LIMITE - AVISO_ANTECIPADO);

    // 2. Agenda o redirecionamento automático para o logout
    timeoutSessao = setTimeout(() => {
        window.location.href = 'logout.php';
    }, TEMPO_LIMITE);
}

function resetarCronometro() {
    iniciarCronometroSessao();
}

document.addEventListener('DOMContentLoaded', iniciarCronometroSessao);

document.addEventListener('click', (e) => {
    if (e.target.tagName === 'BUTTON') resetarCronometro();
});