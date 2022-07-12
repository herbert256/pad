<?php
// PHP program for n-th element
// of Newman-Conway Sequence

// Recursive Function to
// find the n-th element
function pad_sequence_newmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( isset ( $GLOBALS ['pad_seq_cache'] [$n] ) )
		return $GLOBALS ['pad_seq_cache'] [$n];

	$now = pad_sequence_newmanConway ( pad_sequence_newmanConway ($n - 1)     )
	       +
		   pad_sequence_newmanConway ( $n - pad_sequence_newmanConway($n - 1) );

	$GLOBALS ['pad_seq_cache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>
