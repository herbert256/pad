<?php

  // Notifications: one message to a person - an order shipped, a comment answered - written
  // once as a template and sent on the channels asked for: mail, a list on the site, a push
  // to the browser, Slack, Discord, Teams, Telegram, ntfy, Matrix, a text message. The
  // channels are the files of subs/channels/types/ (padKinds): a channel is added by adding
  // its file. What Laravel's notifications give, as plain functions:
  //
  //   padNotify ( $user, 'order-shipped', [ 'order' => 42 ], [ 'mail', 'database', 'slack' ] );
  //   padNotify ( $user, 'order-shipped', [ 'order' => 42 ], [ 'mail' ], [ 'queue' => TRUE ] );
  //
  //   _notifications/order-shipped.pad       the message - a section per channel or the
  //                                          plain text for all of them:
  //     Order {$order} has shipped.
  //     @title@
  //     Your order {$order} is on its way
  //     @slack@
  //     :package: Order *{$order}* shipped
  //
  // padNotify              renders the message for every channel and sends it - now, or as
  //                        one job per channel through padQueue with [ 'queue' => TRUE ] (or
  //                        a queue's name), the job id answered then; TRUE when every channel
  //                        delivered, else FALSE
  // padNotifyResults       what each channel of the last padNotify did: [ channel => [ ok,
  //                        error, gone ] ] - gone: the push subscriptions the push service
  //                        said are no more, for the application to forget
  // padNotifications       the notifications the database channel kept for a recipient - the
  //                        user logged in by default - newest first, the unread ones, or all
  // padNotificationRead    marks one of them read by its id, or 'all' of them
  // padWebPushKeys         a new VAPID key pair for the webpush channel - the two values to put
  //                        in .env as VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY
  // padNotifyChannels      the channels there are
  // padNotifyQueued        a queued notification, sent by the worker (lib/queue.php, @notify)
  //
  // The recipient ($to) is an array - a user row: id, email, phone, and a field per channel
  // naming the person there (telegram - a chat id, ntfy - a topic, matrix - a room id,
  // webpush - the browser's subscriptions) - or an e-mail address alone. A channel's settings
  // come from the options ([ 'slack' => [ 'url' => ... ] ]), then from that row, then from
  // padEnv - SLACK_WEBHOOK_URL in the application's .env. An address a request goes to - the
  // webhook of Slack, Discord or Teams, a homeserver, a gateway - is never the row's, only the
  // options' or padEnv's, and the row's fields are checked: a chat, topic or room that is
  // none, a push endpoint on no browser's push service, is not sent to. A setting that is
  // missing everywhere is an error under the strict check naming the variable to set; a
  // channel that fails to deliver is no author's error: padNotify answers FALSE,
  // padNotifyResults says why, and the failure is logged (padLog).
  // The code is in subs/channels/, read the first time one of these runs.

  function padNotify ( $to, $name, $data = [], $channels = NULL, $options = [] ) {

    include_once PAD . 'subs/channels/_lib/notify.php';

    return padNotifyGo ( $to, $name, $data, $channels, $options );

  }

  function padNotifyResults () {

    return padNotifyStore ( 'results' ) ?? [];

  }

  function padNotifications ( $to = NULL, $all = FALSE ) {

    include_once PAD . 'subs/channels/_lib/store.php';

    return padNotifyStoreList ( $to, $all );

  }

  function padNotificationRead ( $id, $to = NULL ) {

    include_once PAD . 'subs/channels/_lib/store.php';

    return padNotifyStoreRead ( $id, $to );

  }

  function padWebPushKeys () {

    include_once PAD . 'subs/channels/_lib/webpush.php';

    return padWebPushNewKeys ();

  }

  function padNotifyChannels () {

    return padKinds ( 'channels' );

  }

  function padNotifyQueued ( $data ) {

    include_once PAD . 'subs/channels/_lib/notify.php';

    return padNotifyWork ( $data );

  }

  function padNotifyStore ( $key, $value = NULL ) {

    static $store = [];

    if ( func_num_args () > 1 )
      $store [$key] = $value;

    return $store [$key] ?? NULL;

  }

?>
