<?php

  // The pager: page links for a tag that the page handling option slices.
  //
  // padPagerKeep   called by the handling walk for every level written with a page option,
  //                once its other handling options have run up to the page: it books the
  //                page, the rows per page and the number of rows to page through under the
  //                level's name, in $padPager, where {pager 'name'} finds it later in the
  //                page. A Select table has had its page cut by the SQL limit, so the total
  //                is not in the rows: its count statement is booked instead and run only
  //                when a pager asks. The request value the links set is the one the page
  //                option was written with - page=$pg makes them set pg - and 'page'
  //                otherwise.
  // padPagerItems  the links of one booked level as rows: previous, the numbers - the first,
  //                the last and a window around the current page, with a gap where pages
  //                are left out - and next. Each row has kind (prev, next, page, current,
  //                gap), page, label and href ('' for the current page's neighbours that do
  //                not exist and for a gap).
  // padPagerHtml   those rows as the default markup: a nav of links, the current page marked
  //                aria-current="page" and a missing previous or next as a disabled span.
  // padPagerHref   the link to one page: $padGo, so a mount prefix keeps working, the page
  //                the request asked for, and the request's other query values with the
  //                page value set - the engine's own pad* switches are left out.
  //
  // Every app wrote its own previous and next links from the page option's numbers; the
  // handling step knew the total and the current page and kept them to itself.

  function padPagerKeep ( $total ) {

    global $pad, $padPrm, $padParms, $padName, $padDone, $padPager, $padPagerCount;

    $query = 'page';

    foreach ( $padParms [$pad] as $one )
      if ( $one ['padPrmKind'] == 'option' and $one ['padPrmName'] == 'page'
           and preg_match ( '/^\s*page\s*=\s*\$([A-Za-z_][A-Za-z0-9_]*)/', $one ['padPrmOrg'] ?? '', $match ) )
        $query = $match [1];

    $limit = ( ( $padDone [$pad] ['page'] ?? '' ) === 'limit' );

    $padPager [ $padName [$pad] ] = [
      'page'  => max ( 1, (int) ( $padPrm [$pad] ['page'] ?? 1 ) ),
      'rows'  => (int) ( $padPrm [$pad] ['rows'] ?? 10 ),
      'total' => $limit ? NULL : $total,
      'count' => $limit ? ( $padPagerCount [$pad] ?? '' ) : '',
      'query' => $query
    ];

  }

  function padPagerItems ( $info, $window, $query ) {

    if ( $info ['total'] === NULL )
      $info ['total'] = $info ['count'] ? (int) db ( $info ['count'] ) : 0;

    if ( $info ['rows'] < 1 )
      return [];

    $pages = (int) ceil ( $info ['total'] / $info ['rows'] );

    if ( $pages < 2 )
      return [];

    $page  = $info ['page'];
    $shown = min ( $page, $pages );
    $items = [];

    $numbers = [ 1, $pages ];
    for ( $n = max ( 1, $shown - $window ); $n <= min ( $pages, $shown + $window ); $n++ )
      $numbers [] = $n;

    $numbers = array_unique ( $numbers );
    sort ( $numbers );

    $items [] = padPagerItem ( 'prev', ( $page > 1 ) ? min ( $page - 1, $pages ) : 0, '‹', $query );

    $before = 0;

    foreach ( $numbers as $n ) {

      // One page left out is shown rather than replaced by a gap of the same width.

      if ( $n == $before + 2 )
        $items [] = padPagerItem ( 'page', $before + 1, $before + 1, $query );
      elseif ( $n > $before + 2 )
        $items [] = padPagerItem ( 'gap', 0, '…', $query );

      $items [] = padPagerItem ( ( $n == $page ) ? 'current' : 'page', $n, $n, $query );

      $before = $n;

    }

    $items [] = padPagerItem ( 'next', ( $page < $pages ) ? $page + 1 : 0, '›', $query );

    return $items;

  }

  function padPagerItem ( $kind, $page, $label, $query ) {

    return [
      'kind'  => $kind,
      'page'  => $page ?: '',
      'label' => (string) $label,
      'href'  => $page ? padPagerHref ( $query, $page ) : ''
    ];

  }

  function padPagerHref ( $query, $page ) {

    global $padGo, $padStartPage;

    $keep  = [];
    $first = array_key_first ( $_GET );

    foreach ( $_GET as $key => $value )
      if     ( $key === $first and $value === '' ) continue;
      elseif ( (string) $key === $query          ) continue;
      elseif ( str_starts_with ( (string) $key, 'pad' ) ) continue;
      else   $keep [$key] = $value;

    $keep [$query] = $page;

    return $padGo . $padStartPage . '&' . http_build_query ( $keep );

  }

  function padPagerHtml ( $items ) {

    $catalog = padTransCatalog ();
    $out     = [];

    foreach ( $items as $item ) {

      $href  = htmlspecialchars ( $item ['href'], ENT_QUOTES, 'UTF-8' );
      $label = htmlspecialchars ( $item ['label'], ENT_QUOTES, 'UTF-8' );

      if ( $item ['kind'] == 'prev' or $item ['kind'] == 'next' ) {

        $aria = htmlspecialchars ( ( $item ['kind'] == 'prev' )
          ? ( $catalog ['pager.previous'] ?? 'Previous page' )
          : ( $catalog ['pager.next']     ?? 'Next page'     ), ENT_QUOTES, 'UTF-8' );

        $out [] = $href
          ? "<a href=\"$href\" rel=\"{$item ['kind']}\" aria-label=\"$aria\">$label</a>"
          : "<span class=\"{$item ['kind']}\" aria-disabled=\"true\" aria-label=\"$aria\">$label</span>";

      }
      elseif ( $item ['kind'] == 'gap'     ) $out [] = "<span class=\"gap\">$label</span>";
      elseif ( $item ['kind'] == 'current' ) $out [] = "<a href=\"$href\" aria-current=\"page\">$label</a>";
      else                                   $out [] = "<a href=\"$href\">$label</a>";

    }

    $nav = htmlspecialchars ( $catalog ['pager.label'] ?? 'Pagination', ENT_QUOTES, 'UTF-8' );

    return "<nav class=\"pager\" aria-label=\"$nav\">" . implode ( ' ', $out ) . '</nav>';

  }

?>
