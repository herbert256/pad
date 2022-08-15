<?php

// PHP implementation of
// Juggler Sequence

// function prints the
// juggler Sequence

function pSeq_juggler ($n) {


	$GLOBALS ['padSeq_juggler_array'] [] = $n;

	$a = $n;

	// print the first term

	// calculate terms until
	// last term is not 1
	while ($a != 1)
	{
		$b = 0;

		// Check if previous
		// term is even or odd
		if ($a % 2 == 0)

			// calculate next term
			$b = floor(sqrt($a));

		else

			// for odd previous term
			// calculate next term
			$b = floor(sqrt($a) * sqrt($a) *
								sqrt($a));

		echo($b . " ");
		$a = $b;
	}
}

?>
