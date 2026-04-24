<!-- <nav class="navbar bg-body-tertiary"> -->
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container">
        <div class="row">
          <div class="col-3">
            <img src="img/NCorreios_hori_cor_pos.png" alt="Correios" class="logo d-inline-block ">
          </div>
        
          <div class="col-6">
            <div class="collapse navbar-collapse float justify-content-evenly" id="navbarNav">
              <ul class="navbar-nav">
                <?php 
                if($usuario['perfil'] == 'ADMINISTRADOR'){
                ?>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" aria-current="page" href="index.php">Carregar Planilha</a>
                </li>
              <?php } ?>

                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="agendamento.html">Agendamento</a>
                </li>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="cartas.php">Cartas</a>
                </li>

                <?php 
                if($usuario['perfil'] == 'ADMINISTRADOR' OR $usuario['perfil'] == 'GESTOR'){
                ?>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="etiquetas.php">Etiquetas</a>
                </li>
                <?php } ?>
              </ul>
            </div>
          </div>     

        
          <div class="col-2">
            <div class="float text-center pt-4">
              <p class="h5"><strong><?php echo $usuario['nome']?></strong></p>                      
            </div>
          </div>
        

        
          <div class="col-1 pt-4">
            <a href="logout.php" class="btn btn-outline-warning text-dark-emphasis">Sair</a>
          </div>
        </div>    
          

      </div>      
    </nav>