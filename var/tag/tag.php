<?php

  $padIdx = 0;

  if ( $name )
    for ( $i=$pad; $i; $i-- )
      if ( $padName [$i] == $name ) {
        $padIdx = $i;
        break;
      }
  
  global $padTag, $padType;

  if ( ! $padIdx )
    for ($i=$pad; $i; $i--) 
      if ( $padTag [$i] <> 'if' and $padTag [$i] <> 'case' and $padType[$i] <> 'tag' ) {
        $padIdx = $i;
        break;
      }

  list ( $tag, $parm ) = padSplit ( ':', $names[0] );

  return include pad . "tag/$tag.php";

?>