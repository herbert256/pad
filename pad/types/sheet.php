<?php

  // Spreadsheet views whose cells are evaluated by the formulas subsystem.

  $padSheetKind = $padTag[$pad];
  if(!padKind('sheets',$padSheetKind)){padError('Unknown sheet view');return FALSE;}
  $padTagGo = PAD . 'subs/sheets/types/' . $padSheetKind;

  return include PAD . 'types/_go/tag.php';

?>
