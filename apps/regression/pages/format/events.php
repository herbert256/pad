<?php

  // The page format/formats asks for its data in every format that writes. Rendered as a
  // page it is a list; asked with padFormat= or an Accept header it answers the events it
  // exposes - a calendar, a feed, a map, a spreadsheet ... - with the options of
  // $padFormatOptions: the title of a feed, the name of a calendar and of a GPX document.

  $events = [
    [ 'title' => 'Opening talk', 'start' => '2026-11-06T09:00:00Z', 'end' => '2026-11-06T10:00:00Z', 'location' => 'Hall A, Ghent',
      'lat' => 51.0543, 'lon' => 3.7174, 'link' => 'https://example.com/talks/opening', 'summary' => 'Data in & out' ],
    [ 'title' => 'Dinner', 'start' => '2026-11-07', 'location' => 'Graslei',
      'lat' => 51.0545, 'lon' => 3.7206, 'link' => 'https://example.com/dinner', 'summary' => 'All welcome' ]
  ];

  $padExpose        = [ 'events' ];
  $padFormatOptions = [ 'title' => 'Formats week', 'name' => 'Formats week' ];

?>
