<?php include('layouts/header.php'); ?>
<main class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 120px);">
  <div class="row justify-content-center w-100">
    <div class="col-lg-5 col-md-6 col-sm-8">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4">
          <div class="text-center mb-3">
            <div class="signo-icon mb-2">
              <i class="bi bi-stars display-4 text-primary"></i>
            </div>
            <h1 class="h3 mb-2 fw-bold">Descubra seu Signo</h1>
            <p class="text-muted small">Digite sua data de nascimento e descubra qual é o seu signo zodiacal</p>
          </div>
          
          <form id="signo-form" method="POST" action="show_zodiac_sign.php" novalidate>
            <div class="mb-3">
              <label for="data_nascimento" class="form-label fw-semibold">
                <i class="bi bi-calendar-event me-2"></i>Data de Nascimento
              </label>
              <input type="date" 
                     class="form-control" 
                     id="data_nascimento" 
                     name="data_nascimento" 
                     required 
                     max="<?= date('Y-m-d') ?>" />
              <div class="form-text small">
                <i class="bi bi-info-circle me-1"></i>
                Selecione a data do seu nascimento
              </div>
            </div>
            
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg px-3 py-2">
                <i class="bi bi-search me-2"></i>Descobrir meu signo
              </button>
            </div>
          </form>
          
          <div class="text-center mt-3">
            <small class="text-muted">
              <i class="bi bi-shield-check me-1"></i>
              Sua data é utilizada apenas para calcular seu signo
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
