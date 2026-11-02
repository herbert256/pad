<?php
  $type = include __DIR__ . '/js.php';
  $type ['aliases'] = [ 'golang' ];
  $type ['rules'] ['kwd'] = '\b(?:break|case|chan|const|continue|default|defer|else|fallthrough|for|func|go|goto|if|import|interface|map|package|range|return|select|struct|switch|type|var)\b';
  $type ['rules'] ['lit'] = '\b(?:true|false|nil|iota)\b';
  return $type;
?>
