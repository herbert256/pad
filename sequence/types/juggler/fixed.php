<?php

	include_once '/pad/sequence/types/juggler/include.php';

	$padSeqJugglerArray = [];

	if ( $padSeqJuggler and is_numeric($padSeqJuggler) )

	  padSeqJuggler ($padSeqJuggler);

	elseif ( $padSeqParm and is_numeric($padSeqParm) )

	  padSeqJuggler ($padSeqParm);

	elseif ( count ($padSeqFixed) )
 
 	  padSeqJuggler ( reset($padSeqParm) );

 	else 

 	  padSeqJuggler ( $padSeqStart );

	return $padSeqJugglerArray;

?>