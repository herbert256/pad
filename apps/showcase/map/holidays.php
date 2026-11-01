<?php

  // The public holidays of 2026 in each of the sixteen states of Germany, as the holidays
  // subsystem has them (padHolidays, pad/subs/holidays/types/de.php) - the nine of every
  // state and the state's own - each under the region code the map reads too: its ISO
  // 3166-2 code without DE-.

  $holidays = [];

  foreach ( [ 'bw', 'by', 'be', 'bb', 'hb', 'hh', 'he', 'mv', 'ni', 'nw', 'rp', 'sl', 'sn', 'st', 'sh', 'th' ] as $state )
    $holidays [] = [ 'state' => $state,
                     'days'  => count ( array_filter ( padHolidays ( 'de', 2026, $state ), fn ( $day ) => $day ['type'] == 'public' ) ) ];

?>
