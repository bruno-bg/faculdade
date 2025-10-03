package com.mycompany.gerenciabanco;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.util.Locale;
import java.util.Scanner;

/**
 * Aplicação de Gerência Bancária em arquivo único.
 * - Classe principal: BancoApp (contém o método main)
 * - Classe de domínio (no mesmo arquivo): ContaBancaria (dados pessoais + operações)
 * - Menu com do..while e switch..case (estilo clássico para compatibilidade ampla)
 */
public class BancoApp {

    public static void main(String[] args) {
        Locale.setDefault(new Locale("pt", "BR"));
        Scanner sc = new Scanner(System.in);

        System.out.println("=== Bem-vindo ao BancoApp ===");

        // Coleta de dados do usuário (dados pessoais)
        System.out.print("Informe seu NOME: ");
        String nome = sc.nextLine().trim();

        System.out.print("Informe seu SOBRENOME: ");
        String sobrenome = sc.nextLine().trim();

        String cpf;
        while (true) {
            System.out.print("Informe seu CPF (apenas números): ");
            cpf = sc.nextLine().trim();
            if (validaCpfBasico(cpf)) break;
            System.out.println("CPF inválido. Tente novamente (11 dígitos numéricos).");
        }

        // Cria a conta com saldo inicial zero
        ContaBancaria conta = new ContaBancaria(nome, sobrenome, cpf);

        int opcao;
        do {
            exibirMenu();
            opcao = lerOpcao(sc);

            switch (opcao) {
                case 1:
                    // Consulta de saldo
                    System.out.println("\n--- SALDO ---");
                    System.out.println("Titular: " + conta.getNomeCompleto() + " | CPF: " + conta.getCpfFormatado());
                    System.out.println("Saldo atual: R$ " + conta.getSaldoFormatado());
                    break;

                case 2:
                    // Depósito
                    System.out.println("\n--- DEPÓSITO ---");
                    BigDecimal valorDeposito = lerValorMonetario(sc, "Informe o valor a depositar: R$ ");
                    try {
                        conta.depositar(valorDeposito);
                        System.out.println("Depósito efetuado. Novo saldo: R$ " + conta.getSaldoFormatado());
                    } catch (IllegalArgumentException ex) {
                        System.out.println("Falha no depósito: " + ex.getMessage());
                    }
                    break;

                case 3:
                    // Saque
                    System.out.println("\n--- SAQUE ---");
                    BigDecimal valorSaque = lerValorMonetario(sc, "Informe o valor a sacar: R$ ");
                    try {
                        conta.sacar(valorSaque);
                        System.out.println("Saque efetuado. Novo saldo: R$ " + conta.getSaldoFormatado());
                    } catch (IllegalArgumentException ex) {
                        System.out.println("Falha no saque: " + ex.getMessage());
                    }
                    break;

                case 0:
                    System.out.println("\nEncerrando... Obrigado por utilizar o BancoApp. Até logo!");
                    break;

                default:
                    System.out.println("Opção inválida. Tente novamente.");
                    break;
            }

        } while (opcao != 0);

        sc.close();
    }

    // ======= Métodos de apoio à UI =======

    private static void exibirMenu() {
        System.out.println("\n=== MENU ===");
        System.out.println("1) Consultar saldo");
        System.out.println("2) Depositar");
        System.out.println("3) Sacar");
        System.out.println("0) Sair");
        System.out.print("Escolha uma opção: ");
    }

    private static int lerOpcao(Scanner sc) {
        while (true) {
            String entrada = sc.nextLine().trim();
            try {
                return Integer.parseInt(entrada);
            } catch (NumberFormatException e) {
                System.out.print("Entrada inválida. Digite um número (0-3): ");
            }
        }
    }

    /**
     * Lê um valor monetário, aceitando vírgula ou ponto como separador decimal.
     * Regras:
     *  - Se tiver vírgula e ponto, assume ponto como milhar e vírgula como decimal (ex: 1.234,56).
     *  - Senão, troca vírgula por ponto (ex: 1234,56 -> 1234.56).
     */
    private static BigDecimal lerValorMonetario(Scanner sc, String prompt) {
        while (true) {
            System.out.print(prompt);
            String raw = sc.nextLine().trim().replace(" ", "");

            String normalizada;
            boolean temVirgula = raw.contains(",");
            boolean temPonto = raw.contains(".");

            if (temVirgula && temPonto) {
                // Usa o separador mais à direita como decimal e remove os demais
                int lastComma = raw.lastIndexOf(',');
                int lastDot = raw.lastIndexOf('.');
                int sep = Math.max(lastComma, lastDot);
                String inteira = raw.substring(0, sep).replaceAll("[.,]", "");
                String fracionaria = raw.substring(sep + 1);
                normalizada = inteira + "." + fracionaria;
            } else {
                // Só vírgula ou só ponto: normaliza para ponto
                normalizada = raw.replace(",", ".");
            }

            try {
                BigDecimal valor = new BigDecimal(normalizada);
                return valor.setScale(2, RoundingMode.HALF_UP);
            } catch (NumberFormatException e) {
                System.out.println("Valor inválido. Ex.: 100,00 | 100.00 | 1.234,56");
            }
        }
    }

    /**
     * Validação básica de CPF: 11 dígitos numéricos (não valida dígitos verificadores).
     */
    private static boolean validaCpfBasico(String cpf) {
        if (cpf == null) return false;
        if (!cpf.matches("\\d{11}")) return false;
        // Rejeita sequências triviais (11111111111 etc.)
        return !cpf.chars().allMatch(ch -> ch == cpf.charAt(0));
    }
}

/**
 * Classe de domínio: dados pessoais + operações bancárias.
 * Encapsula estado e regras de negócio mínimas (depósito/saque).
 */
class ContaBancaria {
    private final String nome;
    private final String sobrenome;
    private final String cpf; // armazenado apenas como dígitos
    private BigDecimal saldo;

    public ContaBancaria(String nome, String sobrenome, String cpf) {
        this.nome = validaTextoObrigatorio(nome, "nome");
        this.sobrenome = validaTextoObrigatorio(sobrenome, "sobrenome");
        this.cpf = validaCpf(cpf);
        this.saldo = BigDecimal.ZERO.setScale(2, RoundingMode.HALF_UP);
    }

    // ======= Operações =======

    public void depositar(BigDecimal valor) {
        if (valor == null || valor.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("O valor do depósito deve ser positivo.");
        }
        saldo = saldo.add(valor).setScale(2, RoundingMode.HALF_UP);
    }

    public void sacar(BigDecimal valor) {
        if (valor == null || valor.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("O valor do saque deve ser positivo.");
        }
        if (valor.compareTo(saldo) > 0) {
            throw new IllegalArgumentException("Saldo insuficiente para saque.");
        }
        saldo = saldo.subtract(valor).setScale(2, RoundingMode.HALF_UP);
    }

    // ======= Getters utilitários =======

    public String getNomeCompleto() {
        return nome + " " + sobrenome;
    }

    public String getCpfFormatado() {
        // Formata 000.000.000-00
        return cpf.substring(0, 3) + "." + cpf.substring(3, 6) + "." + cpf.substring(6, 9) + "-" + cpf.substring(9);
    }

    public String getSaldoFormatado() {
        return saldo.toPlainString();
    }

    public BigDecimal getSaldo() {
        return saldo;
    }

    // ======= Validações =======

    private static String validaTextoObrigatorio(String s, String campo) {
        if (s == null || s.trim().isEmpty()) {
            throw new IllegalArgumentException("Campo obrigatório não informado: " + campo);
        }
        return s.trim();
    }

    private static String validaCpf(String cpf) {
        if (cpf == null || !cpf.matches("\\d{11}")) {
            throw new IllegalArgumentException("CPF inválido: informe exatamente 11 dígitos numéricos.");
        }
        boolean repetido = cpf.chars().allMatch(ch -> ch == cpf.charAt(0));
        if (repetido) throw new IllegalArgumentException("CPF inválido (sequência repetida).");
        return cpf;
    }
}
