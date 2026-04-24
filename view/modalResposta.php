
<!--
Modais para informar o status do retorno: VERDADEIRO ou FALSO
-->


<div class="modal fade" id="modal_verdadeiro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="label_modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body text-center bg-success">
        <p id="msg_sucesso" class="h2 text-white">Executado com sucesso!</p>
        <br><br>
        <button id="btn_ok_success" type="button" data-bs-dismiss="modal" class="btn btn-outline-light">OK</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="modal_falso" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="label_modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body text-center bg-danger">
        <p id="msg_erro" class="h2 text-white">Verifique e tente novamente!</p>
        <br><br>
        <button type="button" data-bs-dismiss="modal" class="btn btn-outline-light">OK</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modal_alerta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="label_modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body text-center bg-warning">
        <p id="msg_alerta" class="h2 text-dark">Atenção!</p>
        <br>
        <div id="conteudo_modal_alerta" class="h2 text-dark text-center">
        </div>
        <input type="hidden" id="id_acao" name="id_acao" value="">
        <br>  
        <button id="btn_ok_alerta" type="button" data-bs-dismiss="modal" class="btn btn-outline-light text-dark border-dark">EXCLUIR</button>
        <button id="btn_cancelar_alerta" type="button" class="btn btn-outline-danger text-dark border-dark" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>




<!-- 
  MODAL DO AJAX CARREGANDO
-->

<div class="modal fade" id="modal_ajax" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="label_modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body text-center">
        <p>Aguarde...</p>
        <div id="load_ajax"><img src='img/load.gif'></div>
      </div>
    </div>
  </div>
</div>

