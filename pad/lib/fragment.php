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
  //
  // A {nocache} part of the section renders on a hit too: kept as its source ('nocache'),
  // the stored rendering is then made a template again and rendered - lib/nocache.php.

  function padFragmentHit () {

    global $pad, $padFragment, $padFragmentCache, $padFragmentOnly, $padToolbarData;

    $fragment = [
      'key'     => padFragmentKey (),
      'ttl'     => padFragmentTtl (),
      'hit'     => FALSE,
      'body'    => '',
      'stacks'  => [],
      'pagers'  => [],
      'nocache' => NULL
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

    // Counted for the debug toolbar (exits/_lib/toolbar.php).

    $padToolbarData [ ( $entry === FALSE ) ? 'fragmentMiss' : 'fragmentHit' ] =
      ( $padToolbarData [ ( $entry === FALSE ) ? 'fragmentMiss' : 'fragmentHit' ] ?? 0 ) + 1;

    if ( $entry === FALSE )
      return FALSE;

    $padFragment [$pad] ['hit']     = TRUE;
    $padFragment [$pad] ['body']    = padFragmentNonce ( $entry ['body'], TRUE );
    $padFragment [$pad] ['stacks']  = padFragmentNonce ( $entry ['stacks'], TRUE );
    $padFragment [$pad] ['pagers']  = $entry ['pagers'];
    $padFragment [$pad] ['nocache'] = $entry ['nocache'];

    return TRUE;

  }

  function padFragmentEnd () {

    global $pad, $padFragment, $padFragmentCache, $padResult;

    $fragment = $padFragment [$pad] ?? NULL;

    if ( ! $fragment )
      return;

    if ( $fragment ['hit'] ) {

      // A section with {nocache} parts rendered its stored text as a template, the parts
      // made again (level/cached.php) - that rendering is the result.

      if ( ! $fragment ['nocache'] )
        $padResult [$pad] = $fragment ['body'];

      foreach ( $fragment ['stacks'] as [ $name, $text, $once ] )
        padStackPush ( $name, $text, $once );

      foreach ( $fragment ['pagers'] as $name => $booking )
        padPagerBook ( $name, $booking );

    } elseif ( $padFragmentCache ) {

      // The {nocache} parts are kept as their source (lib/nocache.php): what they rendered
      // this time - a token of this visitor's among it - is not what is stored.

      $body   = padFragmentNonce ( padNocacheCut ( $padResult [$pad], $nocache ), FALSE );
      $stacks = padFragmentNonce ( $fragment ['stacks'], FALSE );

      if ( ( $fragment ['keep'] ?? TRUE ) and padFragmentStorable ( $body, $stacks ) )
        padFragmentPut ( $fragment ['key'], $body, $fragment ['ttl'], $stacks, $fragment ['pagers'], $nocache );

    }

  }

  // The CSP nonce is the response's, not the section's: kept as a marker, and a hit writes
  // the nonce of the request it answers there. Kept as it was, it is one no later header
  // names - so a section holding it was not kept at all, and under a style-src 'nonce' no
  // section with a chart, a datatable or a widget, whose <style> carries it, was ever kept.

  // The marker is sealed (padMarkSeal), as a {stack} marker is: a fixed one that a value
  // brought into the section unescaped took each later request's real nonce, and a script
  // injected with it passed the policy.

  function padFragmentNonceMark () {

    return "\u{E0A0}" . padMarkSeal ( 'nonce' ) . "\u{E0A0}";

  }

  function padFragmentNonce ( $text, $in ) {

    global $padNonce;

    if ( is_array ( $text ) )
      return array_map ( fn ( $one ) => padFragmentNonce ( $one, $in ), $text );

    if ( ! is_string ( $text ) )
      return $text;

    if ( $in )
      return str_contains ( $text, padFragmentNonceMark () ) ? str_replace ( padFragmentNonceMark (), padNonce (), $text ) : $text;

    return ( ( $padNonce ?? '' ) !== '' ) ? str_replace ( $padNonce, padFragmentNonceMark (), $text ) : $text;

  }

  // The sections being kept round this level are not kept after all - what a {pdf} in one
  // makes is the answer of the request, and on a hit its handler did not run: the first
  // request got the document, every later one the page's HTML. Up to a {nocache} round the
  // level: that part renders again on a hit, the caller with it, so the sections round the
  // part are kept - a {poll} in a {nocache} kept no section round it at all.

  function padFragmentKeepNot () {

    global $pad, $padFragment, $padTag, $padType;

    for ( $i = $pad; $i >= 0; $i-- ) {
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] )
        $padFragment [$i] ['keep'] = FALSE;
      if ( ( $padTag [$i] ?? '' ) == 'nocache' and ( $padType [$i] ?? '' ) == 'pad' )
        break;
    }

  }

  // A rendering that holds something of this visitor's or of this request's is not kept,
  // as the page cache keeps no such page (padCacheStorable): the session's CSRF token - a
  // {form}, {csrf}, a live region - went to every later visitor of the section, who could
  // post with it on the first one's behalf, and a CSP nonce left in it is one no later
  // header names (padFragmentNonce keeps the response's as a marker). Neither are the
  // visitor's own ids.

  function padFragmentStorable ( $body, $stacks ) {

    global $padCsrfIssued, $padNonce, $padSesID, $padReqID;

    $text = $body . serialize ( $stacks );

    foreach ( [ $padCsrfIssued ?? '', $padNonce ?? '', $padSesID ?? '', $padReqID ?? '' ] as $id )
      if ( $id !== '' and str_contains ( $text, $id ) )
        return FALSE;

    // Nor what this machine alone is shown - a {debug} box (padCacheLocalOnly): the body
    // is still in the engine's encoding here, so it is read as the page will go out.

    if ( padCacheLocalOnly ( padUnprotect ( padUnescape ( $text ) ) ) )
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

    return md5 ( "$padApp $what" . padFragmentLocale () . ' ' . serialize ( padFragmentVary ( $vary ) ) );

  }

  // A section is written in the request's locale - {trans}, currency, localDate - as a cached
  // page is: with several locales each has an entry of its own, where the first visitor's
  // language was served to every other. An application of one locale keeps the key it had.

  function padFragmentLocale ( $locale = NULL ) {

    global $padLocales, $padLocale;

    if ( count ( (array) ( $padLocales ?? [] ) ) < 2 )
      return '';

    return ' locale ' . ( $locale ?? $padLocale );

  }

  // A vary= value as the key holds it: a single value as its text, so a section rendered
  // with vary=$userId - the integer 42 - is forgotten by padFragmentForget ( 'basket', '42' ),
  // the same user as the request or a row spells it; serialized with its type the two were
  // different keys, and the forget missed. A list stays a list.

  function padFragmentVary ( $vary ) {

    if ( $vary === NULL )
      return '';

    return is_scalar ( $vary ) ? (string) $vary : $vary;

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
  // page bookings it made, 'nocache' => the sources of its {nocache} parts or NULL ]. A file
  // holds the expiry time on its first line, followed - when there were pushes, bookings or
  // parts - by the length of the three serialized together, which then stand in front of
  // the body. An entry written when only the pushes were kept holds their list there, and
  // is read as such.

  function padFragmentGet ( $key ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_fetch' ) ) {

      $entry = apcu_fetch ( "padFragment:$key", $found );

      if ( ! $found )
        return FALSE;

      return ( is_array ( $entry ) ? $entry : [ 'body' => $entry ] ) + [ 'stacks' => [], 'pagers' => [], 'nocache' => NULL ];

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
      'body'    => substr ( $text, $split + 1 + $size ),
      'stacks'  => is_array ( $extra ['stacks']  ?? NULL ) ? $extra ['stacks']  : [],
      'pagers'  => is_array ( $extra ['pagers']  ?? NULL ) ? $extra ['pagers']  : [],
      'nocache' => is_array ( $extra ['nocache'] ?? NULL ) ? $extra ['nocache'] : NULL
    ];

  }

  function padFragmentPut ( $key, $body, $ttl, $stacks = [], $pagers = [], $nocache = NULL ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_store' ) )
      return apcu_store ( "padFragment:$key", [ 'body' => $body, 'stacks' => $stacks, 'pagers' => $pagers, 'nocache' => $nocache ], $ttl );

    padFragmentPurge ();

    $head  = time () + $ttl;
    $extra = '';

    if ( $stacks or $pagers or $nocache ) {
      $extra = serialize ( [ 'stacks' => $stacks, 'pagers' => $pagers, 'nocache' => $nocache ] );
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

    // A named section is forgotten in every locale it was written in (padFragmentLocale).

    if ( $name !== NULL ) {

      $done    = FALSE;
      $locales = ( count ( (array) ( $GLOBALS ['padLocales'] ?? [] ) ) < 2 ) ? [ NULL ] : (array) $GLOBALS ['padLocales'];

      foreach ( $locales as $locale ) {

        $key = md5 ( "$padApp name $name" . padFragmentLocale ( $locale ) . ' ' . serialize ( padFragmentVary ( $vary ) ) );

        if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_delete' ) )
          $done = ( apcu_delete ( "padFragment:$key" ) or $done );
        else
          $done = ( @unlink ( DATA . "cache/fragments/$key" ) or $done );

      }

      return $done;

    }

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_delete' ) )
      return apcu_delete ( new APCUIterator ( '/^padFragment:/' ) );

    foreach ( glob ( DATA . 'cache/fragments/*' ) ?: [] as $file )
      @unlink ( $file );

    return TRUE;

  }

?>
