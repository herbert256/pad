<?php 

	$padPipeBeforeSet = $padPipeAfterSet = '';

	list ( $padSplitBefore, $padSplitAfter) = padPipeSplit ( $padBetween );
	
	if ( $padSplitAfter ) {
		$padBetween       = $padSplitBefore;
		$padPipeBeforeSet = $padSplitAfter;
	}

?>