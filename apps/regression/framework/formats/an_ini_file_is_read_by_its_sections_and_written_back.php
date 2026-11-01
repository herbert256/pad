<?php

  // INI: the keys before any section are fields of the whole, a [section] a record of its
  // keys; a ; or # after a blank ends an unquoted value, a quoted one keeps it and has its
  // escapes, a single-quoted one is as written; key[] adds to a list, key[name] sets a field.
  // Written, a value that would not read back as written is quoted, and read again the text
  // is the value it was written from.

  $iniRead = padFormatRead ( 'ini', <<<'INI'
; settings
title = My app ; a comment
debug = true

[database]
host = localhost
port = 3306
name = "my \"app\" ; not a comment"
path = 'C:\temp'
tags[] = a
tags[] = b
colors[dark] = #000
INI );

  $iniText = padFormatWrite ( 'ini', $iniRead );

  $iniJson = json_encode ( $iniRead, JSON_UNESCAPED_SLASHES );
  $iniShow = str_replace ( "\n", ' | ', trim ( $iniText ) );
  $iniSame = ( padFormatRead ( 'ini', $iniText ) === $iniRead ) ? 'the same' : 'another value';

?>
