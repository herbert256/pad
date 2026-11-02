<?php

  return array (
  'aliases' => 
  array (
    0 => 'xml',
    1 => 'svg',
    2 => 'htm',
  ),
  'rules' => 
  array (
    'com' => '<!--.*?-->',
    'kwd' => '<!DOCTYPE[^>]*>|<\\?xml.*?\\?>',
    'tag' => '<\\/?[A-Za-z][\\w:.-]*|\\/?>',
    'att' => '(?<=\\s)[A-Za-z_:][\\w:.-]*(?=\\s*=)',
    'str' => '"[^"]*"|\'[^\']*\'',
    'lit' => '&[#\\w]+;',
  ),
);

?>
