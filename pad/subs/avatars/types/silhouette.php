<?php
include_once PAD.'subs/avatars/_lib/silhouette.php';
return padAvatarSilhouette(padAvatarChoice('variant','person',['person','organisation']),padWidgetNumber('avatar','size',64),padParmText(padTagParm('label','')));
