//
//  LichessClient.swift
//  LichessReplay
//
//  Client for fetching games from Lichess API.
//

import Foundation

// MARK: - Lichess API Response Models
struct LichessGame: Codable {
    let id: String
    let rated: Bool?
    let variant: String?
    let speed: String?
    let perf: String?
    let createdAt: Int?
    let lastMoveAt: Int?
    let status: String
    let players: LichessPlayers
    let winner: String?
    let moves: String?
    let pgn: String?
    let opening: LichessOpening?
    let clock: LichessClock?
}

struct LichessPlayers: Codable {
    let white: LichessPlayer
    let black: LichessPlayer
}

struct LichessPlayer: Codable {
    let user: LichessUser?
    let rating: Int?
    let ratingDiff: Int?
    let aiLevel: Int?
}

struct LichessUser: Codable {
    let name: String
    let id: String?
}

struct LichessOpening: Codable {
    let eco: String?
    let name: String?
    let ply: Int?
}

struct LichessClock: Codable {
    let initial: Int?
    let increment: Int?
    let totalTime: Int?
}

// MARK: - Lichess Client
class LichessClient: ObservableObject {
    @Published var isLoading = false
    @Published var error: String?

    private let baseURL = "https://lichess.org/api"

    func fetchLastGame(username: String) async throws -> ChessGame {
        let urlString = "\(baseURL)/games/user/\(username)?max=1&pgnInJson=true"
        guard let url = URL(string: urlString) else {
            throw LichessError.invalidURL
        }

        await MainActor.run {
            isLoading = true
            error = nil
        }

        defer {
            Task { @MainActor in
                isLoading = false
            }
        }

        var request = URLRequest(url: url)
        request.setValue("application/x-ndjson", forHTTPHeaderField: "Accept")

        let (data, response) = try await URLSession.shared.data(for: request)

        guard let httpResponse = response as? HTTPURLResponse else {
            throw LichessError.networkError
        }

        switch httpResponse.statusCode {
        case 200:
            break
        case 404:
            throw LichessError.userNotFound(username)
        case 429:
            throw LichessError.rateLimited
        default:
            throw LichessError.serverError(httpResponse.statusCode)
        }

        guard let text = String(data: data, encoding: .utf8), !text.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty else {
            throw LichessError.noGamesFound(username)
        }

        let decoder = JSONDecoder()
        let lichessGame = try decoder.decode(LichessGame.self, from: data)

        return convertToChessGame(lichessGame)
    }

    private func convertToChessGame(_ lichessGame: LichessGame) -> ChessGame {
        let game = ChessGame()

        // Set metadata
        game.whitePlayer = lichessGame.players.white.user?.name ??
            (lichessGame.players.white.aiLevel != nil ? "Stockfish Level \(lichessGame.players.white.aiLevel!)" : "Anonymous")
        game.blackPlayer = lichessGame.players.black.user?.name ??
            (lichessGame.players.black.aiLevel != nil ? "Stockfish Level \(lichessGame.players.black.aiLevel!)" : "Anonymous")
        game.whiteRating = lichessGame.players.white.rating
        game.blackRating = lichessGame.players.black.rating

        // Set result
        if lichessGame.winner == "white" {
            game.result = "1-0"
        } else if lichessGame.winner == "black" {
            game.result = "0-1"
        } else if lichessGame.status == "draw" || lichessGame.status == "stalemate" {
            game.result = "1/2-1/2"
        } else {
            game.result = "*"
        }

        // Set opening
        game.opening = lichessGame.opening?.name ?? ""

        // Set time control
        if let clock = lichessGame.clock {
            let initialMins = (clock.initial ?? 0) / 60
            let increment = clock.increment ?? 0
            game.timeControl = "\(initialMins)+\(increment)"
        }

        // Parse moves from UCI notation (Lichess provides space-separated UCI moves)
        if let movesStr = lichessGame.moves, !movesStr.isEmpty {
            let moves = parseUCIMoves(movesStr)
            game.loadMoves(moves)
        }

        return game
    }

    private func parseUCIMoves(_ movesStr: String) -> [ChessMove] {
        let uciMoves = movesStr.components(separatedBy: " ")
        var moves: [ChessMove] = []
        var position = ChessPosition()

        for uciStr in uciMoves {
            let trimmed = uciStr.trimmingCharacters(in: .whitespacesAndNewlines)
            guard !trimmed.isEmpty, trimmed.count >= 4 else { continue }

            if let move = parseUCIMove(trimmed, position: position) {
                moves.append(move)
                position.makeMove(move)
            }
        }

        return moves
    }

    private func parseUCIMove(_ uci: String, position: ChessPosition) -> ChessMove? {
        guard uci.count >= 4 else { return nil }

        let fromStr = String(uci.prefix(2))
        let toStr = String(uci.dropFirst(2).prefix(2))

        guard let from = Square.from(notation: fromStr),
              let to = Square.from(notation: toStr) else { return nil }

        var promotion: PieceType?
        if uci.count == 5 {
            promotion = PieceType(rawValue: String(uci.last!))
        }

        // Generate SAN notation
        let san = generateSAN(from: from, to: to, promotion: promotion, position: position)

        return ChessMove(from: from, to: to, promotion: promotion, san: san, uci: uci)
    }

    private func generateSAN(from: Square, to: Square, promotion: PieceType?, position: ChessPosition) -> String {
        guard let piece = position.piece(at: from) else { return "" }

        var san = ""

        // Handle castling
        if piece.type == .king {
            let fileDiff = to.file - from.file
            if fileDiff == 2 {
                return "O-O"
            } else if fileDiff == -2 {
                return "O-O-O"
            }
        }

        // Piece symbol
        if piece.type != .pawn {
            san += piece.type.symbol
        }

        // Capture
        let isCapture = position.piece(at: to) != nil ||
            (piece.type == .pawn && from.file != to.file)  // En passant

        if piece.type == .pawn && isCapture {
            san += String(Character(UnicodeScalar(97 + from.file)!))
        }

        if isCapture {
            san += "x"
        }

        // Destination square
        san += to.description

        // Promotion
        if let promo = promotion {
            san += "=\(promo.symbol)"
        }

        return san
    }
}

// MARK: - Lichess Errors
enum LichessError: LocalizedError {
    case invalidURL
    case networkError
    case userNotFound(String)
    case noGamesFound(String)
    case rateLimited
    case serverError(Int)
    case parsingError

    var errorDescription: String? {
        switch self {
        case .invalidURL:
            return "Invalid URL"
        case .networkError:
            return "Network error occurred"
        case .userNotFound(let username):
            return "User '\(username)' not found"
        case .noGamesFound(let username):
            return "No games found for user '\(username)'"
        case .rateLimited:
            return "Rate limited. Please wait a moment."
        case .serverError(let code):
            return "Server error (code: \(code))"
        case .parsingError:
            return "Failed to parse game data"
        }
    }
}
