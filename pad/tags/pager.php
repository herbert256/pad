<?php

  // {pager 'products', window=2}: the page links of a tag written with the page option -
  // ‹ 1 … 4 5 [6] 7 8 … 20 › - from the page, rows and total the handling walk booked under
  // the tag's name (padPagerKeep in lib/pager.php). window is the number of pages shown on
  // each side of the current one, 2 when not given; query names the request value the
  // links set, which is otherwise the variable page= was written with. Without a name the
  // pager follows the last paged tag of the page.
  //
  // Written as a single tag it answers a nav of links, the current page marked
  // aria-current="page". Written as a pair it hands the links over as rows instead - kind,
  // page, label and href - for markup of the template's own; with a single page there are
  // no rows, so the pair shows its @else@. The paged tag must come first: the pager reads
  // what its handling left.

  $padPagerName   = trim ( (string) $padParm );
  $padPagerWindow = max ( 0, (int) padTagParm ( 'window', 2 ) );
  $padPagerQuery  = trim ( (string) padTagParm ( 'query', '' ) );

  if ( $padPagerName === '' )
    $padPagerName = (string) array_key_last ( $padPager ?? [] );

  if ( ! isset ( $padPager [$padPagerName] ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no paged tag named '" . padMakeSafe ( $padPagerName, 40 ) . "' before this {pager} - the tag with page= comes first" );

    return $padPair [$pad] ? [] : '';

  }

  $padPagerItems = padPagerItems ( $padPager [$padPagerName], $padPagerWindow,
                                   $padPagerQuery !== '' ? $padPagerQuery : $padPager [$padPagerName] ['query'] );

  if ( $padPair [$pad] )
    return $padPagerItems;

  return $padPagerItems ? padPagerHtml ( $padPagerItems ) : '';

?>
