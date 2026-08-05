<?php

  // Handles the page option: keeps the rows belonging to one page of the tag's data set.
  //
  // Pages are counted from 1 and rows defaults to 10, so page="3" rows="12" keeps rows
  // 25 to 36; padHandGo() drops everything outside that window. Also reached from
  // handling/types/rows.php when rows is used without a page.

  $padHandPage  = (int) ($padPrm [$pad] ['page'] ??  1);
  $padHandRows  = (int) ($padPrm [$pad] ['rows'] ?? 10);

  $padHandStart = ( ( $padHandPage - 1 ) * $padHandRows ) + 1;
  $padHandEnd   = (   $padHandStart      + $padHandRows ) - 1;

  padHandGo ($padData [$pad], $padHandStart, $padHandEnd);

?>