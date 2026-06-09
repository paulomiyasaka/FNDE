<!-- CONTEÚDO -->
<div class="container mt-4">

    <div class="card">
        <div class="card-body fs-5">

            <h4 class="mb-4 fs-4">Gerar Rótulo QR Code</h4>

            <!-- INPUT + BOTÃO -->
            <div class="input-group mb-3">
                <input
                    type="text"
                    id="codigoPalete"
                    class="form-control fs-5"
                    placeholder="Informe o número do palete"
                >
                <button id="btnInserir" class="btn btn-primary fs-5">
                    Inserir
                </button>
            </div>

            <!-- RESULTADO -->
            <div id="areaResultado" class="d-none">

                <div class="alert alert-secondary fs-5">
                    Palete localizado em englobado
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Id Englogamento:</strong> <span id="rIdAgrupamento"></span>
                    </div>
                    <div class="col-md-3">
                        <strong>SE:</strong> <span id="rEstado"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Centralizadora:</strong> <span id="rCentral"></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Quantidade de Paletes:</strong> <span id="rQtd"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Peso Total (kg):</strong> <span id="rPeso"></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <button id="btnImprimir" class="btn btn-success fs-5 mt-3" disabled>
                            Rótulo DataMatrix
                        </button>
                        <button id="btnImprimirQR" class="btn btn-primary fs-5 mt-3" disabled>
                            Rótulo QR Code
                        </button>
                        <button id="btnImprimirQRUnificado" class="btn btn-warning fs-5 mt-3" disabled>
                            Rótulo QR Code Unificado
                        </button>
                    </div>
                </div>


                <table class="table table-sm table-bordered fs-5">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Palete</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPaletes"></tbody>
                </table>

                
            </div>

        </div>
    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="modalInfo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fs-5">
            <div class="modal-header">
                <h5 class="modal-title">Aviso</h5>
            </div>
            <div class="modal-body" id="modalMensagem"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary fs-5" data-bs-dismiss="modal">
                    Fechar
                </button>
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