<?php
  return [ 'aliases' => [ 'patch' ], 'rules' => [
    'key' => '^(?:diff |index |--- |\+\+\+ )[^\n]*',
    'kwd' => '^@@[^\n]*', 'str' => '^\+[^\n]*', 'com' => '^-[^\n]*', 'lit' => '^\\\\ No newline[^\n]*' ] ];
?>
