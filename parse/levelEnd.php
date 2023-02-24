<?php

  if ( $padParseInfo [$pad] <> 'false' and $padParseInfo [$pad] <> 'main' )
    $padParseResult [ $padParseLevel [$pad] ] [ 'result_true' ] = str_replace("\n", '', $padResult [$pad]);

  if ( $padParseFalse [$pad] ) {
    $padParseInfo [$pad+1] = 'false';
    $padParseResult [ $padParseLevel [$pad] ] ['result_false'] = str_replace("\n", '', padRetrieveContent ( $padParseFalse [$pad] ));
  }

?>