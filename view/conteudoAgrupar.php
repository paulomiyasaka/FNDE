


<div class="container mt-4">

    <!-- FORMULÁRIO -->
    <div class="card mb-3">
        <div class="card-body fs-5">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select id="estado" class="form-select fs-5">
                        <option value="">Selecione</option>
                        <option>DF</option>
                        <option>GO</option>
                        <option>SP</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Centralizadora</label>
                    <select id="centralizadora" class="form-select fs-5">
                        <option value="">Selecione</option>
                        <option>Central A</option>
                        <option>Central B</option>
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

    <!-- LANÇAMENTOS -->
    <div id="areaLancamento" class="card d-none">
        <div class="card-body fs-5">

            <div class="row mb-3">
                <div class="col-md-6 alert alert-secondary fs-5">
                    Paletes: <strong id="totalPaletes">0</strong>
                </div>
                <div class="col-md-6 alert alert-secondary fs-5">
                    Peso Total (kg): <strong id="pesoTotal">0.000</strong>
                </div>
            </div>

            <!-- INPUT + BOTÃO -->
            <div class="input-group mb-3">
                <input id="codigoPalete" class="form-control fs-5" placeholder="Código do palete">
                <button id="btnInserir" class="btn btn-primary fs-5">Inserir</button>
            </div>

            <table class="table table-sm table-bordered fs-5">
                <thead class="table-light">
                    <tr>
                        <th>Registro</th>
                        <th>Palete</th>
                        <th>Peso (kg)</th>
                    </tr>
                </thead>
                <tbody id="tabelaPaletes"></tbody>
            </table>

        </div>
    </div>

</div>

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
                <button class="btn btn-primary fs-5" id="modalConfirmar">Confirmar</button>
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
        Agrupamento pronto para fechamento
    </div>
</div>

