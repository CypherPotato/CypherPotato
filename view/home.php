<!DOCTYPE html>
<html lang="pt-BR">

<?= render_view('component.head', ['title' => 'Página Inicial']) ?>

<body>
    <main>
        <?= render_view('component.header') ?>
        <div class="present">
            <h1>
                Olá. 👋
            </h1>
            <p>
                Sou Gabriel Scatolin (a.k.a. CypherPotato), programador amador desde
                2014, profissional desde 2019.
            </p>
            <p>
                Tenho alguma experiência com:
            </p>
            <ul>
                <li>
                    C#
                </li>
                <li>
                    PHP
                </li>
                <li>
                    SQL
                </li>
                <li>
                    HTML/JS/CSS
                </li>
                <li>
                    Outras coisas, como Regex, criptografia, blockchain e aquele monte de coisa de programador.
                </li>
            </ul>
            <p>
                Sou membro da <a href="https://github.com/dotnet-foundation">.NET Foundation</a>.
            </p>
            <p>
                Visite alguns de meus projetos de código aberto:
            </p>
            <ul>
                <li>
                    <a target="_blank" href="https://sisk.proj.pw/">Sisk</a>, um servidor HTTP leve, fácil e super rápido.
                </li>
                <li>
                    <a target="_blank" href="https://cascadium.project-principium.dev/">Cascadium</a>,
                    um experimento de pré-processador para CSS.
                </li>
                <li>
                    <a target="_blank" href="https://github.com/CypherPotato/cryptonite">Cryptonite</a>, uma biblioteca simples para operações
                    criptográficas.
                </li>
            </ul>
        </div>
        <div class="present" style="padding-block: 1rem;">
            <p style="margin-top: 0;">
                Quer falar comigo?
            </p>
            <p style="margin-bottom: 0;">
                Envie um e-mail para mim: <a href="mailto:gab@proj.pw">gab@proj.pw</a>
            </p>
        </div>
        <div class="present">
            <h1>
                Últimas publicações
            </h1>
        </div>
        <div id="blog-posts" style="padding-block: .5rem;">
            <?php foreach (blog_posts() as $post) : ?>
                <a class="blog-post" href="/<?= $post["link"] ?>">
                    <div role="color" style="background-color: <?= $post["metadata"]->color ?>"></div>
                    <div class="header">
                        <strong><?= $post["metadata"]->title ?></strong>
                        <span><?= $post["metadata"]->published ?></span>
                    </div>
                    <div class="contents">
                        <?= $post["contents"] ?>...
                    </div>
                    <div class="tags">
                        <?php foreach ($post["metadata"]->tags as $tag) : ?>
                            <div><?= $tag ?></div>
                        <?php endforeach; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</body>

</html>