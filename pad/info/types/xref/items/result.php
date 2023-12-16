<?php

  if     ( $padTagResult === NULL        ) padXref ( 'result', 'null'     );
  elseif ( $padTagResult === FALSE       ) padXref ( 'result', 'false'    );
  elseif ( $padTagResult === TRUE        ) padXref ( 'result', 'true'     );
  elseif ( $padTagResult === INF         ) padXref ( 'result', 'inf'      );
  elseif ( $padTagResult === NAN         ) padXref ( 'result', 'nan'      );
  elseif ( is_array    ( $padTagResult ) ) padXref ( 'result', 'array'    );
  elseif ( is_object   ( $padTagResult ) ) padXref ( 'result', 'object'   );
  elseif ( is_resource ( $padTagResult ) ) padXref ( 'result', 'resource' );
  else                                     padXref ( 'result', 'string'   );

?>