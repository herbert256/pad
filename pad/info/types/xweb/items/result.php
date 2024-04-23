<?php

  if     ( $padTagOrg === NULL        ) padXweb ( 'result', 'null'     );
  elseif ( $padTagOrg === FALSE       ) padXweb ( 'result', 'false'    );
  elseif ( $padTagOrg === TRUE        ) padXweb ( 'result', 'true'     );
  elseif ( $padTagOrg === INF         ) padXweb ( 'result', 'inf'      );
  elseif ( $padTagOrg === NAN         ) padXweb ( 'result', 'nan'      );
  elseif ( is_array    ( $padTagOrg ) ) padXweb ( 'result', 'array'    );
  elseif ( is_object   ( $padTagOrg ) ) padXweb ( 'result', 'object'   );
  elseif ( is_resource ( $padTagOrg ) ) padXweb ( 'result', 'resource' );
  else                                  padXweb ( 'result', 'string'   );

?>