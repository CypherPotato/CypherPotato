Há algum tempo eu sinto a necessidade de validar requisições de uma API Restful de maneira eficiente, padronizada e
sem ter que ficar extraindo, validando a dedo e retornando o erro ao cliente no mesmo método do controlador, então
pensei em criar um validador que atenda essa necessidade em manter o código limpo e organizado.

Para isso, os atributos foram meus amigos. Também optei em usar DTOs em `file record`, o que não vai misturar o formato da
requisição com outros arquivos, pois a estrutura do projeto é baseada em arquivos.

Vamos supor que eu preciso criar um usuário em uma rota POST, e esse é o payload que comumente vou receber:

```json
{
    "name": "Roberto da Silva",
    "birthDate": "2001-03-05",
    "email": "roberto-da-silva@example.com"
}
```

E eu posso armazenar isso em:

```csharp
file record UserDTO
{
    public string Name = null!;
    public DateOnly BirthDate;
    public string Email = null!;
}
```

No entanto, alguns parâmetros nulos simplesmente não eram enviados pelo cliente ou dados incorretos eram enviados,
e as mensagens de erro não eram... humanas.

Erros técnicos de posições do JSON, erro de conversão ou erros no parser eram mostrados para o cliente. Eu não
queria isso.

Então, criei um atributo que valida o objeto com base no que foi deserializado, ficando algo como:

```csharp
file record UserDTO
{
    [Validator(nameof(MyValidators.Required), ErrorMessage = "O nome do usuário é obrigatório.")]
    [Validator(nameof(MyValidators.ValidateUsername))]
    public string Name = null!;

    [Validator(nameof(MyValidators.Required), ErrorMessage = "A data de nascimento é obrigatória.")]
    [Validator(nameof(MyValidators.ValidateBirthdate))]
    public DateOnly BirthDate;

    [Validator(nameof(MyValidators.Required), ErrorMessage = "O e-mail é obrigatório.")]
    [Validator(nameof(MyValidators.ValidateEmail))]
    public string Email = null!;
}
```

E os validadores presentes em `MyValidators` eram simples:

```csharp
public class MyValidators
{
    public void ValidateEmail(object? entry) 
    {
        ...
    }

    public void ValidateBirthdate(object? entry)
    {
        ...
    }

    public void ValidatePhone(object? entry)
    {
        ...
    }
}
```

Dentro de cada método, eu fazia a validação do que estava recebendo, e se alguma coisa estivesse errada, um
erro era lançado pela aplicação. Vamos ver o caso do `Required`:

```csharp
public class MyValidators
{
    public void Required(object? entry) 
        => ArgumentNullException.ThrowIfNull(entry);
}
```

Um erro era lançado se a entrada era nula. Isso era ótimo, pois eu conseguia controlar o que não queria que fosse
nulo no meu objeto.

Dessa forma, eu conseguia também sobrescrever a mensagem de erro original com `ErrorMessage`, o que acabou criando
uma forma de eu definir mensagens customizadas para objetos que estavam faltando.

```csharp
file record UserDTO
{
    [Validator(nameof(MyValidators.Required), ErrorMessage = "Essa propriedade está faltando!")]
    public string Name = null!;
}
```

Nisso notei também que o deserializador JSON estava me voltando erros para quando o objeto estava com um tipo diferente
do que o DTO esperava, como o caso de enviar um número, objeto ou qualquer outra coisa em um lugar que espera uma string.

A biblioteca JSON.net possui um handler de exceções, a qual permite que você ignore alguns erros no deserializador. Eu tentei
procurar algo similar na biblioteca nativa System.Text.Json, mas não encontrei.

```csharp
JsonSerializerSettings settings = new JsonSerializerSettings()
{
    Error = (object? sender, ErrorEventArgs args) =>
    {
        object? obj = args.ErrorContext.OriginalObject;
        string? member = args.ErrorContext.Member?.ToString();

        if (obj != null && member != null)
        {
            // verifica se o membro que o JSON está tentando deserializar tem o validador Required
            IEnumerable<string> errMessage =
                ObjectValidator.GetErrorMessagesFor(obj, member, nameof(MyValidators.Required));

            if (errMessage.Count() > 0)
            {
                // o membro possui uma validação "Required", então eu ignoro o erro aqui
                args.ErrorContext.Handled = true;
            }
        }
    }
}
```

A partir deste momento, o deserializador JSON não irá mais mostrar erros, e consigo finalmente verificar se meu objeto
está nos conformes:

```csharp
ObjectValidator validator = new ObjectValidator()
{
    Options = new ValidatorOptions()
    {
        IncludeFields = true,
        IncludeProperties = true
    },
    // aqui eu associo os validadores que defini anteriormente
    ValidatorInstance = new MyValidators()
};
```

```csharp
try
{
    UserDTO result = JsonConvert.DeserializeObject<UserDTO>(json, settings)!;
    validator.Validate(result);
}
catch (Exception ex) 
{
    Console.WriteLine("Erro na validação: {0}", ex.Message);
}
```

Note que isso não foi feito apenas para JSON, mas qualquer forma de validar o que foi deserializado.

O código fonte está disponível no meu [Github](https://github.com/CypherPotato/nuget-packages), e talvez
eu faça um pacote no Nuget para isso depois. Talvez.

Não há segredo de como usar. Sinta-se a vontade para editar, usar, comercializar, se estiver dentro dos
conformes da licença do repositório.