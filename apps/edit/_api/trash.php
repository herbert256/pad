<?php

  // The trash: what is in it, putting an item back where it came from, deleting one for good.

  $id = editArg ( $body, 'id' );

  switch ( editArg ( $body, 'op', 'list' ) ) {

    case 'list':
      return editTrashList ( editStore () );

    case 'restore':
      $meta = editTrashMeta ( editStore (), $id );
      editTrashRestore ( editStore (), $id, editPath ( editRootApp ( (string) $meta ['app'], (string) $meta ['root'] ), $meta ['root'], $meta ['path'] ) );
      return [ 'app' => $meta ['app'], 'root' => $meta ['root'], 'path' => $meta ['path'] ];

    case 'drop':
      editTrashDrop ( editStore (), $id );
      return [];

  }

  editFail ( 'trash: list, restore or drop' );

?>
