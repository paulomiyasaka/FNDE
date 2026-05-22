export class RemoverPalete {
    constructor() {
        this.idAgrupamento = '';
        this.numeroPalete = '';
    }

    setDados(idAgrupamento, numeroPalete) {
        this.idAgrupamento = idAgrupamento;
        this.numeroPalete = numeroPalete;
        
    }

    async remover() {
        const formData = new FormData();
        formData.append('idAgrupamento', this.idAgrupamento);
        formData.append('numeroPalete', this.numeroPalete);
        
        //alert(this.matricula + " - SE: "+this.se+ " - centralizadora: " +this.centralizadora+" - status: "+this.status);
        try{
            const url = 'src/Controller/removerPalete.php';
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