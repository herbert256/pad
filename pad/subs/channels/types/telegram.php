<?php

  // telegram - a message from a Telegram bot: the bot's token in TELEGRAM_BOT_TOKEN, the chat
  // in the recipient's field telegram or TELEGRAM_CHAT_ID - its id, a number (negative for a
  // group), or a channel's @name; a handle of a person is none, a bot cannot write to one. The
  // @telegram@ section, or the plain text, is sent as plain text - no parse mode, so a value is
  // never read as markup.

  return [
    'settings' => [ 'token' => [ 'env' => 'TELEGRAM_BOT_TOKEN' ],
                    'chat'  => [ 'env' => 'TELEGRAM_CHAT_ID', 'to' => 'telegram', 'pattern' => '/^(-?\d{1,20}|@[A-Za-z][A-Za-z0-9_]{3,31})$/D',
                                'is' => "Telegram chat - its id, a number, or a channel's @name" ] ],
    'send'     => function ( $message, $settings ) {
      if ( ! preg_match ( '/^\d+:[A-Za-z0-9_-]+$/D', (string) $settings ['token'] ) )
        return 'the bot token is no token like 123456:ABC-DEF';
      return padNotifyAnswer ( padNotifyHttp ( 'POST', 'https://api.telegram.org/bot' . $settings ['token'] . '/sendMessage',
                                               [ 'chat_id' => (string) $settings ['chat'], 'text' => mb_substr ( $message ['text'], 0, 4096, 'UTF-8' ) ] ), 'Telegram' );
    },
  ];

?>
