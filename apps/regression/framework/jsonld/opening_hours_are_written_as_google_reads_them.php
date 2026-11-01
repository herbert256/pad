<?php

  // Opening hours as Schema.org's openingHours writes them - groups in a list or a text, a day
  // by two letters, three or its name, a range past Sunday, two times a day, a time without
  // its leading zero, a whole day, a night past midnight - each time an
  // OpeningHoursSpecification of its own, one day a text and more a list.

  $shops = [ [ 'name' => 'Lunch break', 'city' => 'Utrecht',
               'hours' => [ 'Mo-Fr 09:00-12:00, 13:00-17:30', 'Saturday 10:00-16:00', 'Su 00:00-23:59' ] ],
             [ 'name' => 'Night shop', 'city' => 'Utrecht',
               'hours' => "Fri-Mon 22:00-3:00\nWe,Th 8:00-12:00" ] ];

?>
