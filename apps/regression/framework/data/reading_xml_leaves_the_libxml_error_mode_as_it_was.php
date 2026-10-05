<?php

  $before = libxml_use_internal_errors ( FALSE ) ? 'on' : 'off';
  $rows   = padData ( '<data><row a="1"/></data>', 'xml' );
  $after  = libxml_use_internal_errors ( FALSE ) ? 'on' : 'off';

?>
