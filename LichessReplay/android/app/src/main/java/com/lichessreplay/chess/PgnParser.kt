package com.lichessreplay.chess

class PgnParser {
    companion object {
        fun parseMoves(pgn: String): List<String> {
            // Remove headers (lines starting with [)
            val movesSection = pgn.lines()
                .dropWhile { it.startsWith("[") || it.isBlank() }
                .joinToString(" ")
                .trim()

            // Remove comments (text in curly braces)
            val withoutComments = movesSection.replace(Regex("\\{[^}]*\\}"), " ")

            // Remove variations (text in parentheses) - simplified, doesn't handle nested
            val withoutVariations = withoutComments.replace(Regex("\\([^)]*\\)"), " ")

            // Remove annotations like !, ?, !!, ??, !?, ?!
            val withoutAnnotations = withoutVariations.replace(Regex("[!?]+"), "")

            // Remove result at the end
            val withoutResult = withoutAnnotations
                .replace("1-0", "")
                .replace("0-1", "")
                .replace("1/2-1/2", "")
                .replace("*", "")

            // Remove move numbers (like "1." or "1...")
            val withoutNumbers = withoutResult.replace(Regex("\\d+\\.+"), "")

            // Split into individual moves and filter empty
            return withoutNumbers
                .split(Regex("\\s+"))
                .filter { it.isNotBlank() }
        }

        fun parseHeaders(pgn: String): Map<String, String> {
            val headers = mutableMapOf<String, String>()
            val headerRegex = Regex("\\[([A-Za-z]+)\\s+\"([^\"]*)\"\\]")

            for (line in pgn.lines()) {
                val match = headerRegex.find(line)
                if (match != null) {
                    headers[match.groupValues[1]] = match.groupValues[2]
                }
            }

            return headers
        }
    }
}
