<!--
{
    "title": "Descomplicando o Path.Combine",
    "link": "2023/08-05-normalizando-path-combine.md",
    "published": "8 de maio, 2023",
    "color": "#1381FF",
    "tags": ["c#"]
}
-->

Em algum momento você já tentou concatenar dois diretórios e o resultado não foi o que você queria? Vou te dar uma solução aqui.

Desde cedo já ouvia que concatenar dois caminhos "na mão" nunca era a melhor solução. A chance de sair um caminho inválido,
errado ou quebrado era muito alta quando você não tinha muito controle do que estavam nestes caminhos.

Então veio a solução: o método [System.IO.Path.Combine](https://learn.microsoft.com/pt-br/dotnet/api/system.io.path.combine?view=net-7.0), mas
a gente nunca usou ele direito.

Vamos supor que você quer concatear o caminho `C:/Users/Admin/` com `/Downloads/myfile.txt`. Parece simples não é? Vamos testar:

```csharp
string caminho1 = "C:/Users/Admin/";
string caminho2 = "/Downloads/myfile.txt";

string arquivo = caminho1 + caminho2;
// C:/Users/Admin//Downloads/myfile.txt
```

Note que o caminho não é o que a gente quer. Tem um `//` ali e provavelmente isso vai dar um erro de execução. E se a gente usar o Path.Combine?

```csharp
string caminho1 = "C:/Users/Admin/";
string caminho2 = "/Downloads/myfile.txt";

string arquivo = Path.Combine(caminho1, caminho2);
// /Downloads/myfile.txt
```

Ainda não é o que queremos. Ele simplesmente ignorou `caminho1`.

# Caminhos são mais complicados do que parecem

Para reproduzir um caminho correto com o método Path.Combine, nós precisaríamos de:

```csharp
string arquivo = Path.Combine("C:", "Users", "Admin", "Downloads", "myfile.txt");
```

Complicado demais! Isso ocorre porque a implementação do Path.Combine é assim: chata. Além disso, ela não permite mais que 260 caracteres no
caminho, mesmo que esteja em outro sistema operacional que não é Windows.

De antemão, é interessante citar o sistema operacional, porque o caractere de separação de diretórios mudam de SO para SO. No Windows é o `\`,
em sistemas *nix é `/` e tenho medo se tiver outro.

Muitas das vezes, as implementações nos dão caminhos relativos que precisam ser concatenados à caminhos absolutos. Se você quiser
obter o caminho relativo à um caminho absoluto com Path.Combine, não conseguirá fazer isso.

```csharp
string caminho1 = "C:/Users/Admin/Downloads/myfile.txt";
string caminho2 = "../../Desktop/imagem.png";

string arquivo = Path.Combine(caminho1, caminho2);

/*
    Expectativa:   C:/Users/Admin/Desktop/imagem.png
    Realidade:     C:/Users/Admin/Downloads/myfile.txt\../../Desktop/imagem.png
*/
```

Então concluimos que o `Path.Combine` é ruim em quase tudo o que precisaríamos.

Vamos construir um método que:
- normalize o caminho para qualquer sistema operacional
- seja seguro na concatenação de caminhos relativos

Podemos começar extraindo todos fragmentos de um caminho, já normalizando eles:

```csharp
string Combine(params string[] paths)
{
    char environmentPathChar = Path.DirectorySeparatorChar;
    List<string> tokens = new List<string>();

    foreach (string path in paths)
    {
        string normalizedPath = path
            .Replace('/', environmentPathChar)
            .Replace('\\', environmentPathChar)
            .Trim(environmentPathChar);

        string[] pathIdentities = normalizedPath.Split(
            environmentPathChar,
            StringSplitOptions.RemoveEmptyEntries
        );
        tokens.AddRange(pathIdentities);
    }

    ...
}
```

Em `tokens` agora temos uma lista de todos os fragmentos que estão presentes nos caminhos informados, inclusive os pontos
relativos aos caminhos. O que basta fazer agora é interpretar e construir nosso caminho.

```csharp
string Combine(params string[] paths)
{
    ...

    Stack<int> insertedIndexes = new Stack<int>();
    StringBuilder pathBuilder = new StringBuilder();
    foreach (string token in tokens)
    {
        if (token == ".")
        {
            // informa que é a mesma pasta, não é necessário
            continue;
        }
        else if (token == "..")
        {
            // informa que é a pasta anterior, então vamos retornar o
            // construtor para uma pasta antes
            pathBuilder.Length = insertedIndexes.Pop();
        }
        else
        {
            // é um fragmento do caminho, então vamos concatenar ele
            insertedIndexes.Push(pathBuilder.Length);
            pathBuilder.Append(token);
            pathBuilder.Append(environmentPathChar);
        }
    }

    return pathBuilder.ToString().TrimEnd(environmentPathChar);
}
```

Agora a função acima irá funcionar perfeitamente para caminhos no Windows. Quando executarmos, teremos:

```csharp
string caminho1 = "C:/Users/Admin/Downloads/myfile.txt";
string caminho2 = "../../Desktop/imagem.png";

string arquivo = Combine(caminho1, caminho2);
// C:\Users\Admin\Desktop\imagem.png
```

Note que ele também corrigiu os `/` para a barra do Windows, que é a `\`. O método irá normalizar para o caractere separador de diretórios
do sistema operacional, independente da forma que a entrada é inserida.

Com alguns detalhes a mais para tornar a função mais amigável à produção, podemos adicionar verificações de nulo, contagem de itens e
inserir o `/` no começo caso o caminho comece com separador (para sistemas *nix).

O resultado final é:

```csharp
/// <summary>
/// Combines strings into a normalized path by the running environment.
/// </summary>
/// <param name="paths">An array of parts of the path.</param>
/// <returns>The combined and normalized paths.</returns>
public static string NormalizedCombine(params string[] paths)
{
    if (paths.Length == 0) return "";

    bool startsWithSepChar = paths[0].StartsWith("/") || paths[0].StartsWith("\\");
    char environmentPathChar = Path.DirectorySeparatorChar;
    List<string> tokens = new List<string>();

    for (int ip = 0; ip < paths.Length; ip++)
    {
        string path = paths[ip]
            ?? throw new ArgumentNullException($"The path string at index {ip} is null.");

        string normalizedPath = path
            .Replace('/', environmentPathChar)
            .Replace('\\', environmentPathChar)
            .Trim(environmentPathChar);

        string[] pathIdentities = normalizedPath.Split(
            environmentPathChar,
            StringSplitOptions.RemoveEmptyEntries
        );

        tokens.AddRange(pathIdentities);
    }

    Stack<int> insertedIndexes = new Stack<int>();
    StringBuilder pathBuilder = new StringBuilder();
    foreach (string token in tokens)
    {
        if (token == ".")
        {
            continue;
        }
        else if (token == "..")
        {
            pathBuilder.Length = insertedIndexes.Pop();
        }
        else
        {
            insertedIndexes.Push(pathBuilder.Length);
            pathBuilder.Append(token);
            pathBuilder.Append(environmentPathChar);
        }
    }

    if (startsWithSepChar)
        pathBuilder.Insert(0, environmentPathChar);

    return pathBuilder.ToString().TrimEnd(environmentPathChar);
}
```

E alguns testes incluem:

```csharp
static void Main(string[] args)
{
    DirectorySeparator = '\\'; // windows
    string pathA = "D:/archives/";
    string pathB = @"\2001\media\file.img";

    Console.WriteLine("NormalizedCombine : {0} + {1} = {2}", pathA, pathB, NormalizedCombine(pathA, pathB));
    Console.WriteLine("IO.Path.Combine   : {0} + {1} = {2}\n", pathA, pathB, Path.Combine(pathA, pathB));

    DirectorySeparator = '/'; // unix
    pathA = "/usr/bin";
    pathB = "config/file.yml";

    Console.WriteLine("NormalizedCombine : {0} + {1} = {2}", pathA, pathB, NormalizedCombine(pathA, pathB));
    Console.WriteLine("IO.Path.Combine   : {0} + {1} = {2}\n", pathA, pathB, Path.Combine(pathA, pathB));

    DirectorySeparator = '\\';
    pathA = "C:/Users/Foobar";
    pathB = "..\\Administrator/notes.txt";

    Console.WriteLine("NormalizedCombine : {0} + {1} = {2}", pathA, pathB, NormalizedCombine(pathA, pathB));
    Console.WriteLine("IO.Path.Combine   : {0} + {1} = {2}\n", pathA, pathB, Path.Combine(pathA, pathB));

    DirectorySeparator = '/'; // unix
    pathA = "/home\\path/mixed\\spaces spaces";
    pathB = "file.txt";

    Console.WriteLine("NormalizedCombine : {0} + {1} = {2}", pathA, pathB, NormalizedCombine(pathA, pathB));
    Console.WriteLine("IO.Path.Combine   : {0} + {1} = {2}\n", pathA, pathB, Path.Combine(pathA, pathB));
}

/*
    Saída:

    NormalizedCombine : D:/archives/ + \2001\media\file.img = D:\archives\2001\media\file.img
    IO.Path.Combine   : D:/archives/ + \2001\media\file.img = \2001\media\file.img

    NormalizedCombine : /usr/bin + config/file.yml = /usr/bin/config/file.yml
    IO.Path.Combine   : /usr/bin + config/file.yml = /usr/bin\config/file.yml

    NormalizedCombine : C:/Users/Foobar + ..\Administrator/notes.txt = C:\Users\Administrator\notes.txt
    IO.Path.Combine   : C:/Users/Foobar + ..\Administrator/notes.txt = C:/Users/Foobar\..\Administrator/notes.txt

    NormalizedCombine : /home\path/mixed\spaces spaces + file.txt = /home/path/mixed/spaces spaces/file.txt
    IO.Path.Combine   : /home\path/mixed\spaces spaces + file.txt = /home\path/mixed\spaces spaces\file.txt
*/
```

Note que: no exemplo acima, troquei de `Path.DirectorySeparatorChar` para uma variável compartilhada para poder simular o comportamento
em outros sistemas operacionais.

# Conclusão

O método `Path.Combine` nativo do .NET tem seu propósito e uso, mas as vezes não é o mais indicado e pode não se ajustar à todas as situações.
Lidar com caminhos é complicado e pode ser perigoso.

Usar caminhos relativos também é perigoso se você não confia alguma das entradas para calcular o caminho final. Caso não tenha intenção
de calcular caminhos relativos, desative estes trechos do código.

```csharp
//Stack<int> insertedIndexes = new Stack<int>();
StringBuilder pathBuilder = new StringBuilder();
foreach (string token in tokens)
{
    if (token == ".")
    {
        continue;
    }
    else if (token == "..")
    {
        continue;
        //pathBuilder.Length = insertedIndexes.Pop();
    }
    else
    {
        //insertedIndexes.Push(pathBuilder.Length);
        pathBuilder.Append(token);
        pathBuilder.Append(environmentPathChar);
    }
}
```

A variável `insertedIndexes` não será mais relevante para indexar a posição de cada separador. Neste caso, `.` e `..` serão ignorados e não
serão "interpretados" por nosso método.