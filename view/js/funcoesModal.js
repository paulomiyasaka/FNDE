export function modalResposta(id_modal, status, id_mensagem, msg){
	
	let elementoMensagem = document.getElementById(id_mensagem);
	let elementoModal = document.getElementById(id_modal);
	const modalResposta = new bootstrap.Modal(elementoModal, {
		keyboard: true
	});

	if (elementoMensagem) {
        elementoMensagem.innerHTML = msg;
    }

	let instanciaModal = bootstrap.Modal.getOrCreateInstance(elementoModal);

    if (status === 'show' || status === true) {
        instanciaModal.show();
    } else if (status === 'hide' || status === false) {
        instanciaModal.hide();
    }
	
}


export function atualizarPagina(){

	document.location.reload(true);
	
}


export function formReset(){

	document.getElementsByTagName("form")[0].reset();

}

export function bloquearSubmit(e){
    e.preventDefault();    
}

export function focusInput(idInput){
	const input = document.getElementById(idInput);
	input.focus();
}

export function hiddenModal(idModal, idInput){
	const modalErro = document.getElementById(idModal);
	if (modalErro) {
	    modalErro.addEventListener('hidden.bs.modal', () => {
	        //console.log('Modal de erro fechado. O usuário pode tentar corrigir os dados.');
	        //window.location.reload();
	        focusInput(idInput);
	    });
	}
}


export function voltar(){
	window.history.back();
}


export function url(url){
	location.href=url;
}
