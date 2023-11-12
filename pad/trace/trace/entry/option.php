<?php

  include pad . 'trace/trace/store/start.php';

  $padTraceType = 'option'; 
  $padTraceGo   = $pad; 

  include pad . 'trace/trace/trace/start.php';

  if ( padTagParm ('trace') !== TRUE )
    $padTraceMaxLevel = $pad + ( padTagParm ('trace') - 1 );

  include pad . 'trace/trace/level/start.php';
  include pad . 'trace/trace/items/parms.php';
  include pad . 'trace/trace/items/true.php'; 
  include pad . 'trace/trace/items/flags.php';
  include pad . 'trace/trace/items/base.php';  
  include pad . 'trace/trace/items/data.php';      
  include pad . 'trace/trace/items/false.php';   

?>