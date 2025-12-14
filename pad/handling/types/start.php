<?php

  $padHandCount = count ( $padData [$pad] );
  $padHandStart = $padPrm [$pad] ['start'] ?? 1;
  $padHandEnd   = $padPrm [$pad] ['end']   ?? $padHandCount;
  $padHandRows  = $padPrm [$pad] ['rows']  ?? 0 ;

  if ( $padHandStart < 0 )
    $padHandStart = $padHandCount + $padHandStart + 1;

  if ( $padHandEnd < 0 )
    $padHandEnd = $padHandCount + $padHandEnd;

  if ( ! isset ( $padPrm [$pad] ['start'] ) )
    if ( isset ( $padPrm [$pad] ['rows'] ) )
      $padHandStart = $padHandEnd - $padHandRows + 1;

  padHandGo ( $padData [$pad], $padHandStart, $padHandEnd, $padHandRows );

?>