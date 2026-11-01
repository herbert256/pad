<?php

  // {holidays 'nl', year=2026}{$date} {$name}{/holidays} - the public holidays of a country,
  // drawn from the rules of its file in subs/holidays/types/ by the holidays subsystem,
  // subs/holidays/holidays.php: as a pair the rows, as a single tag a table of them.

  return include PAD . 'subs/holidays/holidays.php';

?>
