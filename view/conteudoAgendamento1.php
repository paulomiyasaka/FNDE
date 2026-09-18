<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Agendamento e Cargas</title>
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="css/agendamento.css">
</head>
<body>

    <!-- Navbar sem logotipo -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <!--<span class="navbar-brand mb-0 h1">Sistema Logístico</span>-->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" id="nav-agen" href="#" onclick="showSection('agendamento')">Agendamentos</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="nav-nf" role="button" data-bs-toggle="dropdown">Notas Fiscais</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" onclick="showSection('nf-cadastro')">Cadastrar Nota</a></li>
                            <li><a class="dropdown-item" href="#" onclick="showSection('nf-consulta')">Consultar Notas</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Área Principal -->
    <main class="container-fluid px-4 mt-5 pt-4">

        <!-- ==========================================
             SEÇÃO: AGENDAMENTO (Página Principal)
        =========================================== -->
        <section id="sec-agendamento">
            <h2 class="mb-4">Gestão de Agendamentos</h2>
            
            <!-- Cadastro de Agendamento -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white fw-bold">Novo Agendamento</div>
                <div class="card-body">
                    <form id="form-agendamento">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Livro / SKU</label>
                                <input type="text" class="form-control" id="sku-agen" placeholder="Digite o SKU ou Título do Livro (Autocomplete)">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data da Solicitação</label>
                                <input type="date" class="form-control" id="data-solic-agen">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data Prevista de Entrega</label>
                                <input type="date" class="form-control" id="data-prevista-agen">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Peso Previsto (kg)</label>
                                <input type="number" step="0.01" class="form-control" id="peso-previsto" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Qtd. Paletes Previsto</label>
                                <input type="number" class="form-control" id="paletes-previsto" placeholder="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Anexo do E-mail de Solicitação</label>
                                <input type="file" class="form-control" id="arquivo-email" accept=".pdf,.eml,.msg">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Observação do Agendamento</label>
                                <textarea class="form-control" id="obs-agen" rows="2" placeholder="Informações adicionais sobre o agendamento..."></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Cadastrar Agendamento</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Consulta de Agendamentos -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Consultar Agendamentos</h5>
                    <div class="row g-2 mb-3 bg-light p-3 rounded border">
                        <div class="col-md-2"><label class="small text-muted">Período Inicial</label><input type="date" class="form-control form-control-sm"></div>
                        <div class="col-md-2"><label class="small text-muted">Período Final</label><input type="date" class="form-control form-control-sm"></div>
                        <div class="col-md-2">
                            <label class="small text-muted">Status</label>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option>Pendente</option>
                                <option>Cancelado</option>
                                <option>Entregue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Fornecedor / Livro (SKU)</label>
                            <select class="form-select form-select-sm"><option>Todos os Fornecedores</option></select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button class="btn btn-primary btn-sm flex-fill"><i class="fas fa-search"></i> Pesquisar</button>
                            <button class="btn btn-success btn-sm flex-fill"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive table-scroll">
                        <table class="table table-hover border align-middle text-nowrap" id="tabela-agendamentos">
                            <thead class="table-dark">
                                <tr>
                                    <th onclick="sortTable(0, 'tabela-agendamentos')">ID <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(1, 'tabela-agendamentos')">SKU / Livro <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(2, 'tabela-agendamentos')">Solicitação <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(3, 'tabela-agendamentos')">Data Prevista <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(4, 'tabela-agendamentos')">Peso Previsto <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(5, 'tabela-agendamentos')">Paletes Prev. <i class="fas fa-sort float-end mt-1"></i></th>
                                    <th onclick="sortTable(6, 'tabela-agendamentos')">Status<i class="fas fa-sort float-end mt-1"></i></th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-agendamentos">
                                <!-- Preenchido via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================
             SEÇÃO: NOTAS FISCAIS (Cadastro)
        =========================================== -->
        <section id="sec-nf-cadastro" class="d-none">
            <h2 class="mb-4">Recebimento e Cadastro de Nota Fiscal</h2>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form id="form-nf" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Agendamento (SKU / Fornecedor)</label>
                            <select class="form-select" id="select-agendamento-nf">
                                <option value="">Selecione o agendamento pendente...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data de Entrega Realizada</label>
                            <input type="date" class="form-control" id="data-entrega-realizada">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Protocolo SEI</label>
                            <input type="text" class="form-control" placeholder="Ex: SEI-12345" maxlength="8">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Número da Nota Fiscal</label>
                            <input type="text" class="form-control" placeholder="Nº da NF" maxlength="15">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Peso Entregue (kg)</label>
                            <input type="number" step="0.01" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Paletes Entregues</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Arquivo da Nota Fiscal</label>
                            <input type="file" class="form-control" accept=".pdf,.xml">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Observação da Entrega</label>
                            <textarea class="form-control" rows="2" placeholder="Observações sobre o estado da entrega, avarias, etc."></textarea>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="button" class="btn btn-success px-4" onclick="triggerToast('Nota Fiscal e Entrega salvas com sucesso!', 'success')">Salvar Recebimento</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- ==========================================
             SEÇÃO: NOTAS FISCAIS (Consulta)
        =========================================== -->
        <section id="sec-nf-consulta" class="d-none">
            <h2 class="mb-4">Consultar Notas Fiscais e Recebimentos</h2>
            <div class="row g-2 mb-3 bg-light p-3 rounded shadow-sm border align-items-end">
                <div class="col-md-2"><label class="small text-muted">Período Inicial</label><input type="date" class="form-control"></div>
                <div class="col-md-2"><label class="small text-muted">Período Final</label><input type="date" class="form-control"></div>
                <div class="col-md-4"><label class="small text-muted">SKU / Fornecedor</label><select class="form-select"><option>Todos</option></select></div>
                <div class="col-md-2"><button class="btn btn-primary w-100"><i class="fas fa-search"></i> Pesquisar</button></div>
                <div class="col-md-2"><button class="btn btn-secondary w-100"><i class="fas fa-download"></i> Baixar Lote</button></div>
            </div>
            
            <div class="table-responsive table-scroll rounded mb-4">
                <table class="table table-hover table-bordered align-middle text-nowrap" id="tabela-nf">
                    <thead class="table-dark text-white">
                        <tr>
                            <th class="text-center" style="width: 50px;"><input type="checkbox" id="selectAllNf"></th>
                            <th onclick="sortTable(1, 'tabela-nf')">Protocolo SEI <i class="fas fa-sort float-end mt-1"></i></th>
                            <th onclick="sortTable(2, 'tabela-nf')">SKU <i class="fas fa-sort float-end mt-1"></i></th>
                            <th onclick="sortTable(3, 'tabela-nf')">Nº Nota Fiscal <i class="fas fa-sort float-end mt-1"></i></th>
                            <th onclick="sortTable(4, 'tabela-nf')">Data Realizada <i class="fas fa-sort float-end mt-1"></i></th>
                            <th>Peso Real / Prev.</th>
                            <th>Paletes Real / Prev.</th>
                            <th>Anexo NF</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-nf">
                        <!-- Preenchido via JS -->
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- ==========================================
         MODAIS
    =========================================== -->
    
    <!-- Modal Cancelar Agendamento -->
    <div class="modal fade" id="modalCancelAgen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Cancelar Agendamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p>Tem certeza que deseja <strong>cancelar</strong> o agendamento do SKU:<br><span id="target-cancel" class="fs-5 text-danger fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="button" class="btn btn-danger" onclick="executeCancel()">Confirmar Cancelamento</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reativar Agendamento -->
    <div class="modal fade" id="modalReactivateAgen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-redo"></i> Reativar Agendamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p>Deseja <strong>reativar</strong> o agendamento do SKU:<br><span id="target-reactivate" class="fs-5 text-success fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="button" class="btn btn-success" onclick="executeReactivate()">Confirmar Reativação</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Nota Fiscal -->
    <div class="modal fade" id="modalEditarNf" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="fas fa-edit"></i> Corrigir Dados da Nota Fiscal / Entrega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SKU</label>
                            <input type="text" class="form-control" id="edit-nf-sku" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Protocolo SEI</label>
                            <input type="text" class="form-control" id="edit-nf-protocolo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nº Nota Fiscal</label>
                            <input type="text" class="form-control" id="edit-nf-numero">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data Entrega Realizada</label>
                            <input type="date" class="form-control" id="edit-nf-data">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Peso Entregue (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="edit-nf-peso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Paletes Entregues</label>
                            <input type="number" class="form-control" id="edit-nf-paletes">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Substituir Arquivo NF</label>
                            <input type="file" class="form-control" accept=".pdf,.xml">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="saveEditNf()">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toast-text"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/agendamento.js"></script>
</body>
</html>