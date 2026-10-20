<?php

  // A store named beside the store the tag pulls is the parameter of an action that takes
  // stores, as a list there is (parm/quick.php): the first one written as a bare option, whose
  // value it becomes - {pull:a intersection, 'b'} is {pull:a intersection='b'}. The tag's own
  // action takes it before, parm/kind.php. It was dropped without a word, the intersection made
  // with nothing, and then named as a second store nothing reads.

  if ( $pqPull and is_string ( $pqFindParm ) and isset ( $pqStore [$pqFindParm] ) )

    foreach ( $padParms [$pad] as $pqFindIdx => $pqFindOne )

      if ( $pqFindOne ['padPrmKind'] == 'option' and $pqFindOne ['padPrmValue'] === TRUE
           and pqAction ( $pqFindOne ['padPrmName'] )
           and ( padFileExists ( PQ . 'actions/double/' . $pqFindOne ['padPrmName'] )
                 or padFileExists ( PQ . 'actions/merge/' . $pqFindOne ['padPrmName'] ) ) ) {

        $padParms [$pad] [$pqFindIdx] ['padPrmValue'] = $pqFindParm;

        $pqFindParm = '';

        break;

      }

?>
