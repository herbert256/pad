<?php

  if     ( count ($names) == 0 ) return INF;
  elseif ( count ($names) == 1 ) return include pad . 'var/any/one.php';
  else                           return include pad . 'var/any/names.php';

?>