<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookworm Adventures - Sign Language Word Game</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(180deg, #2d5016 0%, #1a2e0a 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .game-container {
            background: linear-gradient(135deg, #8b6f47 0%, #6b5437 100%);
            border-radius: 20px;
            padding: 30px;
            max-width: 1200px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            color: #2d1f0f;
            border: 4px solid #d4a574;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #6b5437 0%, #8b6f47 50%, #6b5437 100%);
            padding: 15px 20px;
            border-radius: 10px;
            border: 2px solid #d4a574;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #f5e6d3;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .stats {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
            background: rgba(245, 230, 211, 0.1);
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid #d4a574;
        }

        .stat-label {
            font-size: 11px;
            color: #d4a574;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #f5e6d3;
        }

        .main-content {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .character-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            min-width: 120px;
        }

        .character-display {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .character-name {
            font-size: 12px;
            color: #d4a574;
            text-align: center;
            font-weight: bold;
        }

        .pet-selection-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 25px 0;
        }

        .pet-card {
            background: rgba(0, 0, 0, 0.2);
            border: 3px solid #d4a574;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            border-color: #4ecdc4;
            box-shadow: 0 10px 20px rgba(78, 205, 196, 0.3);
        }

        .pet-card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .pet-card-title {
            font-size: 16px;
            font-weight: bold;
            color: #f5e6d3;
            margin-bottom: 8px;
        }

        .pet-card-desc {
            font-size: 12px;
            color: #d4a574;
            line-height: 1.4;
        }

        .game-board {
            flex: 1;
            background: linear-gradient(135deg, #3d2a15 0%, #5c4a2f 100%);
            border-radius: 10px;
            padding: 25px;
            border: 3px solid #d4a574;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .board-title {
            font-size: 12px;
            color: #d4a574;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .tile {
            aspect-ratio: 1;
            background: linear-gradient(135deg, #c9a961 0%, #a68a4f 100%);
            border: 3px solid #8b7355;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 20px;
            font-weight: bold;
            position: relative;
            user-select: none;
            color: #2d1f0f;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
        
        }

        .tile:hover {
            transform: scale(1.08) translateY(-3px);
            border-color: #f5e6d3;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .tile.selected {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            border-color: #2d9b8f;
            box-shadow: 0 8px 16px rgba(78, 205, 196, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .tile-letter {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            opacity: 0.8;
        }

        .tile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        
        }
s
        .sidebar {
            width: 280px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .word-section {
            background: linear-gradient(135deg, #3d2a15 0%, #5c4a2f 100%);
            border-radius: 10px;
            padding: 20px;
            border: 2px solid #d4a574;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            font-size: 11px;
            color: #d4a574;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .current-word {
            background: rgba(78, 205, 196, 0.15);
            border: 2px solid #4ecdc4;
            border-radius: 8px;
            padding: 15px;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #f5e6d3;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: 2px solid #d4a574;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            color: #fff;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(78, 205, 196, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-clear {
            background: linear-gradient(135deg, #a68a4f 0%, #8b7355 100%);
            color: #f5e6d3;
        }

        .btn-clear:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-new-game {
            background: linear-gradient(135deg, #d4a574 0%, #c9a961 100%);
            color: #2d1f0f;
            width: 100%;
            margin-top: 10px;
        }

        .btn-new-game:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(212, 165, 116, 0.4);
        }

         .btn-exit-game {
            background: linear-gradient(135deg, #d4a574 0%, #c9a961 100%);
            color: #2d1f0f;
            width: 100%;
            margin-top: 10px;
        }

        .btn-exit-game:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(212, 165, 116, 0.4);
        }


        .high-score-list {
            max-height: 150px;
            overflow-y: auto;
        }

        .high-score-list::-webkit-scrollbar {
            width: 6px;
        }

        .high-score-list::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .high-score-list::-webkit-scrollbar-thumb {
            background: #d4a574;
            border-radius: 3px;
        }

        .score-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            background: rgba(212, 165, 116, 0.15);
            border-radius: 5px;
            margin-bottom: 6px;
            font-size: 12px;
            color: #f5e6d3;
            border-left: 3px solid #d4a574;
        }

        .score-rank {
            font-weight: bold;
            color: #d4a574;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 12px;
            min-height: 20px;
            font-weight: bold;
        }

        .message.success {
            background: rgba(78, 205, 196, 0.3);
            color: #4ecdc4;
            border: 1px solid #4ecdc4;
        }

        .message.error {
            background: rgba(255, 100, 100, 0.3);
            color: #ff6464;
            border: 1px solid #ff6464;
        }

        .words-found-list {
            max-height: 120px;
            overflow-y: auto;
        }

        .words-found-list::-webkit-scrollbar {
            width: 6px;
        }

        .words-found-list::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .words-found-list::-webkit-scrollbar-thumb {
            background: #d4a574;
            border-radius: 3px;
        }

        .word-tag {
            display: inline-block;
            background: rgba(78, 205, 196, 0.2);
            border: 1px solid #4ecdc4;
            color: #4ecdc4;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            margin: 4px 4px 4px 0;
            font-weight: bold;
        }

        .no-words {
            color: #d4a574;
            text-align: center;
            font-size: 12px;
            padding: 10px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(135deg, #8b6f47 0%, #6b5437 100%);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            border: 4px solid #d4a574;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            color: #2d1f0f;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-title {
            font-size: 32px;
            font-weight: bold;
            color: #f5e6d3;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .modal-section {
            margin-bottom: 25px;
        }

        .modal-section-title {
            font-size: 16px;
            font-weight: bold;
            color: #d4a574;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-section-content {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #d4a574;
            color: #f5e6d3;
            line-height: 1.6;
            font-size: 14px;
        }

        .scoring-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(212, 165, 116, 0.3);
        }

        .scoring-item:last-child {
            border-bottom: none;
        }

        .scoring-label {
            color: #d4a574;
            font-weight: bold;
        }

        .scoring-value {
            color: #4ecdc4;
            font-weight: bold;
        }

        .rules-list {
            list-style: none;
            padding: 0;
        }

        .rules-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #f5e6d3;
        }

        .rules-list li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #4ecdc4;
            font-weight: bold;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-modal {
            flex: 1;
            padding: 15px;
            border: 2px solid #d4a574;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
        }

        .btn-start {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            color: #fff;
            margin-bottom:8px;
        }

        .btn-start:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(78, 205, 196, 0.4);
        }
        .btn-back {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            color: #fff;
            margin-bottom:8px;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(78, 205, 196, 0.4);
        }

        @media (max-width: 1024px) {
            .main-content {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .character-section {
                flex-direction: row;
                gap: 15px;
                min-width: auto;
            }

            .modal-content {
                padding: 25px;
            }

            .modal-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>



    <div class="modal-overlay active" id="petSelectionModal">
        <div class="modal-content">
            <div class="modal-title">Choose Your Pet Companion</div>
            <div style="color: #f5e6d3; text-align: center; margin-bottom: 30px; font-size: 14px;">
                Your pet will grow as you earn points! Pick the one you like best.
            </div>
            
            <div class="pet-selection-grid">
                <div class="pet-card" onclick="selectPet('dinosour')">
                    <img src="Images/gamepets/dino_egg.png" alt="Dinosaur">
                    <div class="pet-card-title">Dinosaur</div>
                    <div class="pet-card-desc">Starts as an egg and grows into a majestic dinosaur</div>
                </div>
                <div class="pet-card" onclick="selectPet('spirit')">
                    <img src="Images/gamepets/spirit_egg.png" alt="Forest Spirit">
                    <div class="pet-card-title">Forest Spirit</div>
                    <div class="pet-card-desc">A magical creature that grows stronger and wiser</div>
                </div>
            </div>

            <button class="btn-modal btn-start" style="width: 100%;" onclick="startGameAfterPetSelection()">Continue</button>
             <button class="btn-modal btn-back" style="width: 100%;" onclick="history.back()">Back</button>
        </div>
    </div>

    <div class="modal-overlay" id="instructionsModal">
        <div class="modal-content">
            <div class="modal-title">Sign Adventures</div>
            
            <div class="modal-section">
                <div class="modal-section-title">How to Play</div>
                <div class="modal-section-content">
                    <ul class="rules-list">
                        <li>Click any tiles to form words in any order</li>
                        <li>Words must be at least 3 letters long</li>
                        <li>Each letter tile represents a Sign Language Alphabet letter</li>
                        <li>Use both English and Filipino words</li>
                        <li>After finding a word, the used letters will change to new ones</li>
                        <li>Submit your word to earn points</li>
                    </ul>
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Scoring System</div>
                <div class="modal-section-content">
                    <div class="scoring-item">
                        <span class="scoring-label">3-letter word:</span>
                        <span class="scoring-value">30 points</span>
                    </div>
                    <div class="scoring-item">
                        <span class="scoring-label">4-letter word:</span>
                        <span class="scoring-value">40 points</span>
                    </div>
                    <div class="scoring-item">
                        <span class="scoring-label">5-letter word:</span>
                        <span class="scoring-value">50 points</span>
                    </div>
                    <div class="scoring-item">
                        <span class="scoring-label">6+ letter word:</span>
                        <span class="scoring-value">Word Length × 10</span>
                    </div>
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Tips</div>
                <div class="modal-section-content">
                    <ul class="rules-list">
                        <li>Look for longer words to get more points</li>
                        <li>Letters change after each valid word, creating new opportunities</li>
                        <li>Your high score is saved automatically</li>
                        <li>Try different letter combinations for hidden words</li>
                    </ul>
                </div>
            </div>

            <div class="modal-buttons">
                <button class="btn-modal btn-start" onclick="startGame()">Start Game</button>
            </div>
        </div>
    </div>

    <div class="game-container">
        <div class="header">
            <div class="title">Sign Adventures</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-label">Current Score</div>
                    <div class="stat-value" id="score">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">High Score</div>
                    <div class="stat-value" id="best-score">0</div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="character-section">
                <img id="petImage" class="character-display" src="Images/gamepets/dino_egg.png" alt="Pet">
                <div class="character-name" id="petName">Dinosaur</div>
            </div>

            <div class="game-board">
                <div class="board-title">4x4 Sign Language Grid</div>
                <div class="grid" id="grid"></div>
                <div class="button-group">
                    <button class="btn-submit" id="submit-btn" onclick="submitWord()">Submit Word</button>
                    <button class="btn-clear" id="clear-btn" onclick="clearWord()">Clear</button>
                </div>
                <div class="message" id="message"></div>
            </div>

            <div class="sidebar">
                <div class="word-section">
                    <div class="section-title">Current Word</div>
                    <div class="current-word" id="current-word">-</div>
                </div>

                <div class="word-section">
                    <div class="section-title">Words Found</div>
                    <div id="words-found" class="words-found-list">
                        <div class="no-words">No words yet</div>
                    </div>
                </div>

                <div class="word-section">
                    <div class="section-title">Top Scores</div>
                    <div class="high-score-list" id="high-scores"></div>
                </div>

                <button class="btn-new-game" onclick="newGame()">New Game</button>
                <button class="btn-exit-game" onclick="location.href='fsl.php'">Exit Game</button>
            </div>
        </div>
    </div>

    <script>
       
        
        // TROUBLESHOOTING:
        // - If images don't load, check the file names and case sensitivity
        // - Clear browser cache (Ctrl+Shift+Del) and reload
        // - Check browser console (F12) for 404 errors
        // ============================================
    
        
        // Alphabet with emojis representing hand signs
        const SL_ALPHABET = {
            A: '🤌', B: '✋', C: '👌', D: '☝️',
            E: '✌️', F: '🤟', G: '👉', H: '🖖',
            I: '🤘', J: '👏', K: '🤏', L: '👍',
            M: '✊', N: '✊', O: '⭕', P: '👌',
            Q: '☝️', R: '✌️', S: '✊', T: '☝️',
            U: '✌️', V: '✌️', W: '👋', X: '☝️',
            Y: '🤘', Z: '✌️'
        };

        // English and Filipino words for the game
        const VALID_WORDS = new Set([

/* =========================
   COMMON ENGLISH WORDS
   ========================= */

    'CAT', 'DOG', 'BAT', 'RAT', 'HAT', 'MAT', 'SAT', 'FAT', 'VAT', 'PAT',
            'BIG', 'DIG', 'RIG', 'FIG', 'JIG', 'PIG', 'WIG', 'GIG',
            'BOX', 'FOX', 'HEX', 'SEX', 'WAX', 'TAX', 'MAX', 'LAX',
            'BED', 'FED', 'LED', 'RED', 'WED', 'ZED',
            'BAD', 'DAD', 'FAD', 'GAD', 'HAD', 'LAD', 'MAD', 'PAD', 'RAD', 'SAD', 'TAD', 'WAD',
            'BAN', 'CAN', 'DAN', 'FAN', 'MAN', 'PAN', 'RAN', 'TAN', 'VAN', 'WAN',
            'BAG', 'GAG', 'HAG', 'JAG', 'LAG', 'NAG', 'RAG', 'SAG', 'TAG', 'WAG',
            'BUS', 'PUS', 'GUS',
            'CUP', 'PUP', 'SUP',
            'CAR', 'BAR', 'FAR', 'JAR', 'TAR', 'WAR',
            'AGE', 'AGO', 'AIR', 'ALL', 'AND', 'ANT', 'ANY', 'APE', 'ARC', 'ARE', 'ARK', 'ARM', 'ART', 'ASH', 'ASK', 'ATE',
            'AXE', 'AYE', 'BEE', 'BIT', 'BOW', 'BOY', 'BUS', 'BUT', 'BUY', 'DAM', 'DAY', 'DEN', 'DEW', 'DID', 'DIE', 'DIM',
            'DIP', 'DRY', 'EAR', 'EAT', 'EEL', 'EGG', 'ELF', 'END', 'ERA', 'EVE', 'EYE', 'FEW', 'FLY', 'FOG', 'FOR', 'FUN',
            'GAS', 'GAY', 'GET', 'GNU', 'GOD', 'GOT', 'GUM', 'GUN', 'GUY', 'GYM', 'HAS', 'HAY', 'HEN', 'HER', 'HID', 'HIM',
            'HIS', 'HIT', 'HOT', 'HOW', 'HUG', 'ICE', 'ICY', 'ILL', 'INK', 'INN', 'ION', 'IRE', 'ITS', 'JAM', 'JET', 'JOB',
            'JOG', 'JOY', 'KEY', 'KID', 'LAP', 'LAW', 'LAY', 'LEG', 'LET', 'LID', 'LIE', 'LIP', 'LIT', 'LOG', 'LOT', 'LOW',
            'MAD', 'MAP', 'MAY', 'MEN', 'MET', 'MIX', 'MOB', 'MUD', 'NET', 'NEW', 'NOR', 'NOT', 'NOW', 'NUT', 'OAK', 'OAR',
            'ODD', 'OFF', 'OFT', 'OIL', 'OLD', 'ONE', 'OPT', 'OUR', 'OUT', 'OWE', 'OWL', 'OWN', 'OXO', 'PAY', 'PEA', 'PEN',
            'PET', 'PIN', 'PIT', 'PLY', 'POD', 'POP', 'POT', 'RAG', 'RAM', 'RAN', 'RAP', 'RAY', 'RED', 'REP', 'RID', 'RIM',
            'RIP', 'ROD', 'ROT', 'ROW', 'RUB', 'RUG', 'RUN', 'RUT', 'RYE', 'SAC', 'SAD', 'SAP', 'SAT', 'SAW', 'SAY', 'SEA',
            'SET', 'SEW', 'SHE', 'SHY', 'SIN', 'SIP', 'SIR', 'SIS', 'SIT', 'SIX', 'SKI', 'SKY', 'SOB', 'SON', 'SOT', 'SOW',
            'SOY', 'SPA', 'SPY', 'STY', 'SUM', 'SUN', 'TAB', 'TAD', 'TAG', 'TAN', 'TAP', 'TAR', 'TAT', 'TAX', 'TEA', 'TEN',
            'THE', 'TIC', 'TIE', 'TIN', 'TIP', 'TOE', 'TON', 'TOO', 'TOP', 'TOY', 'TRY', 'TUB', 'TUG', 'TWO', 'URN', 'USE',
            'VAN', 'VAT', 'VET', 'VIA', 'WAD', 'WAG', 'WAR', 'WAS', 'WAX', 'WAY', 'WEB', 'WED', 'WEE', 'WET', 'WHO', 'WHY',
            'WIG', 'WIN', 'WIT', 'WOE', 'WOK', 'WON', 'WOO', 'WOW', 'YAK', 'YAM', 'YAP', 'YAW', 'YEA', 'YES', 'YET', 'YOU',
            'ZAP', 'ZEN', 'ZIG', 'ZIP', 'ZOO',
          
'ABOUT','AFTER','AGAIN','ALWAYS','ANGRY','APPLE','AROUND','ASKING','ATTEND',
'BABY','BACK','BAG','BALANCE','BALL','BANK','BASIC','BEACH','BECAUSE','BEFORE',
'BEGIN','BEHIND','BELIEVE','BEST','BETTER','BIRTHDAY','BOTTLE','BREAD','BROTHER',
'BUILD','BUSINESS','BUY','CALL','CAMERA','CANDY','CAR','CARE','CARRY','CELL',
'CHAIR','CHANGE','CHECK','CHILD','CHOOSE','CHURCH','CLEAN','CLOTHES','COFFEE',
'COLOR','COME','COMPUTER','COOK','COOL','COUNT','CRY','DANCE','DAUGHTER',
'DAY','DEAL','DEAR','DECIDE','DELIVER','DINNER','DOCTOR','DOOR','DREAM',
'DRINK','DRIVE','EARLY','EAT','EMAIL','ENJOY','ENOUGH','EVERY','EXAM',
'EXCITED','EXPLAIN','FACE','FAMILY','FARM','FAST','FATHER','FEEL','FIGHT',
'FIND','FINISH','FOOD','FORGET','FRIEND','FRONT','FRUIT','FUN','FUTURE',
'GAME','GARDEN','GIFT','GIRL','GIVE','GO','GOOD','GREAT','GROUP',
'HAPPY','HARD','HEALTH','HEAR','HELP','HOME','HOUSE','HUNGRY','IMPORTANT',
'INSIDE','JOB','JOIN','JOURNEY','KEEP','KITCHEN','KNOW','LAUGH','LEARN',
'LEAVE','LEFT','LESSON','LIGHT','LISTEN','LITTLE','LIVE','LOOK','LOVE',
'LUNCH','MAKE','MANAGER','MARKET','MEAL','MEET','MONEY','MORNING','MOTHER',
'MOVE','MUSIC','NAME','NEED','NEVER','NIGHT','NOISE','NORMAL','OFFICE',
'ORDER','OUTSIDE','PAPER','PARENT','PARTY','PAY','PEOPLE','PHONE','PLACE',
'PLAN','PLAY','PLEASE','POLICE','PRESENT','PROBLEM','PUT','RAIN','READY',
'RECEIVE','REMEMBER','RENT','REPAIR','REPEAT','REST','RIDE','RIGHT','ROOM',
'RUN','SAFE','SCHOOL','SEE','SELL','SEND','SHARE','SHOP','SLEEP',
'SLOW','SMILE','SON','SPEAK','SPEND','START','STAY','STOP','STORE',
'STUDY','SUNDAY','TABLE','TALK','TASTE','TEACH','TEAM','THANK','THING',
'THINK','TIME','TODAY','TOMORROW','TRAVEL','TRY','TURN','UNDER','UNDERSTAND',
'USE','WAIT','WAKE','WALK','WANT','WASH','WATCH','WATER','WEEK',
'WELCOME','WORK','WRITE','YEAR'


/* =========================
   Extra ENGLISH WORDS
   ========================= */

,'ABILITY','ABOVE','ACCEPT','ACCOUNT','ACTIVITY','ADDRESS','ADULT','ADVICE',
'AFFECT','AGREE','AIRPORT','ALONE','ANIMAL','ANSWER','ANYONE','ANYTHING',
'APARTMENT','APPEAR','APPOINTMENT','ARRIVE','ARTICLE','ARTIST','ASLEEP',
'ASSIGNMENT','ASSIST','ATTENTION','AVAILABLE','AVERAGE','AWARD','AWAY',
'BALCONY','BARBER','BEAUTIFUL','BECOME','BEDROOM','BEHAVIOR','BELONG',
'BESIDE','BETWEEN','BICYCLE','BILL','BLANKET','BLOOD','BOARD','BORROW',
'BOTHER','BRANCH','BREAK','BRING','BROWSER','BUILDING','BURN','BUTTON',
'CALENDAR','CAMP','CANCEL','CAPTAIN','CARD','CAREER','CARPET','CASH',
'CAUSE','CEILING','CENTER','CENTURY','CERTAIN','CHANCE','CHANNEL',
'CHARACTER','CHARGE','CHEAP','CHEER','CHEF','CHEMISTRY','CHOICE',
'CIRCLE','CLASS','CLEAR','CLIENT','CLIMB','CLOSE','CLOUD','COACH',
'COAT','COLLEGE','COMFORT','COMMENT','COMMON','COMMUNITY','COMPANY',
'COMPARE','COMPLETE','CONCERN','CONFIRM','CONNECT','CONSIDER','CONTROL',
'CORNER','CORRECT','COST','COURSE','COVER','CREATE','CREDIT','CULTURE',
'CUSTOMER','DAMAGE','DANGER','DARK','DATA','DEAD','DEEP','DELIVERY',
'DEMAND','DEPARTMENT','DESIGN','DETAIL','DEVELOP','DIFFERENT','DIRECTION',
'DISCUSS','DISTANCE','DIVIDE','DOUBLE','DOWNLOAD','DRAW','DRESS',
'DRIVER','DROP','DURING','EARTH','EASILY','EDUCATION','EFFECT',
'ENERGY','ENGINE','ENTER','ENVIRONMENT','ERROR','EVENT','EVERYONE',
'EXAMPLE','EXERCISE','EXIST','EXPECT','EXPERIENCE','EXTRA','FACTOR',
'FAILED','FAIR','FAMOUS','FARTHER','FEATURE','FEBRUARY','FIELD',
'FINAL','FOLLOW','FOREVER','FORM','FREE','FRESH','FRIDAY',
'GASOLINE','GENERAL','GENTLE','GLOBAL','GROUND','GROW',
'HAPPEN','HEAVY','HISTORY','HOLD','HOLIDAY','HOPE',
'IMAGINE','INCLUDE','INCREASE','INFORMATION','INTEREST','INTERNET',
'INVITE','ISLAND','JANUARY','JULY','JUNE','KEYBOARD',
'LANGUAGE','LARGE','LATE','LEVEL','LIBRARY','LIMIT','LOCAL',
'LOCK','LONG','LOST','LOWER','MAGAZINE','MAIN','MAJOR',
'MEMBER','MESSAGE','MINUTE','MODEL','MONTH','MOUNTAIN',
'NEAR','NEARBY','NOTICE','NUMBER','OBJECT','OFFER',
'ONLINE','OPEN','OPINION','OPTION','ORANGE','OWNER',
'PACKAGE','PAINT','PARTNER','PASSWORD','PEACE',
'PERFECT','PERSON','PHOTO','PICTURE','PILLOW',
'PLATE','POCKET','POINT','POPULAR','POSITION',
'POWER','PRACTICE','PREPARE','PRICE','PRINT',
'PRIVATE','PRODUCT','PROGRAM','PUBLIC','PURCHASE',
'QUALITY','QUESTION','QUICK','QUIET',
'RANDOM','REASON','RECORD','REFRESH','RELAX',
'REPORT','RESULT','RETURN','REVIEW','RIVER',
'SATURDAY','SCIENCE','SEARCH','SECOND','SECTION',
'SECURE','SERIOUS','SERVICE','SEVERAL','SIGN',
'SIMPLE','SINGLE','SKILL','SMART','SPECIAL',
'SPIRIT','SPORT','STAFF','STATION','STATUS',
'STREET','STRONG','STUDENT','SUBJECT','SUCCESS',
'SUPPORT','SYSTEM','TARGET','TEACHER','TECHNOLOGY',
'TEMPERATURE','THURSDAY','TOGETHER','TRAFFIC',
'TRAINING','TRUTH','TUESDAY','UNIVERSITY',
'UPDATE','UPGRADE','URGENT','USER',
'VALUE','VERSION','VIDEO','VISIT','VOICE',
'WAITING','WEATHER','WEBSITE','WEDNESDAY',
'WINDOW','WINNER','WITHOUT','WORLD','WORRY'



/* =========================
   COMMON FILIPINO / TAGLISH WORDS
   ========================= */

     ,'AKO', 'ANG', 'ANI', 'APE', 'ASA', 'ASI', 'ATE', 'AWA', 'AYE',
            'BAA', 'BAG', 'BAI', 'BAL', 'BAM', 'BAN', 'BAO', 'BAP', 'BAR', 'BAS', 'BAT', 'BAW', 'BAX', 'BAY', 'BAZ',
            'BEL', 'BEN', 'BES', 'BET', 'BEY', 'BID', 'BIG', 'BIL', 'BIM', 'BIN', 'BIO', 'BIS', 'BIT', 'BIZ',
            'BOA', 'BOD', 'BOG', 'BOL', 'BON', 'BOO', 'BOP', 'BOS', 'BOT', 'BOW', 'BOX', 'BOY', 'BRO', 'BUG', 'BUM', 'BUN', 'BUS', 'BUT', 'BUY',
            'CAB', 'CAD', 'CAM', 'CAN', 'CAP', 'CAR', 'CAT', 'CAW', 'CAX', 'CAY',
            'COB', 'COD', 'COG', 'COM', 'CON', 'COP', 'COR', 'COS', 'COT', 'COW', 'COX', 'COY', 'COZ', 'CRY', 'CUB', 'CUD', 'CUE', 'CUM', 'CUP', 'CUR', 'CUS', 'CUT',
            'DAB', 'DAD', 'DAG', 'DAL', 'DAM', 'DAN', 'DAP', 'DAS', 'DAT', 'DAW', 'DAX', 'DAY',
            'DEN', 'DEP', 'DES', 'DET', 'DEW', 'DEX', 'DEY', 'DID', 'DIG', 'DIM', 'DIN', 'DIP', 'DIS', 'DIT', 'DIV', 'DOC', 'DOE', 'DOG', 'DOM', 'DON', 'DOR', 'DOS', 'DOT', 'DOW', 'DOX', 'DOZ', 'DRY', 'DUB', 'DUD', 'DUE', 'DUG', 'DUN', 'DUO', 'DUP', 'DYE',
            'EAR', 'EAT', 'EEL', 'EGG', 'EGO', 'EKE', 'ELD', 'ELF', 'ELK', 'ELL', 'ELM', 'ELS', 'EME', 'EMS', 'EMU', 'END', 'ENE', 'ENG', 'ENS', 'EON', 'ERA', 'ERE', 'ERG', 'ERN', 'ERR', 'ERS', 'ESS', 'ETA', 'ETH', 'EVE', 'EWE', 'EYE',
            'FAB', 'FAD', 'FAG', 'FAN', 'FAR', 'FAS', 'FAT', 'FAX', 'FAY', 'FED', 'FEE', 'FEH', 'FEM', 'FEN', 'FER', 'FES', 'FET', 'FEU', 'FEW', 'FEZ', 'FIB', 'FID', 'FIE', 'FIG', 'FIN', 'FIR', 'FIT', 'FIX', 'FIZ', 'FLU', 'FLY', 'FOB', 'FOE', 'FOG', 'FOH', 'FOL', 'FOP', 'FOR', 'FOU', 'FOX', 'FOY', 'FRY', 'FUB', 'FUD', 'FUG', 'FUN', 'FUR',
            'GAB', 'GAD', 'GAG', 'GAL', 'GAM', 'GAN', 'GAP', 'GAR', 'GAS', 'GAT', 'GAY', 'GED', 'GEE', 'GEL', 'GEM', 'GEN', 'GET', 'GHI', 'GIB', 'GID', 'GIE', 'GIG', 'GIN', 'GIP', 'GIT', 'GNU', 'GOA', 'GOB', 'GOD', 'GOO', 'GOR', 'GOS', 'GOT', 'GOX', 'GOY', 'GOZ', 'GUL', 'GUM', 'GUN', 'GUP', 'GUS', 'GUT', 'GUV', 'GUY', 'GYM', 'GYP',
            'HAD', 'HAE', 'HAG', 'HAH', 'HAJ', 'HAM', 'HAO', 'HAP', 'HAS', 'HAT', 'HAW', 'HAX', 'HAY', 'HEH', 'HEM', 'HEN', 'HEP', 'HER', 'HES', 'HET', 'HEW', 'HEX', 'HEY', 'HIC', 'HID', 'HIE', 'HIM', 'HIN', 'HIP', 'HIS', 'HIT', 'HMM', 'HOB', 'HOD', 'HOE', 'HOG', 'HOM', 'HON', 'HOP', 'HOS', 'HOT', 'HOW', 'HOX', 'HOY', 'HUB', 'HUE', 'HUG', 'HUH', 'HUM', 'HUN', 'HUP', 'HUT', 'HYP',
            'IAMB', 'ICE', 'ICH', 'ICK', 'ICY', 'IDE', 'IDS', 'IFF', 'IFS', 'IGG', 'ILK', 'ILL', 'IMP', 'INK', 'INN', 'INS', 'ION', 'IRE', 'IRK', 'ISH', 'ISM', 'ITS', 'IVY',
            'JAB', 'JAG', 'JAM', 'JAR', 'JAW', 'JAX', 'JAY', 'JEE', 'JET', 'JEU', 'JEW', 'JIB', 'JIG', 'JIN', 'JOB', 'JOE', 'JOG', 'JOT', 'JOW', 'JOX', 'JOY', 'JUG', 'JUN', 'JUS', 'JUT',
            'KAB', 'KAE', 'KAF', 'KAS', 'KAT', 'KAY', 'KEA', 'KED', 'KEF', 'KEG', 'KEN', 'KEP', 'KET', 'KEY', 'KHI', 'KID', 'KIE', 'KIF', 'KIN', 'KIP', 'KIR', 'KIS', 'KIT', 'KOA', 'KOB', 'KOI', 'KOP', 'KOR', 'KOS', 'KUE', 'KYE',
            'LAB', 'LAC', 'LAD', 'LAG', 'LAM', 'LAP', 'LAR', 'LAS', 'LAT', 'LAV', 'LAW', 'LAX', 'LAY', 'LEA', 'LED', 'LEE', 'LEG', 'LEI', 'LEK', 'LES', 'LET', 'LEU', 'LEV', 'LEX', 'LEY', 'LEZ', 'LID', 'LIE', 'LIN', 'LIP', 'LIS', 'LIT', 'LOG', 'LOO', 'LOP', 'LOS', 'LOT', 'LOW', 'LOX', 'LUG', 'LUM', 'LUV', 'LUX', 'LYE',
            'MAA', 'MAC', 'MAD', 'MAE', 'MAG', 'MAL', 'MAM', 'MAN', 'MAP', 'MAR', 'MAS', 'MAT', 'MAW', 'MAX', 'MAY', 'MED', 'MEL', 'MEM', 'MEN', 'MET', 'MEW', 'MHO', 'MIB', 'MIC', 'MID', 'MIG', 'MIL', 'MIM', 'MIR', 'MIS', 'MIX', 'MOA', 'MOB', 'MOC', 'MOD', 'MOG', 'MOL', 'MOM', 'MON', 'MOO', 'MOP', 'MOR', 'MOS', 'MOT', 'MOW', 'MOX', 'MOY', 'MOZ', 'MUD', 'MUG', 'MUM', 'MUN', 'MUS', 'MUT',
            'NAB', 'NAE', 'NAG', 'NAH', 'NAM', 'NAN', 'NAP', 'NAR', 'NAS', 'NAT', 'NAW', 'NAX', 'NAY', 'NEB', 'NEE', 'NEG', 'NEP', 'NET', 'NEW', 'NIB', 'NID', 'NIE', 'NIL', 'NIM', 'NIP', 'NIT', 'NIX', 'NOB', 'NOD', 'NOG', 'NOH', 'NOM', 'NOO', 'NOP', 'NOR', 'NOS', 'NOT', 'NOW', 'NOX', 'NOY', 'NOZ', 'NUB', 'NUN', 'NUS', 'NUT',
            'OAF', 'OAK', 'OAR', 'OAS', 'OAT', 'OBA', 'OBE', 'OBI', 'OCA', 'OCH', 'ODA', 'ODD', 'ODE', 'ODS', 'OES', 'OFF', 'OFT', 'OHM', 'OHO', 'OHS', 'OIK', 'OIL', 'OKA', 'OKE', 'OLD', 'OLE', 'OMS', 'ONE', 'ONO', 'ONS', 'OOH', 'OOT', 'OOZ', 'OPE', 'OPS', 'OPT', 'ORA', 'ORB', 'ORC', 'ORE', 'ORS', 'ORT', 'OSE', 'OUD', 'OUR', 'OUS', 'OUT', 'OVA', 'OWE', 'OWL', 'OWN', 'OXO', 'OXY', 'OYE', 'OYS',
            'PAC', 'PAD', 'PAH', 'PAL', 'PAM', 'PAN', 'PAP', 'PAR', 'PAS', 'PAT', 'PAW', 'PAX', 'PAY', 'PEA', 'PEC', 'PED', 'PEE', 'PEG', 'PEH', 'PEN', 'PEP', 'PER', 'PES', 'PET', 'PEW', 'PHI', 'PHO', 'PHT', 'PIA', 'PIC', 'PID', 'PIE', 'PIG', 'PIN', 'PIP', 'PIS', 'PIT', 'PIU', 'PIX', 'PLY', 'POD', 'POH', 'POI', 'POL', 'POM', 'POO', 'POP', 'POS', 'POT', 'POW', 'POX', 'PRO', 'PRY', 'PSI', 'PST', 'PUB', 'PUD', 'PUG', 'PUL', 'PUN', 'PUP', 'PUR', 'PUS', 'PUT',
            'PYA', 'PYE', 'PYX'

,'AKO','IKAW','SIYA','TAYO','KAYO','SILA',
'BAHAY','KWARTO','SALA','KUSINA','BANYO','BUBONG','PADER','PINTUAN','BINTANA',
'KAIN','INOM','TULOG','GISING','LIGO','HILAMOS','SUOT','HUBAD',
'BAON','ULAM','KANIN','ISDA','MANOK','BABOY','GULAY','TINAPAY','GATAS',
'TUBIG','KAPE','ASUKAL','ALMUSAL','TANGHALI','HAPUNAN','MERYENDA',
'NANAY','TATAY','ATE','KUYA','LOLO','LOLA','PAMILYA','ANAK','KAPATID',
'KAIBIGAN','KAKLASE','KATRABAHO','GURO','BOSS','TRABAHO','OPISINA',
'ESKWELA','ARAL','PROYEKTO','TAKDANG','PAGSUSULIT','MARKA',
'GALA','LAKAD','SAKAY','BABA','AKYAT','PASOK','LABAS',
'BILI','BENTA','BAYAD','UTANG','PISO','SALAPI','PERA','SAHOD',
'PALENGKE','TINDERO','TINDERA','MAMIMILI','RESIBO',
'SAYA','LUNGKOT','GALIT','TAKOT','HIYA','KILIG','PAGOD','ANTOK',
'INIT','LAMIG','ULAN','ARAW','HANGIN','BAGYO',
'OO','HINDI','SIGE','TEKA','BAKIT','PAANO','KAILAN','SAAN',
'MAHAL','ALAGA','TULONG','INTINDI','PATAWAD','SALAMAT',
'TEXT','CHAT','LOAD','WIFI','CELLPHONE','TUMAWAG','MENSAHE',
'DRIVER','PASAHERO','JEEP','TRICYCLE','BUS','TREN',
'PULIS','DOKTOR','NARS','SIMBAHAN',
'BIRTHDAY','HANDOG','REGALO','HANDaan',
'PAGKAIN','LARUAN','LARO','PALARO',
'PAGLUTO','PRITO','PAKULO','IHaw',
'LINIS','WALIS','HUGAS','PLANTS','HALAMAN',
'BUKAS','NGAYON','KAHAPON','BUWAN','TAON',
'PANGARAP','PLANO','DISKARTE','SIPAG','TIYAGA','TRUE'


/* ======================
   EXTRA FILIPINO / TAGLISH WORDS
   ====================== */

,'ABALA','ABOT','ADLAW','AGAD','AGOS','AKALA','ALAGAAN','ALIS',
'AMBA','AMBAG','AMPON','ANDAR','ANIM','APAT','ARAWAN','ASENSO',
'ASIKASO','ASIN','ASO','ATIN','BABA','BABAE','BAGONG','BAHAGI',
'BAHAYAN','BAKITAN','BALIK','BALITA','BANGKO','BARKADA','BASA',
'BASURA','BATA','BAYANI','BENTAHA','BILANG','BILIS','BISITA',
'BUHAY','BUKOD','BUNDOK','BUNTIS','BUSOG','BUTIL',
'DAGAT','DAHAN','DAHIL','DALAWA','DALO','DAPAT','DARATING',
'DASAL','DATING','DILIM','DISKARTE','DULO','DUMATING',
'GABI','GABAY','GAMIT','GAMOT','GAPOS','GASTOS','GINTO',
'GISINGAN','GULONG','GUSTO','HABANG','HABOL','HALAGA',
'HALAMANAN','HANAP','HANDOGAN','HAPAG','HARAP','HATID',
'HILING','HINTO','HUGOT','HULI','HUSAY',
'IBIG','IBIGAY','IBON','IGLESIA','IHATID',
'ILAW','ILOG','ISIP','ISLANDIA','IWAN',
'KAALAMAN','KABUHAYAN','KAHON','KAHULUGAN',
'KAIBIGANAN','KAKAYAHAN','KALABAN','KALENDARYO',
'KALIDAD','KALINGA','KALYE','KAMAY','KAMUSTA',
'KANTA','KAPAL','KAPANGYARIHAN','KAPATIRAN',
'KAPAYAPAAN','KAPIT','KARAMDAMAN','KARAPATAN',
'KARNE','KASAMA','KASAL','KASALANAN','KASAPI',
'KASAYSAYAN','KASO','KATOTOHANAN','KATULONG',
'KATAPATAN','KATAWAN','KATAYUAN','KAUSAP',
'KAWAWA','KAWANI','KAWAN','KILALA','KILOS',
'KITAAN','KOMERSYO','KOMUNIDAD','KONTROL',
'KULANG','KULAY','KUMAIN','KUMUSTA',
'LABAN','LABASAN','LAGI','LAHAT','LAHOK',
'LAKI','LANG','LANGIT','LAPIT','LARAWAN',
'LAWAK','LIHAM','LIKOD','LIPAT','LIPUNAN',
'LUNES','LUPA','LUTUIN','MAAGA','MAHALAGA',
'MAHIRAP','MALAKAS','MALAKI','MALAMAN',
'MALAPIT','MALAYO','MALI','MALINIS',
'MANGYARI','MARAMI','MARTES','MASAYA',
'MAYAMAN','MAYBE','MINSAN','MULI',
'NABASA','NAGALIT','NAGLAKAD','NAKITA',
'NARINIG','NARITO','NASAAN','NGITI',
'NGAYON','NIYOG','OBLIGASYON','ORAS',
'PABOR','PAGASA','PAGLALAKBAY','PAGMAMAHAL',
'PAGTULONG','PAHIRAM','PAKIKIPAG','PAKITANG',
'PALA','PALAD','PANGALAN','PANGARAPAN',
'PANGINOON','PANGYAYARI','PANINIWALA',
'PANTAY','PAPEL','PARAAN','PARANG',
'PARTE','PASALAMAT','PASOKAN','PATAAS',
'PATALIM','PATAWARIN','PATULOY',
'PERAHAN','PILIPINAS','PILIPINO',
'PINAGARALAN','PINAKAMALAKI','PINAKAUNA',
'PLATAPORMA','POBLASYON','PROBLEMA',
'PROBINSYA','PRODUKTO','PROGRAMA',
'REHIYON','RESPETO','RIZAL','SABADO',
'SABAY','SAKALING','SALITA','SALOOBIN',
'SAMAHAN','SANHI','SARILI','SARAP',
'SARILIIN','SEMANa','SERBISYO',
'SIGURADO','SILANGAN','SISTEMA',
'SITWASYON','SOBRA','SULAT','SUMAMA',
'SUMUSUNOD','SUNOD','SUPORTA',
'TABANG','TAHANAN','TAHIMIK',
'TALAGA','TANONG','TAPOS','TATLO',
'TATAG','TATLONG','TAYUAN',
'TIWALA','TULONGAN','TUMAWA',
'TUNAY','TUNGKOL','TUPAD',
'UGALI','UNAHAN','UNANG','UNLAD',
'UTOS','WAGAS','WASTO','YAMAN'

]);


        let gameState = {
            letters: [],
            selectedTiles: [],
            score: 0,
            wordsFound: [],
            gameBoard: [],
            selectedPet: localStorage.getItem('selectedPet') || ' dinosour',
            petStage: 'egg' // egg, kid, teen, adult
        };

        const PET_STAGES = {
            egg: { minScore: 0, maxScore: 99 },
            kid: { minScore: 100, maxScore: 299 },
            teen: { minScore: 300, maxScore: 599 },
            adult: { minScore: 600, maxScore: Infinity }
        };

        const PET_IMAGES = {
            dinosour: {
                egg: 'Images/gamepets/dino_egg.png',
                kid: 'Images/gamepets/dino_kid.png',
                teen: 'Images/gamepets/dino_teen.png',
                adult: 'Images/gamepets/dino_adult.png'
            },
            spirit: {
                egg: 'Images/gamepets/spirit_egg.png',
                kid: 'Images/gamepets/spirit_kid.png',
                teen: 'Images/gamepets/spirit_teen.png',
                adult: 'Images/gamepets/spirit_adult.png'
            }
        };

        const PET_NAMES = {
            dinosour: 'Dinosaur',
            spirit: 'Forest Spirit'
        };

        function initGame() {
            loadHighScores();
            // Pet selection modal will show first
            gameState.selectedPet = localStorage.getItem('selectedPet') || 'dinosour';
        }

        function selectPet(petType) {
            gameState.selectedPet = petType;
            localStorage.setItem('selectedPet', petType);
            document.querySelectorAll('.pet-card').forEach(card => {
                card.style.borderColor = '#d4a574';
            });
            event.currentTarget.style.borderColor = '#4ecdc4';
        }

        function startGameAfterPetSelection() {
            if (!gameState.selectedPet) {
                alert('Please select a pet first!');
                return;
            }
            document.getElementById('petSelectionModal').classList.remove('active');
            document.getElementById('instructionsModal').classList.add('active');
        }

        function updatePetDisplay() {
            const pet = gameState.selectedPet;
            const stage = gameState.petStage;
            const petImage = document.getElementById('petImage');
            const petNameDisplay = document.getElementById('petName');

            if (petImage && PET_IMAGES[pet] && PET_IMAGES[pet][stage]) {
                petImage.src = PET_IMAGES[pet][stage];
            }
            if (petNameDisplay) {
                petNameDisplay.textContent = PET_NAMES[pet];
            }
        }

        function updatePetStage() {
            const score = gameState.score;
            let newStage = 'egg';

            if (score >= 600) newStage = 'adult';
            else if (score >= 300) newStage = 'teen';
            else if (score >= 100) newStage = 'kid';
            else newStage = 'egg';

            if (newStage !== gameState.petStage) {
                gameState.petStage = newStage;
                updatePetDisplay();
            }
        }

        function startGame() {
            // Hide the instructions modal
            document.getElementById('instructionsModal').classList.remove('active');
            // Reset game state without showing modal again
            gameState = {
                letters: [],
                selectedTiles: [],
                score: 0,
                wordsFound: [],
                gameBoard: generateRandomBoard(),
                selectedPet: gameState.selectedPet,
                petStage: 'egg'
            };
            renderBoard();
            updateCurrentWord();
            updateWordsList();
            updatePetDisplay();
            document.getElementById('message').textContent = '';
            document.getElementById('current-word').textContent = '-';
        }

        function generateRandomBoard() {
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            const board = [];
            for (let i = 0; i < 16; i++) {
                board.push(letters[Math.floor(Math.random() * letters.length)]);
            }
            return board;
        }

        function renderBoard() {
            const gridElement = document.getElementById('grid');
            gridElement.innerHTML = '';
            gameState.gameBoard.forEach((letter, index) => {
                const tile = document.createElement('div');
                tile.className = 'tile';
                if (gameState.selectedTiles.includes(index)) {
                    tile.classList.add('selected');
                }
                const img = document.createElement('img');
                img.className = 'tile-image';
                img.alt=`sign Letter ${letter}`;

                const basepath = `Images/gameCardsABC/${letter}`;
                img.src = `${basepath}.PNG`;

                img.onerror = function() {
                   this.onerror = null;
                   this.src = `${basepath}.GIF`;
                };
               
                
                tile.onclick = () => selectTile(index);
                tile.appendChild(img);
                gridElement.appendChild(tile);
            });
        }


        function selectTile(index) {
            if (gameState.selectedTiles.includes(index)) {
                gameState.selectedTiles.pop();
            } else {
                // No adjacent rule - users can freely choose any letters
                gameState.selectedTiles.push(index);
            }
            updateCurrentWord();
            renderBoard();
        }

        function updateCurrentWord() {
            const word = gameState.selectedTiles.map(i => gameState.gameBoard[i]).join('');
            document.getElementById('current-word').textContent = word || '-';
        }

        function submitWord() {
            const word = gameState.selectedTiles.map(i => gameState.gameBoard[i]).join('');

            if (word.length < 3) {
                showMessage('Word must be at least 3 letters!', 'error');
                return;
            }

            if (!VALID_WORDS.has(word.toUpperCase())) {
                showMessage('Not a valid word!', 'error');
                return;
            }

            if (gameState.wordsFound.includes(word)) {
                showMessage('Already found!', 'error');
                return;
            }

            gameState.wordsFound.push(word);
            const points = word.length * 10;
            gameState.score += points;
            document.getElementById('score').textContent = gameState.score;

            showMessage(`Great! +${points} points!`, 'success');
            updateWordsList();
            
            // Update pet growth stage
            updatePetStage();
            
            // Change the used letter tiles to new letters
            changeTiles(gameState.selectedTiles);
            
            clearWord();
            saveHighScore();
        }

        function changeTiles(indices) {
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            indices.forEach(index => {
                gameState.gameBoard[index] = letters[Math.floor(Math.random() * letters.length)];
            });
            renderBoard();
        }

        function clearWord() {
            gameState.selectedTiles = [];
            updateCurrentWord();
            renderBoard();
            document.getElementById('message').textContent = '';
        }

        function showMessage(text, type) {
            const msg = document.getElementById('message');
            msg.textContent = text;
            msg.className = `message ${type}`;
        }

        function updateWordsList() {
            const container = document.getElementById('words-found');
            if (gameState.wordsFound.length === 0) {
                container.innerHTML = '<div class="no-words">No words yet</div>';
            } else {
                container.innerHTML = gameState.wordsFound.map(w => 
                    `<div class="word-tag">${w}</div>`
                ).join('');
            }
        }

        function newGame() {
            gameState = {
                letters: [],
                selectedTiles: [],
                score: 0,
                wordsFound: [],
                gameBoard: generateRandomBoard()
            };
            renderBoard();
            updateCurrentWord();
            updateWordsList();
            document.getElementById('message').textContent = '';
            document.getElementById('current-word').textContent = '-';
        }

        function saveHighScore() {
            let scores = JSON.parse(localStorage.getItem('slScores')) || [];
            scores.push({
                score: gameState.score,
                date: new Date().toLocaleDateString(),
                words: gameState.wordsFound.length
            });
            scores.sort((a, b) => b.score - a.score);
            scores = scores.slice(0, 10);
            localStorage.setItem('slScores', JSON.stringify(scores));
            loadHighScores();
        }

        function loadHighScores() {
            let scores = JSON.parse(localStorage.getItem('slScores')) || [];
            const bestScore = scores.length > 0 ? scores[0].score : 0;
            document.getElementById('best-score').textContent = bestScore;

            const container = document.getElementById('high-scores');
            if (scores.length === 0) {
                container.innerHTML = '<div style="color: #aaa; text-align: center; padding: 20px;">No scores yet</div>';
            } else {
                container.innerHTML = scores.map((s, i) => 
                    `<div class="score-item"><span class="score-rank">#${i+1}</span> <span>${s.score} pts</span> <span style="font-size: 12px; color: #999;">${s.date}</span></div>`
                ).join('');
            }
        }

        window.addEventListener('load', initGame);
    </script>
</body>
</html>
