<?php
  $type = include __DIR__ . '/js.php';
  $type ['aliases'] = [ 'rs' ];
  $type ['rules'] ['kwd'] = '\b(?:as|async|await|break|const|continue|crate|dyn|else|enum|extern|fn|for|if|impl|in|let|loop|match|mod|move|mut|pub|ref|return|self|Self|static|struct|super|trait|type|unsafe|use|where|while)\b';
  $type ['rules'] ['lit'] = '\b(?:true|false|None|Some|Ok|Err)\b';
  $type ['rules'] ['str'] = <<<'REGEX'
(?:br|cr|r)(?<rustHashes>\#{0,255})"(?:(?!"\k<rustHashes>)[\s\S])*+(?:"\k<rustHashes>|\z)|(?:b|c)?"[^"\\]*+(?:\\.[^"\\]*+)*+(?:"|(?:\\)?\z)|b?'(?:\\(?:[nrt0\\'"]|x[0-9a-fA-F]{2}|u\{[0-9a-fA-F_]{1,7}\})|[\xC2-\xF4][\x80-\xBF]{1,3}|[^'\\\r\n])'
REGEX;
  $type ['rules'] ['var'] = "'[A-Za-z_]\\w*";
  return $type;
?>
