<?php

  // The pages a benchmark times: every page of every application as the regression crawl
  // walks them, fetched bare - shared by ?benchmark and ?benchmark/check. The regression
  // family is left out: its pages are test fixtures, not pages anyone serves, and some of
  // them fetch their own probes or run the gate itself, which inside a benchmark ran every
  // suite again and, on a server with a handful of workers, starved the fetches it waited
  // for.
  //
  // A few at a time: a page that fetches a page of its own needs a free worker for it, and
  // the local php -S server has eight.

  function benchUrls () {

    global $padHost;

    $urls = [];

    foreach ( padAppsList () as $page => $one )
      if ( ! str_starts_with ( $one ['app'], 'regression/' ) )
        $urls [$page] = $padHost . $one ['app'] . '/?' . $one ['item'] . '&padInclude';

    return $urls;

  }

  function benchWindow () {

    return max ( 1, min ( 24, (int) ( $_GET ['window'] ?? 4 ) ) );

  }

?>
