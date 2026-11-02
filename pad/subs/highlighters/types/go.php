<?php
  $type = include __DIR__ . '/js.php';
  $type ['aliases'] = [ 'golang' ];
  $type ['rules'] ['kwd'] = '\b(?:break|case|chan|const|continue|default|defer|else|fallthrough|for|func|go|goto|if|import|interface|map|package|range|return|select|struct|switch|type|var)\b';
  $type ['rules'] ['lit'] = '\b(?:true|false|nil|iota)\b';
  $type ['rules'] ['str'] = <<<'REGEX'
"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|(?:\\)?\z|(?=[\r\n]))|`[^`]*+(?:`|\z)|'(?:\\(?:[abfnrtv\\'"]|x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8}|[0-7]{3})|[\xC2-\xF4][\x80-\xBF]{1,3}|[^'\\\r\n])'
REGEX;
  return $type;
?>
