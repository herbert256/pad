<?php
  
  $padPagTyp = padTagParm ( 'type', 'get' );
   
  return include pad . "page/$padPagTyp.php";
   
?>