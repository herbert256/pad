//
//  ChessModels.swift
//  LichessReplay
//
//  Chess game models for position, moves, and game state.
//

import Foundation

// MARK: - Piece Types
enum PieceType: String, CaseIterable {
    case king = "k"
    case queen = "q"
    case rook = "r"
    case bishop = "b"
    case knight = "n"
    case pawn = "p"

    var symbol: String {
        switch self {
        case .king: return "K"
        case .queen: return "Q"
        case .rook: return "R"
        case .bishop: return "B"
        case .knight: return "N"
        case .pawn: return ""
        }
    }
}

enum PieceColor: String {
    case white = "w"
    case black = "b"

    var opposite: PieceColor {
        self == .white ? .black : .white
    }
}

struct ChessPiece: Equatable {
    let type: PieceType
    let color: PieceColor

    var fenChar: Character {
        let char = type.rawValue.first!
        return color == .white ? Character(char.uppercased()) : char
    }

    static func from(fen char: Character) -> ChessPiece? {
        let lower = char.lowercased()
        guard let type = PieceType(rawValue: lower) else { return nil }
        let color: PieceColor = char.isUppercase ? .white : .black
        return ChessPiece(type: type, color: color)
    }
}

// MARK: - Square
struct Square: Hashable, CustomStringConvertible {
    let file: Int // 0-7 (a-h)
    let rank: Int // 0-7 (1-8)

    var description: String {
        let fileChar = Character(UnicodeScalar(97 + file)!)
        return "\(fileChar)\(rank + 1)"
    }

    static func from(notation: String) -> Square? {
        guard notation.count == 2 else { return nil }
        let chars = Array(notation.lowercased())
        guard let fileAscii = chars[0].asciiValue,
              fileAscii >= 97, fileAscii <= 104,
              let rankNum = Int(String(chars[1])),
              rankNum >= 1, rankNum <= 8 else { return nil }
        return Square(file: Int(fileAscii) - 97, rank: rankNum - 1)
    }

    var isLight: Bool {
        (file + rank) % 2 == 1
    }
}

// MARK: - Move
struct ChessMove: Identifiable, Equatable {
    let id = UUID()
    let from: Square
    let to: Square
    let promotion: PieceType?
    let san: String  // Standard Algebraic Notation (e.g., "Nf3", "e4")
    let uci: String  // UCI notation (e.g., "g1f3", "e2e4")

    init(from: Square, to: Square, promotion: PieceType? = nil, san: String = "", uci: String? = nil) {
        self.from = from
        self.to = to
        self.promotion = promotion
        self.san = san
        self.uci = uci ?? "\(from)\(to)\(promotion?.rawValue ?? "")"
    }

    static func from(uci: String) -> ChessMove? {
        guard uci.count >= 4 else { return nil }
        let fromStr = String(uci.prefix(2))
        let toStr = String(uci.dropFirst(2).prefix(2))
        guard let from = Square.from(notation: fromStr),
              let to = Square.from(notation: toStr) else { return nil }

        var promotion: PieceType?
        if uci.count == 5 {
            promotion = PieceType(rawValue: String(uci.last!))
        }

        return ChessMove(from: from, to: to, promotion: promotion)
    }
}

// MARK: - Position
class ChessPosition: ObservableObject {
    @Published var board: [[ChessPiece?]]
    @Published var sideToMove: PieceColor
    @Published var castlingRights: CastlingRights
    @Published var enPassantSquare: Square?
    @Published var halfmoveClock: Int
    @Published var fullmoveNumber: Int

    struct CastlingRights: OptionSet {
        let rawValue: Int
        static let whiteKingside = CastlingRights(rawValue: 1 << 0)
        static let whiteQueenside = CastlingRights(rawValue: 1 << 1)
        static let blackKingside = CastlingRights(rawValue: 1 << 2)
        static let blackQueenside = CastlingRights(rawValue: 1 << 3)
        static let all: CastlingRights = [.whiteKingside, .whiteQueenside, .blackKingside, .blackQueenside]
        static let none: CastlingRights = []
    }

    static let startingFEN = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1"

    init() {
        self.board = Array(repeating: Array(repeating: nil, count: 8), count: 8)
        self.sideToMove = .white
        self.castlingRights = .all
        self.enPassantSquare = nil
        self.halfmoveClock = 0
        self.fullmoveNumber = 1
        loadFEN(ChessPosition.startingFEN)
    }

    init(fen: String) {
        self.board = Array(repeating: Array(repeating: nil, count: 8), count: 8)
        self.sideToMove = .white
        self.castlingRights = .all
        self.enPassantSquare = nil
        self.halfmoveClock = 0
        self.fullmoveNumber = 1
        loadFEN(fen)
    }

    func piece(at square: Square) -> ChessPiece? {
        board[square.rank][square.file]
    }

    func setPiece(_ piece: ChessPiece?, at square: Square) {
        board[square.rank][square.file] = piece
    }

    func loadFEN(_ fen: String) {
        let parts = fen.split(separator: " ")
        guard parts.count >= 1 else { return }

        // Clear board
        board = Array(repeating: Array(repeating: nil, count: 8), count: 8)

        // Parse piece placement
        let ranks = parts[0].split(separator: "/")
        for (rankIndex, rankStr) in ranks.enumerated() {
            let rank = 7 - rankIndex
            var file = 0
            for char in rankStr {
                if let num = char.wholeNumberValue {
                    file += num
                } else if let piece = ChessPiece.from(fen: char) {
                    if file < 8 && rank >= 0 && rank < 8 {
                        board[rank][file] = piece
                    }
                    file += 1
                }
            }
        }

        // Parse side to move
        if parts.count > 1 {
            sideToMove = parts[1] == "w" ? .white : .black
        }

        // Parse castling rights
        if parts.count > 2 {
            castlingRights = []
            let castling = String(parts[2])
            if castling.contains("K") { castlingRights.insert(.whiteKingside) }
            if castling.contains("Q") { castlingRights.insert(.whiteQueenside) }
            if castling.contains("k") { castlingRights.insert(.blackKingside) }
            if castling.contains("q") { castlingRights.insert(.blackQueenside) }
        }

        // Parse en passant
        if parts.count > 3 && parts[3] != "-" {
            enPassantSquare = Square.from(notation: String(parts[3]))
        } else {
            enPassantSquare = nil
        }

        // Parse halfmove clock
        if parts.count > 4 {
            halfmoveClock = Int(parts[4]) ?? 0
        }

        // Parse fullmove number
        if parts.count > 5 {
            fullmoveNumber = Int(parts[5]) ?? 1
        }
    }

    func toFEN() -> String {
        var fen = ""

        // Piece placement
        for rank in (0..<8).reversed() {
            var emptyCount = 0
            for file in 0..<8 {
                if let piece = board[rank][file] {
                    if emptyCount > 0 {
                        fen += "\(emptyCount)"
                        emptyCount = 0
                    }
                    fen += String(piece.fenChar)
                } else {
                    emptyCount += 1
                }
            }
            if emptyCount > 0 {
                fen += "\(emptyCount)"
            }
            if rank > 0 {
                fen += "/"
            }
        }

        // Side to move
        fen += " \(sideToMove.rawValue)"

        // Castling rights
        var castlingStr = ""
        if castlingRights.contains(.whiteKingside) { castlingStr += "K" }
        if castlingRights.contains(.whiteQueenside) { castlingStr += "Q" }
        if castlingRights.contains(.blackKingside) { castlingStr += "k" }
        if castlingRights.contains(.blackQueenside) { castlingStr += "q" }
        fen += " \(castlingStr.isEmpty ? "-" : castlingStr)"

        // En passant
        fen += " \(enPassantSquare?.description ?? "-")"

        // Halfmove clock and fullmove number
        fen += " \(halfmoveClock) \(fullmoveNumber)"

        return fen
    }

    func makeMove(_ move: ChessMove) {
        guard let movingPiece = piece(at: move.from) else { return }

        // Handle castling
        if movingPiece.type == .king {
            let fileDiff = move.to.file - move.from.file
            if abs(fileDiff) == 2 {
                // Castling
                let rookFromFile = fileDiff > 0 ? 7 : 0
                let rookToFile = fileDiff > 0 ? 5 : 3
                let rookFrom = Square(file: rookFromFile, rank: move.from.rank)
                let rookTo = Square(file: rookToFile, rank: move.from.rank)
                if let rook = piece(at: rookFrom) {
                    setPiece(nil, at: rookFrom)
                    setPiece(rook, at: rookTo)
                }
            }
            // Remove castling rights
            if movingPiece.color == .white {
                castlingRights.remove(.whiteKingside)
                castlingRights.remove(.whiteQueenside)
            } else {
                castlingRights.remove(.blackKingside)
                castlingRights.remove(.blackQueenside)
            }
        }

        // Handle rook moves (remove castling rights)
        if movingPiece.type == .rook {
            if move.from.file == 0 {
                if movingPiece.color == .white { castlingRights.remove(.whiteQueenside) }
                else { castlingRights.remove(.blackQueenside) }
            } else if move.from.file == 7 {
                if movingPiece.color == .white { castlingRights.remove(.whiteKingside) }
                else { castlingRights.remove(.blackKingside) }
            }
        }

        // Handle en passant capture
        if movingPiece.type == .pawn && move.to == enPassantSquare {
            let capturedPawnRank = movingPiece.color == .white ? move.to.rank - 1 : move.to.rank + 1
            setPiece(nil, at: Square(file: move.to.file, rank: capturedPawnRank))
        }

        // Set en passant square
        if movingPiece.type == .pawn && abs(move.to.rank - move.from.rank) == 2 {
            let epRank = (move.from.rank + move.to.rank) / 2
            enPassantSquare = Square(file: move.from.file, rank: epRank)
        } else {
            enPassantSquare = nil
        }

        // Update halfmove clock
        if movingPiece.type == .pawn || self.piece(at: move.to) != nil {
            halfmoveClock = 0
        } else {
            halfmoveClock += 1
        }

        // Move the piece
        setPiece(nil, at: move.from)

        // Handle promotion
        if let promotion = move.promotion {
            setPiece(ChessPiece(type: promotion, color: movingPiece.color), at: move.to)
        } else {
            setPiece(movingPiece, at: move.to)
        }

        // Update fullmove number
        if sideToMove == .black {
            fullmoveNumber += 1
        }

        // Switch side to move
        sideToMove = sideToMove.opposite
    }

    func copy() -> ChessPosition {
        ChessPosition(fen: toFEN())
    }
}

// MARK: - Game
class ChessGame: ObservableObject, Identifiable {
    let id = UUID()

    @Published var moves: [ChessMove] = []
    @Published var currentMoveIndex: Int = -1
    @Published var position: ChessPosition

    // Game metadata
    var whitePlayer: String = ""
    var blackPlayer: String = ""
    var whiteRating: Int?
    var blackRating: Int?
    var result: String = "*"
    var event: String = ""
    var date: String = ""
    var timeControl: String = ""
    var opening: String = ""

    // Position history for navigation
    private var positionHistory: [String] = []

    init() {
        self.position = ChessPosition()
        self.positionHistory = [position.toFEN()]
    }

    var currentFEN: String {
        position.toFEN()
    }

    var isAtStart: Bool {
        currentMoveIndex == -1
    }

    var isAtEnd: Bool {
        currentMoveIndex == moves.count - 1
    }

    func loadMoves(_ moves: [ChessMove]) {
        self.moves = moves
        self.currentMoveIndex = -1
        self.position = ChessPosition()
        self.positionHistory = [position.toFEN()]

        // Build position history
        let tempPosition = ChessPosition()
        for move in moves {
            tempPosition.makeMove(move)
            positionHistory.append(tempPosition.toFEN())
        }
    }

    func goToStart() {
        currentMoveIndex = -1
        position.loadFEN(positionHistory[0])
    }

    func goToEnd() {
        currentMoveIndex = moves.count - 1
        if !positionHistory.isEmpty {
            position.loadFEN(positionHistory[positionHistory.count - 1])
        }
    }

    func goToMove(_ index: Int) {
        guard index >= -1 && index < moves.count else { return }
        currentMoveIndex = index
        position.loadFEN(positionHistory[index + 1])
    }

    func nextMove() -> Bool {
        guard currentMoveIndex < moves.count - 1 else { return false }
        currentMoveIndex += 1
        position.loadFEN(positionHistory[currentMoveIndex + 1])
        return true
    }

    func previousMove() -> Bool {
        guard currentMoveIndex >= 0 else { return false }
        currentMoveIndex -= 1
        position.loadFEN(positionHistory[currentMoveIndex + 1])
        return true
    }
}

// MARK: - Engine Analysis
struct EngineAnalysis {
    var score: Double  // In pawns (+ = white advantage)
    var mate: Int?     // Moves to mate (+ = white mates, - = black mates)
    var depth: Int
    var nodes: Int
    var nps: Int       // Nodes per second
    var bestMove: String
    var pv: [String]   // Principal variation
    var time: Int      // Time in milliseconds

    var scoreText: String {
        if let mate = mate {
            return mate > 0 ? "M\(mate)" : "M\(abs(mate))"
        }
        let sign = score >= 0 ? "+" : ""
        return "\(sign)\(String(format: "%.1f", score))"
    }

    var barPercentage: Double {
        if let mate = mate {
            return mate > 0 ? 100 : 0
        }
        // Clamp to -10 to +10 range
        let clamped = max(-10, min(10, score))
        return 50 + (clamped * 5)
    }

    static var empty: EngineAnalysis {
        EngineAnalysis(score: 0, mate: nil, depth: 0, nodes: 0, nps: 0, bestMove: "", pv: [], time: 0)
    }
}
