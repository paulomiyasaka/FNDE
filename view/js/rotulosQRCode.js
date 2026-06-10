import { getPaletesAgrupados } from './getPaletesAgrupados.js';
// ELEMENTOS
const codigoPalete = document.getElementById("codigoPalete");
let palete = 0;
const btnInserir = document.getElementById("btnInserir");
const btnImprimir = document.getElementById("btnImprimir");
const btnImprimirQR = document.getElementById("btnImprimirQR");
const btnImprimirQRUnificado = document.getElementById("btnImprimirQRUnificado");

const areaResultado = document.getElementById("areaResultado");
const rEstado = document.getElementById("rEstado");
const rIdAgrupamento = document.getElementById("rIdAgrupamento");
const rCentral = document.getElementById("rCentral");
const rQtd = document.getElementById("rQtd");
const rPeso = document.getElementById("rPeso");
const tabelaPaletes = document.getElementById("tabelaPaletes");
let index = 1;

// TOAST
const toastEl = document.getElementById("toastMsg");
const toastBody = toastEl.querySelector(".toast-body");
const toast = new bootstrap.Toast(toastEl);

// MODAL
const modal = new bootstrap.Modal(document.getElementById("modalInfo"));
const modalMensagem = document.getElementById("modalMensagem");

// ENTER
codigoPalete.addEventListener("keypress", e => {
    if (e.key === "Enter") {
        e.preventDefault();
        inserir();
    }
});

// CLICK
btnInserir.onclick = inserir;

async function inserir() {

    let codigo = codigoPalete.value;
    codigo = codigo.substring(0, 11).toUpperCase();
    

    if (!codigo) {
        mostrarToast("Informe o número do palete", "erro");
        return;
    }
    //alert(codigo);
    /*
    const paletesAgrupados = await getPaletesAgrupados(codigo); 
    console.log(paletesAgrupados.resultado);
    if (paletesAgrupados.resultado == false) {
        areaResultado.classList.add("d-none");
        btnImprimir.disabled = true;
        btnImprimirQR.disabled = true;
        btnImprimirQRUnificado.disabled = true;
        mostrarModal(`O palete número <strong>${codigo}</strong> não foi agrupado.`);
        return;
    }
    */

  
        

    palete = codigoPalete.value;
    preencherResultado(codigoPalete.value);
    mostrarToast("Palete inserido na lista", "sucesso");
}

function preencherResultado(dados) {
    
    let codigo = dados.substring(0, 11).toUpperCase();
    let peso = dados.substring(11, 22);
    let centralizadora = dados.substring(91, 94).toUpperCase();
    let se = dados.substring(94, 97).toUpperCase();

    areaResultado.classList.remove("d-none");
    console.log(dados);
    //console.log(dados.paletes);
    rEstado.textContent = se;
    rIdAgrupamento.textContent = "QR Code Único - TESTE";
    
    rCentral.textContent = `${centralizadora} - ${se}`;
    rQtd.textContent = index;

    const pesoTotal = peso;
    rPeso.textContent = pesoTotal;
    /*
    tabelaPaletes.innerHTML = "";
    tabelaPaletes.innerHTML += `
        <tr>
            <td>${index++}</td>
            <td>${codigo}</td>
        </tr>
    `;
    */
    
    
    btnImprimir.disabled = true;
    btnImprimirQR.disabled = true;
    btnImprimirQRUnificado.disabled = false;
/*
      //Percorre as partes e adiciona na tabela
        // Cria uma nova linha (tr)
        const linha = document.createElement('tr');

        // Cria a célula do índice
        const celulaIndice = document.createElement('td');
        celulaIndice.textContent = index;

        // Cria a célula do conteúdo
        const celulaConteudo = document.createElement('td');
        celulaConteudo.textContent = codigoPalete.value; // .trim() remove espaços extras

        // Adiciona as células na linha e a linha na tabela
        linha.appendChild(celulaIndice);
        linha.appendChild(celulaConteudo);
        tabelaPaletes.appendChild(linha);

        tabelaPaletes.innerHTML = ""; 
*/
        // 2. Adiciona uma nova linha no final da tabela
        let novaLinha = tabelaPaletes.insertRow();

        // 3. Adiciona as células (colunas) nessa linha
        let celulaId = novaLinha.insertCell(0);
        let celulaNome = novaLinha.insertCell(1);

        // 4. Insere o conteúdo nas células
        celulaId.textContent = index++;
        celulaNome.textContent = codigoPalete.value;
        tabelaPaletes.appendChild(novaLinha);
        
}

// PADRÕES
function mostrarModal(msg) {
    modalMensagem.innerHTML = msg;
    modal.show();
}

function mostrarToast(msg, tipo) {
    toastEl.classList.remove("text-bg-success", "text-bg-danger");
    toastEl.classList.add(tipo === "erro" ? "text-bg-danger" : "text-bg-success");
    toastBody.textContent = msg;
    toast.show();
}

// IMPRIMIR
btnImprimir.onclick = () => {
    mostrarToast("Abrindo rótulo DataMatrix para impressão...", "sucesso");
    window.open(`gerar_rotulo.php?id=${palete}`, "_blank");
};

btnImprimirQR.onclick = () => {
    mostrarToast("Abrindo rótulo QR Code para impressão...", "sucesso");
    window.open(`gerar_rotulo_qr_code.php?id=${palete}`, "_blank");
};

btnImprimirQRUnificado.onclick = () => {
    mostrarToast("Abrindo rótulo QR Code Unificado para impressão...", "sucesso");
    window.open(`rotulo_teste_qr_unico.php?p=${palete}`, "_blank");
};


