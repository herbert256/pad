<?php
passthru('node '.escapeshellarg(__DIR__.'/_charts-process-parity.cjs').' step',$status);exit($status);
