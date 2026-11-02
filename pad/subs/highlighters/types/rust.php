<?php
  $type = include __DIR__ . '/js.php';
  $type ['aliases'] = [ 'rs' ];
  $type ['rules'] ['kwd'] = '\b(?:as|async|await|break|const|continue|crate|dyn|else|enum|extern|fn|for|if|impl|in|let|loop|match|mod|move|mut|pub|ref|return|self|Self|static|struct|super|trait|type|unsafe|use|where|while)\b';
  $type ['rules'] ['lit'] = '\b(?:true|false|None|Some|Ok|Err)\b';
  return $type;
?>
