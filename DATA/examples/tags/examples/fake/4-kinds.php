<?php

  $kinds = array_map ( fn ( $kind ) => [ 'kind' => $kind ], array_values ( array_diff ( padKinds ( 'fakes' ), [ 'date' ] ) ) );

?>
