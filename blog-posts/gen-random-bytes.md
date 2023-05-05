Vou mostrar para vocês como fiz meu próprio gerador de bytes aleatórios que permite a utilização de uma semente em seu "motor". Escrevi o código em C# e utilizei código não seguro para alcançar o melhor desempenho possível.

Este algorítmo usa o [Geradores congruentes lineares](https://pt.wikipedia.org/wiki/Geradores_congruentes_lineares) como algoritmo para geração dos números aleatórios.

O código é este:

```cs
unsafe static byte* RandomBytes(ulong seed, int length)
{
    byte* pointer = (byte*)Marshal.AllocHGlobal(length);

    for (int i = 0; i < length; i++)
    {
        ulong x0 = (seed + 2969) * (ulong)(i + 1) * 3181;
        ulong x1 = 4703 * x0 + 2917;
        *(pointer + i) = (byte)(x1 & 0xFF);
    }

    return pointer;
}
```

Agora vamos linha por linha:

- Na declaração de `pointer`, é alocado `length` bytes no heap global do aplicativo, e um ponteiro para o primeiro byte é associado.
- Na linha, `ulong x0 = (seed + 2969) * (ulong)(i + 1) * 3181;`, estou associando à `x0` um indexador da semente, somando `2969`, um número constante e primo, para evitar que o gerador crie valores zerados se a semente for zero. A multiplicação de `(i + 1)` mantém uma construção linear conforme os bytes são criados, onde sua posição implica diretamente no número gerado. `3181` é um outro número primo constante para criar entropia.
- Em `ulong x1 = 4703 * x0 + 2917;`, usamos o conteúdo de `x0` como módulo do gerador, aplicando `4703` e `2917` também como números primos.
- Em `*(pointer + i) = (byte)(x1 & 0xFF);`, obtemos a referência do ponteiro, somando a posição do array e atribuímos `x1 & 0xFF` àquele ponteiro.

No final, retornamos um ponteiro para o array gerado.

Não há necessidade de fixar o ponteiro gerado, pois o mesmo está alocado no heap global.

Podemos agora fazer um teste do nosso recém criado algorítmo:

```cs
unsafe static void Main(string[] args)
{
    const int length = 16;

    ulong[] seeds = new ulong[] { 0, 1, 2, 5, 120, 121, 6512 };

    foreach (ulong seed in seeds)
    {
        byte* bytes = RandomBytes(seed, length);
        Console.Write($"{seed,14} : ");
        for (int i = 0; i < length; i++)
        {
            byte posByte = *(bytes + i);
            Console.Write($"{posByte:X2} ");
        }
        Console.WriteLine();
    }

    Console.ReadKey();
}
```

O resultado será este:
 
```
             0 : 20 DB 96 51 0C C7 82 3D F8 B3 6E 29 E4 9F 5A 15
             1 : 93 C1 EF 1D 4B 79 A7 D5 03 31 5F 8D BB E9 17 45
             2 : 06 A7 48 E9 8A 2B CC 6D 0E AF 50 F1 92 33 D4 75
             5 : 5F 59 53 4D 47 41 3B 35 2F 29 23 1D 17 11 0B 05
           120 : 08 AB 4E F1 94 37 DA 7D 20 C3 66 09 AC 4F F2 95
           121 : 7B 91 A7 BD D3 E9 FF 15 2B 41 57 6D 83 99 AF C5
          6512 : 70 7B 86 91 9C A7 B2 BD C8 D3 DE E9 F4 FF 0A 15
```

Podemos perceber que não há similaridade nos dados gerados, sequer ordem ou padrão, o que define nosso pseudo-aleatorimo.

É possível reproduzir esse algorítmo de forma mais simples, em menos linhas, usando Linq ou até mesmo listas. A ideia de usar ponteiros é ter um desempenho impecável.