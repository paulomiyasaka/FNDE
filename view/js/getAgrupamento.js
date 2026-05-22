export async function getAgrupamento(agrupamento) {
	
    const formData = new FormData();
    formData.append('agrupamento', agrupamento);
    try{
        const url = 'src/Controller/GetAgrupamento.php';
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