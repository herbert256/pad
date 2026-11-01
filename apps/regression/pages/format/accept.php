<?php

  // The Accept headers of browsers, feed readers, agents and scripts, each asked of a page
  // exposing a list (format/events) and of one exposing a single value (format/scalar): a
  // header that accepts text/html - by name, or through a wildcard at a quality as high as
  // the format's - gets the page, whatever it prefers; a data format is chosen only where it
  // does not, and a format the page cannot give - a table where it has no list - renders the
  // page too, where 406 is for padFormat= asked outright. text/markdown asks for no pipe
  // table: an agent asking for the page as Markdown wants the page, not its first list.

  function acceptAsk ( $page, $accept ) {

    global $padGoExt;

    $answer = padCurl ( [ 'url' => $padGoExt . "$page&padInclude", 'headers' => [ 'Accept' => $accept ] ] );

    return $answer ['result'] . ' ' . explode ( ';', $answer ['headers'] ['Content-Type'] ?? '' ) [0];

  }

  $acceptHeaders = [
    'agent'       => 'text/markdown, text/html;q=0.9, */*;q=0.8',
    'markdown'    => 'text/markdown',
    'feed reader' => 'application/rss+xml, application/atom+xml, application/xml;q=0.9, */*;q=0.8',
    'calendar'    => 'text/calendar',
    'html low'    => 'text/calendar, text/html;q=0.1',
    'html refused'=> 'text/csv, text/html;q=0',
    'any as high' => 'text/calendar, */*',
    'any lower'   => 'text/calendar, */*;q=0.5',
    'text lower'  => 'text/calendar, text/*;q=0.5',
    'axios'       => 'application/json, text/plain, */*',
    'jquery'      => 'application/json, text/javascript, */*; q=0.01',
    'csv'         => 'text/csv'
  ];

  foreach ( [ 'format/events', 'format/scalar' ] as $acceptPage ) {

    echo "== $acceptPage\n";

    foreach ( $acceptHeaders as $acceptName => $acceptHeader )
      echo "$acceptName: ", acceptAsk ( $acceptPage, $acceptHeader ), "\n";

  }

  echo '== asked outright: ', padCurl ( $padGoExt . 'format/scalar&padFormat=ics' ) ['result'];

?>
