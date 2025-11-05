# Configuração do Ambiente C/C++ no VS Code com MSYS2

Este guia detalha o processo de configuração do ambiente de desenvolvimento C/C++ no Visual Studio Code usando MSYS2 como sistema de compilação.

## 1. Instalação do MSYS2

1. Baixe o instalador do MSYS2 em: https://www.msys2.org/
2. Execute o instalador e siga as instruções padrão
3. O MSYS2 será instalado por padrão em `C:\msys64`

## 2. Instalação do Compilador GCC e Ferramentas de Desenvolvimento

Após instalar o MSYS2, abra o terminal "MSYS2 MSYS" no menu iniciar e execute os seguintes comandos:

```bash
# Atualiza o sistema de pacotes
pacman -Syu

# Instala o compilador GCC
pacman -S mingw-w64-x86_64-gcc

# Instala o depurador GDB
pacman -S mingw-w64-x86_64-gdb
```

## 3. Configuração do VS Code

### 3.1 Extensões Necessárias

Instale as seguintes extensões no VS Code:
- C/C++ (Microsoft)
- C/C++ Extension Pack (opcional, mas recomendado)

### 3.2 Configuração do IntelliSense

Criamos o arquivo `c_cpp_properties.json` na pasta `.vscode` com as seguintes configurações:

\`\`\`json
{
    "configurations": [
        {
            "name": "Win32",
            "includePath": [
                "${workspaceFolder}/**",
                "C:/msys64/mingw64/include",
                "C:/msys64/mingw64/lib/gcc/x86_64-w64-mingw32/15.2.0/include",
                "C:/msys64/mingw64/include/c++/15.2.0",
                "C:/msys64/mingw64/include/c++/15.2.0/x86_64-w64-mingw32",
                "C:/msys64/mingw64/x86_64-w64-mingw32/include"
            ],
            "defines": [
                "_DEBUG",
                "UNICODE",
                "_UNICODE"
            ],
            "compilerPath": "C:/msys64/mingw64/bin/gcc.exe",
            "cStandard": "c17",
            "intelliSenseMode": "windows-gcc-x64"
        }
    ],
    "version": 4
}
\`\`\`

## 4. Verificação da Instalação

Para verificar se tudo está funcionando corretamente:

1. Abra o PowerShell e execute:
   ```powershell
   gcc --version
   ```
   Deve mostrar a versão do GCC instalada (no nosso caso, 15.2.0)

2. No VS Code:
   - Os arquivos .c devem ter realce de sintaxe
   - O IntelliSense deve funcionar (autocompletar, dicas de erro)
   - Inclusões como #include <stdio.h> não devem mostrar erros

## 5. Estrutura do Projeto

```
algoritmos-programacao-estruturada/
│
├── .vscode/
│   └── c_cpp_properties.json    # Configurações do C/C++
│
├── *.c                          # Arquivos fonte em C
└── README.md                    # Este arquivo
```

## 6. Possíveis Problemas e Soluções

### 6.1 Erro "não é possível localizar stdio.h"
- Verifique se os caminhos em `c_cpp_properties.json` estão corretos
- Confirme se o GCC foi instalado corretamente
- Reinicie o VS Code

### 6.2 IntelliSense não funciona
- Verifique se a extensão C/C++ está instalada
- Reinicie o VS Code para recarregar as configurações
- Certifique-se de que o caminho do compilador está correto

## 7. Próximos Passos

1. Para compilar um programa:
   ```powershell
   gcc seu_programa.c -o seu_programa.exe
   ```

2. Para executar:
   ```powershell
   ./seu_programa.exe
   ```

## 8. Dicas Adicionais

- Use a paleta de comandos (Ctrl+Shift+P) e digite "C/C++" para ver todos os comandos disponíveis
- O VS Code oferece depuração integrada (será configurada posteriormente)
- Mantenha o MSYS2 atualizado executando `pacman -Syu` periodicamente

## 9. Referências

- [Documentação oficial do VS Code para C++](https://code.visualstudio.com/docs/languages/cpp)
- [MSYS2](https://www.msys2.org/)
- [MinGW-w64](http://mingw-w64.org/)