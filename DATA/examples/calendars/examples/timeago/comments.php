<?php

  // A thread whose comments were posted some time before now.

  $comments = [];

  foreach ( [ [ 'Maya',   '#2a78d6', '-4 months',   'Is there a way to export the report as a spreadsheet?' ],
              [ 'Jonas',  '#1baf7a', '-3 weeks',    'The CSV answer of the same page does exactly that.' ],
              [ 'Priya',  '#eb6834', '-1 day',      'Tried it today - one line in the page and it works.' ],
              [ 'Tomás',  '#7c5cff', '-2 hours',    'Can the columns be chosen?' ],
              [ 'Maya',   '#2a78d6', '-5 minutes',  'Yes: list them in the page, in the order you want.' ],
              [ 'Ken',    '#e34948', '-3 seconds',  'Thanks, everyone!' ] ] as list ( $name, $color, $when, $text ) )
    $comments [] = [ 'name' => $name, 'color' => $color, 'posted' => padNow ()->modify ( $when )->format ( 'Y-m-d H:i:s' ), 'text' => $text ];

?>
