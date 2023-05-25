<?php
  
  $item = padPageGetName ($item);

  $go = go ( $item );

  if ( $go )
    padRedirect ( "reference/show&item=$go" );
  
  $onlyResult = onlyResult ( padApp . $item . '.html' );

?>