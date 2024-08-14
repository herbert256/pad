<?php
  
  $GLOBALS ['catch'] .= '-C';

  echo $GLOBALS ['catch'] ;
  echo '<br>' ;    
  echo padErrorGet ( $padCatchException );

  exit;

  padDump ( $padCatchText ); 


?>