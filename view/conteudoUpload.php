  <div class="container">
    <div class="row justify-content-center align-middle p-5">
      <div class="col-4 text-center">
        <!-- <form method="POST" action="importar.php" enctype="multipart/form-data"> -->
          <form method="post">
            <!--
          <div class="mb-3 text-center">
            <label for="tabela" class="form-label ">Informe o nome da tabela:</label>
            <input class="form-control text-center" type="text" id="tabela" name="tabela" required>
          </div>
            -->
          <div class="mb-3 text-center">
            <label for="file" class="form-label ">Selecionar planilha tipo .XLSX</label>
            <input class="form-control" type="file" id="file" name="file" accept=".xlsx"  aria-describedby="CaixaHelpBlock" required>
            <div id="CaixaHelpBlock" class="form-text">
              O nome do arquivo deve ser igual ao da tabela do banco de dados e o cabeçalho da planilha se refere aos atributos.
            </div>
          </div>
          <button type="submit" class="btn btn-outline-success">Salvar Arquivo</button>

        </form>        
      </div>
    </div>  

    <!--
    <div class="row justify-content-center align-middle p-5">
      <div class="col-6 text-center">

      <a href="gerarHTML.php" class="btn btn-primary">Gerar HTML</a>
      <a href="gerarPDF.php" class="btn btn-secondary">Gerar PDF</a>
        
      </div>
    -->
    </div> 
  </div>

