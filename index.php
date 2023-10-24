<?php

define('APP_ROOT', __DIR__);
define('NO_CACHE', 1);

require "./lib/fw.php";
require "./lib/app.php";
require "./lib/parsedown.php";

set_view('/', 'home');
set_route('GET', '/<year>/<post>', function ($q) {
    $path = APP_ROOT . "/blog-posts/" . $q["year"] . "/" . $q["post"] . ".md";
    $contents = file_get_contents($path);
    $meta = get_meta($contents, $metaPos);
    $contents = substr($contents, $metaPos + 3);

    render_view('blog-post', ["contents" => $contents, "meta" => $meta]);
});

router_execute();
