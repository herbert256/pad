<?php

  if     ( $padTagOrg === NULL        ) padXref ( 'result', 'null'     );
  elseif ( $padTagOrg === FALSE       ) padXref ( 'result', 'false'    );
  elseif ( $padTagOrg === TRUE        ) padXref ( 'result', 'true'     );
  elseif ( $padTagOrg === INF         ) padXref ( 'result', 'inf'      );
  elseif ( $padTagOrg === NAN         ) padXref ( 'result', 'nan'      );
  elseif ( is_array    ( $padTagOrg ) ) padXref ( 'result', 'array'    );
  elseif ( is_object   ( $padTagOrg ) ) padXref ( 'result', 'object'   );
  elseif ( is_resource ( $padTagOrg ) ) padXref ( 'result', 'resource' );
  else                                  padXref ( 'result', 'string'   );

?>