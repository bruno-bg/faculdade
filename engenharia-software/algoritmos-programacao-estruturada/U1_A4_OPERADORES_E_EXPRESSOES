#include <stdio.h>

int main() {
    int num1, num2, num3; // Declaração das variáveis inteiras

    // Entrada de dados
    printf("Digite o primeiro numero inteiro: ");
    scanf("%d", &num1);

    printf("Digite o segundo numero inteiro: ");
    scanf("%d", &num2);

    printf("Digite o terceiro numero inteiro: ");
    scanf("%d", &num3);

    // --------------------------
    // Operadores Aritméticos
    // --------------------------
    int soma = num1 + num2 + num3;
    int subtracao = num1 - num2 - num3;
    int multiplicacao = num1 * num2 * num3;
    float divisao = (float) num1 / num2 / num3; // conversão para float

    printf("\n=== RESULTADOS MATEMÁTICOS ===\n");
    printf("Soma: %d\n", soma);
    printf("Subtracao: %d\n", subtracao);
    printf("Multiplicacao: %d\n", multiplicacao);
    printf("Divisao: %.2f\n", divisao);

    // --------------------------
    // Operadores Relacionais
    // --------------------------
    printf("\n=== RESULTADOS RELACIONAIS ===\n");

    if (num1 > num2) {
        printf("O primeiro numero (%d) e maior que o segundo (%d).\n", num1, num2);
    } else {
        printf("O primeiro numero (%d) NAO e maior que o segundo (%d).\n", num1, num2);
    }

    if (num2 < num3) {
        printf("O segundo numero (%d) e menor que o terceiro (%d).\n", num2, num3);
    } else {
        printf("O segundo numero (%d) NAO e menor que o terceiro (%d).\n", num2, num3);
    }

    // --------------------------
    // Operadores Lógicos
    // --------------------------
    printf("\n=== RESULTADOS LÓGICOS ===\n");

    // Verifica se o primeiro número é positivo E o segundo é par
    if ((num1 > 0) && (num2 % 2 == 0)) {
        printf("O primeiro numero e positivo E o segundo numero e par.\n");
        printf("Ambas as condicoes sao verdadeiras!\n");
    } else {
        printf("As condicoes NAO sao verdadeiras ao mesmo tempo.\n");
    }

    printf("\nPrograma finalizado com sucesso.\n");

    return 0;
}

