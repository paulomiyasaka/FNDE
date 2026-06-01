<div class="container mt-4">

    <div id="areaListagem" class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Englobamentos Existentes</h5>
            <button id="btnNovoLancamento" class="btn btn-success fs-5">Novo Lançamento</button>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered fs-5" id="tabelaAgrupamentos">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">SE</th>
                        <th width="30%">Centralizadora</th>
                        <th width="10%">Qtd. Paletes</th>
                        <th width="10%">Qtd. Encomendas</th>
                        <th width="20%">Data</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td colspan="4" class="text-center text-muted">Carregando agrupamentos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="blocoFormulario" class="d-none">
        
        <div class="mb-3">
            <button id="btnVoltarLista" class="btn btn-secondary fs-5">← Voltar para a Lista</button>
        </div>

        <div class="card mb-3">
            <div class="card-body fs-5">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">SE Origem:</label>
                        <select id="select_superintendencia_origem" class="form-select fs-5" name="superintendencia_origem" required>
                            <option value="" selected disabled>Selecione</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Centralizadora Origem:</label>
                        <select id="select_centralizadora_origem" name="centralizadora_origem" class="form-select fs-5" required>
                            <option value="CAJ" selected >CLI CAJAMAR</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SE Destino:</label>
                        <select id="select_superintendencia" class="form-select fs-5" name="superintendencia" required>
                            <option value="" selected disabled>Selecione</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Centralizadora Destino:</label>
                        <select id="select_centralizadora" name="centralizadora" class="form-select fs-5" required>
                            <option value="" selected disabled>Selecione</option>
                        </select>
                    </div>                    

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button id="btnAbrir" class="btn btn-primary fs-5">Abrir</button>
                        <button id="btnCancelar" class="btn btn-danger fs-5 d-none">Cancelar</button>
                        <button id="btnFechar" class="btn btn-success fs-5 d-none">Fechar</button>
                    </div>

                </div>
            </div>
        </div>

        <div id="areaLancamento" class="card d-none">
            <div class="card-body fs-5">

                <div class="row mb-3">
                    <div class="col-md-4 alert alert-secondary fs-5">
                        ID Englobamento: <strong id="id_agrupamento"></strong>
                    </div>
                
                    <div class="col-md-4 alert alert-secondary fs-5">
                        Paletes: <strong id="totalPaletes">0</strong>
                    </div>
                    <div class="col-md-4 alert alert-secondary fs-5">
                        Peso Total (kg): <strong id="pesoTotal">0.000</strong>
                    </div>                
                </div>

                <div class="row input-group">
                    <div class="col-md-6 alert alert-secondary fs-5">
                    <label class="form-label">Travar Lançamento de Paletes para a Centralizadora Destino:</label>
                    </div>
                    <div class="col-md-6 alert alert-secondary fs-5">
                        <select id="select_travar_centralizadora" name="travar_centralizadora" class="form-select fs-5" required>
                            <option value="SIM" selected>SIM</option>
                            <option value="NAO">NÃO</option>
                        </select>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input id="codigoPalete" class="form-control text-center fs-4" placeholder="Informe o código do palete">                    
                    <button id="btnInserir" class="btn btn-primary fs-5">Inserir</button>
                </div>

                <table class="table table-sm table-bordered fs-5">
                    <thead class="table-light">
                        <tr>
                            <th>Registro</th>
                            <th>Palete</th>
                            <th>Peso (kg)</th>
                            <th>Sigla Centralizadora</th>
                            <th>Superintendência</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPaletes"></tbody>
                </table>

            </div>
        </div>

    </div> </div>
<!-- MODAL -->
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fs-5">
            <div class="modal-header">
                <h5 id="modalTitulo"></h5>
            </div>
            <div id="modalMensagem" class="modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary fs-5" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary fs-5" id="modalConfirmarAgrupamento">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastMsg" class="toast">
        <div class="toast-body fs-5"></div>
    </div>
</div>


<!-- OVERLAY DE FECHAMENTO -->
<div id="overlayFechamento" class="overlay-fechamento d-none">
    <div class="overlay-conteudo">
        Englogamento pronto para fechar.
    </div>
</div>