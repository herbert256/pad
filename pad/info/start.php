<?php
 
  include_once pad . 'info/lib.php';

  $padInfoId  = hrtime (true);
  $padTraceId = 0;
  $padXmlId   = 0;
  $padXrefId  = 0;

  if ( isset ( $_REQUEST['padInclude'] ) ) 
    $padInfoDir = "pages/$padStartPage/include/$padInfoId";
  else                                     
    $padInfoDir = "pages/$padStartPage/complete/$padInfoId";

  if ( $padStats   ) include pad . 'info/types/stats/start.php';
  if ( $padRequest ) include pad . 'info/types/request/start.php';
  if ( padTrace    ) include pad . 'info/types/trace/start.php';
  if ( $padTrack   ) include pad . 'info/types/track/start.php';
  if ( padXml      ) include pad . 'info/types/xml/start.php';
  if ( padXref     ) include pad . 'info/types/xref/start.php';
  
?>