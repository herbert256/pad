<?php

  // The fragment cache: a section of a page kept rendered, so a hit skips the tags inside it
  // - and the tag's own data retrieval - while the page around it stays dynamic.
  //
  //   {cache 'top-products', ttl=300} ... {/cache}    a named section
  //   {topProducts cache=300} ... {/topProducts}       any tag, the option form
  //
  // level/start.php asks padFragmentHit() before the tag handler runs. A hit skips the
  // handler and the level renders nothing; level/end.php then calls padFragmentEnd(), which
  // puts the stored rendering in place of the level's result - before the end-phase options
  // and the closing pipe, so those run on a hit as on a miss - or, on a miss, stores what the
  // level rendered. PHP that ran before the template - the page's own .php - runs either
  // way: the saving is the work done inside the section.
  //
  // What a key depends on is the author's to say. A named section is keyed on the
  // application and its name, so one name is one section wherever it stands; the option
  // form on the application, the page, the tag as written and its evaluated parameters.
  // Both add vary=, for whatever else the rendering depends on: vary=$userId keeps one copy
  // per user.
  //
  // $padFragmentCache picks the store: 'file' (DATA/cache/fragments/), 'apcu', or FALSE to
  // render every time.
  //
  // A {push} made while a section renders is part of what the section does, so it is kept
  // with the rendering ('stacks') and made again on a hit - lib/stack.php records it. So is
  // the page booking of a paged tag in it ('pagers', padPagerKeep in lib/pager.php): a
  // {pager} after the section found no paged tag on a hit, or one with a single page.

  function padFragmentHit () {

    global $pad, $padFragment, $padFragmentCache, $padFragmentOnly, $padToolbarData;

    $fragment = [
      'key'    => padFragmentKey (),
      'ttl'    => padFragmentTtl (),
      'hit'    => FALSE,
      'body'   => '',
      'stacks' => [],
      'pagers' => []
    ];

    // A request for one response fragment alone (lib/respond.php) renders the section: a
    // {fragment} inside it must close to be the response, and a stored rendering has no
    // levels that close - the request ended in "there is no fragment named ...". So does
    // the post of a live region (lib/live.php), whose {live} must render to be answered;
    // what such a post renders follows its event, so it is not stored either.

    if ( (string) $padFragmentOnly !== '' or padLive () !== '' )
      return FALSE;

    $padFragment [$pad] = $fragment;

    if ( ! $padFragmentCache )
      return FALSE;

    $entry = padFragmentGet ( $padFragment [$pad] ['key'] );

    // Counted for the debug toolbar (lib/toolbar.php).

    $padToolbarData [ ( $entry === FALSE ) ? 'fragmentMiss' : 'fragmentHit' ] =
      ( $padToolbarData [ ( $entry === FALSE ) ? 'fragmentMiss' : 'fragmentHit' ] ?? 0 ) + 1;

    if ( $entry === FALSE )
      return FALSE;

    $padFragment [$pad] ['hit']    = TRUE;
    $padFragment [$pad] ['body']   = $entry ['body'];
    $padFragment [$pad] ['stacks'] = $entry ['stacks'];
    $padFragment [$pad] ['pagers'] = $entry ['pagers'];

    return TRUE;

  }

  function padFragmentEnd () {

    global $pad, $padFragment, $padFragmentCache, $padResult;

    $fragment = $padFragment [$pad] ?? NULL;

    if ( ! $fragment )
      return;

    if ( $fragment ['hit'] ) {

      $padResult [$pad] = $fragment ['body'];

      foreach ( $fragment ['stacks'] as [ $name, $text, $once ] )
        padStackPush ( $name, $text, $once );

      foreach ( $fragment ['pagers'] as $name => $booking )
        padPagerBook ( $name, $booking );

    } elseif ( $padFragmentCache and padFragmentStorable ( $padResult [$pad], $fragment ['stacks'] ) )

      padFragmentPut ( $fragment ['key'], $padResult [$pad], $fragment ['ttl'], $fragment ['stacks'], $fragment ['pagers'] );

  }

  // A rendering that holds something of this visitor's or of this request's is not kept,
  // as the page cache keeps no such page (padCacheStorable): the session's CSRF token - a
  // {form}, {csrf}, a live region - went to every later visitor of the section, who could
  // post with it on the first one's behalf, and the CSP nonce was one no later header
  // names, so the section's scripts stopped running. Neither are the visitor's own ids.

  function padFragmentStorable ( $body, $stacks ) {

    global $padCsrfIssued, $padNonce, $padSesID, $padReqID;

    $text = $body . serialize ( $stacks );

    foreach ( [ $padCsrfIssued ?? '', $padNonce ?? '', $padSesID ?? '', $padReqID ?? '' ] as $id )
      if ( $id !== '' and str_contains ( $text, $id ) )
        return FALSE;

    return TRUE;

  }

  function padFragmentNamed () {

    global $pad, $padTag, $padType;

    return ( $padTag [$pad] == 'cache' and $padType [$pad] == 'pad' );

  }

  function padFragmentKey () {

    global $pad, $padOpt, $padPrm, $padOrg, $padApp, $padPage;

    $vary = padTagParm ( 'vary', '' );

    if ( padFragmentNamed () )
      $what = 'name ' . ( $padOpt [$pad] [1] ?? '' );
    else
      $what = "page $padPage " . $padOrg [$pad] . ' ' . serialize ( $padOpt [$pad] ) . ' '
            . serialize ( array_diff_key ( $padPrm [$pad], [ 'cache' => 1, 'ttl' => 1, 'vary' => 1 ] ) );

    // A clean URL route is one page for every value its brackets bind (lib/route.php):
    // products/[id] for 42 and for 43 are two pages, and a section of one is not the other's.

    if ( ! padFragmentNamed () and preg_match_all ( '/\[([a-zA-Z][a-zA-Z0-9_]*)\+?\]/', $padPage, $route ) )
      foreach ( $route [1] as $name )
        $what .= " $name=" . serialize ( $GLOBALS [$name] ?? '' );

    return md5 ( "$padApp $what " . serialize ( $vary ) );

  }

  // The seconds an entry lives: ttl=, or the value of the option form - cache=3600 - and five
  // minutes when neither says.

  function padFragmentTtl () {

    $ttl = padTagParm ( 'ttl', '' );

    if ( $ttl === '' and ! padFragmentNamed () )
      $ttl = padTagParm ( 'cache', '' );

    if ( $ttl === TRUE or ! is_numeric ( $ttl ) )
      $ttl = 300;

    return max ( 1, (int) $ttl );

  }

  // An entry is [ 'body' => the rendering, 'stacks' => the pushes it made, 'pagers' => the
  // page bookings it made ]. A file holds the expiry time on its first line, followed -
  // when there were pushes or bookings - by the length of the two serialized together,
  // which then stand in front of the body. An entry written when only the pushes were kept
  // holds their list there, and is read as such.

  function padFragmentGet ( $key ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_fetch' ) ) {

      $entry = apcu_fetch ( "padFragment:$key", $found );

      if ( ! $found )
        return FALSE;

      return ( is_array ( $entry ) ? $entry : [ 'body' => $entry ] ) + [ 'stacks' => [], 'pagers' => [] ];

    }

    $file = DATA . "cache/fragments/$key";

    if ( ! is_file ( $file ) )
      return FALSE;

    $text  = (string) file_get_contents ( $file );
    $split = strpos ( $text, "\n" );

    if ( $split === FALSE )
      return FALSE;

    $head = explode ( ' ', substr ( $text, 0, $split ) );

    if ( (int) $head [0] < time () )
      return FALSE;

    $size  = (int) ( $head [1] ?? 0 );
    $extra = $size ? @unserialize ( substr ( $text, $split + 1, $size ), [ 'allowed_classes' => FALSE ] ) : [];

    if ( ! is_array ( $extra ) )
      $extra = [];

    if ( ! array_key_exists ( 'stacks', $extra ) and ! array_key_exists ( 'pagers', $extra ) )
      $extra = [ 'stacks' => $extra ];

    return [
      'body'   => substr ( $text, $split + 1 + $size ),
      'stacks' => is_array ( $extra ['stacks'] ?? NULL ) ? $extra ['stacks'] : [],
      'pagers' => is_array ( $extra ['pagers'] ?? NULL ) ? $extra ['pagers'] : []
    ];

  }

  function padFragmentPut ( $key, $body, $ttl, $stacks = [], $pagers = [] ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_store' ) )
      return apcu_store ( "padFragment:$key", [ 'body' => $body, 'stacks' => $stacks, 'pagers' => $pagers ], $ttl );

    padFragmentPurge ();

    $head  = time () + $ttl;
    $extra = '';

    if ( $stacks or $pagers ) {
      $extra = serialize ( [ 'stacks' => $stacks, 'pagers' => $pagers ] );
      $head .= ' ' . strlen ( $extra );
    }

    return padFilePut ( "cache/fragments/$key", "$head\n$extra$body" );

  }

  // File entries do not expire by themselves: at most once an hour - the purged marker's
  // mtime says when the last sweep ran - the ones past their time are deleted.

  function padFragmentPurge () {

    $marker = DATA . 'cache/fragments/.purged';

    if ( is_file ( $marker ) and filemtime ( $marker ) > time () - 3600 )
      return;

    padFilePut ( 'cache/fragments/.purged', '' );

    foreach ( glob ( DATA . 'cache/fragments/*' ) ?: [] as $file ) {
      $head = (string) @file_get_contents ( $file, FALSE, NULL, 0, 20 );
      if ( (int) $head < time () )
        @unlink ( $file );
    }

  }

  // Empties the store, or with a name the one named section - for the code that changed what
  // the section shows: padFragmentForget ( 'top-products' ) after a product was saved. A
  // named section that carries vary= is forgotten per variant: pass the same vary value.

  function padFragmentForget ( $name = NULL, $vary = '' ) {

    global $padApp, $padFragmentCache;

    if ( $name !== NULL ) {

      $key = md5 ( "$padApp name $name " . serialize ( $vary ) );

      if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_delete' ) )
        return apcu_delete ( "padFragment:$key" );

      return @unlink ( DATA . "cache/fragments/$key" );

    }

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_delete' ) )
      return apcu_delete ( new APCUIterator ( '/^padFragment:/' ) );

    foreach ( glob ( DATA . 'cache/fragments/*' ) ?: [] as $file )
      @unlink ( $file );

    return TRUE;

  }

?>
