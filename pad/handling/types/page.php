<?php

  // Handles the page option: keeps the rows belonging to one page of the tag's data set.
  //
  // Pages are counted from 1 and rows defaults to 10, so page="3" rows="12" keeps rows
  // 25 to 36; padHandGo() drops everything outside that window. Also reached from
  // handling/types/rows.php when rows is used without a page.
  //
  // A {select} level has had its page cut by the SQL limit already - padSelectLimit marks
  // the two options 'limit' - and paging those rows again would keep nothing past page 1.
  //
  // A level written with page= books its page, rows and total for a {pager} first -
  // padPagerKeep in lib/pager.php - counted after the handling options written before it.

  if ( isset ( $padPrm [$pad] ['page'] ) ) {
    padPagerKeep ( count ( $padData [$pad] ) );
    $padHandPaged = TRUE;
  }

  if ( ( $padDone [$pad] ['page'] ?? '' ) === 'limit' )
    return;

  // A page below one - ?pg=0, a page value that is no number - is the first page, as the
  // SQL limit of a select (padSelectLimit) and the {pager} take it; it kept no rows at all,
  // under a pager that marked page 1 as the one shown.

  $padHandPage  = max ( 1, (int) ($padPrm [$pad] ['page'] ??  1) );
  $padHandRows  = (int) ($padPrm [$pad] ['rows'] ?? 10);

  $padHandStart = ( ( $padHandPage - 1 ) * $padHandRows ) + 1;
  $padHandEnd   = (   $padHandStart      + $padHandRows ) - 1;

  padHandGo ($padData [$pad], $padHandStart, $padHandEnd);

?>