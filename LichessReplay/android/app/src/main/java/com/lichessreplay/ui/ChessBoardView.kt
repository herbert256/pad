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
val BestMoveArrowColor = Color(0xCC3399FF)  // Semi-transparent blue

@Composable
fun ChessBoardView(
    board: ChessBoard,
    flipped: Boolean = false,
    interactionEnabled: Boolean = false,
    onMove: ((Square, Square) -> Unit)? = null,
    onTap: (() -> Unit)? = null,
    bestMoveArrow: Pair<Square, Square>? = null,  // Arrow from first square to second
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
                if (!interactionEnabled && onTap != null) {
                    // When interaction is disabled, just detect taps to notify parent
                    Modifier.pointerInput(Unit) {
                        detectTapGestures { onTap() }
                    }
                } else if (interactionEnabled && onMove != null) {
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

        // Draw best move arrow
        if (bestMoveArrow != null) {
            val (arrowFrom, arrowTo) = bestMoveArrow

            // Convert squares to screen coordinates (center of each square)
            val fromFile = if (flipped) 7 - arrowFrom.file else arrowFrom.file
            val fromRank = if (flipped) arrowFrom.rank else 7 - arrowFrom.rank
            val toFile = if (flipped) 7 - arrowTo.file else arrowTo.file
            val toRank = if (flipped) arrowTo.rank else 7 - arrowTo.rank

            val startX = fromFile * squareSize + squareSize / 2
            val startY = fromRank * squareSize + squareSize / 2
            val endX = toFile * squareSize + squareSize / 2
            val endY = toRank * squareSize + squareSize / 2

            // Calculate arrow properties
            val arrowWidth = squareSize * 0.15f
            val headLength = squareSize * 0.35f
            val headWidth = squareSize * 0.35f

            // Calculate angle
            val dx = endX - startX
            val dy = endY - startY
            val angle = kotlin.math.atan2(dy, dx)

            // Shorten the arrow slightly so it doesn't cover the piece centers
            val shortenAmount = squareSize * 0.25f
            val adjustedStartX = startX + kotlin.math.cos(angle) * shortenAmount
            val adjustedStartY = startY + kotlin.math.sin(angle) * shortenAmount
            val adjustedEndX = endX - kotlin.math.cos(angle) * shortenAmount
            val adjustedEndY = endY - kotlin.math.sin(angle) * shortenAmount

            // Draw arrow shaft
            drawLine(
                color = BestMoveArrowColor,
                start = Offset(adjustedStartX, adjustedStartY),
                end = Offset(adjustedEndX - kotlin.math.cos(angle) * headLength * 0.5f,
                            adjustedEndY - kotlin.math.sin(angle) * headLength * 0.5f),
                strokeWidth = arrowWidth
            )

            // Draw arrow head using a path
            val headBaseX = adjustedEndX - kotlin.math.cos(angle) * headLength
            val headBaseY = adjustedEndY - kotlin.math.sin(angle) * headLength
            val perpAngle = angle + kotlin.math.PI.toFloat() / 2

            val path = androidx.compose.ui.graphics.Path().apply {
                moveTo(adjustedEndX, adjustedEndY)
                lineTo(
                    headBaseX + kotlin.math.cos(perpAngle) * headWidth / 2,
                    headBaseY + kotlin.math.sin(perpAngle) * headWidth / 2
                )
                lineTo(
                    headBaseX - kotlin.math.cos(perpAngle) * headWidth / 2,
                    headBaseY - kotlin.math.sin(perpAngle) * headWidth / 2
                )
                close()
            }
            drawPath(path, BestMoveArrowColor)
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
