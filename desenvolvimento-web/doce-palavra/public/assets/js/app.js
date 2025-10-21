// Scripts gerais do Doce Palavra
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Função para mostrar notificações de sucesso
    window.showSuccess = function(message) {
        Toast.fire({
            icon: 'success',
            title: message,
            background: '#d4edda',
            color: '#155724'
        });
    };

    // Função para mostrar notificações de erro
    window.showError = function(message) {
        Toast.fire({
            icon: 'error',
            title: message,
            background: '#f8d7da',
            color: '#721c24'
        });
    };

    // Função para mostrar notificações de informação
    window.showInfo = function(message) {
        Toast.fire({
            icon: 'info',
            title: message,
            background: '#d1ecf1',
            color: '#0c5460'
        });
    };

    // Confirmação para exclusão
    window.confirmDelete = function(message, callback) {
        Swal.fire({
            title: 'Tem certeza?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    };

    // Confirmação para salvar
    window.confirmSave = function(callback) {
        Swal.fire({
            title: 'Salvar alterações?',
            text: 'As alterações serão salvas no sistema.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, salvar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    };

    // Adicionar tooltips a todos os elementos com title
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus',
            delay: { show: 500, hide: 100 }
        });
    });

    // Melhorar acessibilidade dos formulários
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
            }
        });
    });

    // Adicionar animações aos cards
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Melhorar navegação por teclado
    document.addEventListener('keydown', function(e) {
        // ESC para fechar modais
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }
    });

    // Adicionar indicadores de carregamento
    window.showLoading = function(message = 'Carregando...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    };

    window.hideLoading = function() {
        Swal.close();
    };

    // Auto-save para formulários longos
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        let timeout;
        textarea.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                localStorage.setItem('draft_' + textarea.name, textarea.value);
            }, 1000);
        });

        // Restaurar rascunho
        const draft = localStorage.getItem('draft_' + textarea.name);
        if (draft && !textarea.value) {
            textarea.value = draft;
        }
    });

    // Limpar rascunhos ao salvar
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const textareas = form.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                localStorage.removeItem('draft_' + textarea.name);
            });
        });
    });
});
