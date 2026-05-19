export class CancelarAgrupamento {
    constructor() {
        this.id = 0;
        this.status = 'CANCELADO';
    }

    setDados(id) {
        this.id = id;
    }

    async cancelar() {
        const formData = new FormData();
        formData.append('id', this.id);
        formData.append('status', this.status);
        //alert(this.matricula + " - SE: "+this.se+ " - centralizadora: " +this.centralizadora+" - status: "+this.status);
        try{
            const url = 'src/Controller/cancelarAgrupamento.php';
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            //console.log("Dados: "+data.centralizadora);
            if (data.resultado) {
                //return data.resultado; 
                return data.agrupamento; 
            } else {
                console.error("Erro no PHP:", data.mensagem);
                return null;
            }    

        }catch(error){
            console.error("Erro na requisição:", error);
            return null;
        }
    }
}