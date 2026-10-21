<?php

  // A CSV row with fewer cells than the header - a trailing empty cell left out, as a
  // spreadsheet export does - has every header field, the missing ones empty: Bob's row had
  // no city, so {$city} on it found the page's own $city, and with no such variable the
  // page ended "Field '$c' not found".

  $city = 'the page city';

?>
