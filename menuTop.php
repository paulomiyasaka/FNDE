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
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="agrupar.php">Agrupar</a>
                </li>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="rotulos.php">Rótulos</a>
                </li>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="agendar.php">Agendar</a>
                </li>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="notas_fiscais.php">Notas Fiscais</a>
                </li>
                <?php 
                if($usuario['perfil'] == 'ADMINISTRADOR' OR $usuario['perfil'] == 'GESTOR'){
                ?>
                <li class="nav-item">
                  <a class="fw-bold fs-5 nav-link m-3 text-dark-emphasis btn btn-outline-warning" href="etiquetas.php">Usuários</a>
                </li>
                <?php } ?>

                <?php 
                if($usuario['perfil'] == 'ADMINISTRADOR'){
                ?>
                <li class="nav-item">
                  
                </li>
              <?php } ?>
              </ul>
            </div>
          </div>     

        
          <div class="col-2">
            <div class="float text-center pt-4">
              <p class="h6"><strong><?php echo $usuario['nome']?></strong></p>                      
            </div>
          </div>
        

        
          <div class="col-1 pt-4">
            <a href="logout.php" class="btn btn-outline-warning text-dark-emphasis">Sair</a>
            <a class="fw-bold nav-link text-dark-emphasis btn btn-outline-dark" aria-current="page" href="index.php">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
</svg>
                  </a>
          </div>
        </div>    
          

      </div>      
    </nav>