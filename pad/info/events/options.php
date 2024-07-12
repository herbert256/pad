<?php

  if ( padTrace )
    if ( $padTraceOptions )
      padTrace ( 'option', $padOptionName, "type ==> $padOptions" );

  if ( padXapp )
    include pad . 'info/types/xapp/events/options.php'; 

?>