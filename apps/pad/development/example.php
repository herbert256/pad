<?php

  $example = $example ?? 'hello/index';

  $file = padApp . $example . '.html';

  $onlyResult = onlyResult ($file);

  if ( substr($example, 0, 1) == '_' ) 
    $skipResult = ',skipResult';
  else
    $skipResult = '';
    
?>