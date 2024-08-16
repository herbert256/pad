<?php

  $padLvlFunVar [$pad] = [];

  foreach ( get_defined_vars () as $padStrKey => $padStrVal )
    if ( ! isset ( $GLOBALS [$padStrKey] ) )
      if ( padValidStore ( $padStrKey ) )
        if ( ! isset ( $padCurrent [$pad] [$padStrKey] ) )
          if ( ! isset ( $padPrm [$pad] [$padStrKey] ) )
            if ( ! isset ( $padOpt [$pad] [$padStrKey] ) )
              if ( ! isset ( $padSetLvl [$pad] [$padStrKey] ) )
                if ( ! isset ( $padTablel [$pad] [$padStrKey] ) )
                  $padLvlFunVar [$pad] [$padStrKey] = $padStrVal;

?>