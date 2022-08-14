<?php

  $pFldCnt++;

  $pPipe    = strpos ( $pBetween, '|' );
  $pSpace   = strpos ( $pBetween, ' ' );

  if ($pPipe and (!$pSpace or $pPipe < $pSpace) ) {
    
    $pFld  = rtrim(substr($pBetween, 1, $pPipe-1));
    $pExpl = pExplode(substr($pBetween, $pPipe+1), '|');

  } elseif ($pSpace and (!$pPipe or $pSpace < $pPipe) ) {

    $pFld  = rtrim(substr($pBetween, 1, $pSpace-1));
    $pExpl = pExplode(substr($pBetween, $pSpace+1), '|');

  } else {

    $pFld  = rtrim(substr($pBetween, 1));
    $pExpl = [];

  }
  
  if ( substr($pFld, 0, 1) == '$' )
    $pFld = pField_value ($pFld);

  $pVal = pField_value ($pFld);

  $pVal_base = $pVal;

  if ( ! pField_check ( $pFld ) ) 
      pError ( "Field '$pFld' not found" )

?>