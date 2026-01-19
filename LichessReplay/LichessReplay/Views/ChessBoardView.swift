//
//  ChessBoardView.swift
//  LichessReplay
//
//  SwiftUI chess board view with piece rendering.
//

import SwiftUI

struct ChessBoardView: View {
    @ObservedObject var position: ChessPosition
    var flipped: Bool = false
    var highlightedSquares: Set<Square> = []
    var lastMove: ChessMove?

    private let lightSquareColor = Color(red: 0.94, green: 0.85, blue: 0.71)
    private let darkSquareColor = Color(red: 0.71, green: 0.53, blue: 0.39)
    private let highlightColor = Color.yellow.opacity(0.5)
    private let lastMoveColor = Color.green.opacity(0.3)

    var body: some View {
        GeometryReader { geometry in
            let size = min(geometry.size.width, geometry.size.height)
            let squareSize = size / 8

            ZStack {
                // Board squares
                ForEach(0..<8, id: \.self) { rank in
                    ForEach(0..<8, id: \.self) { file in
                        let displayRank = flipped ? rank : 7 - rank
                        let displayFile = flipped ? 7 - file : file
                        let square = Square(file: displayFile, rank: displayRank)

                        squareView(
                            square: square,
                            squareSize: squareSize,
                            x: CGFloat(file) * squareSize,
                            y: CGFloat(rank) * squareSize
                        )
                    }
                }

                // Coordinate labels
                coordinateLabels(squareSize: squareSize)
            }
            .frame(width: size, height: size)
        }
        .aspectRatio(1, contentMode: .fit)
    }

    @ViewBuilder
    private func squareView(square: Square, squareSize: CGFloat, x: CGFloat, y: CGFloat) -> some View {
        let isLight = square.isLight
        let isHighlighted = highlightedSquares.contains(square)
        let isLastMoveSquare = lastMove?.from == square || lastMove?.to == square

        ZStack {
            // Base square color
            Rectangle()
                .fill(isLight ? lightSquareColor : darkSquareColor)

            // Last move highlight
            if isLastMoveSquare {
                Rectangle()
                    .fill(lastMoveColor)
            }

            // Selection highlight
            if isHighlighted {
                Rectangle()
                    .fill(highlightColor)
            }

            // Piece
            if let piece = position.piece(at: square) {
                PieceView(piece: piece)
                    .padding(squareSize * 0.05)
            }
        }
        .frame(width: squareSize, height: squareSize)
        .position(x: x + squareSize / 2, y: y + squareSize / 2)
    }

    @ViewBuilder
    private func coordinateLabels(squareSize: CGFloat) -> some View {
        // File labels (a-h)
        ForEach(0..<8, id: \.self) { file in
            let displayFile = flipped ? 7 - file : file
            let letter = String(Character(UnicodeScalar(97 + displayFile)!))

            Text(letter)
                .font(.system(size: squareSize * 0.15, weight: .medium))
                .foregroundColor(file % 2 == 0 ? darkSquareColor : lightSquareColor)
                .position(
                    x: CGFloat(file) * squareSize + squareSize * 0.9,
                    y: squareSize * 7.9
                )
        }

        // Rank labels (1-8)
        ForEach(0..<8, id: \.self) { rank in
            let displayRank = flipped ? rank + 1 : 8 - rank
            let number = String(displayRank)

            Text(number)
                .font(.system(size: squareSize * 0.15, weight: .medium))
                .foregroundColor(rank % 2 == 0 ? lightSquareColor : darkSquareColor)
                .position(
                    x: squareSize * 0.1,
                    y: CGFloat(rank) * squareSize + squareSize * 0.1
                )
        }
    }
}

// MARK: - Piece View
struct PieceView: View {
    let piece: ChessPiece

    var body: some View {
        Image(systemName: pieceSystemImage)
            .resizable()
            .aspectRatio(contentMode: .fit)
            .foregroundStyle(
                piece.color == .white ? Color.white : Color.black,
                piece.color == .white ? Color.black : Color.white
            )
            .shadow(color: .black.opacity(0.3), radius: 2, x: 1, y: 1)
    }

    private var pieceSystemImage: String {
        let suffix = ".fill"
        switch piece.type {
        case .king: return "crown\(suffix)"
        case .queen: return "crown\(suffix)"
        case .rook: return "building.columns\(suffix)"
        case .bishop: return "arrowtriangle.up\(suffix)"
        case .knight: return "hare\(suffix)"
        case .pawn: return "circle\(suffix)"
        }
    }
}

// MARK: - Custom Chess Piece Images
struct ChessPieceImage: View {
    let piece: ChessPiece
    let size: CGFloat

    var body: some View {
        Text(pieceUnicode)
            .font(.system(size: size * 0.85))
            .foregroundColor(piece.color == .white ? .white : .black)
            .shadow(color: piece.color == .white ? .black : .white, radius: 0.5)
    }

    private var pieceUnicode: String {
        let base: Int
        switch piece.type {
        case .king: base = 0x2654
        case .queen: base = 0x2655
        case .rook: base = 0x2656
        case .bishop: base = 0x2657
        case .knight: base = 0x2658
        case .pawn: base = 0x2659
        }
        // White pieces are 0x2654-0x2659, black are 0x265A-0x265F
        let codePoint = piece.color == .white ? base : base + 6
        return String(UnicodeScalar(codePoint)!)
    }
}

// MARK: - Alternative Board View with Unicode Pieces
struct ChessBoardUnicodeView: View {
    @ObservedObject var position: ChessPosition
    var flipped: Bool = false
    var lastMove: ChessMove?

    private let lightSquareColor = Color(red: 0.94, green: 0.85, blue: 0.71)
    private let darkSquareColor = Color(red: 0.71, green: 0.53, blue: 0.39)
    private let lastMoveColor = Color(red: 0.8, green: 0.9, blue: 0.5, opacity: 0.7)

    var body: some View {
        GeometryReader { geometry in
            let size = min(geometry.size.width, geometry.size.height)
            let squareSize = size / 8

            VStack(spacing: 0) {
                ForEach(0..<8, id: \.self) { rank in
                    HStack(spacing: 0) {
                        ForEach(0..<8, id: \.self) { file in
                            let displayRank = flipped ? rank : 7 - rank
                            let displayFile = flipped ? 7 - file : file
                            let square = Square(file: displayFile, rank: displayRank)
                            let isLight = (displayFile + displayRank) % 2 == 1
                            let isLastMoveSquare = lastMove?.from == square || lastMove?.to == square

                            ZStack {
                                Rectangle()
                                    .fill(isLastMoveSquare ? lastMoveColor : (isLight ? lightSquareColor : darkSquareColor))

                                if let piece = position.piece(at: square) {
                                    ChessPieceImage(piece: piece, size: squareSize)
                                }
                            }
                            .frame(width: squareSize, height: squareSize)
                        }
                    }
                }
            }
            .frame(width: size, height: size)
            .border(Color.black, width: 2)
        }
        .aspectRatio(1, contentMode: .fit)
    }
}

#Preview {
    VStack {
        ChessBoardUnicodeView(position: ChessPosition())
            .frame(width: 400, height: 400)
    }
    .padding()
}
