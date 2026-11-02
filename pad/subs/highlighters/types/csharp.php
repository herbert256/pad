<?php
// Lexical colouring only; strings/comments consume whole tokens before keywords.
return ['aliases'=>['cs', 'c#'],'rules'=>[
'com' => <<<'RX'
\/\/[^\r\n]*|\/\*(?:[^*]|\*(?!\/))*+(?:\*\/|\z)
RX,
'str' => <<<'RX'
@"(?:[^"]|"")*+(?:"|\z)|"""(?:(?!""")[\s\S])*+(?:"""|\z)|"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|\z)|\x27[^\x27\\\r\n]*+(?:\\.[^\x27\\\r\n]*+)*+(?:\x27|\z)
RX,
'kwd' => <<<'RX'
\b(?:abstract|as|base|bool|break|byte|case|catch|char|checked|class|const|continue|decimal|default|delegate|do|double|else|enum|event|explicit|extern|false|finally|fixed|float|for|foreach|goto|if|implicit|in|int|interface|internal|is|lock|long|namespace|new|null|object|operator|out|override|params|private|protected|public|readonly|ref|return|sbyte|sealed|short|sizeof|stackalloc|static|string|struct|switch|this|throw|true|try|typeof|uint|ulong|unchecked|unsafe|ushort|using|virtual|void|volatile|while|async|await|record|required|init|var)\b
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
