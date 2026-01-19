//
//  ContentView.swift
//  LichessReplay
//
//  Main application view with game replay and analysis.
//

import SwiftUI

struct ContentView: View {
    @EnvironmentObject var appState: AppState
    @State private var username = ""
    @State private var boardFlipped = false

    var body: some View {
        ZStack {
            // Background
            LinearGradient(
                colors: [Color(hex: "1a1a2e"), Color(hex: "16213e")],
                startPoint: .top,
                endPoint: .bottom
            )
            .ignoresSafeArea()

            VStack(spacing: 0) {
                // Header
                headerView

                Divider()
                    .background(Color.white.opacity(0.1))

                // Main content
                if let game = appState.currentGame {
                    gameContentView(game: game)
                } else if appState.isLoading {
                    loadingView
                } else {
                    welcomeView
                }
            }
        }
        .alert("Error", isPresented: .constant(appState.errorMessage != nil)) {
            Button("OK") {
                appState.errorMessage = nil
            }
        } message: {
            Text(appState.errorMessage ?? "")
        }
        .onAppear {
            Task {
                try? await appState.engineManager.startActiveEngine()
            }
        }
    }

    // MARK: - Header
    private var headerView: some View {
        HStack(spacing: 20) {
            Text("Lichess Game Replay")
                .font(.title2)
                .fontWeight(.light)
                .foregroundColor(.white)

            Spacer()

            // Search field
            HStack {
                Image(systemName: "magnifyingglass")
                    .foregroundColor(.gray)

                TextField("Lichess username", text: $username)
                    .textFieldStyle(.plain)
                    .foregroundColor(.white)
                    .onSubmit {
                        fetchGame()
                    }
            }
            .padding(10)
            .background(Color(hex: "16213e"))
            .cornerRadius(8)
            .frame(width: 250)

            Button("Fetch Game") {
                fetchGame()
            }
            .buttonStyle(PrimaryButtonStyle())
            .disabled(username.isEmpty || appState.isLoading)

            Spacer()

            // Engine selector
            Picker("Engine", selection: $appState.engineManager.selectedEngineType) {
                ForEach(EngineType.allCases) { engine in
                    Text(engine.rawValue).tag(engine)
                }
            }
            .pickerStyle(.segmented)
            .frame(width: 200)
            .onChange(of: appState.engineManager.selectedEngineType) { _, newValue in
                Task {
                    try? await appState.engineManager.switchEngine(to: newValue)
                }
            }

            Button(action: {
                NSApp.sendAction(Selector(("showSettingsWindow:")), to: nil, from: nil)
            }) {
                Image(systemName: "gear")
                    .font(.title2)
            }
            .buttonStyle(.plain)
            .foregroundColor(.white)
        }
        .padding()
        .background(Color.black.opacity(0.2))
    }

    // MARK: - Welcome View
    private var welcomeView: some View {
        VStack(spacing: 20) {
            Image(systemName: "crown.fill")
                .font(.system(size: 80))
                .foregroundColor(.yellow.opacity(0.5))

            Text("Enter a Lichess username to replay their last game")
                .font(.title3)
                .foregroundColor(.gray)

            Text("Use the search field above to get started")
                .font(.subheadline)
                .foregroundColor(.gray.opacity(0.7))
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
    }

    // MARK: - Loading View
    private var loadingView: some View {
        VStack(spacing: 20) {
            ProgressView()
                .scaleEffect(1.5)
                .tint(.white)

            Text("Fetching game data...")
                .foregroundColor(.gray)
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
    }

    // MARK: - Game Content
    private func gameContentView(game: ChessGame) -> some View {
        HStack(spacing: 20) {
            // Left panel - Board and controls
            VStack(spacing: 15) {
                // Player info (top - black or white depending on flip)
                playerInfoView(
                    name: boardFlipped ? game.whitePlayer : game.blackPlayer,
                    rating: boardFlipped ? game.whiteRating : game.blackRating,
                    color: boardFlipped ? .white : .black
                )

                // Chess board
                ChessBoardUnicodeView(
                    position: game.position,
                    flipped: boardFlipped,
                    lastMove: game.currentMoveIndex >= 0 ? game.moves[game.currentMoveIndex] : nil
                )
                .frame(maxWidth: 500, maxHeight: 500)

                // Player info (bottom)
                playerInfoView(
                    name: boardFlipped ? game.blackPlayer : game.whitePlayer,
                    rating: boardFlipped ? game.blackRating : game.whiteRating,
                    color: boardFlipped ? .black : .white
                )

                // Navigation controls
                navigationControls(game: game)

                // Move counter
                moveCounterView
            }
            .padding()

            // Right panel - Moves and Analysis
            VStack(spacing: 0) {
                // Game info header
                gameInfoHeader(game: game)

                // Moves list
                movesListView(game: game)

                Divider()
                    .background(Color.white.opacity(0.1))

                // Analysis panel
                analysisPanel
            }
            .frame(width: 320)
            .background(Color(hex: "16213e"))
        }
        .padding()
    }

    // MARK: - Player Info
    private func playerInfoView(name: String, rating: Int?, color: PieceColor) -> some View {
        HStack {
            Circle()
                .fill(color == .white ? Color.white : Color.black)
                .frame(width: 16, height: 16)
                .overlay(
                    Circle()
                        .stroke(Color.gray, lineWidth: 1)
                )

            Text(name)
                .font(.headline)
                .foregroundColor(.white)

            if let rating = rating {
                Text("(\(rating))")
                    .font(.subheadline)
                    .foregroundColor(.gray)
            }

            Spacer()
        }
        .padding(.horizontal)
    }

    // MARK: - Navigation Controls
    private func navigationControls(game: ChessGame) -> some View {
        HStack(spacing: 15) {
            Button(action: { game.goToStart() }) {
                Image(systemName: "backward.end.fill")
            }
            .buttonStyle(ControlButtonStyle())
            .disabled(game.isAtStart)

            Button(action: { _ = game.previousMove() }) {
                Image(systemName: "backward.fill")
            }
            .buttonStyle(ControlButtonStyle())
            .disabled(game.isAtStart)

            Button(action: { _ = game.nextMove() }) {
                Image(systemName: "forward.fill")
            }
            .buttonStyle(ControlButtonStyle())
            .disabled(game.isAtEnd)

            Button(action: { game.goToEnd() }) {
                Image(systemName: "forward.end.fill")
            }
            .buttonStyle(ControlButtonStyle())
            .disabled(game.isAtEnd)

            Spacer()
                .frame(width: 20)

            Button(action: { boardFlipped.toggle() }) {
                Image(systemName: "arrow.up.arrow.down")
            }
            .buttonStyle(ControlButtonStyle())
        }
    }

    // MARK: - Move Counter
    private var moveCounterView: some View {
        HStack {
            if let game = appState.currentGame {
                Text("Move: \(game.currentMoveIndex + 1) / \(game.moves.count)")
                    .font(.subheadline)
                    .foregroundColor(.gray)
            }
        }
    }

    // MARK: - Game Info Header
    private func gameInfoHeader(game: ChessGame) -> some View {
        VStack(alignment: .leading, spacing: 8) {
            Text(game.result)
                .font(.title2)
                .fontWeight(.bold)
                .foregroundColor(.white)

            if !game.opening.isEmpty {
                Text(game.opening)
                    .font(.caption)
                    .foregroundColor(.gray)
            }

            if !game.timeControl.isEmpty {
                HStack {
                    Image(systemName: "clock")
                    Text(game.timeControl)
                }
                .font(.caption)
                .foregroundColor(.gray)
            }
        }
        .frame(maxWidth: .infinity, alignment: .leading)
        .padding()
        .background(Color.black.opacity(0.2))
    }

    // MARK: - Moves List
    private func movesListView(game: ChessGame) -> some View {
        ScrollViewReader { proxy in
            ScrollView {
                LazyVStack(spacing: 2) {
                    ForEach(Array(stride(from: 0, to: game.moves.count, by: 2)), id: \.self) { index in
                        HStack(spacing: 0) {
                            // Move number
                            Text("\(index / 2 + 1).")
                                .font(.system(.caption, design: .monospaced))
                                .foregroundColor(.gray)
                                .frame(width: 35, alignment: .leading)

                            // White's move
                            moveButton(game: game, index: index)

                            // Black's move
                            if index + 1 < game.moves.count {
                                moveButton(game: game, index: index + 1)
                            } else {
                                Spacer()
                                    .frame(width: 80)
                            }

                            Spacer()
                        }
                        .padding(.horizontal, 10)
                        .padding(.vertical, 2)
                    }
                }
                .padding(.vertical, 10)
            }
            .onChange(of: game.currentMoveIndex) { _, newIndex in
                withAnimation {
                    proxy.scrollTo(newIndex, anchor: .center)
                }
                // Trigger analysis
                Task {
                    await appState.engineManager.analyze(fen: game.currentFEN)
                }
            }
        }
    }

    private func moveButton(game: ChessGame, index: Int) -> some View {
        let isActive = game.currentMoveIndex == index
        let move = game.moves[index]

        return Button(action: {
            game.goToMove(index)
        }) {
            Text(move.san)
                .font(.system(.body, design: .monospaced))
                .foregroundColor(isActive ? .white : .gray)
                .padding(.horizontal, 8)
                .padding(.vertical, 4)
                .background(isActive ? Color.blue : Color.clear)
                .cornerRadius(4)
        }
        .buttonStyle(.plain)
        .frame(width: 80, alignment: .leading)
        .id(index)
    }

    // MARK: - Analysis Panel
    private var analysisPanel: some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack {
                Text(appState.engineManager.selectedEngineType.rawValue)
                    .font(.headline)
                    .foregroundColor(.white)

                Spacer()

                Toggle("", isOn: $appState.engineManager.isAnalysisEnabled)
                    .toggleStyle(.switch)
                    .scaleEffect(0.8)
            }

            if appState.engineManager.isAnalysisEnabled {
                if let analysis = appState.engineManager.currentAnalysis {
                    analysisContentView(analysis: analysis)
                } else {
                    Text("Analyzing...")
                        .foregroundColor(.gray)
                        .font(.caption)
                }
            } else {
                Text("Analysis disabled")
                    .foregroundColor(.gray)
                    .font(.caption)
                    .italic()
            }
        }
        .padding()
    }

    private func analysisContentView(analysis: EngineAnalysis) -> some View {
        VStack(alignment: .leading, spacing: 10) {
            // Evaluation bar and score
            HStack(spacing: 15) {
                // Eval bar
                GeometryReader { geo in
                    ZStack(alignment: .bottom) {
                        Rectangle()
                            .fill(Color.black)

                        Rectangle()
                            .fill(Color.white)
                            .frame(height: geo.size.height * analysis.barPercentage / 100)
                    }
                }
                .frame(width: 20, height: 100)
                .cornerRadius(4)

                VStack(alignment: .leading, spacing: 8) {
                    // Score
                    Text(analysis.scoreText)
                        .font(.system(size: 28, weight: .bold, design: .monospaced))
                        .foregroundColor(scoreColor(for: analysis))

                    // Depth
                    HStack {
                        Text("Depth:")
                            .foregroundColor(.gray)
                        Text("\(analysis.depth)")
                            .foregroundColor(.blue)
                            .fontWeight(.semibold)
                    }
                    .font(.caption)

                    // Best move
                    HStack {
                        Text("Best:")
                            .foregroundColor(.gray)
                        Text(analysis.bestMove)
                            .foregroundColor(.blue)
                            .fontWeight(.semibold)
                    }
                    .font(.caption)
                }
            }

            // PV line
            if !analysis.pv.isEmpty {
                Text(analysis.pv.prefix(6).joined(separator: " "))
                    .font(.system(.caption, design: .monospaced))
                    .foregroundColor(.gray.opacity(0.8))
                    .lineLimit(2)
            }
        }
    }

    private func scoreColor(for analysis: EngineAnalysis) -> Color {
        if analysis.mate != nil {
            return .red
        }
        if analysis.score > 0.3 {
            return .white
        } else if analysis.score < -0.3 {
            return .gray
        }
        return .blue
    }

    // MARK: - Actions
    private func fetchGame() {
        guard !username.isEmpty else { return }
        appState.isLoading = true
        appState.errorMessage = nil

        Task {
            do {
                let game = try await appState.lichessClient.fetchLastGame(username: username)
                await MainActor.run {
                    appState.currentGame = game
                    appState.isLoading = false
                }
                // Start analysis
                await appState.engineManager.analyze(fen: game.currentFEN)
            } catch {
                await MainActor.run {
                    appState.errorMessage = error.localizedDescription
                    appState.isLoading = false
                }
            }
        }
    }

}

// MARK: - Button Styles
struct PrimaryButtonStyle: ButtonStyle {
    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .padding(.horizontal, 20)
            .padding(.vertical, 10)
            .background(configuration.isPressed ? Color.blue.opacity(0.7) : Color.blue)
            .foregroundColor(.white)
            .cornerRadius(8)
            .scaleEffect(configuration.isPressed ? 0.98 : 1.0)
    }
}

struct ControlButtonStyle: ButtonStyle {
    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .font(.title2)
            .frame(width: 44, height: 44)
            .background(configuration.isPressed ? Color.white.opacity(0.15) : Color.white.opacity(0.1))
            .foregroundColor(.white)
            .cornerRadius(8)
    }
}

// MARK: - Color Extension
extension Color {
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        let a, r, g, b: UInt64
        switch hex.count {
        case 3: // RGB (12-bit)
            (a, r, g, b) = (255, (int >> 8) * 17, (int >> 4 & 0xF) * 17, (int & 0xF) * 17)
        case 6: // RGB (24-bit)
            (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        case 8: // ARGB (32-bit)
            (a, r, g, b) = (int >> 24, int >> 16 & 0xFF, int >> 8 & 0xFF, int & 0xFF)
        default:
            (a, r, g, b) = (1, 1, 1, 0)
        }
        self.init(
            .sRGB,
            red: Double(r) / 255,
            green: Double(g) / 255,
            blue: Double(b) / 255,
            opacity: Double(a) / 255
        )
    }
}

#Preview {
    ContentView()
        .environmentObject(AppState())
        .frame(width: 1100, height: 750)
}
