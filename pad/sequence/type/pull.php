  <?php

  $pad_seq_pull_list = $pad_seq_store [$pad_pull_store];

  if ( ! count($pad_seq_pull_list) )
    return;

  if ( $pad_seq_build == 'jump' and pad_file_exists ( PAD_HOME . "pad/sequence/types/$pad_tag/init.php" )) {
    $pad_sequence = $pad_seq_pull_list[0];
    include PAD_HOME . "pad/sequence/types/$pad_tag/init.php";
    foreach ( $pad_seq_prepare as $pad_k => $pad_v )
      $pad_seq_pull_list [$pad_k] = $pad_seq_prepare [$pad_k];
  }

  $pad_seq_go = $pad_seq_loop_idx;

  while ( $pad_seq_go <= count($pad_seq_pull_list) ) {

    $pad_seq_loop_idx = $pad_seq_pull_list [$pad_seq_go-1];

    if ( ! include 'go/one.php' )
        break;

    $pad_seq_go = $pad_seq_go + $pad_seq_increment;

  }

?>