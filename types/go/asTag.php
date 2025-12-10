<?php

  if ( padStartAndClose ('end') )
    return TRUE;
  
  return padAsTag ( $padAsTag, $padContent, $padOpt[$pad] );

?>