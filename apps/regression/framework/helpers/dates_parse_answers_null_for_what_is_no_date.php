<?php

  // NULL, empty or blank text, booleans, arrays, INF and NAN, text that is no date, and a
  // date that does not exist - 2026-02-30, which PHP itself would turn into 2 March - are
  // no date: NULL, never an error, since they are what a database or a form hands over.

  $values = [ NULL, '', '   ', TRUE, FALSE, [], [ '2026-10-05' ], INF, NAN, 'nonsense',
              '2026-02-30', '2026-13-01', '31/31/2026', new stdClass ];

  $none = json_encode ( array_map ( fn ( $value ) => padDateParse ( $value ) === NULL, $values ) );

?>
