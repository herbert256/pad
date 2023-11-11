<?php

  $padTagContent = '';

  ob_start();
  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";
  $padTagOb     = ob_get_clean();

  if ( $padXml ) 
    include pad . 'xml/level/go.php';

  if     ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( $padTagResult === INF         ) $padTagResult = NULL;
  elseif ( $padTagResult === NAN         ) $padTagResult = NULL;

  if ( $padTagOb ) {
    $padTagContent .= $padTagOb;
    if ( padSingleValue ( $padTagResult ) )
      $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  if ( padTagParm ('true')  ) $padTagResult = include pad . '_options/true.php';
  if ( padTagParm ('false') ) $padTagResult = include pad . '_options/false.php';
  if ( padTagParm ('null')  ) $padTagResult = include pad . '_options/null.php';
  if ( padTagParm ('array') ) $padTagResult = include pad . '_options/array.php';
  if ( padTagParm ('empty') ) $padTagResult = include pad . '_options/empty.php';

  if ( $padTagResult === NULL )
    return;

  padGetTrueFalse ( $padTagContent , $padTagTrue, $padTagFalse );

  if     ( $padTagResult === TRUE       ) $padTagTrueFalse = TRUE;
  elseif ( $padTagResult === FALSE      ) $padTagTrueFalse = FALSE;
  elseif ( ! is_array ( $padTagResult ) ) $padTagTrueFalse = TRUE;
  elseif ( count ( $padTagResult )      ) $padTagTrueFalse = TRUE;
  else                                    $padTagTrueFalse = FALSE;

  if ( $padTagContent )
    if ( $padTagTrueFalse ) $padContent      = padContent ( $padContent,      $padTagTrue  );
    else                    $padFalse [$pad] = padContent ( $padFalse [$pad], $padTagFalse );

?>