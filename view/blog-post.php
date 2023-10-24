<!DOCTYPE html>
<html lang="pt-BR">

<?= render_view(
    'component.head',
    [
        'title' => $meta->title,
        'description' => trim(substr($contents, 0, 180)) . '...'
    ]
) ?>

<body>
    <main>
        <?= render_view('component.header') ?>
        <style>
            #content>h1,
            #content>h2 {
                margin-top: 50px;
            }

            #content table {
                width: 100%;
                border-collapse: collapse;
            }

            #content table td {
                padding: 8px 12px;
                border: 1px solid #bbb;
            }

            #content table thead tr {
                background-color: #eee;
            }

            #title {
                text-align: center;
                font-size: 38px;
                margin-block: 80px 50px;
                border-bottom: 1px solid #bbb;
                padding-block: 2rem 1rem;
            }

            #title>.sub-texts {
                text-align: center;
                font-size: 16px;
                font-weight: 400;
                opacity: .95;
                letter-spacing: 0.12mm;
                margin-top: 5px;
            }
        </style>
        <a style="display: block; padding-block: 1rem;" href="../">
            &larr; Voltar ao diretório
        </a>
        <h1 id="title">
            <?= $meta->title ?>
            <div class="sub-texts">
                Publicado: <?= $meta->published ?>
            </div>
        </h1>
        <article id="content">
            <?php
            $p = new Parsedown();
            echo  $p->text($contents);
            ?>
        </article>
        <script>
            Prism.highlightAll();
        </script>
    </main>
</body>

</html>