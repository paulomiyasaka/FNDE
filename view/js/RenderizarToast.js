export class RenderizarToast {
    constructor() {
        this.container = this._criarContainer();
    }

    // Método privado para garantir que o container exista apenas uma vez
    _criarContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * @param {string} mensagem 
     * @param {string} tipo (success, danger, warning, primary)
     */
    exibir(mensagem, tipo = 'success', tempoExibicao = 5000) {
        const id = `toast-${Date.now()}`;
        const icone = this._getIcone(tipo);
        
        const html = `
            <div id="${id}" class="toast align-items-center text-white bg-${tipo} border-0 fs-5" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        ${icone} ${mensagem}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const htmlAmarelo = `
            <div id="${id}" class="toast align-items-center text-dark bg-${tipo} border-0 fs-5" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        ${icone} ${mensagem}
                    </div>
                    <button type="button" class="btn-close btn-close-dark me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        if(tipo == 'warning'){
            this.container.insertAdjacentHTML('beforeend', htmlAmarelo);
        }else{
            this.container.insertAdjacentHTML('beforeend', html);    
        }
        

        const elemento = document.getElementById(id);
        const toast = new bootstrap.Toast(elemento, { delay: tempoExibicao });
        
        toast.show();

        // Remove do DOM após fechar para manter a página leve
        elemento.addEventListener('hidden.bs.toast', () => elemento.remove());
    }

    _getIcone(tipo) {
        const icones = {
            success: '<i class="bi bi-check-circle-fill me-2"></i>',
            danger:  '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
            warning: '<i class="bi bi-exclamation-circle-fill me-2"></i>',
            primary: '<i class="bi bi-info-circle-fill me-2"></i>'
        };
        return icones[tipo] || '';
    }
}
