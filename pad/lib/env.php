<?php

  // Environment values: what differs per machine and must not stand in the code - the
  // database password, an API key, a debug switch - read from the server's environment or
  // from a .env file beside the code, so one application runs unchanged on a laptop and on
  // a production server, and the secrets never reach git.
  //
  //   $padSqlPassword = padEnv ( 'DB_PASSWORD' );            in _config/config.php
  //   $debug          = padEnv ( 'APP_DEBUG', FALSE );       in a page's .php
  //
  // padEnv         the value of a key: the real environment first (getenv, $_ENV, $_SERVER),
  //                then the application's _config/.env, then .env in the PAD home, else
  //                the default (a Closure default is called)
  // padEnvReal     a key's value in the real environment of the process, or NULL
  // padEnvFile     the pairs of one .env file, read and parsed once per request
  // padEnvParse    the pairs of a .env text: KEY=VALUE lines, # comments, export in front,
  //                "double quoted" values with escapes and ${OTHER}, 'single quoted' ones
  //                literal, and the words true, false, null and empty
  // padEnvQuote    where the closing quote of a quoted value stands
  // padEnvExpand   the escapes and the ${OTHER} references of a value
  // padEnvCast     the words true, false, null and empty - (true) ... too - as their values
  // padEnvError    reports a fault - padError, or a thrown error while a config file is
  //                still being read and PAD's error handling does not stand yet
  //
  // The engine loads every file of lib/ before it reads any configuration (inits/inits.php
  // has lib.php second, config.php later), so a _config/config.php can call padEnv - the
  // place a password is needed first. A key starting with HTTP_ is never read from the
  // real environment: those are the request's own headers, sent by the client, and a
  // header Proxy: must not become HTTP_PROXY. The parsed files live in a static, not in a
  // global, so the secrets they hold are not in the variables a dump or an error report
  // lists.

  function padEnv ( $key, $default = NULL ) {

    if ( ! is_string ( $key ) or $key === '' ) {
      padEnvError ( 'padEnv: the key must be a name like DB_PASSWORD - not '
                  . ( $key === '' ? 'an empty string' : get_debug_type ( $key ) ) );
      return ( $default instanceof Closure ) ? $default () : $default;
    }

    $real = padEnvReal ( $key );

    if ( $real !== NULL )
      return padEnvCast ( $real );

    $home = rtrim ( $GLOBALS ['padHome'] ?? dirname ( PAD ), '/' );

    foreach ( [ APP . '_config/.env', "$home/.env" ] as $file ) {

      $pairs = padEnvFile ( $file );

      if ( array_key_exists ( $key, $pairs ) )
        return $pairs [$key];

    }

    return ( $default instanceof Closure ) ? $default () : $default;

  }

  // The real environment as PHP sees it: getenv() - the process environment, and under
  // Apache what SetEnv gives - then $_ENV, then $_SERVER (where PHP-FPM's env[...] and a
  // fastcgi_param land). Only scalar values, as text; a value that is an array - $_SERVER's
  // argv - is no environment value.

  function padEnvReal ( $key ) {

    if ( strncasecmp ( $key, 'HTTP_', 5 ) == 0 )
      return NULL;

    $value = getenv ( $key );

    if ( $value !== FALSE )
      return $value;

    foreach ( [ $_ENV, $_SERVER ] as $set )
      if ( isset ( $set [$key] ) and is_scalar ( $set [$key] ) )
        return (string) $set [$key];

    return NULL;

  }

  // One file, read the first time a key is asked for and kept for the rest of the request.
  // A file that is not there has no pairs.

  function padEnvFile ( $file ) {

    static $files = [];

    if ( isset ( $files [$file] ) )
      return $files [$file];

    $files [$file] = [];

    if ( ! is_file ( $file ) or ! is_readable ( $file ) )
      return [];

    $text = file_get_contents ( $file );

    if ( $text === FALSE )
      return [];

    $home  = rtrim ( $GLOBALS ['padHome'] ?? dirname ( PAD ), '/' ) . '/';
    $shown = str_starts_with ( $file, $home ) ? substr ( $file, strlen ( $home ) ) : $file;

    return $files [$file] = padEnvParse ( $text, $shown );

  }

  // The format of the .env files other tools read too, so one file serves them all:
  //
  //   # a comment
  //   APP_NAME=Shop                 a bare value, trimmed; a # at its start or after a
  //                                 space starts a comment
  //   export DB_HOST=localhost      export in front, as a shell script writes it
  //   GREETING="Hello\nWorld"       double quotes: \n \r \t \" \\ \$ and ${OTHER}
  //   PATTERN='a ${literal} \n'     single quotes: the text exactly as written
  //   DEBUG=false                   true, false, null and empty - also (true) and the
  //                                 like, any case - are TRUE, FALSE, NULL and ''
  //
  // A quoted value may run over several lines, and is always text: "false" quoted is the
  // word, not FALSE. ${OTHER} is OTHER from the real environment, else as set earlier in
  // the same file, else empty; it works in bare values too. A later line for the same key
  // wins, as in a shell. A line that is none of these is reported with its line number -
  // never with its text, which may hold the secret.

  function padEnvParse ( $text, $file = '.env' ) {

    $pairs = [];
    $raw   = [];

    $text = str_replace ( [ "\r\n", "\r" ], "\n", (string) $text );

    if ( str_starts_with ( $text, "\xEF\xBB\xBF" ) )
      $text = substr ( $text, 3 );

    $lines = explode ( "\n", $text );
    $count = count ( $lines );

    for ( $index = 0; $index < $count; $index++ ) {

      $at   = $index + 1;
      $line = ltrim ( $lines [$index] );

      if ( trim ( $line ) === '' or $line [0] == '#' )
        continue;

      $line = preg_replace ( '/^export\s+/', '', $line );

      if ( ! preg_match ( '/^([A-Za-z_][A-Za-z0-9_.]*)\s*=(.*)$/', $line, $match ) ) {
        padEnvError ( "padEnv: line $at of $file is not a KEY=VALUE line" );
        continue;
      }

      $key   = $match [1];
      $value = ltrim ( $match [2] );
      $quote = $value [0] ?? '';

      if ( $quote != '"' and $quote != "'" ) {

        $value         = trim ( preg_replace ( '/(^|\s)#.*$/', '', $value ) );
        $raw   [$key]  = padEnvExpand ( $value, $raw, FALSE );
        $pairs [$key]  = padEnvCast ( $raw [$key] );

        continue;

      }

      $body = substr ( $value, 1 );

      while ( ( $end = padEnvQuote ( $body, $quote ) ) === FALSE ) {

        if ( $index + 1 >= $count ) {
          padEnvError ( "padEnv: the quoted value of $key on line $at of $file is never closed" );
          continue 2;
        }

        $body .= "\n" . $lines [++$index];

      }

      $after = trim ( substr ( $body, $end + 1 ) );

      if ( $after !== '' and $after [0] != '#' ) {
        padEnvError ( "padEnv: the value of $key on line $at of $file has text after its closing quote" );
        continue;
      }

      $value         = substr ( $body, 0, $end );
      $raw   [$key]  = ( $quote == '"' ) ? padEnvExpand ( $value, $raw, TRUE ) : $value;
      $pairs [$key]  = $raw [$key];

    }

    return $pairs;

  }

  // The position of the quote that closes a value, FALSE when this text does not hold it
  // yet. In double quotes a backslash escapes the character after it, \" among them.

  function padEnvQuote ( $body, $quote ) {

    if ( $quote == "'" ) {
      $end = strpos ( $body, "'" );
      return ( $end === FALSE ) ? FALSE : $end;
    }

    preg_match ( '/^(?:[^"\\\\]|\\\\.)*/s', $body, $match );

    $end = strlen ( $match [0] );

    return ( $end < strlen ( $body ) and $body [$end] == '"' ) ? $end : FALSE;

  }

  // One pass over the value, so an escaped \$ stays a dollar and never starts a reference.
  // An escape that is not one of the six keeps its backslash.

  function padEnvExpand ( $value, $raw, $escapes ) {

    $pattern = $escapes ? '/\\\\(.)|\$\{([A-Za-z_][A-Za-z0-9_.]*)\}/s'
                        : '/()\$\{([A-Za-z_][A-Za-z0-9_.]*)\}/';

    return preg_replace_callback ( $pattern, function ( $match ) use ( $raw ) {

      if ( ( $match [2] ?? '' ) !== '' )
        return padEnvReal ( $match [2] ) ?? $raw [ $match [2] ] ?? '';

      return match ( $match [1] ) {
        'n'     => "\n",
        'r'     => "\r",
        't'     => "\t",
        '"'     => '"',
        '\\'    => '\\',
        '$'     => '$',
        default => '\\' . $match [1]
      };

    }, $value );

  }

  // The words every .env reader knows. An explicit null is NULL - padEnv then answers
  // NULL, not the default: the key is there, and says so.

  function padEnvCast ( $value ) {

    return match ( strtolower ( $value ) ) {
      'true',  '(true)'  => TRUE,
      'false', '(false)' => FALSE,
      'null',  '(null)'  => NULL,
      'empty', '(empty)' => '',
      default            => $value
    };

  }

  // padError needs the error action, which inits/error.php sets up after the configuration
  // has been read - and a config file is where padEnv is used first. Until then the fault
  // is thrown, as inits/configCheck.php throws a configuration typo, and the boot handlers
  // that stand from the first line of the request report it.

  function padEnvError ( $message ) {

    if ( ! function_exists ( 'padErrorGo' ) )
      throw new \ErrorException ( "PAD: $message" );

    padError ( $message );

  }

?>
