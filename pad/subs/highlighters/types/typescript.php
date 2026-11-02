<?php
  $type = include __DIR__ . '/js.php';
  $type ['aliases'] = [ 'ts', 'tsx' ];
  $type ['rules'] ['kwd'] = '\b(?:abstract|any|as|asserts|boolean|declare|enum|implements|infer|interface|is|keyof|module|namespace|never|number|override|private|protected|public|readonly|require|satisfies|string|symbol|type|unknown|unique)\b|' . $type ['rules'] ['kwd'];
  $type ['rules'] ['str'] = <<<'REGEX'
"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|(?:\\)?\z|(?=[\r\n]))|'[^'\\\r\n]*+(?:\\.[^'\\\r\n]*+)*+(?:'|(?:\\)?\z|(?=[\r\n]))|`[^`\\]*+(?:\\.[^`\\]*+)*+(?:`|(?:\\)?\z)
REGEX;
  return $type;
?>
