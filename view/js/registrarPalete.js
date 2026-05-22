export class RegistrarPalete {
    constructor() {
        this.idAgrupamento = '';
        this.numeroPalete = '';
        this.pesoLiquido = '';
        this.pesoMinimoEstimado = '';
        this.pesoMaximoEstimado = '';
        this.EncomendaInicial = '';
        this.EncomendaFinal = '';
        this.codigoSKU = '';
        this.quantidadeEncomendas = '';
        this.faseUnitizacao = '';
        this.siglaCentralizadora = '';
        this.se = '';
    }

    setDados(idAgrupamento, numeroPalete, pesoLiquido, pesoMinimoEstimado, pesoMaximoEstimado, encomendaInicial, encomendaFinal, codigoSKU, quantidadeEncomendas, faseUnitizacao, siglaCentralizadora, se) {
        this.idAgrupamento = idAgrupamento;
        this.numeroPalete = numeroPalete;
        this.pesoLiquido = pesoLiquido;
        this.pesoMinimoEstimado = pesoMinimoEstimado;
        this.pesoMaximoEstimado = pesoMaximoEstimado;
        this.encomendaInicial = encomendaInicial;
        this.encomendaFinal = encomendaFinal;
        this.codigoSKU = codigoSKU;
        this.quantidadeEncomendas = quantidadeEncomendas;
        this.faseUnitizacao = faseUnitizacao;
        this.siglaCentralizadora = siglaCentralizadora;
        this.se = se;
    }

    async registrar() {
        const formData = new FormData();
        formData.append('idAgrupamento', this.idAgrupamento);
        formData.append('numeroPalete', this.numeroPalete);
        formData.append('pesoLiquido', this.pesoLiquido);
        formData.append('pesoMinimoEstimado', this.pesoMinimoEstimado);
        formData.append('pesoMaximoEstimado', this.pesoMaximoEstimado);
        formData.append('encomendaInicial', this.encomendaInicial);
        formData.append('encomendaFinal', this.encomendaFinal);
        formData.append('codigoSKU', this.codigoSKU);
        formData.append('quantidadeEncomendas', this.quantidadeEncomendas);
        formData.append('faseUnitizacao', this.faseUnitizacao);
        formData.append('siglaCentralizadora', this.siglaCentralizadora);
        formData.append('se', this.se);
        //alert(this.matricula + " - SE: "+this.se+ " - centralizadora: " +this.centralizadora+" - status: "+this.status);
        try{
            const url = 'src/Controller/registrarPalete.php';
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            //console.log("Dados: "+data.centralizadora);
            if (data.resultado) {
                //return data.resultado; 
                return data; 
            } else {
                console.error("Erro no PHP:", data.mensagem);
                return data;
            }    

        }catch(error){
            console.error("Erro na requisição:", error);
            return null;
        }
    }
}