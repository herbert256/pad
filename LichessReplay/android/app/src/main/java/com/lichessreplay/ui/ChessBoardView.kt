package com.lichessreplay.ui

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.gestures.detectDragGestures
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.foundation.layout.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.graphics.nativeCanvas
import androidx.compose.ui.input.pointer.pointerInput
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
val LegalMoveColor = Color(0x6644AA44)
val SelectedSquareColor = Color(0x8844AA44)

@Composable
fun ChessBoardView(
    board: ChessBoard,
    flipped: Boolean = false,
    interactionEnabled: Boolean = false,
    onMove: ((Square, Square) -> Unit)? = null,
    modifier: Modifier = Modifier
) {
    val lastMove = board.getLastMove()
    val context = LocalContext.current

    // Selection and drag state
    var selectedSquare by remember { mutableStateOf<Square?>(null) }
    var dragFromSquare by remember { mutableStateOf<Square?>(null) }
    var dragPosition by remember { mutableStateOf<Offset?>(null) }
    var legalMoves by remember { mutableStateOf<List<Square>>(emptyList()) }
    var squareSize by remember { mutableStateOf(0f) }

    // Clear selection when board changes (e.g., after a move)
    LaunchedEffect(board.getFen()) {
        selectedSquare = null
        legalMoves = emptyList()
    }

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

    // Helper function to convert screen position to board square
    fun positionToSquare(x: Float, y: Float, size: Float): Square? {
        if (size <= 0) return null
        val file = (x / size).toInt().coerceIn(0, 7)
        val rank = (y / size).toInt().coerceIn(0, 7)
        val displayFile = if (flipped) 7 - file else file
        val displayRank = if (flipped) rank else 7 - rank
        return Square(displayFile, displayRank)
    }

    Canvas(
        modifier = modifier
            .aspectRatio(1f)
            .fillMaxWidth()
            .then(
                if (interactionEnabled && onMove != null) {
                    Modifier
                        .pointerInput(board, flipped) {
                            detectTapGestures { offset ->
                                squareSize = size.width / 8f
                                val tappedSquare = positionToSquare(offset.x, offset.y, squareSize)
                                if (tappedSquare != null) {
                                    val currentSelected = selectedSquare
                                    if (currentSelected != null && legalMoves.contains(tappedSquare)) {
                                        // Tapped on a legal move target - make the move
                                        onMove(currentSelected, tappedSquare)
                                        selectedSquare = null
                                        legalMoves = emptyList()
                                    } else {
                                        // Check if tapped on own piece
                                        val piece = board.getPiece(tappedSquare)
                                        if (piece != null && piece.color == board.getTurn()) {
                                            // Select this piece
                                            selectedSquare = tappedSquare
                                            legalMoves = board.getLegalMoves(tappedSquare)
                                        } else {
                                            // Tapped on empty or opponent piece without selection - deselect
                                            selectedSquare = null
                                            legalMoves = emptyList()
                                        }
                                    }
                                }
                            }
                        }
                        .pointerInput(board, flipped) {
                            detectDragGestures(
                                onDragStart = { offset ->
                                    squareSize = size.width / 8f
                                    val square = positionToSquare(offset.x, offset.y, squareSize)
                                    if (square != null) {
                                        val piece = board.getPiece(square)
                                        if (piece != null && piece.color == board.getTurn()) {
                                            dragFromSquare = square
                                            dragPosition = offset
                                            legalMoves = board.getLegalMoves(square)
                                            selectedSquare = null // Clear tap selection when dragging
                                        }
                                    }
                                },
                                onDrag = { change, _ ->
                                    change.consume()
                                    dragPosition = change.position
                                },
                                onDragEnd = {
                                    val from = dragFromSquare
                                    val pos = dragPosition
                                    if (from != null && pos != null && squareSize > 0) {
                                        val to = positionToSquare(pos.x, pos.y, squareSize)
                                        if (to != null && legalMoves.contains(to)) {
                                            onMove(from, to)
                                        }
                                    }
                                    dragFromSquare = null
                                    dragPosition = null
                                    legalMoves = emptyList()
                                },
                                onDragCancel = {
                                    dragFromSquare = null
                                    dragPosition = null
                                    legalMoves = emptyList()
                                }
                            )
                        }
                } else Modifier
            )
    ) {
        squareSize = size.width / 8f

        // Draw squares
        for (rank in 0..7) {
            for (file in 0..7) {
                val displayRank = if (flipped) rank else 7 - rank
                val displayFile = if (flipped) 7 - file else file

                val isLight = (file + rank) % 2 == 0
                var squareColor = if (isLight) BoardLight else BoardDark

                val square = Square(displayFile, displayRank)

                // Highlight last move
                if (lastMove != null) {
                    if (square == lastMove.from || square == lastMove.to) {
                        squareColor = HighlightColor
                    }
                }

                // Highlight selected square (from tap or drag)
                if (dragFromSquare == square || selectedSquare == square) {
                    squareColor = SelectedSquareColor
                }

                drawRect(
                    color = squareColor,
                    topLeft = Offset(file * squareSize, rank * squareSize),
                    size = Size(squareSize, squareSize)
                )

                // Draw legal move indicators
                if (legalMoves.contains(square)) {
                    val centerX = file * squareSize + squareSize / 2
                    val centerY = rank * squareSize + squareSize / 2
                    val targetPiece = board.getPiece(square)
                    if (targetPiece != null) {
                        // Draw ring for capture
                        drawCircle(
                            color = LegalMoveColor,
                            radius = squareSize * 0.45f,
                            center = Offset(centerX, centerY)
                        )
                        // Draw inner square color to make it a ring
                        drawCircle(
                            color = squareColor,
                            radius = squareSize * 0.35f,
                            center = Offset(centerX, centerY)
                        )
                    } else {
                        // Draw dot for empty square
                        drawCircle(
                            color = LegalMoveColor,
                            radius = squareSize * 0.15f,
                            center = Offset(centerX, centerY)
                        )
                    }
                }

                // Draw piece (skip if it's being dragged)
                if (dragFromSquare != square) {
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
        }

        // Draw dragging piece on top
        val fromSquare = dragFromSquare
        val pos = dragPosition
        if (fromSquare != null && pos != null) {
            val piece = board.getPiece(fromSquare)
            if (piece != null) {
                val pieceImage = pieceImages[Pair(piece.color, piece.type)]
                if (pieceImage != null) {
                    val pieceDrawSize = (squareSize * 1.2f).toInt() // Slightly larger when dragging
                    val halfSize = pieceDrawSize / 2

                    drawImage(
                        image = pieceImage,
                        srcOffset = IntOffset.Zero,
                        srcSize = IntSize(pieceImage.width, pieceImage.height),
                        dstOffset = IntOffset(
                            (pos.x - halfSize).toInt(),
                            (pos.y - halfSize).toInt()
                        ),
                        dstSize = IntSize(pieceDrawSize, pieceDrawSize)
                    )
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
