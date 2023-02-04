<?php

  if ( $padTag [$pad] == 'level')
    return padArrToHtml ( $padData[$pad-1] );
  else
    return padArrToHtml ( $padData[$pad-1] [$padKey[$pad-1]] );

?>