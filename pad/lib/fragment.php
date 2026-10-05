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

  function padFragmentHit () {

    global $pad, $padFragment, $padFragmentCache;

    $padFragment [$pad] = [
      'key'  => padFragmentKey (),
      'ttl'  => padFragmentTtl (),
      'hit'  => FALSE,
      'body' => ''
    ];

    if ( ! $padFragmentCache )
      return FALSE;

    $body = padFragmentGet ( $padFragment [$pad] ['key'] );

    if ( $body === FALSE )
      return FALSE;

    $padFragment [$pad] ['hit']  = TRUE;
    $padFragment [$pad] ['body'] = $body;

    return TRUE;

  }

  function padFragmentEnd () {

    global $pad, $padFragment, $padFragmentCache, $padResult;

    $fragment = $padFragment [$pad] ?? NULL;

    if ( ! $fragment )
      return;

    if ( $fragment ['hit'] )
      $padResult [$pad] = $fragment ['body'];
    elseif ( $padFragmentCache )
      padFragmentPut ( $fragment ['key'], $padResult [$pad], $fragment ['ttl'] );

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

  function padFragmentGet ( $key ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_fetch' ) ) {
      $body = apcu_fetch ( "padFragment:$key", $found );
      return $found ? $body : FALSE;
    }

    $file = DATA . "cache/fragments/$key";

    if ( ! is_file ( $file ) )
      return FALSE;

    $text  = (string) file_get_contents ( $file );
    $split = strpos ( $text, "\n" );

    if ( $split === FALSE or (int) substr ( $text, 0, $split ) < time () )
      return FALSE;

    return substr ( $text, $split + 1 );

  }

  function padFragmentPut ( $key, $body, $ttl ) {

    global $padFragmentCache;

    if ( $padFragmentCache == 'apcu' and function_exists ( 'apcu_store' ) )
      return apcu_store ( "padFragment:$key", $body, $ttl );

    padFragmentPurge ();

    return padFilePut ( "cache/fragments/$key", ( time () + $ttl ) . "\n" . $body );

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
