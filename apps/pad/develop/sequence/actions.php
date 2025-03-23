<?php


  foreach ( glob ( APP . 'sequence/actions/single/*.pad' ) as $file ) {

    $action = str_replace ( APP . 'sequence/actions/single', '', $file   );
    $action = str_replace ( '.pad',                         '', $action );

    file_put_contents ( "sequence/actions/single/$action", 1 );

  }


  foreach ( glob ( APP . 'sequence/actions/double/*.pad' ) as $file ) {

    $action = str_replace ( APP . 'sequence/actions/double/', '', $file  );
    $action = str_replace ( '.pad',                          '', $action );

    file_put_contents ( "sequence/actions/double/$action", 1 );
    file_put_contents ( "sequence/store/actions/$action" , 1 );

  }


?>