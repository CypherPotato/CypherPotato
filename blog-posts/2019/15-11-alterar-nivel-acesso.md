<!--
{
    "title": "Como alterar nível de acesso de propriedade em uma herança?",
    "link": "2019/15-11-alterar-nivel-acesso.md",
    "published": "15 de novembro, 2019",
    "color": "#7E0525",
    "tags": ["q&a", "c#"]
}
-->

## Pergunta:

Eu tenho uma classe que quando herdada quero que um de seus atributos de público se torne privado, para que o usuário não tenho acesso ao uso dessa variável nessa classe, mas não sei como fazer isso.

```csharp
public class Pai {
    public string Exemplo {get; set;}
}

public class Filho : Pai {
    private string Exemplo {get; set;}
}

var pai = new Pai() {
    Exemplo = "OK" // funciona
}

var pai = new Filho() {
    Exemplo = "OK" // não funciona
}
```

Existe alguma forma de fazer esse feito? Eu estava vendo a *keyword* `internal`, que poderia ser utilizada nesse caso, pois estou fazendo um SDK, mas se eu utilizar ela na `Pai`, o usuário também não conseguira acessar essa propriedade no `Pai` e queria que isso fosse possível.

-------

## Resposta de CypherPotato:

Você não pode fazer isso.

Em C#, quando um método tem uma assinatura pública, é obrigatório o uso público dele, independente de sua sobrecarga.

Você pode sobrecarregar um método com o mesmo nome na classe filho usando a instrução `new`. Dessa forma, a declaração do membro irá sobrepor o que está sendo herdado da classe pai se a declaração for pública.

Dessa forma, você irá derivar `Exemplo` mas não irá utilizar a mesma assinatura da classe `Pai`. Você ainda pode acessar `Pai.Exemplo` da sua classe derivada quando usando o assessor `base`.

As chamadas devem ser compatíveis com o nível de acesso. Você não pode acessar o `private` de outra classe que não é a sua. Neste exemplo abaixo, `ExemploHerdado()` irá retornar o que foi definido em seu assessor público.

```csharp
public class Pai {
    public string Exemplo {get; set;}
}

public class Filho : Pai {
    private new string Exemplo {get => "Teste";}

    public string ExemploHerdado { get {
            return this.Exemplo; // Acessa Pai.Exemplo, pois ele é público e o método também é
        }
    }
}

public static void Main()
{
    Pai x = new Pai();
    x.Exemplo = "Olá, mundo";

    Filho y = new Filho();
    y.Exemplo = "Foo, bar";

    Console.WriteLine(x.Exemplo);
    Console.WriteLine(y.Exemplo);
    Console.WriteLine(y.ExemploHerdado);
}
```

<sup>Veja funcionando no [.NET Fiddle][1].</sup>

No exemplo acima, `y.Exemplo` chama o método `Pai.Exemplo` porque o que foi definido em `Filho` é privado. Nem é uma sobrecarga porque a assinatura do acesso é diferente. Você não pode acessar um método privado de uma classe que não seja a sua, então, você chama o que está público.

Quando chamo `Filho.ExemploHerdado`, eu retorno a propriedade redeclarada e privada `Exemplo`, com o valor que defini.

Causaria confusão e iria infringir a semântica de segurança de classes se isso fosse possível. Você não gostaria de declarar um método privado e que o mesmo fosse utilizado como público em uma classe herdeira. O mesmo acontece ao contrário.

É bom que exista essa obrigatoriedade porque é um contrato indicando que você deverá utilizar o método daquele jeito em que foi originalmente declarado, e não de outra forma de implementação em que for utilizado.

A [documentação][2] também explica bem isso.


  [1]: https://dotnetfiddle.net/AiSk8A
  [2]: https://learn.microsoft.com/pt-br/dotnet/csharp/programming-guide/classes-and-structs/knowing-when-to-use-override-and-new-keywords