<?php

  if     ( count ($names) == 0 ) return INF;
  elseif ( count ($names) == 1 ) return include pad . 'at/any/one.php';
  else                           return include pad . 'at/any/names.php';

?>