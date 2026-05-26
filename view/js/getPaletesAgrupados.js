    export async function getPaletesAgrupados(palete) {
    const formData = new FormData();
    formData.append('palete', palete);
	
    try{
        const url = 'src/Controller/GetPaletesAgrupados.php';
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

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