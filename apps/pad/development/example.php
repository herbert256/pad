<?php

  {$exampleTitle} = $item;

  $source = padApp . "$item.html";    

  $html = ( padExists ( $source) ) ? file_get_contents ( $source ) : '';

  if ( strpos($html, '{demo') !== false )  
    $onlyResult = ',onlyResult';
  else
    $onlyResult = '';

  if ( strpos($file, '/_') !== FALSE ) 
    $skipResult = ',skipResult';
  else
    $skipResult = '';

?>