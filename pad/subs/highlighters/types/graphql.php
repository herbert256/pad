<?php
// Lexical colouring only; strings/comments consume whole tokens before keywords.
return ['aliases'=>['gql'],'rules'=>[
'com' => <<<'RX'
\#[^\r\n]*
RX,
'str' => <<<'RX'
"""(?:(?!""")[\s\S])*+(?:"""|\z)|"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|\z)|\x27[^\x27\\\r\n]*+(?:\\.[^\x27\\\r\n]*+)*+(?:\x27|\z)
RX,
'kwd' => <<<'RX'
\b(?:query|mutation|subscription|fragment|on|schema|scalar|type|interface|union|enum|input|extend|directive|implements|repeatable)\b
RX,
'num' => <<<'RX'
\b(?:0[xX][\da-fA-F]+|\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)\b
RX,
'lit' => <<<'RX'
\b(?:true|false|null)\b
RX,
'pun' => <<<'RX'
[{}\[\]();,:!$@]
RX,
]];
