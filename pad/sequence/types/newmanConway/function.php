<?php
// PHP program for n-th element
// of Newman-Conway Sequence

// Recursive Function to
// find the n-th element
function pSequence_newmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( isset ( $GLOBALS ['padSeq_cache'] [$n] ) )
		return $GLOBALS ['padSeq_cache'] [$n];

	$now = pSequence_newmanConway ( pSequence_newmanConway ($n - 1)     )
	       +
		   pSequence_newmanConway ( $n - pSequence_newmanConway($n - 1) );

	$GLOBALS ['padSeq_cache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>
