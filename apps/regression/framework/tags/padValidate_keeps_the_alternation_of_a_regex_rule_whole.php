<?php

  $alt = json_encode ( [ padValidate ( [ 'size' => 'required|regex:/^(S|M|L)$/' ], [ 'size' => 'M' ] ),
                         padValidate ( [ 'size' => 'required|regex:/^(S|M|L)$/i|max:1' ], [ 'size' => 'x' ] ),
                         padValidate ( [ 'size' => 'regex:#^a|b$#|max:1' ], [ 'size' => 'b' ] ) ] );

?>
