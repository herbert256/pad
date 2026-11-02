<?php
  // jsonc - JSON with line/block comments and optional trailing commas; duplicate keys use JSON's last-value rule.
  return [ 'name'=>'JSON with comments', 'mime'=>'application/jsonc', 'extension'=>['jsonc'], 'shape'=>'data',
    'lib'=>'jsonc', 'raw'=>'exact', 'read'=>fn($text,$options)=>padJsoncRead($text,$options),
    'write'=>fn($data,$options)=>json_encode($data,JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ];
?>
