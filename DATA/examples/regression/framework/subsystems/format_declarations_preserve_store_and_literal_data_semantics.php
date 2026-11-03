<?php

$templates = [
    '{format:json "nativeOne"}[{"text":"a@else@b","count":2}]{/format:json}{data:nativeOne}{$text}:{$count}{/data:nativeOne}',
    '{format:auto "nativeTwo", format="csv"}name,score
Ada,12{/format:auto}{data:nativeTwo}{$name}:{$score}{/data:nativeTwo}',
    '{format:json}[{"name":"Bea"}]{/format:json "nativeThree"}{data:nativeThree}{$name}{/data:nativeThree}',
    '{format:json "nativeFour"}[1,2]{/format:json}{format:json "nativeFour"}[3,4]{/format:json}{data:nativeFour}{$nativeFour}{/data:nativeFour}'
  ];
  $answers = array_map ( fn ( $template ) => trim ( padUnprotect ( padUnescape ( padCode ( $template ) ) ) ), $templates );
  $answer = implode ( '; ', $answers );

?>
