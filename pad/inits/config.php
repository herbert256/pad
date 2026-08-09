<?php

  // Assembles this request's configuration, lowest precedence first: the framework defaults
  // in config/, then the application's own _config/config.php, then the shared _common
  // application (skipped when the application switched $padCommon off), then the $padInfo
  // debug selectors.
  //
  // The application config is included twice on purpose. The first pass lets it choose
  // $padCommon and the debug and output settings; the output selector
  // config/output/$padOutputType.php is then applied, and the second pass gives the
  // application the last word over whatever _common or that selector changed. On the
  // command line a 'web' output type is silently turned into 'console' in between.
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

  if ( $padCommon )
    include COMMON . '_config/config.php';

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include PAD . "config/info/$padInfoType.php";
  }

  if ( php_sapi_name() == 'cli' and $padOutputType == 'web' )
    $padOutputType = 'console';

  include PAD . "config/output/$padOutputType.php";

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) )
    include_once PAD . 'inits/configSet.php';

  if ( $padErrorTry )
    include PAD . 'config/try.php';

  // What this application chose for itself: each value that differs from the framework's
  // default, keyed by the reference family that lists it. The cli web-to-console turn above
  // counts - it is what that request really ran under. The xref recorder writes these to
  // DATA/reference/config/ when a padReference crawl asks.

  $padConfigSet = [];

  if ( $padErrorAction != $padConfigDefault ['error']      ) $padConfigSet ['error']      = $padErrorAction;
  if ( $padOutputType  != $padConfigDefault ['outputType'] ) $padConfigSet ['outputType'] = $padOutputType;
  if ( $padCache       != $padConfigDefault ['cache'] and is_string ( $padCache ) )
                                                             $padConfigSet ['cache']      = $padCache;
  if ( $padInfo        != $padConfigDefault ['info'] and $padInfo )
                                                             $padConfigSet ['info']       = $padInfo;

?>