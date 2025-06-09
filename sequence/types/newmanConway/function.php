<?php
// PHP program for n-th element
// of Newman-Conway Seq

// Recursive Function to
// find the n-th element

function pqNewmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( ! isset ( $GLOBALS ['pqCache'] ) )
	  $GLOBALS ['pqCache'] = [];

	if ( isset ( $GLOBALS ['pqCache'] [$n] ) )
		return $GLOBALS ['pqCache'] [$n];

	$now = pqNewmanConway ( pqNewmanConway ($n - 1)     )
	       +
		   pqNewmanConway ( $n - pqNewmanConway($n - 1) );

	$GLOBALS ['pqCache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>