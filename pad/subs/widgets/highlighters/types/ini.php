<?php
return ['aliases'=>['properties'], 'rules'=>[
'com'=>'^[ \\t]*[;#][^\\n]*',
'str'=>'"[^"\\n]*"|\\\'[^\\\'\\n]*\\\'',
'tag'=>'^[ \\t]*\\[[^\\]\\n]+\\]',
'key'=>'^[ \\t]*[A-Za-z_][\\w.-]*(?=[ \\t]*=)',
'lit'=>'\\b(?:true|false|yes|no|on|off|null)\\b',
'num'=>'\\b\\d+(?:\\.\\d+)?\\b'
]];
?>
