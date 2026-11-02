<?php
passthru('node '.escapeshellarg(__DIR__.'/_charts-process-parity.cjs').' control',$status);exit($status);
