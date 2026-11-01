<?php

  // padPalette takes the name of a palette in any case and with spaces around it, as
  // {palette} and palette= of {chart} do: romaO in Crameri's own spelling, ' Viridis '.

  $romao   = implode ( ' ', padPalette ( 'romaO', 1 ) );
  $viridis = implode ( ' ', padPalette ( ' Viridis ', 3 ) );
  $same    = ( padPalette ( 'ROMAO' ) === padPalette ( 'romao' ) ) ? 'the same twelve' : 'other colours';

?>
