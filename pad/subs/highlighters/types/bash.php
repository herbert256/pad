<?php

  return array (
  'aliases' => 
  array (
    0 => 'sh',
    1 => 'shell',
    2 => 'zsh',
  ),
  'rules' => 
  array (
    'com' => '(?<![^\\s])#[^\\n]*',
    'str' => '"[^"\\\\]*+(?:\\\\.[^"\\\\]*+)*+"|\'[^\']*\'',
    'var' => '\\$\\{[^}\\n]*\\}|\\$[\\w@#?$!*-]',
    'kwd' => '\\b(?:if|then|else|elif|fi|for|while|until|do|done|case|esac|in|function|return|local|export|readonly|exit|break|continue|source|echo|cd|set|unset|shift|true|false)\\b',
    'opt' => '(?<=\\s)--?[A-Za-z][\\w-]*',
    'num' => '\\b\\d+\\b',
    'pun' => '[|&;<>]+|\\$\\(|[()]',
  ),
);

?>
