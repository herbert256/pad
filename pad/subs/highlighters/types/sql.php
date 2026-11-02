<?php

  return array (
  'aliases' => 
  array (
  ),
  'rules' => 
  array (
    'com' => '--[^\\n]*|\\/\\*.*?\\*\\/',
    'str' => '\'[^\']*+(?:\'\'[^\']*+)*\'',
    'key' => '"[^"\\n]*"|`[^`\\n]*`',
    'kwd' => '(?i)\\b(?:select|from|where|and|or|not|in|is|null|like|between|join|left|right|inner|outer|full|cross|on|as|group|by|order|having|limit|offset|union|all|distinct|insert|into|values|update|set|delete|create|table|index|view|drop|alter|add|primary|key|foreign|references|default|unique|case|when|then|else|end|asc|desc|count|sum|avg|min|max|exists|with|returning)\\b(?-i)',
    'num' => '\\b\\d+(?:\\.\\d+)?\\b',
    'var' => '[:@?]\\w*|\\{\\$\\w+\\}',
    'pun' => '[(),;.*=<>]',
  ),
);

?>
