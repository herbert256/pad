<?php

  include pad . 'trace/store/start.php';

  $padTraceType = 'option'; 
  $padTraceGo   = $pad; 

  include pad . 'trace/trace/start.php';

  if ( padTagParm ('trace') !== TRUE )
    $padTraceMaxLevel = $pad + ( padTagParm ('trace') - 1 );

  include pad . 'trace/level/start.php';
  include pad . 'trace/items/parms.php';
  include pad . 'trace/items/true.php'; 
  include pad . 'trace/items/flags.php';
  include pad . 'trace/items/base.php';  
  include pad . 'trace/items/data.php';      
  include pad . 'trace/items/false.php';   

?>