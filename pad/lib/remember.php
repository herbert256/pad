<?php

  // The application cache and rate limits: values a page's PHP keeps between requests -
  // the answer of a slow query or a remote service, a count of login attempts - so the
  // work is done once and not on every request.
  //
  //   $rates = padRemember ( 'rates', 600, function () { return db ( "ARRAY * FROM rates" ); } );
  //   if ( ! padRateLimit ( "login:$ip", 5, 60 ) ) padRedirect ( 'login/wait' );
  //
  // padCacheGet              the value kept under a key, else the default (a Closure is called)
  // padCachePut              keeps a value under a key for $ttl seconds - NULL is for ever
  // padCacheHas              whether a key holds a value that has not expired
  // padCacheForget           removes a key
  // padCacheFlush            removes every key of this application, rate limits included
  // padRemember              the value kept under a key, or the callback's, kept for $ttl
  // padRateLimit             counts a hit on a key: TRUE within $maxAttempts per window of
  //                          $decaySeconds, FALSE over it (a fixed window)
  // padRateLimitRemaining    the hits left in the current window
  // padRateLimitAvailableIn  the seconds until the current window ends
  // padRateLimitClear        ends the window: the key starts from nothing
  //
  // padCacheAppDir / padCacheAppFile / padCacheAppRead / padCacheAppKeep / padCacheAppWrite
  // / padCacheAppExpires / padCacheAppRefused / padCacheAppShow / padCacheAppSweep /
  // padCacheAppLocked / padRateLimitArgs / padRateLimitWindow are the private helpers below.
  //
  // The store is a directory per application, DATA/cache/app/<application>/, so each
  // application has its own entries: one file per key, named by a hash of the key, holding
  // the expiry time on its first line (0 for ever) and the serialized value after it. A file
  // is written beside its place and renamed over it (padFilePut), so a reader sees the old
  // value or the new one, never half of one. Values are read back with allowed_classes
  // FALSE - a file that was tampered with cannot make an object - so what is kept is arrays
  // and scalars: a value holding an object is refused when it is put, where reading it back
  // would have given a broken __PHP_Incomplete_Class. A rate limit is an entry of the same
  // store under a hash of its own, its value the hits, its expiry the end of the window; a
  // hit is counted under a lock, so requests that come at once are each counted. Once an
  // hour a write sweeps the entries that expired and were never read again - a rate limit
  // per visitor leaves one file each. A replayed request (lib/replay.php) writes nothing.
  //
  // The page cache's own read of a stored page is padCacheBody (cache/types/), so these
  // names are free for the application.

  function padCacheGet ( $key, $default = NULL ) {

    $file  = padCacheAppFile ( $key, 'cache', 'padCacheGet' );
    $entry = ( $file === '' ) ? NULL : padCacheAppRead ( $file );

    if ( $entry )
      return $entry ['value'];

    return ( $default instanceof Closure ) ? $default () : $default;

  }

  // A ttl of 0 or less keeps nothing: what the key held is removed and the answer is FALSE,
  // as it is for a value that cannot be kept.

  function padCachePut ( $key, $value, $ttl = 3600 ) {

    $file    = padCacheAppFile    ( $key, 'cache', 'padCachePut' );
    $expires = padCacheAppExpires ( $ttl, 'padCachePut' );

    if ( $file === '' or $expires === NULL )
      return FALSE;

    if ( $expires < 0 ) {
      if ( is_file ( $file ) and ! padReplaying () )
        @unlink ( $file );
      return FALSE;
    }

    return padCacheAppKeep ( $file, $value, $expires, 'padCachePut', $key );

  }

  function padCacheHas ( $key ) {

    $file = padCacheAppFile ( $key, 'cache', 'padCacheHas' );

    return ( $file !== '' and padCacheAppRead ( $file ) !== NULL );

  }

  // TRUE when the key held a value that had not expired yet.

  function padCacheForget ( $key ) {

    $file = padCacheAppFile ( $key, 'cache', 'padCacheForget' );

    if ( $file === '' or ! is_file ( $file ) )
      return FALSE;

    $held = ( padCacheAppRead ( $file ) !== NULL );

    if ( ! padReplaying () )
      @unlink ( $file );

    return $held;

  }

  // The files of this application's directory only - an application below it, should one
  // stand in a subdirectory of this one, keeps its own.

  function padCacheFlush () {

    if ( padReplaying () )
      return TRUE;

    foreach ( glob ( padCacheAppDir () . '*.cache' ) ?: [] as $file )
      @unlink ( $file );

    return TRUE;

  }

  // A stored NULL counts as kept: a callback that found nothing is not asked again until
  // the entry expires. A ttl of 0 or less keeps nothing, and the callback runs every time.
  // The expiry is counted from when the callback is done, not from when it started.

  function padRemember ( $key, $ttl, $callback ) {

    if ( ! is_callable ( $callback ) ) {
      padError ( 'padRemember: the third argument is the callback that makes the value - not '
               . padCacheAppShow ( $callback ) );
      return NULL;
    }

    // A ttl of 0 or less runs the callback also when the key holds a value an earlier call
    // kept: that value was answered, and a page asking for a fresh one got the stale one.

    $file  = padCacheAppFile ( $key, 'cache', 'padRemember' );
    $ahead = ( $file === '' ) ? NULL : padCacheAppExpires ( $ttl, 'padRemember' );

    if ( $ahead === NULL or $ahead < 0 )
      return $callback ();

    $entry = padCacheAppRead ( $file );

    if ( $entry )
      return $entry ['value'];

    $value   = $callback ();
    $expires = padCacheAppExpires ( $ttl, 'padRemember' );

    if ( $expires >= 0 )
      padCacheAppKeep ( $file, $value, $expires, 'padRemember', $key );

    return $value;

  }

  // A fixed window: the first hit starts it, $decaySeconds long, and it allows $maxAttempts
  // hits; a hit over the limit is not counted - the window ends when it would have anyway.

  function padRateLimit ( $key, $maxAttempts, $decaySeconds = 60 ) {

    $args = padRateLimitArgs ( 'padRateLimit', $key, $maxAttempts, $decaySeconds );

    if ( ! $args )
      return FALSE;

    [ $file, $max, $decay ] = $args;

    return padCacheAppLocked ( function () use ( $file, $max, $decay ) {

      [ $hits, $reset ] = padRateLimitWindow ( $file );

      if ( $hits >= $max )
        return FALSE;

      padCacheAppWrite ( $file, $hits + 1, $reset ?: time () + $decay );

      return TRUE;

    } );

  }

  function padRateLimitRemaining ( $key, $maxAttempts ) {

    $args = padRateLimitArgs ( 'padRateLimitRemaining', $key, $maxAttempts, 60 );

    if ( ! $args )
      return 0;

    [ $hits ] = padRateLimitWindow ( $args [0] );

    return max ( 0, $args [1] - $hits );

  }

  // 0 when no window is running: the next hit is allowed now.

  function padRateLimitAvailableIn ( $key ) {

    $file = padCacheAppFile ( $key, 'rate', 'padRateLimitAvailableIn' );

    if ( $file === '' )
      return 0;

    [ , $reset ] = padRateLimitWindow ( $file );

    return $reset ? max ( 0, $reset - time () ) : 0;

  }

  function padRateLimitClear ( $key ) {

    $file = padCacheAppFile ( $key, 'rate', 'padRateLimitClear' );

    if ( $file === '' )
      return FALSE;

    if ( is_file ( $file ) and ! padReplaying () )
      @unlink ( $file );

    return TRUE;

  }

  // The arguments of a rate limit checked: [ file, maximum, seconds ], or NULL after the
  // fault is reported. A maximum of 0 allows nothing.

  function padRateLimitArgs ( $function, $key, $maxAttempts, $decaySeconds ) {

    $file = padCacheAppFile ( $key, 'rate', $function );

    if ( $file === '' )
      return NULL;

    if ( ! ( is_int ( $maxAttempts ) or ( is_string ( $maxAttempts ) and ctype_digit ( $maxAttempts ) ) )
         or $maxAttempts < 0 ) {
      padError ( "$function: the maximum of attempts is a whole number, 0 or more - not "
               . padCacheAppShow ( $maxAttempts ) );
      return NULL;
    }

    if ( ! ( is_int ( $decaySeconds ) or is_float ( $decaySeconds )
             or ( is_string ( $decaySeconds ) and is_numeric ( $decaySeconds ) ) )
         or ! ( $decaySeconds >= 1 ) or is_infinite ( (float) $decaySeconds ) ) {
      padError ( "$function: the window is a number of seconds, 1 or more - not "
               . padCacheAppShow ( $decaySeconds ) );
      return NULL;
    }

    // A window too long for a whole number - 1e20 seconds - is one that ends in the year
    // 9999: the cast to a whole number ended the request, and an end of the window past
    // PHP_INT_MAX was written as a float that was never read back, so no hit counted.

    return [ $file, (int) $maxAttempts, (int) ceil ( min ( (float) $decaySeconds, 253402300799 - time () ) ) ];

  }

  // [ hits, end of the window ] of the window that is running, [ 0, 0 ] when none is.

  function padRateLimitWindow ( $file ) {

    $entry = padCacheAppRead ( $file );

    if ( ! $entry or ! is_int ( $entry ['value'] ) )
      return [ 0, 0 ];

    return [ $entry ['value'], $entry ['expires'] ];

  }

  function padCacheAppDir () {

    return DATA . 'cache/app/' . $GLOBALS ['padApp'] . '/';

  }

  // The file of a key, '' after the fault is reported for a key that is no key. A cache
  // key and a rate limit of the same name are two entries: the kind is part of the hash.

  function padCacheAppFile ( $key, $kind, $function ) {

    if ( is_int ( $key ) or is_float ( $key ) or $key instanceof Stringable )
      $key = (string) $key;

    if ( ! is_string ( $key ) or $key === '' ) {
      padError ( "$function: the key is a name like 'rates' or \"login:\$ip\" - not "
               . padCacheAppShow ( $key ) );
      return '';
    }

    return padCacheAppDir () . md5 ( "$kind\n$key" ) . '.cache';

  }

  // [ 'value' => ..., 'expires' => time or 0 ] of an entry that has not expired, NULL for
  // none - also for a file that is half there or not ours.

  function padCacheAppRead ( $file ) {

    $text = is_file ( $file ) ? @file_get_contents ( $file ) : FALSE;

    if ( $text === FALSE or ! str_contains ( $text, "\n" ) )
      return NULL;

    [ $expires, $data ] = explode ( "\n", $text, 2 );

    if ( ! ctype_digit ( $expires ) or ( $expires > 0 and $expires <= time () ) )
      return NULL;

    $value = @unserialize ( $data, [ 'allowed_classes' => FALSE ] );

    if ( $value === FALSE and $data !== serialize ( FALSE ) )
      return NULL;

    return [ 'value' => $value, 'expires' => (int) $expires ];

  }

  function padCacheAppKeep ( $file, $value, $expires, $function, $key ) {

    $refused = padCacheAppRefused ( $value );

    if ( $refused !== '' ) {
      padError ( "$function: the cache keeps arrays and scalars - the value for '$key' holds $refused" );
      return FALSE;
    }

    return padCacheAppWrite ( $file, $value, $expires );

  }

  function padCacheAppWrite ( $file, $value, $expires ) {

    if ( padReplaying () )
      return TRUE;

    if ( padFilePut ( $file, "$expires\n" . serialize ( $value ) ) === FALSE )
      return FALSE;

    padCacheAppSweep ( dirname ( $file ) . '/' );

    return TRUE;

  }

  // The end of an entry's life as a time, 0 for ever, -1 when it is over already, NULL
  // after the fault is reported. A number of seconds, NULL, a moment (DateTimeInterface) or
  // a span (DateInterval); one so far ahead that it does not fit is for ever.

  function padCacheAppExpires ( $ttl, $function ) {

    if ( $ttl === NULL )
      return 0;

    $now = time ();

    if ( $ttl instanceof DateTimeInterface )
      $seconds = $ttl->getTimestamp () - $now;
    elseif ( $ttl instanceof DateInterval )
      $seconds = ( new DateTimeImmutable ( "@$now" ) )->add ( $ttl )->getTimestamp () - $now;
    elseif ( ( is_int ( $ttl ) or is_float ( $ttl ) or ( is_string ( $ttl ) and is_numeric ( $ttl ) ) )
             and ! is_nan ( (float) $ttl ) )
      $seconds = (float) $ttl;
    else {
      padError ( "$function: the ttl is a number of seconds, NULL for ever, a DateTimeInterface or a DateInterval - not "
               . padCacheAppShow ( $ttl ) );
      return NULL;
    }

    if ( $seconds <= 0 )
      return -1;

    $expires = $now + ceil ( $seconds );

    return ( $expires >= PHP_INT_MAX ) ? 0 : (int) $expires;

  }

  // What in a value cannot come back as it went in, '' when nothing: an object - a Closure,
  // a DateTime, a stdClass - or a resource, at any depth.

  function padCacheAppRefused ( $value, $depth = 0 ) {

    if ( is_object ( $value ) )
      return 'an object of class ' . get_class ( $value );

    if ( str_starts_with ( gettype ( $value ), 'resource' ) )
      return 'a resource';

    if ( is_array ( $value ) and $depth < 256 )
      foreach ( $value as $one )
        if ( ( $refused = padCacheAppRefused ( $one, $depth + 1 ) ) !== '' )
          return $refused;

    return '';

  }

  function padCacheAppShow ( $value ) {

    if ( $value === '' )
      return 'an empty string';

    if ( is_string ( $value ) )
      return "'" . mb_substr ( $value, 0, 40 ) . "'";

    if ( is_bool ( $value ) )
      return $value ? 'TRUE' : 'FALSE';

    if ( is_int ( $value ) or is_float ( $value ) )
      return (string) $value;

    return get_debug_type ( $value );

  }

  // Once an hour - the .swept file's time says when the last sweep ran - the entries whose
  // time is over are removed. Reading the first line is enough to know.

  function padCacheAppSweep ( $dir ) {

    $stamp = $dir . '.swept';
    $now   = time ();

    if ( is_file ( $stamp ) and filemtime ( $stamp ) > $now - 3600 )
      return;

    @touch ( $stamp );

    foreach ( glob ( $dir . '*.cache' ) ?: [] as $file ) {

      $handle = @fopen ( $file, 'r' );

      if ( ! $handle )
        continue;

      $expires = trim ( (string) fgets ( $handle ) );

      fclose ( $handle );

      if ( ctype_digit ( $expires ) and $expires > 0 and $expires <= $now )
        @unlink ( $file );

    }

  }

  // A rate limit reads, adds one and writes: two requests doing that at once would both
  // read 4 and both write 5. The application's lock file makes them take turns; it is held
  // for the few file operations of one hit. Without a lock - a directory that cannot be
  // written - the hit is counted all the same.

  function padCacheAppLocked ( $callback ) {

    global $padDirMode;

    $dir = padCacheAppDir ();

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, $padDirMode ?? 0755, TRUE );

    $lock = @fopen ( $dir . '.lock', 'c' );

    if ( $lock )
      flock ( $lock, LOCK_EX );

    try {
      return $callback ();
    } finally {
      if ( $lock ) {
        flock  ( $lock, LOCK_UN );
        fclose ( $lock );
      }
    }

  }

?>
