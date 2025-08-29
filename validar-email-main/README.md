# Projeto de Validação de E-mail

## 📋 Descrição

Este projeto consiste em um formulário de validação de e-mail interativo desenvolvido com HTML, CSS e JavaScript. A aplicação utiliza expressões regulares para validar o formato do e-mail e a biblioteca SweetAlert2 para exibir mensagens de resposta visualmente atrativas.

## ✨ Funcionalidades

- **Validação de E-mail**: Verifica se o e-mail inserido está em formato válido
- **Interface Responsiva**: Design adaptado para diferentes tamanhos de tela
- **Feedback Visual em Tempo Real**: Mudança de cores na borda do input durante a digitação
- **Alertas Modernos**: Utilização do SweetAlert2 para exibição de mensagens de sucesso ou erro
- **Validação em Tempo Real**: Feedback instantâneo durante a digitação do usuário

## 🛠️ Tecnologias Utilizadas

- **HTML5**: Estruturação da página
- **CSS3**: Estilização e design responsivo
- **JavaScript**: Validação do formulário e interatividade
- **SweetAlert2**: Biblioteca para criação de modais e alertas personalizados
- **Expressões Regulares**: Para validação do formato de e-mail

## 🚀 Como Utilizar

1. Abra o arquivo `index.html` em qualquer navegador moderno
2. Digite um endereço de e-mail no campo de entrada
3. Clique no botão "Verificar E-mail" ou aguarde a validação em tempo real
4. Observe o feedback visual e os alertas correspondentes

## 📊 Regras de Validação

A validação de e-mail segue a expressão regular:

```javascript
/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
```

Esta expressão verifica se:

- O e-mail não contém espaços
- Possui um caractere '@'
- Tem texto antes e depois do '@'
- Inclui pelo menos um ponto após o '@'

## 🎨 Design e Interface

O projeto apresenta um design limpo e moderno com:

- Gradiente suave no fundo
- Sombreamento e arredondamento nos elementos
- Feedback visual por cores (verde para válido, vermelho para inválido)
- Animações suaves de transição
- Modais responsivos e personalizados

## 🔧 Solução de Problemas

Foi implementada uma correção específica para evitar o deslocamento da página quando os modais do SweetAlert2 são exibidos, utilizando:

- Configurações CSS específicas para os containers do SweetAlert2
- Parâmetros personalizados como `heightAuto: false` e `scrollbarPadding: false`
- Estilos CSS para garantir que o corpo da página mantenha sua posição

## 🔍 Melhorias Futuras

- Implementar validação de força de senha
- Adicionar integração com APIs de verificação de e-mail
- Incluir modo escuro
- Expandir para um formulário de cadastro completo

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Autor
Bruno Guimarães - [GitHub](https://github.com/bruno-bg)


## Atividade Acadêmica

Este projeto foi desenvolvido como parte da disciplina de Projeto Integrado Síntese - Ads do curso de Desenvolvimento Web da Anhanguera.