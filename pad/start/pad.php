<?php

  include_once pad . 'start/_lib.php';

  include pad . 'start/start/start.php';
  include pad . 'inits/level.php'; 
  include pad . "start/$padStrBld.php"; 
  include pad . 'start/level.php'; 
  include pad . 'start/end/end.php';
  
  return padEscape ( $padPad [$pad+1] );

?>