<?php

  $q = 'cache';

  $results = [
    [ 'title' => 'Fragment cache',
      'url'   => '?manual/cache',
      'body'  => 'Some parts of a page cost more to render than others: a list of the best selling products, a menu built from the database, a report over a year of orders. The fragment cache keeps the rendered text of such a section for a number of seconds and serves it again on the next request without running the tags inside it. Name the section, give it a ttl, and forget it when what it shows has changed.' ],
    [ 'title' => 'Remote data',
      'url'   => '?manual/curl',
      'body'  => 'A page that shows exchange rates or the weather fetches them from another server. Every request would wait for that server, so the answer is kept for a while: the ttl option says how many seconds. When the source fails afterwards, the last good copy in the cache is served and the failure is logged, so a slow or broken service never takes the page down with it.' ],
    [ 'title' => 'Page cache',
      'url'   => '?manual/config',
      'body'  => 'With the page cache switched on in the configuration, a whole page is kept after its first rendering, with an ETag, and a browser that asks again with that tag gets a 304 Not Modified. Pages with a guard, a flash message or a form are never cached. The cache can live in files, APCu, Redis, Memcached or the database.' ] ];

?>
