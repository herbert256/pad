<?php

  // A column named offset is no OFFSET clause: after h.offset a ', {0}' is an ordinary item,
  // so '007' stays text. A window frame wants a number: ROWS BETWEEN {0} PRECEDING with '1'
  // was quoted, "Integer is required for ROWS-type frame".

  $row   = db ( "record h.offset, {0} as code from (select 1 as `offset`) h", [ '007' ] );
  $frame = db ( "array name, sum(salary) over (order by name rows between {0} preceding and current row) as s from staff order by name", [ '1' ] );

  $code = $row ['code'];
  $sums = implode ( ' ', array_column ( $frame, 's' ) );

?>
