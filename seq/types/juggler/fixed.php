<?php

	include_once 'include.php';

	$padSeqJugglerArray = [];

	if ( isset($padSeqJuggler) and $padSeqJuggler and is_numeric($padSeqJuggler) )

	  padSeqJuggler ($padSeqJuggler);

	elseif ( $padSeqParm and is_numeric($padSeqParm) )

	  padSeqJuggler ($padSeqParm);

	elseif ( count ($padSeqFor) )
 
 	  padSeqJuggler ( reset($padSeqParm) );

 	else 

 	  padSeqJuggler ( $padSeqStart );

	return $padSeqJugglerArray;

?>