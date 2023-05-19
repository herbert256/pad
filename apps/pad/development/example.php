<?php

  $example = $example ?? 'hello/index';

  $file = padApp . $example . 'html';

  $html = ( padExists( $file ) ) ? file_get_contents( $file ) : ''; 

  if ( strpos($html, '{demo') !== false or str_ends_with($example, 'index'))  
    $onlyResult = ',onlyResult';
  else
    $onlyResult = '';

  if ( substr($example, 0, 1) == '_' ) 
    $skipResult = ',skipResult';
  else
    $skipResult = '';
    
?>