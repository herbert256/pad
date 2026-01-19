package com.lichessreplay.ui

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.layout.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.nativeCanvas
import com.lichessreplay.chess.*

val BoardLight = Color(0xFFF0D9B5)
val BoardDark = Color(0xFFB58863)
val HighlightColor = Color(0xFFCDD26A)

@Composable
fun ChessBoardView(
    board: ChessBoard,
    flipped: Boolean = false,
    modifier: Modifier = Modifier
) {
    val lastMove = board.getLastMove()

    Canvas(
        modifier = modifier
            .aspectRatio(1f)
            .fillMaxWidth()
    ) {
        val squareSize = size.width / 8f

        // Draw squares
        for (rank in 0..7) {
            for (file in 0..7) {
                val displayRank = if (flipped) rank else 7 - rank
                val displayFile = if (flipped) 7 - file else file

                val isLight = (file + rank) % 2 == 0
                var squareColor = if (isLight) BoardLight else BoardDark

                // Highlight last move
                if (lastMove != null) {
                    val square = Square(displayFile, displayRank)
                    if (square == lastMove.from || square == lastMove.to) {
                        squareColor = HighlightColor
                    }
                }

                drawRect(
                    color = squareColor,
                    topLeft = Offset(file * squareSize, rank * squareSize),
                    size = Size(squareSize, squareSize)
                )

                // Draw piece
                val piece = board.getPiece(displayFile, displayRank)
                if (piece != null) {
                    val pieceChar = getPieceUnicode(piece)
                    val textSize = squareSize * 0.75f

                    drawContext.canvas.nativeCanvas.apply {
                        val paint = android.graphics.Paint().apply {
                            this.textSize = textSize
                            this.textAlign = android.graphics.Paint.Align.CENTER
                            this.color = if (piece.color == PieceColor.WHITE) {
                                android.graphics.Color.WHITE
                            } else {
                                android.graphics.Color.BLACK
                            }
                            this.isAntiAlias = true

                            // Add shadow for white pieces for visibility
                            if (piece.color == PieceColor.WHITE) {
                                setShadowLayer(3f, 1f, 1f, android.graphics.Color.DKGRAY)
                            }
                        }

                        drawText(
                            pieceChar,
                            file * squareSize + squareSize / 2,
                            rank * squareSize + squareSize / 2 + textSize / 3,
                            paint
                        )
                    }
                }
            }
        }

        // Draw file labels (a-h)
        val labelSize = squareSize * 0.15f
        drawContext.canvas.nativeCanvas.apply {
            val paint = android.graphics.Paint().apply {
                textSize = labelSize
                color = android.graphics.Color.argb(180, 0, 0, 0)
                isAntiAlias = true
            }

            for (file in 0..7) {
                val displayFile = if (flipped) 7 - file else file
                val label = ('a' + displayFile).toString()
                val x = file * squareSize + squareSize - labelSize * 0.5f
                val y = size.height - labelSize * 0.3f

                // Use contrasting color based on square
                paint.color = if ((file + 7) % 2 == 0) {
                    android.graphics.Color.argb(200, 181, 136, 99) // Dark square color
                } else {
                    android.graphics.Color.argb(200, 240, 217, 181) // Light square color
                }

                drawText(label, x, y, paint)
            }

            // Draw rank labels (1-8)
            for (rank in 0..7) {
                val displayRank = if (flipped) rank + 1 else 8 - rank
                val label = displayRank.toString()
                val x = labelSize * 0.3f
                val y = rank * squareSize + labelSize * 1.2f

                // Use contrasting color based on square
                paint.color = if ((rank) % 2 == 0) {
                    android.graphics.Color.argb(200, 181, 136, 99) // Dark square color
                } else {
                    android.graphics.Color.argb(200, 240, 217, 181) // Light square color
                }

                drawText(label, x, y, paint)
            }
        }
    }
}

private fun getPieceUnicode(piece: Piece): String {
    // Use filled characters for both colors (solid pieces)
    return when (piece.type) {
        PieceType.KING -> "♚"
        PieceType.QUEEN -> "♛"
        PieceType.ROOK -> "♜"
        PieceType.BISHOP -> "♝"
        PieceType.KNIGHT -> "♞"
        PieceType.PAWN -> "♟"
    }
}
