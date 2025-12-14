<?php

	$padBetweenOrg = $padBetween;

  if ( $padBetween and in_array ( $padBetween[0], ['$','!','#','&','?','@'] ) )
  	return;

	$padPipeBeforeSet = $padPipeAfterSet = '';

	list ( $padSplitBefore, $padSplitAfter) = padPipeSplit ( $padBetween );

	if ( $padSplitAfter ) {
		$padBetweenOrg    = $padBetween;
		$padBetween       = $padSplitBefore;
		$padPipeBeforeSet = $padSplitAfter;
	}

?>