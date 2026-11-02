<?php
return array (
  'aliases' => 
  array (
    0 => 'docker',
  ),
  'rules' => 
  array (
    'com' => '^[ \\t]*#[^\\n]*',
    'str' => '"(?:\\\\.|[^"\\\\\\n])*"|\\\'[^\\\'\\n]*\\\'',
    'kwd' => '^[ \\t]*(?:[Ff][Rr][Oo][Mm]|[Rr][Uu][Nn]|[Cc][Mm][Dd]|[Ll][Aa][Bb][Ee][Ll]|[Mm][Aa][Ii][Nn][Tt][Aa][Ii][Nn][Ee][Rr]|[Ee][Xx][Pp][Oo][Ss][Ee]|[Ee][Nn][Vv]|[Aa][Dd][Dd]|[Cc][Oo][Pp][Yy]|[Ee][Nn][Tt][Rr][Yy][Pp][Oo][Ii][Nn][Tt]|[Vv][Oo][Ll][Uu][Mm][Ee]|[Uu][Ss][Ee][Rr]|[Ww][Oo][Rr][Kk][Dd][Ii][Rr]|[Aa][Rr][Gg]|[Oo][Nn][Bb][Uu][Ii][Ll][Dd]|[Ss][Tt][Oo][Pp][Ss][Ii][Gg][Nn][Aa][Ll]|[Hh][Ee][Aa][Ll][Tt][Hh][Cc][Hh][Ee][Cc][Kk]|[Ss][Hh][Ee][Ll][Ll])\\b|\\b[Aa][Ss]\\b',
    'var' => '\\$\\{[^}\\n]+\\}|\\$[A-Za-z_]\\w*',
    'opt' => '--[a-z][\\w-]*(?:=[^ \\n]+)?',
    'pun' => '[\\\\\\[\\],]',
  ),
);
?>
