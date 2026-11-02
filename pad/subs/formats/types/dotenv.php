<?php
  // dotenv - Literal configuration assignments; no variable interpolation, execution or environment mutation.
  return ['name'=>'Dotenv data','mime'=>'text/plain','accept'=>[],'extension'=>['env'],'shape'=>'data','lib'=>'dotenv','raw'=>'exact',
    'read'=>fn($text,$options)=>padDotenvRead($text,$options),'write'=>fn($data,$options)=>padDotenvWrite($data,$options)];
?>
