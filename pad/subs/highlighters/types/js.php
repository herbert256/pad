<?php

  return array (
  'aliases' => 
  array (
    0 => 'javascript',
    2 => 'json5',
    3 => 'mjs',
  ),
  'rules' => 
  array (
    'com' => '\\/\\/[^\\n]*|\\/\\*.*?\\*\\/',
    'str' => '"[^"\\\\\\n]*+(?:\\\\.[^"\\\\\\n]*+)*+"|\'[^\'\\\\\\n]*+(?:\\\\.[^\'\\\\\\n]*+)*+\'|`[^`\\\\]*+(?:\\\\.[^`\\\\]*+)*+`',
    'kwd' => '\\b(?:async|await|break|case|catch|class|const|continue|default|delete|do|else|export|extends|finally|for|from|function|if|import|in|instanceof|let|new|of|return|static|super|switch|this|throw|try|typeof|var|void|while|yield)\\b',
    'lit' => '\\b(?:true|false|null|undefined|NaN|Infinity)\\b',
    'num' => '\\b\\d+(?:\\.\\d+)?(?:[eE][+-]?\\d+)?\\b|\\b0x[0-9a-fA-F]+\\b',
    'fn' => '\\b[A-Za-z_$][\\w$]*(?=\\s*\\()',
    'pun' => '[{}\\[\\]();,.]|=>',
  ),
);

?>
