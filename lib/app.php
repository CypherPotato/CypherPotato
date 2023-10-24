<?php

function get_meta($contents, &$metaPos)
{
    $metaSt = strpos($contents, '<!--');
    $metaPos = strpos($contents, '-->');
    $meta = trim(substr($contents, $metaSt + 4, $metaPos - 4));
    return json_decode($meta, false);
}

function blog_posts()
{
    $posts = [];

    foreach (glob(APP_ROOT . "/blog-posts/*/*.md") as $post) {
        $contents = file_get_contents($post);
        $metaObj = get_meta($contents, $metaPos);

        preg_match('/(\d{4}).(\d{2})-(\d{2})/', $post, $matches);
        preg_match('/(\d{4}[\\\\\/].*)\.md/', $post, $link);

        $posts[] = [
            "link" => $link[1],
            "order" => intval($matches[1] . $matches[3] . $matches[2]),
            "contents" => substr($contents, $metaPos + 4, 230),
            "metadata" => $metaObj
        ];
    }

    usort($posts, function ($A, $B) {
        $a = $A["order"];
        $b = $B["order"];

        if ($a == $b) {
            return 0;
        }

        return $a < $b ? 1 : -1;
    });

    return $posts;
}
