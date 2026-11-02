<?php

  // A duration of ISO 8601 passes as it is, in capitals; one in words - numbers and their
  // units - is written as ISO 8601, in days, hours, minutes and seconds.

  $times = [ [ 'name' => 'ISO',                'image' => 'a.jpg', 'prep' => 'PT1M33S' ],
             [ 'name' => 'ISO in lower case',  'image' => 'b.jpg', 'prep' => 'p1dt2h' ],
             [ 'name' => 'Minutes',            'image' => 'c.jpg', 'prep' => '90 min' ],
             [ 'name' => 'Hours and minutes',  'image' => 'd.jpg', 'prep' => '1 h 30 min' ],
             [ 'name' => 'A part of an hour',  'image' => 'e.jpg', 'prep' => '1.5 hours' ],
             [ 'name' => 'Written out',        'image' => 'f.jpg', 'prep' => '1 hour, 5 minutes and 30 seconds' ],
             [ 'name' => 'Past a day',         'image' => 'g.jpg', 'prep' => '36 hours' ],
             [ 'name' => 'Days',               'image' => 'h.jpg', 'prep' => '2 days' ],
             [ 'name' => 'None at all',        'image' => 'i.jpg', 'prep' => '0 min' ] ];

?>
