<?php
// PHP program for n-th element
// of Newman-Conway Seq

// Recursive Function to
// find the n-th element

function padSeqNewmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( ! isset ( $GLOBALS ['padSeqCache'] ) )
	  $GLOBALS ['padSeqCache'] = [];

	if ( isset ( $GLOBALS ['padSeqCache'] [$n] ) )
		return $GLOBALS ['padSeqCache'] [$n];

	$now = padSeqNewmanConway ( padSeqNewmanConway ($n - 1)     )
	       +
		   padSeqNewmanConway ( $n - padSeqNewmanConway($n - 1) );

	$GLOBALS ['padSeqCache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>