export class RegistrarAgrupamento {
    constructor() {
        this.matricula = '';
        this.se = '';
        this.centralizadora = '';
        this.status = '';
    }

    setDados(matricula, se, centralizadora, status) {
        this.matricula = matricula;
        this.se = se;
        this.centralizadora = centralizadora;
        this.status = status;
    }

    async registrar() {
        const formData = new FormData();
        formData.append('matricula', this.matricula);
        formData.append('se', this.se);
        formData.append('centralizadora', this.centralizadora);
        formData.append('status', this.status);
        alert(this.matricula + " - SE: "+this.se+ " - centralizadora: " +this.centralizadora+" - status: "+this.status);
        try{
            const url = 'src/Controller/registrarAgrupamento.php';
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            //console.log("Dados: "+data.centralizadora);
            if (data.resultado) {
                return data; 
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