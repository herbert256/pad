<?php

  $curl = padCurl ($data);

  if ( str_starts_with ( $curl ['result'],  '2' ) )
    return padData ( $curl ['data'], $curl ['type'], $name );
  else
    return padError ( "Curl failed: " . $curl ['result'] );   

?>