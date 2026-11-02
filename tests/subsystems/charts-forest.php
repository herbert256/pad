<?php
passthru('node '.escapeshellarg(__DIR__.'/_charts-process-parity.cjs').' forest',$status);exit($status);
