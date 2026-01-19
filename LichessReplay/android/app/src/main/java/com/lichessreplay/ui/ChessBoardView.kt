package com.lichessreplay.ui

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.layout.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.graphics.nativeCanvas
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.IntOffset
import androidx.compose.ui.unit.IntSize
import androidx.core.content.ContextCompat
import androidx.core.graphics.drawable.toBitmap
import com.lichessreplay.R
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
    val context = LocalContext.current

    // Load piece images
    val pieceImages = remember {
        mapOf(
            Pair(PieceColor.WHITE, PieceType.KING) to ContextCompat.getDrawable(context, R.drawable.piece_white_king)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.WHITE, PieceType.QUEEN) to ContextCompat.getDrawable(context, R.drawable.piece_white_queen)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.WHITE, PieceType.ROOK) to ContextCompat.getDrawable(context, R.drawable.piece_white_rook)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.WHITE, PieceType.BISHOP) to ContextCompat.getDrawable(context, R.drawable.piece_white_bishop)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.WHITE, PieceType.KNIGHT) to ContextCompat.getDrawable(context, R.drawable.piece_white_knight)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.WHITE, PieceType.PAWN) to ContextCompat.getDrawable(context, R.drawable.piece_white_pawn)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.KING) to ContextCompat.getDrawable(context, R.drawable.piece_black_king)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.QUEEN) to ContextCompat.getDrawable(context, R.drawable.piece_black_queen)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.ROOK) to ContextCompat.getDrawable(context, R.drawable.piece_black_rook)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.BISHOP) to ContextCompat.getDrawable(context, R.drawable.piece_black_bishop)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.KNIGHT) to ContextCompat.getDrawable(context, R.drawable.piece_black_knight)?.toBitmap()?.asImageBitmap(),
            Pair(PieceColor.BLACK, PieceType.PAWN) to ContextCompat.getDrawable(context, R.drawable.piece_black_pawn)?.toBitmap()?.asImageBitmap()
        )
    }

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
                    val pieceImage = pieceImages[Pair(piece.color, piece.type)]
                    if (pieceImage != null) {
                        val padding = squareSize * 0.05f
                        val pieceSize = (squareSize - padding * 2).toInt()

                        drawImage(
                            image = pieceImage,
                            srcOffset = IntOffset.Zero,
                            srcSize = IntSize(pieceImage.width, pieceImage.height),
                            dstOffset = IntOffset(
                                (file * squareSize + padding).toInt(),
                                (rank * squareSize + padding).toInt()
                            ),
                            dstSize = IntSize(pieceSize, pieceSize)
                        )
                    }
                }
            }
        }

        // Draw file labels (a-h) and rank labels (1-8)
        val labelSize = squareSize * 0.22f
        drawContext.canvas.nativeCanvas.apply {
            val paint = android.graphics.Paint().apply {
                textSize = labelSize
                isAntiAlias = true
                isFakeBoldText = true
            }

            // File labels (a-h) at bottom of each column
            for (file in 0..7) {
                val displayFile = if (flipped) 7 - file else file
                val label = ('a' + displayFile).toString()
                val x = file * squareSize + squareSize - labelSize * 0.7f
                val y = size.height - labelSize * 0.25f

                // Use black color for coordinates
                paint.color = android.graphics.Color.BLACK

                drawText(label, x, y, paint)
            }

            // Rank labels (1-8) at left of each row
            for (rank in 0..7) {
                val displayRank = if (flipped) rank + 1 else 8 - rank
                val label = displayRank.toString()
                val x = labelSize * 0.25f
                val y = rank * squareSize + labelSize * 1.0f

                // Use black color for coordinates
                paint.color = android.graphics.Color.BLACK

                drawText(label, x, y, paint)
            }
        }
    }
}
