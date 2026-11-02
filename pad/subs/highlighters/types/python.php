<?php
  $strings = <<<'REGEX'
"""[^"\\]*+(?:\\.[^"\\]*+|"(?!"")[^"\\]*+)*+(?:"""|(?:\\)?\z)|'''[^'\\]*+(?:\\.[^'\\]*+|'(?!'')[^'\\]*+)*+(?:'''|(?:\\)?\z)|"[^"\\\r\n]*+(?:\\.[^"\\\r\n]*+)*+(?:"|(?:\\)?\z|(?=[\r\n]))|'[^'\\\r\n]*+(?:\\.[^'\\\r\n]*+)*+(?:'|(?:\\)?\z|(?=[\r\n]))
REGEX;
  return [ 'aliases' => [ 'py' ], 'rules' => [
    'com' => '\#[^\n]*',
    'str' => $strings,
    'kwd' => '\b(?:and|as|assert|async|await|break|case|class|continue|def|del|elif|else|except|finally|for|from|global|if|import|in|is|lambda|match|nonlocal|not|or|pass|raise|return|try|while|with|yield)\b',
    'lit' => '\b(?:True|False|None)\b', 'num' => '\b(?:0[xX][0-9a-fA-F_]+|\d[\d_]*(?:\.\d[\d_]*)?(?:[eE][+-]?\d+)?)\b',
    'fn' => '\b[A-Za-z_]\w*(?=\s*\()', 'pun' => '[{}\[\]();,:.]' ] ];
?>
