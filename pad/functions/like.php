<?php
  
  $escape = '\\';

  $expr = '/((?:'.preg_quote($escape, '/').')?(?:'.preg_quote($escape, '/').'|%|_))/';
  $parts = preg_split($expr, $parm, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

  $expr = '/^';
  $lastWasPercent = FALSE;
  
  foreach ($parts as $padart) {
    
      switch ($padart) {
          case $escape.$escape:
              $expr .= preg_quote($escape, '/');
              break;
          case $escape.'%':
              $expr .= '%';
              break;
          case $escape.'_':
              $expr .= '_';
              break;
          case '%':
              if (!$lastWasPercent)
                  $expr .= '.*?';
              break;
          case '_':
              $expr .= '.';
              break;
          default:
              $expr .= preg_quote($padart, '/');
              break;
      }
      
      $lastWasPercent = $padart == '%';
  }
  
  $expr .= '$/i';

  return preg_match($expr, $value) ? '1' : '';

?>