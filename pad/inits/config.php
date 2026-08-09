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

?>