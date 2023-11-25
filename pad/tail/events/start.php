<?php

  include_once pad . 'tail/lib/tail.php';

  $padTailId    = hrtime (true);
  $padTailDir   = "tail/$padPage/$padTailId";

  $padTailCnt   = 0;
  $padTraceLine = 0;
  $padXmlId     = 0;
  $padXrefId    = 0;

                     include pad . 'tail/types/tail/start.php';
  if ( $padRequest ) include pad . 'tail/types/request/start.php';
  if ( $padTrace   ) include pad . 'tail/types/trace/entry/config.php';
  if ( $padTrack   ) include pad . 'tail/types/track/start.php';
  if ( padXml      ) include pad . 'tail/types/xml/start.php';
  if ( padXref     ) include pad . 'tail/types/xref/start.php';

  if ( padTailExists ( padApp . 'config/tail.php' ) )
    include padApp . 'config/tail.php';
  
?>