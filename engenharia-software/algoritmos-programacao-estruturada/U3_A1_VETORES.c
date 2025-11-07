#include <stdio.h>

int main() {
    int numeros[5];  // Declaração do vetor com 5 elementos inteiros
    int soma = 0;    // Variável acumuladora da soma
    int i;           // Variável de controle do loop

    printf("=== PROGRAMA DE SOMA DE VETOR ===\n\n");

    // Leitura dos valores fornecidos pelo usuário
    for (i = 0; i < 5; i++) {
        printf("Digite o %dº numero inteiro: ", i + 1);
        scanf("%d", &numeros[i]);
        soma += numeros[i];  // Acumula a soma
    }

    // Exibição dos valores armazenados no vetor
    printf("\n=== VALORES INSERIDOS ===\n");
    for (i = 0; i < 5; i++) {
        printf("Elemento %d: %d\n", i + 1, numeros[i]);
    }

    // Exibição do resultado da soma
    printf("\nSoma total dos elementos: %d\n", soma);
    printf("Programa encerrado com sucesso.\n");

    return 0;
}
