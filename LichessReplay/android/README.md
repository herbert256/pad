# Lichess Replay - Android

An Android application to replay Lichess games with Stockfish analysis.

## Features

- Fetch the last game of any Lichess user
- Interactive chess board with move navigation
- Auto-play with adjustable speed
- Stockfish engine integration for position analysis
- Move list with clickable navigation
- Flip board option
- Dark theme matching the web version

## Requirements

- Android Studio Hedgehog (2023.1.1) or newer
- Android SDK 34
- Kotlin 1.9.22
- Minimum Android API 26 (Android 8.0)

## Setup

1. Open the `android` folder in Android Studio
2. Wait for Gradle sync to complete
3. Build and run on an emulator or device

## Stockfish Setup

The app uses Stockfish for chess analysis. To enable Stockfish:

1. Download the Stockfish Android binary from https://stockfishchess.org/download/
2. Extract the appropriate binary for your target architecture (arm64-v8a, armeabi-v7a, x86, x86_64)
3. Place the binary in `app/src/main/assets/stockfish`

Without Stockfish, the app will still work but analysis will be disabled.

### Building Stockfish for Android

If you need to build Stockfish yourself:

```bash
# Clone Stockfish
git clone https://github.com/official-stockfish/Stockfish.git
cd Stockfish/src

# Build for Android (requires Android NDK)
# For arm64-v8a:
make ARCH=armv8 COMP=ndk build

# For armeabi-v7a:
make ARCH=armv7 COMP=ndk build
```

## Project Structure

```
android/
├── app/
│   ├── src/main/
│   │   ├── java/com/lichessreplay/
│   │   │   ├── chess/          # Chess logic (board, moves, PGN parser)
│   │   │   ├── data/           # Lichess API and repository
│   │   │   ├── stockfish/      # Stockfish engine integration
│   │   │   ├── ui/             # Compose UI components
│   │   │   │   ├── theme/      # Material 3 theme
│   │   │   │   ├── ChessBoardView.kt
│   │   │   │   ├── GameScreen.kt
│   │   │   │   └── GameViewModel.kt
│   │   │   └── MainActivity.kt
│   │   ├── assets/             # Stockfish binary goes here
│   │   └── res/                # Android resources
│   └── build.gradle.kts
├── gradle/
│   └── libs.versions.toml      # Version catalog
├── build.gradle.kts
├── settings.gradle.kts
└── README.md
```

## Architecture

- **UI Layer**: Jetpack Compose with Material 3
- **ViewModel**: Manages game state and coordinates between UI and data layers
- **Repository**: Handles Lichess API communication
- **Chess Engine**: Custom chess logic for move validation and board state
- **Stockfish**: Native process communication via UCI protocol

## API Usage

The app uses the public Lichess API:
- Endpoint: `https://lichess.org/api/games/user/{username}?max=1&pgnInJson=true`
- No authentication required for public games

## License

MIT License

## Credits

- [Lichess](https://lichess.org) for the game data API
- [Stockfish](https://stockfishchess.org) for the chess engine
- Chess piece unicode symbols from the Unicode Standard
