#include <stdio.h>

// Função para calcular o salário bruto
float calcular_salario_bruto(float valor_hora, float horas_trabalhadas) {
    return valor_hora * horas_trabalhadas;
}

// Função para calcular o desconto de 9%
float calcular_desconto(float salario_bruto) {
    return salario_bruto * 0.09;
}

// Função para calcular o salário líquido
float calcular_salario_liquido(float salario_bruto, float desconto) {
    return salario_bruto - desconto;
}

int main() {
    float valor_hora, horas_trabalhadas;
    float salario_bruto, desconto, salario_liquido;

    printf("=== CALCULO DE SALARIO MENSAL ===\n\n");

    // Entrada de dados
    printf("Informe o valor da hora trabalhada (R$): ");
    scanf("%f", &valor_hora);

    printf("Informe a quantidade de horas trabalhadas no mes: ");
    scanf("%f", &horas_trabalhadas);

    // Chamadas das funções
    salario_bruto = calcular_salario_bruto(valor_hora, horas_trabalhadas);
    desconto = calcular_desconto(salario_bruto);
    salario_liquido = calcular_salario_liquido(salario_bruto, desconto);

    // Saída dos resultados
    printf("\n=== RESULTADOS ===\n");
    printf("Salario Bruto: R$ %.2f\n", salario_bruto);
    printf("Desconto (9%%): R$ %.2f\n", desconto);
    printf("Salario Liquido: R$ %.2f\n", salario_liquido);

    printf("\nPrograma encerrado com sucesso.\n");

    return 0;
}
