export async function getSuperintendenciaOrigem() {
	
    try{
        const url = 'src/Controller/GetSuperintendenciaOrigem.php';
        const response = await fetch(url, {
            method: 'POST'
        });
        const data = await response.json();
        //console.log("Dados: "+data.se);
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