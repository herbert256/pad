<?php
// Lexical colouring only; strings/comments consume whole tokens before keywords.
return ['aliases'=>['kt', 'kts'],'rules'=>[
'com' => <<<'RX'
\/\/[^\r\n]*|\/\*(?:[^*]|\*(?!\/))*+(?:\*\/|\z)
RX,
'str' => <<<'RX'
"""(?:(?!""")[\s\S])*+(?:"""|\z)|"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|\z)|\x27[^\x27\\\r\n]*+(?:\\.[^\x27\\\r\n]*+)*+(?:\x27|\z)
RX,
'kwd' => <<<'RX'
\b(?:as|break|class|continue|do|else|false|for|fun|if|in|interface|is|null|object|package|return|super|this|throw|true|try|typealias|typeof|val|var|when|while|by|catch|constructor|delegate|dynamic|field|file|finally|get|import|init|param|property|receiver|set|setparam|where|actual|abstract|annotation|companion|const|crossinline|data|enum|expect|external|final|infix|inline|inner|internal|lateinit|noinline|open|operator|out|override|private|protected|public|reified|sealed|suspend|tailrec|vararg)\b
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
