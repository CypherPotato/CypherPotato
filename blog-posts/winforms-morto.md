## Pergunta:

O Windows Forms já está a aproximadamente 17 anos no mercado desde o .NET Framework 1.0 lançado em 2002, e ainda permanece uma das principais limitações de funcionar apenas em Windows, como o próprio nome sugere, e agora com a vinda de outras interfaces que sucederam ele como o WPF e o UWP isso parece ainda mais certo.

Então o Windows Forms ou winforms está de fato morto? Ou pelo menos quase morto? Ou isso ainda seria uma suposição exagerada?

## Resposta de CypherPotato:

É exagerado pensar na forma que o Windows Forms está morto do mesmo jeito que pensei que o [.NET Framework estivesse morto][1]. Por mais que ambos estão sendo substituídos por tecnologias mais recentes, inovadoras e modernas, continuam tendo suporte. O Windows Forms é utilizado até hoje para pequenos e grandes propósitos pelo seu maior aspecto: é simples.

O propósito de _drag-drop_ dos componentes do Windows Forms torna seu desenvolvimento tão simples e fácil que não necessita estudar a fundo sobre para desenvolver belas aplicações. Recentemente, [a Microsoft levou o Windows Forms para o .NET Core][2], o que lembra que a Microsoft pensa nele, e consequentemente, adapta sua antiga tecnologia nas suas novas. Em breve, podemos ver o Windows Forms rodando nativamente em um Linux ou Mac, o propósito do [.NET 5][3] é esse, afinal.

O que acontecerá com as antigas tecnologias?
---

A resposta mais simples é: se adaptarão as novas, sem perder sua natureza. O .NET Framework será substituído pelo .NET Core, assim como o Windows Forms poderá ser substituído por um futuro Windows Forms Core (isso é uma suposição, isso não existe). Desde que essas tecnologias são agora código-aberto, qualquer um pode portar ou criar sua versão.

> e agora com a vinda de outras interfaces que sucederam ele como o WPF e o UWP isso parece ainda mais certo.

São plataformas mais recentes que o Windows Forms, mas com propósitos diferentes. O WPF vem do XAML, uma linguagem criada pela Microsoft para adaptar os desenvolvedores Mobile, Xamarin e Desktop numa estrutura só. O UWP veio com o propósito de ser um XAML aperfeiçoado, que o único código execute em qualquer plataforma, desde que seja Windows.

Nenhuma é tão simples como o Windows Forms, e tanto a Microsoft quanto sua comunidade é ciente disso.

> Para quem não sabe, o .NET Core 3 já suporta alguns cenários que antes não funcionava nele, como o uso de WinForms, WPF, EF6, e outros. Os cenários que ele não suporta ainda, não será suportado porque é muito ruim e deveria ser abandonado em favor das soluções melhores que tem para o Core. E tem para todas que ainda podem ser úteis.
>
> _Retirado da resposta de Maniero [desta pergunta][4]._

A Microsoft quer que você use suas tecnologias, independente de qual plataforma/sistema seja, e ela está tentando facilitar isso com o .NET Core desde seu lançamento. A tecnologia .NET hoje é presente em todos os sistemas operacionais graças ao .NET Core.

Por suma, o Windows Forms do .NET Framework está parando lentamente, não há mais porquê utilizar ele, assim como um dia o .NET Framework será aposentado também. O .NET Core (e futuramente .NET, apenas) está aí, com tudo que já estamos acostumados, incluindo o Windows Forms.

<hr>

Isso também pode ser útil:

- [O Windows Forms está morto? (Em Inglês)][5]
- [Por que o Windows Forms ainda não morreu? (Em Inglês)][6]
- [A morte do Windows Forms foi um grande exagero. (Em Inglês)][7]


  [1]: https://pt.stackoverflow.com/questions/385594/o-net-framework-est%C3%A1-morto
  [2]: https://www.infoq.com/br/news/2019/02/dotnet-core-3-preview/
  [3]: https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&uact=8&ved=2ahUKEwiE39a2spnjAhWwLLkGHb5JAOsQFjABegQIABAB&url=https%3A%2F%2Fdevblogs.microsoft.com%2Fdotnet%2Fintroducing-net-5%2F&usg=AOvVaw2JlrlbbKypuWhY5MgtVmXD
  [4]: https://pt.stackoverflow.com/a/385615/24529
  [5]: https://iamtimcorey.com/ask-tim-is-winforms-dead/
  [6]: https://medium.com/@beribey/why-is-winform-still-not-dead-should-we-learn-winform-5d776463579b
  [7]: https://blog.submain.com/death-winforms-greatly-exaggerated/