<?php
  switch ( $padCallback ) {
    case 'init' : $mark = 'i';                 break;
    case 'row'  : $mark .= 'r';                break;
    case 'exit' : $mark .= 'x';                break;
  }
?>
