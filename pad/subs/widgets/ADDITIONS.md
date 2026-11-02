# Additional display components

`widget:empty` takes a title, explanatory text and optional safe link/action. `widget:stat` accepts a displayed value, unit, a caller-supplied baseline, numeric delta and detail; it never infers a trend. `widget:errorsummary` consumes the existing `$padFormErrors` by default or an explicit `errors=` map. `fields=` maps error keys to actual control IDs; applications should supply this mapping when names and IDs differ. It does not run validation or move focus. Invalid IDs produce plain text messages.

`section:status` takes name/status/text/link records; status is operational, degraded, outage, maintenance or unknown. It states status in text and does not poll. `section:speakers` accepts name, role, topic, time, biography text, image and link. Times are caller-formatted so their event timezone is explicit. Both use normal section heading levels and escaped fields/safe links.

TOML, INI and Dockerfile highlight definitions use the shared escaping/token engine. They provide lexical presentation, not parser validation. INI also recognizes `properties`; Dockerfile recognizes `docker`.

Effects retain their normal control actions and CSS reduced-motion fallbacks. Confetti/decipher stop active frames when their trigger is removed or reduced motion becomes active. Removed fragments are unobserved and may be initialized again on reinsertion. Flipnumber cancels active Web Animations when reduced motion is enabled.
