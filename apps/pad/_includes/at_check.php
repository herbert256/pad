<?php

  if ( padAtValue ( 'bestaat@niet'           ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaat@*'              ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaat.niet@*'         ) !== INF ) padError ( "oops");

  if ( padAtValue ( 'bestaatniet@any'        ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@current'    ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@function'   ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@level'      ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@options'    ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@parameters' ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@saved'      ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@tables'     ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@variables'  ) !== INF ) padError ( "oops");

  if ( padAtValue ( 'bestaatniet@all'        ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@data'       ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@globals'    ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@sequences'  ) !== INF ) padError ( "oops");
  if ( padAtValue ( 'bestaatniet@tags'       ) !== INF ) padError ( "oops");

  echo 'done';

?>