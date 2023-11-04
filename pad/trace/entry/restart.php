<?php

  include pad . "trace/exit/$padTraceType.php";
  include pad . 'trace/store/start.php';

  $padTraceType  = 'restart'; 
  $padTraceStart = $pad;
 
  include pad . 'trace/trace/start.php';

?>