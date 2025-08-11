const horariosDisponiveis = [
    "08:00", "09:00", "10:00", "11:00",
    "13:00", "14:00", "15:00", "16:00"
  ];
  
  const dataInput = document.getElementById("data");
  const horarioSelect = document.getElementById("horario");
  const form = document.getElementById("agendamento-form");
  const resumoDiv = document.getElementById("resumo");
  
  dataInput.addEventListener("change", () => {
    horarioSelect.innerHTML = ""; // limpa opções
    const dataSelecionada = dataInput.value;
    
    if (dataSelecionada) {
      horariosDisponiveis.forEach(horario => {
        const option = document.createElement("option");
        option.value = horario;
        option.textContent = horario;
        horarioSelect.appendChild(option);
      });
    } else {
      const option = document.createElement("option");
      option.textContent = "Selecione a data primeiro";
      horarioSelect.appendChild(option);
    }
  });
  
  // Função para aplicar máscara no telefone
  function aplicarMascaraTelefone(telefone) {
    telefone = telefone.replace(/\D/g, ''); // Remove tudo que não é número
    telefone = telefone.replace(/^(\d{2})(\d)/g, '($1) $2'); // Coloca parênteses no DDD
    telefone = telefone.replace(/(\d)(\d{4})$/, '$1-$2'); // Coloca hífen no final
    return telefone;
  }
  
  // Função para validar email
  function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }
  
  // Função para validar telefone
  function validarTelefone(telefone) {
    const telefoneNumeros = telefone.replace(/\D/g, '');
    return telefoneNumeros.length >= 10 && telefoneNumeros.length <= 11;
  }
  
  // Aplicar máscara no campo de telefone
  const telefoneInput = document.getElementById("telefone");
  telefoneInput.addEventListener("input", (e) => {
    e.target.value = aplicarMascaraTelefone(e.target.value);
  });
  
  // Adicionar validações nos campos
  const nomeInput = document.getElementById("nome");
  const emailInput = document.getElementById("email");
  
  nomeInput.addEventListener("blur", () => {
    if (nomeInput.value.trim().length < 3) {
      nomeInput.setCustomValidity("O nome deve ter pelo menos 3 caracteres");
    } else {
      nomeInput.setCustomValidity("");
    }
  });
  
  emailInput.addEventListener("blur", () => {
    if (!validarEmail(emailInput.value)) {
      emailInput.setCustomValidity("Digite um e-mail válido");
    } else {
      emailInput.setCustomValidity("");
    }
  });
  
  telefoneInput.addEventListener("blur", () => {
    if (!validarTelefone(telefoneInput.value)) {
      telefoneInput.setCustomValidity("Digite um telefone válido");
    } else {
      telefoneInput.setCustomValidity("");
    }
  });
  
  // Elementos do modal
  const modal = document.getElementById("modal-confirmacao");
  const dadosConfirmacao = document.getElementById("dados-confirmacao");
  const btnConfirmar = document.getElementById("btn-confirmar");
  const btnCancelar = document.getElementById("btn-cancelar");
  
  // Função para formatar a data
  function formatarData(data) {
    const [ano, mes, dia] = data.split('-');
    return `${dia}/${mes}/${ano}`;
  }
  
  // Função para mostrar o modal
  function mostrarModal(dados) {
    dadosConfirmacao.innerHTML = `
      <p><strong>Nome:</strong> ${dados.nome}</p>
      <p><strong>Telefone:</strong> ${dados.telefone}</p>
      <p><strong>E-mail:</strong> ${dados.email}</p>
      <p><strong>Data:</strong> ${formatarData(dados.data)}</p>
      <p><strong>Horário:</strong> ${dados.horario}</p>
    `;
    modal.style.display = "block";
  }
  
  // Função para fechar o modal
  function fecharModal() {
    modal.style.display = "none";
  }
  
  // Evento de clique no botão confirmar
  btnConfirmar.addEventListener("click", () => {
    const resumo = `
      <h3>Agendamento Confirmado!</h3>
      <p><strong>Nome:</strong> ${nomeInput.value.trim()}</p>
      <p><strong>Telefone:</strong> ${telefoneInput.value.trim()}</p>
      <p><strong>E-mail:</strong> ${emailInput.value.trim()}</p>
      <p><strong>Data:</strong> ${formatarData(dataInput.value)}</p>
      <p><strong>Horário:</strong> ${horarioSelect.value}</p>
    `;
    resumoDiv.innerHTML = resumo;
    resumoDiv.style.display = "block";
    form.style.display = "none";
    const novoAgendamentoBtn = document.getElementById("novo-agendamento");
    novoAgendamentoBtn.style.display = "block";
    form.reset();
    horarioSelect.innerHTML = "<option>Selecione a data primeiro</option>";
    fecharModal();
  });
  
  // Evento de clique no botão cancelar
  btnCancelar.addEventListener("click", () => {
    fecharModal();
  });
  
  // Atualizar o evento submit do formulário
  form.addEventListener("submit", (e) => {
    e.preventDefault();
  
    const nome = nomeInput.value.trim();
    const telefone = telefoneInput.value.trim();
    const email = emailInput.value.trim();
    const data = dataInput.value;
    const horario = horarioSelect.value;
  
    // Validações
    if (!nome || nome.length < 3) {
      alert("Por favor, digite um nome válido (mínimo 3 caracteres).");
      return;
    }
  
    if (!validarTelefone(telefone)) {
      alert("Por favor, digite um telefone válido.");
      return;
    }
  
    if (!validarEmail(email)) {
      alert("Por favor, digite um e-mail válido.");
      return;
    }
  
    if (!data || !horario) {
      alert("Por favor, selecione a data e horário da consulta.");
      return;
    }
  
    // Mostrar modal de confirmação
    mostrarModal({
      nome,
      telefone,
      email,
      data,
      horario
    });
  });
  
  const novoAgendamentoBtn = document.getElementById("novo-agendamento");
  
  novoAgendamentoBtn.addEventListener("click", () => {
    resumoDiv.style.display = "none";
    novoAgendamentoBtn.style.display = "none";
    form.style.display = "flex";
    form.reset();
    horarioSelect.innerHTML = "<option>Selecione a data primeiro</option>";
  });
  