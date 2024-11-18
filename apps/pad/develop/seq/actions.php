<?php


  foreach ( glob ( APP . 'seq/actions/single/*.pad' ) as $file ) {

    $action = str_replace ( APP . 'seq/actions/single', '', $file   );
    $action = str_replace ( '.pad',                         '', $action );

    file_put_contents ( PAD . "seq/actions/single/$action", 1 );

  }


  foreach ( glob ( APP . 'seq/actions/double/*.pad' ) as $file ) {

    $action = str_replace ( APP . 'seq/actions/double/', '', $file   );
    $action = str_replace ( '.pad',                          '', $action );

    file_put_contents ( PAD . "seq/actions/double/$action", 1 );
    file_put_contents ( PAD . "seq/store/actions/$action" , 1 );

  }


?>