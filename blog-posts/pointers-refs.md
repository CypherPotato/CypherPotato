Quando devemos usar ponteiros e quando devemos usar referências?

Em uma recente discussão no [Stack Overflow em Português](https://pt.stackoverflow.com/questions/581817/quando-usar-ponteiros-e-quando-usar-refer%c3%aancias), estava questionando o uso destas duas tecnologias que são relativamente idênticas. 

Vamos imaginar este trecho de código:

```csharp
record struct Person
{
    public int Age;
}

static void Main(string[] args)
{
    Person personA = new Person() { Age = 10 };

    ModifyPersonRef(ref personA);
    Console.WriteLine(personA);
}

static void ModifyPersonRef(ref Person person)
{
    person.Age = 15;
}
```

Ao executar, será impresso no console `15`, pois a propriedade `personA.Age` foi modificada por `ModifyPersonRef`. Essa modificação ocorreu porque a referência em C#, envia por trás dos panos, um ponteiro para meu objeto (e algumas coisas a mais).

Não faz sentido usar referências em classes ou arrays, pois eles já são referências. Sempre que uso como argumentos, um ponteiro para o objeto original é criado. Neste caso, `Person` é um tipo por valor, ou seja, precisa do indicador de referência `ref` (`ByRef`) para especificar que quero a referência para estrutura `Person`.

Quando acesso `person` dentro de `ModifyPersonRef` não estou acessando sua referência mas sim para onde ela aponta. É a mesma coisa que um ponteiro faz, não é? Então por que não posso usar um ponteiro ao invés de usar referências?

Vamos reescrever o código acima, mas desta vez usando ponteiros

```csharp
record struct Person
{
    public int Age;
}

static void Main(string[] args)
{
    Person personA = new Person() { Age = 10 };

    ModifyPersonPointer(&personA);
    Console.WriteLine(personA);
}

static void ModifyPersonPointer(Person* pointerToPerson)
{
    Person person = *pointerToPerson;
    person.Age = 30;
}
```

Agora não estou mais usando referência. Quando chamo `ModifyPersonPointer`, estou passando por parâmetro um ponteiro para `personA`, a instância da estrutura que criei logo acima.

O colega [Maniero](https://pt.stackoverflow.com/users/101/maniero) explicou qual a diferença entre os dois usos e qual devemos optar em usar. Não quer explicações e somente a resposta? Ok, **use referências sempre que puder**.

# Ponteiros são perigosos.

Não é atoa que precisamos compilar o código com `/unsafe` e ainda usar `unsafe {}` onde queremos usar ponteiros com C#. A principal (des)motivação é que quase nunca sabemos o que estamos fazendo com ponteiros.

Ele é necessário quando devemos fazer interações diretas com hardware, sistema operacional ou drivers que não fornecem acesso direto à API deles. Também é interessante em casos de otimização extrema onde você precisa ter controle total sobre aquela memória, dado, seja lá o que você está fazendo.

Quando usamos referências, estamos lidando com um ambiente seguro, pois na referência é carregados alguns dados a mais sobre aquele ponteiro, como tamanho, contexto e outras coisas que não precisamos nos preocupar.

Referências são por padrão `in/out`. Maniero também explicou os operadores in, out e ref [aqui](https://pt.stackoverflow.com/a/82632/24529).

Em alguns casos, também podemos optar pela utilação de `Span`, que é um "array de ponteiros gerenciados por referência", um `void**` seguro de usar.

