<!--
{
    "title": "Checklist de Política de Privacidade",
    "published": "23 de outubro, 2023",
    "color": "#621B97",
    "tags": ["criptografia", "segurança", "politica-de-privacidade"]
}
-->

Sabemos que programador não é apenas escrever códigos. O trabalho da programação vai além disso: vai para a área de segurança também. Proteger o seu cliente/empresa é essencial para garantir a segurança do seu emprego.

Quero falar um pouco sobre a tal da **política de privacidade** e o quão importante ela é para manter seus dados protegidos e evitar uma **multa milionária** para seu cliente. Sabia que, dependendo do contrato que você estabeleceu com ele, essa multa também pode ir para você, mero programador? Então vamos nos previnir.

Então vamos pontuar alguns itens interessantes para você não se esquecer de colocar na política de privacidade do seu projeto. Além disso, alguns pontos interessantes para arquitetura do seu projeto.

### Armazenamento de dados

- **Onde são armazenados os dados dos usuários?** - importante indicar se os dados são armazenados em servidores brasileiros ou extrangeiros. Note que, ao armazenar informação em servidores fora do Brasil, a legislação vigente do país extrangeiro deve ser compatível com a nossa legislação e principalmente com a LGPD. Esse [mapa](https://www.serpro.gov.br/lgpd/menu/a-lgpd/mapa-da-protecao-de-dados-pessoais) mostra quais países são adequados para maioria dos protocolos LGPD.
- **Onde são processados estes dados?** - após armazenar, você precisa processar estes dados, certo? Onde são processados? No Brasil? No exterior? Comumente você usa o país do servidor onde está o seu aplicativo pelo seu serviço.
- **O que é armazenado?** - importante informar ao seu usuário o que você está armazenando dele. Dados públicos (nome completo, cpf, nascimento), dados pessoais, dados determinísticos, dados de geolocalização, etc.
- **Por que é armazenado?** - explique por que você coleta esses dados e por que são armazenados.
- **Por quanto tempo é armazenado?** - alguns tipos de informações exigem um armazenamento permanente, outros são armazenados por um tempo menor. Explique isso nessa sessão.
- **Quem tem acesso à estes dados?** - isso inclui sobre quem de seu cliente e outros usuários poderão acessar estes dados armazenados.

#### Serviços de terceiros e seus dados

É muito importante que você leia a política de privacidade para cada serviço terceiro que for embutir no seu serviço. A política de privacidade é um contrato, e os contratos devem ser compatíveis com os termos do seu instrumento.

Para cada serviço que usar, mapeie as perguntas acima e visualize quais estão de acordo com suas políticas. Os que não estiverem devem ser explicados na sua política de privacidade com clareza o que é divergente da atual. Não é necessário informar o nome do serviço, mas que vocês também compartilham essa informação com serviços de terceiros e que eles processam de forma diferente.

### Controle de contas

- **Onde posso solicitar remoção da minha conta?** - muito importante. Na lei LGDP, o usuário deve ter acesso fácil à um canal onde possa solicitar remoção completa de seus dados do seu serviço. Isso inclui todos os dados que sejam determinísticos à ele, ou seja, todos os dados que você consegue associar àquele usuário. Dados não determinísticos, como logs de acesso, IDs, não são necessários serem removidos, mas é sempre interessante explicar ao usuário o que é mantido após a exclusão de dados.
- **Permissão para recebimento de e-mails/notificações** - informe ao seu cliente que ele receberá notificações por e-mail/WhatsApp/etc. É importante essa notificação para que ele não prejudique seu serviço e que não caia nas mãos de uma blacklist.

### Divulgação e marketing

- **Você fará marketing com dados do seu cliente?** - informe se você poderá usar informações do seu cliente para fins de marketing e fins comerciais. No geral, isso envolve enviar mailsletter, promoções, ligar pra ele oferecendo descontos, etc.

### Alterações na política de privacidade

Como dito anteriormente, a política de privacidade **é um contrato** entre o usuário e seu serviço. Qualquer alteração deste contrato, deve ser notificada ao seu usuário e você deve dar uma opção de recusar o contrato e remover seu cadastro do seu serviço.

Sempre informe a data da última alteração à política de privacidade. Armazene as versões antigas em arquivos e disponibilize-as para seus usuários. Também permita que os usuários baixem uma cópia deste contrato.

### Dicas de segurança

- O acesso ao banco de dados **deve ser o mais restrito** possível. Nem mesmo o seu cliente deve ter acesso à ele. Quando eu estabeleço contrato de desenvolvimento com algum cliente, além do termo de confidencialidade, existe uma cláusula que **somente o webmaster** pode acessar, editar e visualizar o banco de dados. É um dos locais mais críticos para segurança de uma empresa. Um acesso mal intencionado pode expor dados privados de seus usuários e causar **prejuízos irrecuperáveis**.
- Criptografia de dados é interessante. A forma que você armazena estes dados é interessante aplicar uma criptografia no sistema. Alguns serviços utilizo criptografia simétrica, onde a chave é criada em um ambiente seguro no sistema operacional. Outros, utilizo criptografia assimétrica, onde a chave privada é uma deriva do próprio usuário.
- Rastreabilidade também te traz segurança, principalmente jurídica. Qualquer acesso, ação tomada no sistema, acesso mal intencionado, é registrado em logs e são rastreados dados que podem auxiliar seus usuários judicialmente.
- Hash de senhas é legal, mas faça direito. Não armazene somente com `hash(senha)`, torna esse hash vulnerável à mil e um ataques diferentes. Crie algo imprevisível, com um salto, um vetor de inicialização (armazenado fora do aplicativo de preferência).
- Proteja seu código fonte. Não digo para encriptar ou ofuscar ele, isso quase nunca é necessário. Proteja o acesso à máquina onde está seu código fonte. As regras do banco de dados também se aplicam à sua máquina. Proteger a aplicação e proteger o banco de dados à todo custo.

----

Essas são algumas das dicas que dou à vocês com base ao que aprendi ao longo da minha carreira. O que vocês acharam? Tem mais dicas para colaborar? Discorda de algo? Comente aqui.