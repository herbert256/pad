<?php

  include_once pad . 'info/lib/info.php';

  $padInfoId    = hrtime (true);
  $padInfoDir   = "info/$padPage/$padInfoId";

  $padInfoCnt   = 0;
  $padTraceLine = 0;
  $padXmlId     = 0;
  $padXrefId    = 0;

  if ( $padMain    ) include pad . 'info/types/main/start.php';
  if ( $padStats   ) include pad . 'info/types/stats/start.php';
  if ( $padRequest ) include pad . 'info/types/request/start.php';
  if ( padTrace    ) include pad . 'info/types/trace/start.php';
  if ( $padTrack   ) include pad . 'info/types/track/start.php';
  if ( padXml      ) include pad . 'info/types/xml/start.php';
  if ( padXref     ) include pad . 'info/types/xref/start.php';

  if ( padInfoExists ( padApp . 'config/info.php' ) )
    include padApp . 'config/info.php';
  
?>