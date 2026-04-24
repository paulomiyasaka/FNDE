export async function getSuperintendencia() {
	
    try{
        const url = 'src/Controller/GetSuperintendencia.php';
        const response = await fetch(url, {
            method: 'POST'
        });
        const data = await response.json();
        //console.log("Dados: "+dados.se);
        if (data.resultado) {
            return data.se; 
        } else {
            console.error("Erro no PHP:", data.mensagem);
            return null;
        }    

    }catch(error){
        console.error("Erro na requisição:", error);
        return null;
    }

    
}