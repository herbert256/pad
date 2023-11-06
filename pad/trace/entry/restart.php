<?php

  include pad . "trace/exit/$padTraceType.php";
  include pad . 'trace/store/start.php';

  $padTraceType = 'restart'; 
  $padTraceGo   = $pad;
 
  include pad . 'trace/trace/start.php';

?>