# silhouette

A local SVG identity placeholder, with explicit `variant="person"` (default) or `variant="organisation"`, `size` 8..2048 and an escaped `label`. Uses inherited currentColor for light/dark theme contrast; it infers no attributes from a name.

`{avatar:silhouette variant="organisation", label="Unknown organisation"/}`. PHP: `padAvatarMake("silhouette", "", ["variant"=>"person"])`.
