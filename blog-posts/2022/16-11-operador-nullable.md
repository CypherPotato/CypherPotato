<!--
{
  "title": "O que significa null! em C#, qual o proposito real dessa junção na atribuição?",
  "link": "2022/16-11-operador-nullable.md",
  "published": "16 de novembro, 2022",
  "color": "#011C54",
  "tags": ["q&a", "c#", "nullable"]
}
-->

O que significa null! em C#, qual o proposito real dessa junção na atribuição?

## Pergunta:

Com [exemplo do site][1] da Microsoft tem nessa classe um atribuição de `null!` como demonstrado na classe `ContactDetails`:

```csharp
public class ContactDetails
{
    public Address Address { get; set; } = null!;
    public string? Phone { get; set; }
}
```

O que significa essa junção de `null` com `!` e para que serve no desenvolvimento?


  [1]: https://learn.microsoft.com/pt-br/ef/core/what-is-new/ef-core-7.0/whatsnew


## Resposta por CypherPotato:

Esse operador ignora os avisos do compilador. Só isso.

No seu contexto, `null!` é apenas um shorthand para *"essa referência aponta para lugar nenhum, não vou inicializar ela e não quero avisos para isso pois sei o que estou fazendo"*.

Em detalhes: o operador `!` quando usado próximo ao fim de uma expressão é o "null-forgiving" (esqueça do nulo em português) que basicamente descarta o aviso do compilador que aquela expressão pode ser nula em um valor que não deve ser nulo.

Veja o exemplo:

```csharp
string name = null; // convertendo um literal nulo em uma referência não-nula
```

No exemplo acima, o tipo `string` não pode ser nulo por natureza porque é um tipo por referência, ou seja, ele deve ser inicializado de uma forma ou de outra. Mesmo que se for com `""`, pois uma string vazia não é nula.

Ainda no exemplo acima:

```csharp
string name = null!; // ok, ignorado
```

Você irá descartar a mensagem de aviso do compilador e irá executar seu código. No entanto, você não poderá fazer muita coisa com essa variável porque ela não foi inicializada.

```csharp
string name = null!;
Console.WriteLine("O tamanho do seu nome é: " + name.Length); // Object reference not set to an instance of an object.
```

A mesma coisa serve para quando você chamar `name!` em outros lugares. Se inicializar ela com um valor nulo ou não inicializar ela, o compilador irá avisar que aquela variável pode ser nula.

Desde o C# 8, foi introduzido o "null-safety", em que tipos por referência não podem ser nulos. Para declarar referências que podem ser nulas, você pode utilizar o operador `?`:

```csharp
string? name = null; // ok
name ?? "João"; // João
name?.Length ?? 10; // 10
```

Isso acontece porque `name` é nulo, e neste caso você pode chamar um "substituto" caso ele seja nulo.

Em resumo, você:
- Usa o `?` depois de um nome que pode ser nulo, podendo utilizar o `??` posteriormente ou evitando dor de cabeça.
- Usa o `!` quando tem certeza que o objeto nunca será nulo.

# Tome cuidado com o `!`

Use-o apenas em contextos que tem certeza que aquela expressão nunca será nula. Vamos observar essa expressão:

```csharp
string? meuCarro = carros.FirstOrDefault()?.Modelo.Nome!.ToString();
```

Na expressão acima, eu não sei se `carros.FirstOrDefault()` trará um valor não-nulo, mas caso ele traga um objeto não-nulo, eu terei certeza que `Modelo.Nome` nunca será nulo, por mais que sua declaração permita isso.

Se `carros.FirstOrDefault()` for nulo, o resto da expressão é descartada e `meuCarro` será nulo. Após isso você pode validar a sua expressão:

```csharp
if (meuCarro is null) {
    throw new Exception("A lista de carros está vazia!");
}
```

Aí é algo que você pode controlar. Se sair colocando `!` em tudo, irá ter erros que nem sempre estarão em seu controle, além de criar um código porco :)

Lembre-se sempre que funcionar é diferente de estar certo.

[![inserir a descrição da imagem aqui][1]][1]


  [1]: https://i.stack.imgur.com/j9vg8.jpg