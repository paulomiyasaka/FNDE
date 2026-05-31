import { getSuperintendencia } from './getSuperintendencia.js';
import { getSuperintendenciaOrigem } from './getSuperintendenciaOrigem.js';
import { getCentralizadora } from './getCentralizadora.js';
import { getCentralizadoraOrigem } from './getCentralizadoraOrigem.js';
import { RegistrarAgrupamento } from './registrarAgrupamento.js';
import { CancelarAgrupamento } from './cancelarAgrupamento.js';
import { FecharAgrupamento } from './fecharAgrupamento.js';
import { RegistrarPalete } from './registrarPalete.js';
import { RemoverPalete } from './removerPalete.js';
import { getSession } from './getSession.js';


let paletes = [];
let pesoTotal = 0;
let limiteAtingido = false;
let opcoesSE = "";
let opcoesSEOrigem = "";
let opcoesCentralizadora = "";
let opcoesCentralizadoraOrigem = "";

const pesoMaximo = 950;
let id = 0;
const campoId = document.getElementById("id_agrupamento");

const selectSuperintendencia = document.getElementById("select_superintendencia");
const selectSuperintendenciaOrigem = document.getElementById("select_superintendencia_origem");

const dadosSE = await getSuperintendencia();
const dadosSEOrigem = await getSuperintendenciaOrigem();

const selectCentralizadora = document.getElementById("select_centralizadora");
const selectCentralizadoraOrigem = document.getElementById("select_centralizadora_origem");
const selectTravarCentralizadora = document.getElementById("select_travar_centralizadora");

const codigoPalete = document.getElementById("codigoPalete");
const totalPaletesEl = document.getElementById("totalPaletes");
const pesoTotalEl = document.getElementById("pesoTotal");
const tabelaPaletes = document.getElementById("tabelaPaletes");

const btnAbrir = document.getElementById("btnAbrir");
const btnCancelar = document.getElementById("btnCancelar");
const btnFechar = document.getElementById("btnFechar");
const btnInserir = document.getElementById("btnInserir");

const areaLancamento = document.getElementById("areaLancamento");

const toastEl = document.getElementById("toastMsg");
const toastBody = toastEl.querySelector(".toast-body");
const toast = new bootstrap.Toast(toastEl);

const modal = new bootstrap.Modal(document.getElementById("modalConfirm"));
const modalTitulo = document.getElementById("modalTitulo");
const modalMensagem = document.getElementById("modalMensagem");
const modalConfirmar = document.getElementById("modalConfirmarAgrupamento");

if (selectSuperintendenciaOrigem && dadosSEOrigem.resultado) {
            let htmlOptionsSEOrigem = '<option value="" selected disabled>Selecione</option>';
            
            dadosSEOrigem.se.forEach(item => {
                htmlOptionsSEOrigem += `<option value="${item.siglaSe}">${item.siglaSe}</option>`;
            });

            selectSuperintendenciaOrigem.innerHTML = htmlOptionsSEOrigem;

            opcoesSEOrigem = dadosSEOrigem.se.map(item => 
                `<option value="${item.siglaSe}">${item.siglaSe}</option>`
            ).join('');

        }

        selectSuperintendenciaOrigem.onchange = async () => {

    const seOrigem = selectSuperintendenciaOrigem.value;
    const dadosCentralizadoraOrigem = await getCentralizadoraOrigem(seOrigem);
    
    if (dadosCentralizadoraOrigem.resultado) {

            let htmlOptionsCentralizadoraOrigem = '<option value="" selected disabled>Selecione</option>';
            
            dadosCentralizadoraOrigem.centralizadora.forEach(item => {
                htmlOptionsCentralizadoraOrigem += `<option value="${item.siglaCentralizadora}">${item.nomeCentralizadora}</option>`;
            });

            selectCentralizadoraOrigem.innerHTML = htmlOptionsCentralizadoraOrigem;

            opcoesCentralizadoraOrigem = dadosCentralizadoraOrigem.centralizadora.map(item => 
                `<option value="${item.siglaCentralizadora}">${item.nomeCentralizadora}</option>`
            ).join('');

        }//select_centralizadora

};



if (selectSuperintendencia && dadosSE.resultado) {
            let htmlOptionsSE = '<option value="" selected disabled>Selecione</option>';
            
            dadosSE.se.forEach(item => {
                htmlOptionsSE += `<option value="${item.siglaSe}">${item.siglaSe}</option>`;
            });

            selectSuperintendencia.innerHTML = htmlOptionsSE;

            opcoesSE = dadosSE.se.map(item => 
                `<option value="${item.siglaSe}">${item.siglaSe}</option>`
            ).join('');

        }//select_destinatario


selectSuperintendencia.onchange = async () => {

    const se = selectSuperintendencia.value;
    const dadosCentralizadora = await getCentralizadora(se);
    
    if (dadosCentralizadora.resultado) {

            let htmlOptionsCentralizadora = '<option value="" selected disabled>Selecione</option>';
            
            dadosCentralizadora.centralizadora.forEach(item => {
                htmlOptionsCentralizadora += `<option value="${item.siglaCentralizadora}">${item.nomeCentralizadora}</option>`;
            });

            selectCentralizadora.innerHTML = htmlOptionsCentralizadora;

            opcoesCentralizadora = dadosCentralizadora.centralizadora.map(item => 
                `<option value="${item.siglaCentralizadora}">${item.nomeCentralizadora}</option>`
            ).join('');

        }//select_centralizadora

};


btnAbrir.onclick = async () => {
    if (!selectSuperintendencia.value || !selectCentralizadora.value) {
        mostrarToast("Preencha SE e Centralizadora", "erro");
        return;
    }
    const session = await getSession();
    const matricula = session.matricula;
    const agrupamento = new RegistrarAgrupamento();
    agrupamento.setDados(matricula, selectSuperintendencia.value, selectCentralizadora.value, 'ABERTO');
    id = await agrupamento.registrar();

    if(id){                
        campoId.innerText = id;
        btnFechar.disabled = selectSuperintendencia.disabled = selectCentralizadora.disabled = true;
        btnAbrir.classList.add("d-none");
        btnCancelar.classList.remove("d-none");
        btnFechar.classList.remove("d-none");
        areaLancamento.classList.remove("d-none");
        
        mostrarToast("Englobamento aberto", "sucesso");
    }

    
};

codigoPalete.addEventListener("keypress", e => {
    if (e.key === "Enter") {
        e.preventDefault();
        inserirPalete();
    }
});

btnInserir.onclick = inserirPalete;

async function inserirPalete() {
    //if (limiteAtingido) return;

    const codigo = codigoPalete.value;
    //if (codigo.length != 97) return;
    if (codigo.length != 97) {
        abrirModal(
            "Palete inválido",
            `Verifique a informação e tente novamente`,
            () => atualizarTela()
        );
        return;
    }

    const numero = codigo.substring(0, 11).toUpperCase();
    const peso = parseFloat(codigo.substring(11, 22)); 
    const pesoMinimoEstimado = parseFloat(codigo.substring(22, 33));
    const pesoMaximoEstimado = parseFloat(codigo.substring(33, 44));
    const encomendaInicial = codigo.substring(44, 57).toUpperCase();
    const encomendaFinal = codigo.substring(57, 70).toUpperCase();
    const codigoSKU = codigo.substring(70, 85).toUpperCase();
    const quantidadeEncomendas = parseFloat(codigo.substring(85, 89));
    const faseUnitizacao = parseFloat(codigo.substring(89, 91));
    const siglaCentralizadora = codigo.substring(91, 94).toUpperCase();
    const siglaSe = codigo.substring(94, 97).toUpperCase();

    
    if (siglaSe.trim() !== selectSuperintendencia.value) {
        abrirModal(
            "Palete Divergente",
            `O palete ${numero} não é destinado a SE/${selectSuperintendencia.value}.<br>Verifique o palete informado.`
        );
        return;
    }

    if(selectTravarCentralizadora.value == 'SIM' && (siglaCentralizadora.trim() !== selectCentralizadora.value)){
        abrirModal(
            "Palete Divergente",
            `O palete ${numero} não é destinado a Centralizadora: ${selectCentralizadora.value}.<br>Verifique o palete informado.`
        );
        return;
    }
    
 
    const existente = paletes.find(p => p.numero === numero);
    if (existente) {
        abrirModal(
            "Palete duplicado",
            `O palete ${numero} já está registrado.<br>Deseja removê-lo?`,
            () => removerPalete(numero)
        );
        return;
    }


    if ((pesoTotal + peso) > pesoMaximo) {
        
        codigoPalete.disabled = true;
        limiteAtingido = true;

        abrirModal(
            "Limite de Peso Atingido",
            `Palete ${numero} desconsiderado.<br>
             Peso total atingiu ou ultrapassou 950 kg.<br>
             Feche ou cancele o englobamento.`             
        );
        return;
    }   

    const registrarPalete = new RegistrarPalete();
    registrarPalete.setDados( id, numero, peso, pesoMinimoEstimado, pesoMaximoEstimado, encomendaInicial, encomendaFinal, codigoSKU, quantidadeEncomendas, faseUnitizacao, siglaCentralizadora, siglaSe);
    const registrar = await registrarPalete.registrar();

    codigoPalete.value = "";
    //atualizarTela();
    //console.log(registrar.resultado);
    if(registrar.resultado){
        mostrarToast("Palete inserido", "sucesso");
        //atualizarTela();
        paletes.push({ id, numero, peso, siglaCentralizadora, siglaSe });
        pesoTotal += peso;
    }else{
        mostrarToast("Erro ao registrar o palete", "erro");
    }
    atualizarTela();
    
}

async function removerPalete(numero) {
    const p = paletes.find(p => p.numero === numero);
    paletes = paletes.filter(p => p.numero !== numero);
    pesoTotal -= p.peso;
    
    const removerPalete = new RemoverPalete();
    removerPalete.setDados( id, numero);
    const remover = await removerPalete.remover();
    
    //atualizarTela();
    //console.log(registrar.resultado);
    if(remover.resultado){
        mostrarToast("Palete removido", "sucesso");
        //atualizarTela();
    }else{
        mostrarToast("Erro ao remover o palete", "erro");
    }
    atualizarTela();
    

}

function atualizarTela() {
    totalPaletesEl.textContent = paletes.length;
    //pesoTotalEl.textContent = pesoTotal.toFixed(3);
    pesoTotalEl.textContent = pesoTotal.toFixed(3).replace('.', ',');

    tabelaPaletes.innerHTML = "";

    if(paletes.length > 1){
        btnFechar.disabled = false;
    }else{
        btnFechar.disabled = true;
    }

    [...paletes].reverse().forEach((p, index) => {
        const registro = paletes.length - index;
        tabelaPaletes.innerHTML += `
            <tr>
                <td>${registro}</td>
                <td>${p.numero}</td>
                <td>${p.peso.toFixed(3).replace('.', ',')}</td>
                <td>${p.siglaCentralizadora}</td>
                <td>${p.siglaSe}</td>
            </tr>`;
    });
}

btnFechar.onclick = () => {
    abrirModal(
        "Fechar Englobamento",
        `Você realmente quer fechar o englobamento?<br><br>
         <strong>Paletes:</strong> ${paletes.length}<br>
         <strong>Peso Total:</strong> ${pesoTotal.toFixed(3).replace('.', ',')} kg`,
        async () => {
            const fecharAgrupamento = new FecharAgrupamento();
            fecharAgrupamento.setDados(id, 'FECHADO');            
            const fechar = await fecharAgrupamento.fechar();
            if(fechar){
                mostrarToast("Englobamento fechado", "sucesso");
                location.reload();
            }else{
                mostrarToast("Erro ao fechar o Englobamento", "erro");
            }            
            
        }
    );
};

btnCancelar.onclick = () => {
    abrirModal(
        "Cancelar Englobamento",
        "Todos os paletes registrados serão descartados.",
        async () => {
            const cancelarAgrupamento = new CancelarAgrupamento();
            cancelarAgrupamento.setDados(id, 'CANCELADO');            
            const cancelar = await cancelarAgrupamento.cancelar();
            if(cancelar){
                mostrarToast("Englobamento cancelado", "sucesso");
                location.reload();
            }else{
                mostrarToast("Erro ao cancelar o Englobamento", "erro");
            }            
            
        }



    );
};

function abrirModal(titulo, mensagem, confirmar) {
    // Reseta o input do código do palete
    codigoPalete.value = "";

    modalTitulo.innerHTML = titulo;
    modalMensagem.innerHTML = mensagem;

    modalConfirmar.onclick = () => {
        modal.hide();
        if (confirmar) confirmar();
    };

    modal.show();
}

function mostrarToast(msg, tipo) {
    toastEl.classList.remove("text-bg-success", "text-bg-danger");
    toastEl.classList.add(tipo === "erro" ? "text-bg-danger" : "text-bg-success");
    toastBody.textContent = msg;
    toast.show();
}
