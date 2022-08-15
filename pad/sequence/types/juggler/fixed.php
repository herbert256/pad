<?php

	include_once 'include.php';

	$padSeqJugglerArray = [];

	if ( isset($padSeqJuggler) and $padSeqJuggler and is_numeric($padSeqJuggler) )

	  padSeq_juggler ($padSeqJuggler);

	elseif ( $padSeqParm and is_numeric($padSeqParm) )

	  padSeq_juggler ($padSeqParm);

	elseif ( count ($padSeqFor) )
 
 	  padSeq_juggler ( reset($padSeqParm) );

 	else 

 	  padSeq_juggler ( $padSeqLoopStart );

	return $padSeqJugglerArray;

?>