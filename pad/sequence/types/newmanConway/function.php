<?php
// PHP program for n-th element
// of Newman-Conway Sequence

// Recursive Function to
// find the n-th element
function padSequence_newmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( isset ( $GLOBALS ['padSeqCache'] [$n] ) )
		return $GLOBALS ['padSeqCache'] [$n];

	$now = padSequence_newmanConway ( padSequence_newmanConway ($n - 1)     )
	       +
		   padSequence_newmanConway ( $n - padSequence_newmanConway($n - 1) );

	$GLOBALS ['padSeqCache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>
