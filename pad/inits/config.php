<?php

  // Assembles this request's configuration, lowest precedence first: the framework defaults
  // in config/, then the application's own _config/config.php, then the shared _common
  // application (skipped when the application switched $padCommon off), then the $padInfo
  // debug selectors. The .env files padEnv reads follow the same order: from _common's
  // configuration on, _common's .env stands between the application's and the PAD home's.
  //
  // The application config is included twice on purpose. The first pass lets it choose
  // $padCommon and the debug and output settings; the output selector
  // config/output/$padOutputType.php is then applied, and the second pass gives the
  // application the last word over whatever _common or that selector changed. On the
  // command line a 'web' output type is silently turned into 'console' in between, and
  // again after the second pass.
  //
  // Finally any settings queued in $padSetConfig (used by exits/output/file.php to hand the
  // next page a different output type) are folded in through inits/configSet.php, and the
  // try/catch settings are loaded when $padErrorTry asks for them.

  include PAD . 'config/config.php';
  include PAD . 'config/sequence.php';

  // The framework's answers for the four closed configuration sets, remembered so the end
  // of this file can see which of them the application deliberately changed - that is what
  // the reference's configuration families call an example of the value.

  $padConfigDefault = [ 'error'      => $padErrorAction,
                        'outputType' => $padOutputType,
                        'info'       => $padInfo,
                        'cache'      => $padCache      ];

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  if ( $padCommon ) {
    padEnvCommon ( TRUE );
    include COMMON . '_config/config.php';
  }

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      if ( file_exists ( PAD . "config/info/$padInfoType.php" ) )
        include PAD . "config/info/$padInfoType.php";
      else
        throw new \ErrorException ( "PAD: there is no info mode named '$padInfoType'" );
  }

  // Unless the caller queued the output type itself: pad export renders on the command line
  // what the web would get (apps/cli/_commands/render.php).

  if ( php_sapi_name() == 'cli' and $padOutputType == 'web' and ! isset ( $padSetConfig ['OutputType'] ) )
    $padOutputType = 'console';

  // A configuration word with no file behind it died on a raw missing include. Reported
  // always - a config typo is not a template mistake the lenient walk papers over. The
  // error action is put right first, so the report itself has a working action to travel
  // by; the checks run again after the second application pass, which has the last word.

  include PAD . 'inits/configCheck.php';

  include PAD . "config/output/$padOutputType.php";

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  // The second pass says 'web' again when the application names it - react and structure
  // do - and on the command line that left the web type with the console selector's
  // settings: web.php read an $padWebEtag304 nothing had set, and pad render died on it.

  if ( php_sapi_name() == 'cli' and $padOutputType == 'web' and ! isset ( $padSetConfig ['OutputType'] ) )
    $padOutputType = 'console';

  include PAD . 'inits/configCheck.php';

  // Every pass that finds a queue applies it - configSet.php empties it behind itself. It
  // was include_once: a request whose first pass already had a queue (pad lint, pad test)
  // never applied the file writer's queued web type on the restart, and the page wrote
  // itself to disk again and again until the restart limit stopped it.

  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) )
    include PAD . 'inits/configSet.php';

  if ( $padErrorTry )
    include PAD . 'config/try.php';

  // What this application chose for itself: each value that differs from the framework's
  // default, keyed by the reference family that lists it. The cli web-to-console turn above
  // counts - it is what that request really ran under. The xref recorder writes these to
  // DATA/reference/config/ when a padReference crawl asks.

  $padConfigSet = [];

  // The error action is deliberate when the value differs - or when the application's own
  // config says the word, because choosing 'pad' out loud is a choice the value cannot
  // show: it is also the default.

  $padConfigApp = file_exists ( APP . '_config/config.php' ) ? padFileGet ( APP . '_config/config.php' ) : '';

  if ( $padErrorAction != $padConfigDefault ['error']
       or str_contains ( $padConfigApp, 'padErrorAction' )   ) $padConfigSet ['error']      = $padErrorAction;
  if ( $padOutputType  != $padConfigDefault ['outputType']
       or str_contains ( $padConfigApp, 'padOutputType' )   ) $padConfigSet ['outputType'] = $padOutputType;

  // The source is read for the two words above and gone again: kept as a global it went
  // whole into every error report - the JSON channel, the dumps under DATA/ - with the
  // database password it holds in clear, two lines above padSqlPassword's "redacted".

  unset ( $padConfigApp );
  if ( $padCache and ( $padCacheServerType ?? '' )         ) $padConfigSet ['cache']      = $padCacheServerType;
  elseif ( $padCache != $padConfigDefault ['cache'] and is_string ( $padCache ) )
                                                             $padConfigSet ['cache']      = $padCache;
  // The info selector is a comma list, so the family carries every chosen mode - each is
  // an example of its own name.

  if ( $padInfo != $padConfigDefault ['info'] and $padInfo )
    $padConfigSet ['info'] = padExplode ( $padInfo, ',' );

?>
