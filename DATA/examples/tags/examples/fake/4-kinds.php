<?php

  $kinds = array_map ( fn ( $kind ) => [ 'kind' => $kind ], array_values ( array_diff ( padFakeKinds (), [ 'date', 'ulid', 'weighted' ] ) ) );

?>
