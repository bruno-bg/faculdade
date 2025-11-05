#include <stdio.h>

int main() {
    int numero;       // variável para armazenar o número digitado
    int soma = 0;     // variável acumuladora para a soma total

    printf("=== PROGRAMA DE SOMA COM WHILE ===\n");
    printf("Digite numeros inteiros para somar.\n");
    printf("Digite 0 para encerrar o programa.\n\n");

    // Estrutura de repetição while com teste no início
    printf("Digite um numero: ");
    scanf("%d", &numero);

    while (numero != 0) {
        soma += numero;  // acumula o valor digitado na variável soma
        printf("Digite outro numero (ou 0 para sair): ");
        scanf("%d", &numero);
    }

    // Exibe o resultado final após o encerramento do loop
    printf("\nA soma total dos numeros digitados e: %d\n", soma);
    printf("Programa encerrado.\n");

    return 0;
}
