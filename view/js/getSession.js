export async function getSession() {
	
    try{
        const url = 'src/Controller/GetSession.php';
        const response = await fetch(url, {
            method: 'POST'
        });
        const data = await response.json();

        if (data.resultado) {
            return data.sessao; 
        } else {
            console.error("Erro no PHP:", data.mensagem);
            return null;
        }    

    }catch(error){
        console.error("Erro na requisição:", error);
        return null;
    }

    
}