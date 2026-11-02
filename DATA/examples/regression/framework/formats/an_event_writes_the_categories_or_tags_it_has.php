<?php

  // An event's CATEGORIES are the row's categories - a list or a text of commas - else its
  // tags: an empty categories, as the ics reader makes for an event without them, leaves the
  // tags to be written, where it wrote an empty CATEGORIES:; a tag that is a list is left out,
  // where it ended the calendar on "Array to string conversion"; no tag at all, no line.

  $catRows = [
    [ 'summary' => 'A', 'start' => '2026-11-01', 'categories' => '', 'tags' => [ 'a', 'b' ] ],
    [ 'summary' => 'B', 'start' => '2026-11-02', 'tags' => [ [ 'n' => 'x' ], 'y', 3 ] ],
    [ 'summary' => 'C', 'start' => '2026-11-03', 'categories' => 'work, home' ],
    [ 'summary' => 'D', 'start' => '2026-11-04', 'categories' => [ 'one;two' ], 'tags' => 'z' ],
    [ 'summary' => 'E', 'start' => '2026-11-05', 'categories' => '', 'tags' => [] ]
  ];

  preg_match_all ( '/^(SUMMARY|CATEGORIES):.*$/m', str_replace ( "\r", '', padFormatWrite ( 'ics', $catRows ) ), $catHits );

  $catShow = implode ( ' ', $catHits [0] );

?>
