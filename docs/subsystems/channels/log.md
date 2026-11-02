# Local diagnostic notification channel

Select `log` through the ordinary `padNotify` channel options. Delivery uses the existing `padLog` destination and permissions. The default record contains the channel, notification name, recipient identity and message ID, each text field bounded to 128 bytes. Arbitrary message data, credentials and the message body are not copied. `body=1` explicitly includes at most 4096 bytes of the text body. The normal logger escapes control characters and serializes context. A write failure is returned as a delivery error, so retry/result handling stays shared.

Use synthetic notification content for local previews. This is diagnostic delivery, not an immutable audit ledger. `php tests/subsystems/channel-log.php` checks the default payload, explicit body setting and write-failure propagation.
