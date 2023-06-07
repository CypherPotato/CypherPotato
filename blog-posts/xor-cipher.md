## Pergunta:

Eu tenho um vetor de bytes `a`, e um segredo que considero seguro, do mesmo tamanho de `a`, chamado de `k`, e estou cogitando utilizar XOR em `a`, onde ocorre na mesma posição de `k`, para obter um vetor criptograficamente seguro.

```csharp
int length = a.Length;
byte[] outputKey = new byte[length];

for (int i = 0; i < length; i++)
{
    outputKey[i] = (byte)(a[i] ^ k[i]);
}
```

Consigo encriptar e decriptar ao usar a mesma chave, de forma unidirecional (o mesmo método encripta e decripta), mas não tenho certeza se é seguro o suficiente usar esse método.

É seguro encriptar/decriptar bytes dessa forma?

## Resposta:

Esse tipo de cifragem é conhecida como "XOR Cipher", ou "Cifra Ou-Exclusiva" em português. Pelo que muitos acreditam, ela é insegura e não deveria ser usada, no entanto, estão errados por esta crença.

A segurança dessa cifragem depende do quão seguro é o que consideramos como "chave" neste algorítmo. Por se tratar de uma operação `A ^ B` de byte-a-byte, precisamos garantir que `B` seja seguro e randômico o suficiente para não deixar rastros de padrões, repetições ou laços que possam ser quebrados uma análise de frequência.

A maior vantagem deste método de criptografia é a simplicidade, performance e facilidade de implantação, oferecendo nenhum ou baixíssmo custo computacional, em vista que o processamento é mínimo nessa operação.

Uma repetição de hashes, que é uma prática comum com este método, é um dos mais perigosos e inseguros. Além de denotar a repetição, cria um padrão na decodificação, que por vez acaba possibilitando a quebra da criptografia em curtíssimo tempo.

Uma exigência deste método é que o tamanho de `A` seja o mesmo de `B`, e por muitas vezes isso não ocorre. Como o caso de cifrar um longo texto `A` com mais de 1000 bytes com uma chave `B` de 32 bytes.

Chaves sequênciais, repetidas, previsíveis ou incrementais, são o principal risco deste método. Repetir `B` até alcançar o tamanho de `A` é um erro, independente se é um hash, dois, cinquênta hashes.

Combinar vários algorítmos de hashes também eleva o custo computacional e não é a melhor forma de corrigir este problema.

Uma prática "segura", é derivar `B` para um stream seguro de bytes de tamanho variável. É possível realizar isso com Pbkdf2.

Consideramos o pseudo-código abaixo:

```csharp
byte[] inputBytes = ... // 1024 bytes
byte[] derivedBytes = Rfc2898DeriveBytes.Pbkdf2(
    key,
    salt, 
    10000,
    HashAlgorithmName.SHA256,
    inputBytes.Length);
byte[] encryptedBytes = XOR(inputBytes, derivedBytes);
```

Temos `inputBytes`, com 1024 bytes, e temos `key` com 32 bytes. Ao criarmos uma deriva Pbkdf2, obtemos um vetor de 1024 bytes, randômicos o suficiente para serem processados com a cifra XOR em nosso vetor original.

A Cifra de XOR funciona muito bem porque é reversível em uma operação bitwise simples. Podemos encriptar ou decriptar usando a mesma operação. Isso ocorre porque ela é uma [função involutiva](https://pt.wikipedia.org/wiki/Involu%C3%A7%C3%A3o_(matem%C3%A1tica)), ou seja, sua própria aplicação é a reversão de seu resultado.

<table>
    <thead>
        <td>A</td>
        <td>B</td>
        <td>XOR</td>
        <td>OR</td>
        <td>AND</td>
    </thead>
    <tbody>
        <tr>
            <td>0</td><td>0</td><td>0</td><td>0</td><td>1</td>
        </tr>
        <tr>
            <td>1</td><td>0</td><td>1</td><td>1</td><td>0</td>
        </tr>
        <tr>
            <td>1</td><td>1</td><td>0</td><td>1</td><td>1</td>
        </tr>
        <tr>
            <td>0</td><td>1</td><td>1</td><td>1</td><td>0</td>
        </tr>
        <tr>
            <td>1</td><td>1</td><td>0</td><td>1</td><td>1</td>
        </tr>
    </tbody>
</table>

Em suma, a operação XOR é barata, eficiente e se usada de forma correta poderá ser sua melhor aliada na criptografia. A segurança depende exclusivamente da segurança da chave gerada e aplicada em sua função, e claro, do quão "secreta" ela também é.