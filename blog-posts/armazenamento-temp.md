Armazenamento de cache em memória é algo essencial que pode ser útil para várias ocasiões. O ASP.NET possui algo
nativo para isso, e achei que seria interessante criar algo para o Sisk também.

A ideia surgiu com o site de uma gravadora, onde a API foi escrita com Sisk, e fazia algumas consultas no banco de dados
MySQL. As consultas eram rápidas, mas conforme muitos usuários entravam no site, a API ficava fazendo várias consultas,
e acaba deixando a aplicação pesada, além de sobrecarregar o servidor.

Foi então onde tive a ideia de criar um cache no lado do servidor. Não acho legal confiar muito no cache do lado do cliente,
pois eles ainda podem tentar explorar seu serviço.

Com isso, a ideia era dele armazenar a resposta em cache por seis horas e depois retornar as futuras requisições com a resposta
armazenada em cache.

Para isso, fiz uma pequena classe, com [código fonte aqui](https://github.com/CypherPotato/MemoryCacheStorage/blob/main/CacheStorage.cs), que
disponibiliza uma classe que lida com cache durante o runtime, e após um determinado tempo, o ponteiro para o objeto é descartado. Depois disso, o GC irá fazer o trabalho dele.

```csharp
[Route(RouteMethod.Get, "/releases")]
[RequestHandler(typeof(CacheRequestHandler))]
static HttpResponse GetReleases(HttpRequest request)
{
    using DbAccess db = new DbAccess();
    var query = db.Releases.AsQueryable();
    
    // ...
    // imagine toda a consulta que faço no banco de dados aqui

    // aqui é um handler que criei para criar respostas JSON rapidamente
    var res = Program.CreateJsonResponse(200, releases);

    // armazeno minha HttpResponse em cache, por 4 horas, apelido ela com o caminho da URL
    // e com uma tag customizada
    Program.Cache.Set(res, TimeSpan.FromHours(4), new string[] { request.FullPath, "releases" });

    return res;
}
```

Reparem que lá em cima eu instancio um `CacheRequestHandler` na minha rota, a qual irá fazer a mágica de
verificar se minha resposta já está pronta ou não.

```csharp
internal class CacheRequestHandler : IRequestHandler
{
    // aqui defino que esse requestHandler deverá executar antes do cliente enviar
    // o conteúdo da requisição. somente os cabeçalhos já servem para mim aqui.
    public RequestHandlerExecutionMode ExecutionMode { get; init; } 
        = RequestHandlerExecutionMode.BeforeContents;

    public HttpResponse? Execute(HttpRequest request, HttpContext context)
    {
        // quando tem cache para o caminho da URL, TryGetValue me traz "true"
        if (Program.Cache.TryGetValue(request.FullPath, out object? res))
        {
            HttpResponse cachedRes = (HttpResponse)res!;

            // defino um cabeçalho amigável para indicar que a resposta vêm de cache
            cachedRes.Headers["X-Cache-Restored"] = DateTime.Now.ToString("s");
            return cachedRes;
        }
        else
        {
            // a resposta não está cacheada, então deverá ser computada
            return null;
        }
    }
}
```

Mas tinha um detalhe interessante nisso tudo: certos momentos, o moderador do site precisava atualizar
informações sobre um lançamento, e essa informação ainda estava em cache, portanto, não iria refletir
imediatamente para os usuários logados no site.

Foi para isso que fiz o `Invalidate`, que através de uma tag ou ID do cache, eu invalido o mesmo.

```csharp
[Route(RouteMethod.Post, "/release/<id>")]
static HttpResponse UpdateReleaseInfo(HttpRequest request)
{
    using DbAccess db = new DbAccess();
    string id = request.Query["id"]!;

    ...
    ...
    ...

    db.SaveChanges();

    // aqui eu invalido todos os caches que estão etiquetados com "requests", que
    // é o caso da resposta dos lançamentos
    Program.Cache.Invalidate("requests");

    return new HttpResponse(200);
}
```

Os objetos `HttpResponse` podem ser reaproveitados uma vez que entregues ao servidor HTTP, como é o caso
que estou cacheando toda a resposta HTTP na hora de enviar para o servidor.

Esse motor de cache é de baixa latência e não tem um worker que verifica periodicamente cada
objeto armazenado em cache. Ao salvar um objeto em cache, em paralelo ao construtor, ele irá aguardar o
tempo definido para invalidar o objeto e então invalidará o mesmo.

Toda a classe é thread-safe e também é possível usar para mais de uma função de cache, não somente respostas
HTTP.

Todo o código fonte do `MemoryCacheStorage` está disponível no meu [repositório](https://github.com/CypherPotato/MemoryCacheStorage/blob/main/CacheStorage.cs).
No momento não está publicado no Nuget, mas tenho planos de publicá-lo após ter certeza que ele está estável o suficiente para por em produção.