(function () {
  'use strict';
  
  const form = document.getElementById('signo-form');
  if (!form) return;
  
  const dataInput = document.getElementById('data_nascimento');
  const submitBtn = form.querySelector('button[type="submit"]');
  
  // Função para validar data
  function validarData(data) {
    const hoje = new Date();
    const dataSelecionada = new Date(data);
    
    // Verifica se a data é válida
    if (isNaN(dataSelecionada.getTime())) {
      return { valido: false, mensagem: 'Data inválida' };
    }
    
    // Verifica se a data não é futura
    if (dataSelecionada > hoje) {
      return { valido: false, mensagem: 'A data não pode ser futura' };
    }
    
    // Verifica se a data não é muito antiga (mais de 150 anos)
    const dataMinima = new Date();
    dataMinima.setFullYear(dataMinima.getFullYear() - 150);
    
    if (dataSelecionada < dataMinima) {
      return { valido: false, mensagem: 'Data muito antiga' };
    }
    
    return { valido: true, mensagem: '' };
  }
  
  // Função para mostrar erro
  function mostrarErro(mensagem) {
    // Remove erro anterior se existir
    const erroAnterior = form.querySelector('.erro-data');
    if (erroAnterior) {
      erroAnterior.remove();
    }
    
    // Cria elemento de erro
    const erroDiv = document.createElement('div');
    erroDiv.className = 'erro-data text-danger mt-2 small';
    erroDiv.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${mensagem}`;
    
    // Insere após o input
    dataInput.parentNode.appendChild(erroDiv);
    
    // Adiciona classe de erro ao input
    dataInput.classList.add('is-invalid');
  }
  
  // Função para remover erro
  function removerErro() {
    const erroAnterior = form.querySelector('.erro-data');
    if (erroAnterior) {
      erroAnterior.remove();
    }
    
    dataInput.classList.remove('is-invalid');
  }
  
  // Função para validar formulário
  function validarFormulario() {
    const data = dataInput.value;
    
    if (!data) {
      mostrarErro('Por favor, selecione uma data de nascimento');
      return false;
    }
    
    const validacao = validarData(data);
    if (!validacao.valido) {
      mostrarErro(validacao.mensagem);
      return false;
    }
    
    removerErro();
    return true;
  }
  
  // Event listeners
  dataInput.addEventListener('input', function() {
    if (this.value) {
      removerErro();
    }
  });
  
  dataInput.addEventListener('change', function() {
    if (this.value) {
      validarFormulario();
    }
  });
  
  form.addEventListener('submit', function (event) {
    if (!validarFormulario()) {
      event.preventDefault();
      event.stopPropagation();
      
      // Adiciona classe de validação
      form.classList.add('was-validated');
      
      // Foca no input com erro
      dataInput.focus();
      return;
    }
    
    // Remove classe de validação se tudo estiver ok
    form.classList.remove('was-validated');
    
    // Desabilita botão durante envio
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processando...';
    
    // Reabilita botão após 2 segundos (fallback)
    setTimeout(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-search me-2"></i>Descobrir meu signo';
    }, 2000);
  });
  
  // Validação em tempo real
  dataInput.addEventListener('blur', function() {
    if (this.value) {
      validarFormulario();
    }
  });
  
  // Inicialização
  document.addEventListener('DOMContentLoaded', function() {
    // Define data máxima como hoje
    const hoje = new Date().toISOString().split('T')[0];
    dataInput.setAttribute('max', hoje);
    
    // Foca no input quando a página carrega
    dataInput.focus();
  });
  
})();