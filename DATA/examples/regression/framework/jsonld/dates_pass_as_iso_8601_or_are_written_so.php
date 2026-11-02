<?php

  $rows = [ [ 'name' => 'ISO date',       'starts' => '2026-11-20' ],
            [ 'name' => 'ISO with zone',  'starts' => '2026-11-20T19:30:00.5Z' ],
            [ 'name' => 'Written out',    'starts' => '20 November 2026' ],
            [ 'name' => 'With a time',    'starts' => '2026-11-20 19:30' ],
            [ 'name' => 'With an offset', 'starts' => 'Nov 20 2026 19:30 +01:00' ],
            [ 'name' => 'Unix time',      'starts' => 1795203000 ],
            [ 'name' => 'Date object',    'startDate' => new DateTimeImmutable ( '2026-11-20 08:00', new DateTimeZone ( 'Europe/Amsterdam' ) ) ] ];

?>
