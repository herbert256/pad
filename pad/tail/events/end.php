<?php

  global $padTraceDir;

  if ( $GLOBALS ['padStats']   ) include pad . 'tail/types/stats/end.php';
  if ( $GLOBALS ['padTrack']   ) include pad . 'tail/types/track/end.php';
  if ( padXml                  ) include pad . 'tail/types/xml/end.php';
  if ( $GLOBALS ['padTrace']   ) include pad . 'tail/types/trace/exit/config.php';
  if ( $GLOBALS ['padRequest'] ) include pad . 'tail/types/request/exit.php';
                                 include pad . 'tail/types/tail/exit.php';

?>