<?php

  if     ( $property ) padXref ( 'at', 'property', $property );
  elseif ( $kind     ) padXref ( 'at', 'kind',     $kind );
  else                 padXref ( 'at', 'name',     $name ); 

?>