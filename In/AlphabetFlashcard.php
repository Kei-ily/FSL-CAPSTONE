<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filipino Sign Language Flashcards</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #0f0f0fff;
        }

        /*header*/

        

        .section-title {
  font-size: 30px;
  font-weight: 700;
  margin: 12px auto;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  text-align: center;

}

.section-description {
  font-size: 18px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto 16px;
  text-align: center;
}

.section-divider {
  width: 96px;
  height: 4px;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  border-radius: 2px;
  margin: 0 auto;
}

.section-divider-v2{
  width: 90vw;
  height: 4px;
  background: linear-gradient(to right, #5a5a5bff, #2c2c2cff);
  border-radius: 2px;
  margin: 10px auto;
}


        .view-buttons {
            display: flex;
            gap: 0.5rem;
            margin: 0 50px;
        }

        .btn-view {
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            background-color: white;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background-color: #f8f9fa;
        }

        .btn-view.active {
            background-color: #212529;
            color: white;
            border-color: #212529;
        }

        
        .btn-icon {
            width: 1rem;
            height: 1rem;
        }

        .container{
            margin: 0 20px;
        }
        .grid-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 50px;
            padding: 1.5rem 0;
        }

        .card {
            position: relative;
            aspect-ratio: 1;
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            padding: 1rem;
            text-align: center;
        }

        .card-label p {
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }

        .edit-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card:hover .edit-btn {
            opacity: 1;
        }

        .flashcard-view {
            display: none;
            min-height: calc(100vh - 80px);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0;
        }

        .flashcard-view.active {
            display: flex;
        }

        .flashcard-container {
            width: 100%;
            max-width: 800px;
        }

        .flashcard {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .flashcard img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .flashcard-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.4), transparent);
        }

        .flashcard-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
        }

        .flashcard-content h2 {
            font-size: 4rem;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .flashcard-content p {
            font-size: 1.125rem;
            color: #cbd5e1;
            text-align: center;
        }

        .flashcard .edit-btn {
            opacity: 1;
        }

        .controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .info {
            text-align: center;
            margin-top: 1rem;
        }

        .info p {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .info .hint {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .modal-header p {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .form-group textarea {
            resize: vertical;
            font-family: inherit;
        }

        .form-group .hint {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .file-upload-group {
            display: flex;
            gap: 0.5rem;
        }

        .file-upload-group input[type="file"] {
            flex: 1;
        }

        .preview-container {
            margin-top: 1rem;
        }

        .preview-image {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            overflow: hidden;
        }

        .preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-outline {
            background-color: white;
            color: #212529;
        }

        .btn-primary {
            background-color: #212529;
            color: white;
            border-color: #212529;
        }

        .btn-primary:hover {
            background-color: #000;
        }

        @media (max-width: 640px) {
            .grid-view {
                grid-template-columns: repeat(2, 1fr);
            }

            .flashcard-content h2 {
                font-size: 3rem;
            }

            .controls {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include "includes/header.php" ?>

    <!-- Container -->
        <div class="container">
            <div class="header-content">
                
                     <h2 class="section-title">ALPHABETS</h2>
          <p class="section-description">
            Tara't matuto ng FSL Alphabets
          </p>
          <div class="section-divider"></div>
                
                <div class="view-buttons">
                    <button class="btn-view active" id="gridViewBtn">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                        </svg>
                        Grid View
                    </button>
                    <button class="btn-view" id="flashcardViewBtn">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        </svg>
                        Flashcard View
                    </button>
                </div>
            </div>
        </div>

         <div class="section-divider-v2"></div>

    <!-- Grid View -->
    <div class="container">
        <div class="grid-view" id="gridView"></div>
    </div>

    <!-- Flashcard View -->
    <div class="flashcard-view" id="flashcardView">
        <div class="container flashcard-container">
            <div class="flashcard" id="flashcard">
                <img id="flashcardImage" src="" alt="">
                <div class="flashcard-overlay"></div>
                <div class="flashcard-content">
                    <h2 id="flashcardLetter"></h2>
                    <p id="flashcardDescription"></p>
                </div>
                <button class="edit-btn" id="flashcardEditBtn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
            </div>
            <div class="controls">
                <button class="btn btn-outline" id="prevBtn">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Undo
                </button>
                <button class="btn btn-outline" id="resetBtn">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Repeat
                </button>
                <button class="btn btn-outline" id="nextBtn">
                    Next
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            <div class="info">
                <p id="cardCounter"></p>
               <!-- <p class="hint">Swipe left for next, swipe right to undo</p> -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Edit Card</h3>
                <p>Customize the image and description for this flashcard.</p>
            </div>
            <div class="form-group">
                <label for="imageUrlInput">Image URL</label>
                <input type="text" id="imageUrlInput" placeholder="https://example.com/image.jpg">
                <p class="hint">Enter a URL to an image showing the sign language gesture</p>
            </div>
            <div class="form-group">
                <label for="fileInput">Or Upload a File</label>
                <div class="file-upload-group">
                    <input type="file" id="fileInput" accept="image/*">
                    <button class="btn btn-outline" onclick="document.getElementById('fileInput').click()">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </button>
                </div>
                <p class="hint">Upload an image file from your device</p>
            </div>
            <div class="form-group">
                <label for="descriptionInput">Description</label>
                <textarea id="descriptionInput" rows="3" placeholder="Filipino Sign Language letter A"></textarea>
            </div>
            <div class="preview-container" id="previewContainer" style="display: none;">
                <label>Preview</label>
                <div class="preview-image">
                    <img id="previewImage" src="/placeholder.svg" alt="Preview">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="closeModalBtn">Exit</button>
                <button class="btn btn-primary" id="saveCardBtn">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        // Default flashcard data
        const defaultCards = [
            { id: '1', letter: 'A', imageUrl: 'Images/flashcardABC/a.png', description: 'Filipino Sign Language letter A' },
            { id: '2', letter: 'B', imageUrl: 'Images/flashcardABC/b.png', description: 'Filipino Sign Language letter B' },
            { id: '3', letter: 'C', imageUrl: 'Images/flashcardABC/c.png', description: 'Filipino Sign Language letter C' },
            { id: '4', letter: 'D', imageUrl: 'Images/flashcardABC/d.png', description: 'Filipino Sign Language letter D' },
            { id: '5', letter: 'E', imageUrl: 'Images/flashcardABC/e.png', description: 'Filipino Sign Language letter E' },
            { id: '6', letter: 'F', imageUrl: 'Images/flashcardABC/f.png', description: 'Filipino Sign Language letter F' },
            { id: '7', letter: 'G', imageUrl: 'Images/flashcardABC/g.png', description: 'Filipino Sign Language letter G' },
            { id: '8', letter: 'H', imageUrl: 'Images/flashcardABC/h.png', description: 'Filipino Sign Language letter H' },
            { id: '9', letter: 'I', imageUrl: 'Images/flashcardABC/i.png', description: 'Filipino Sign Language letter I' },
            { id: '10', letter: 'J', imageUrl: 'Images/flashcardABC/j.png', description: 'Filipino Sign Language letter J' },
            { id: '11', letter: 'K', imageUrl: 'Images/flashcardABC/k.png', description: 'Filipino Sign Language letter K' },
            { id: '12', letter: 'L', imageUrl: 'Images/flashcardABC/l.png', description: 'Filipino Sign Language letter L' },
            { id: '13', letter: 'M', imageUrl: 'Images/flashcardABC/m.png', description: 'Filipino Sign Language letter M' },
            { id: '14', letter: 'N', imageUrl: 'Images/flashcardABC/n.png', description: 'Filipino Sign Language letter N' },
            { id: '15', letter: 'O', imageUrl: 'Images/flashcardABC/o.png', description: 'Filipino Sign Language letter O' },
            { id: '16', letter: 'P', imageUrl: 'Images/flashcardABC/p.png', description: 'Filipino Sign Language letter P' },
            { id: '17', letter: 'Q', imageUrl: 'Images/flashcardABC/q.png', description: 'Filipino Sign Language letter Q' },
            { id: '18', letter: 'R', imageUrl: 'Images/flashcardABC/r.png', description: 'Filipino Sign Language letter R' },
            { id: '19', letter: 'S', imageUrl: 'Images/flashcardABC/s.png', description: 'Filipino Sign Language letter S' },
            { id: '20', letter: 'T', imageUrl: 'Images/flashcardABC/t.png', description: 'Filipino Sign Language letter T' },
            { id: '21', letter: 'U', imageUrl: 'Images/flashcardABC/u.png', description: 'Filipino Sign Language letter U' },
            { id: '22', letter: 'V', imageUrl: 'Images/flashcardABC/v.png', description: 'Filipino Sign Language letter V' },
            { id: '23', letter: 'W', imageUrl: 'Images/flashcardABC/w.png', description: 'Filipino Sign Language letter W' },
            { id: '24', letter: 'X', imageUrl: 'Images/flashcardABC/x.png', description: 'Filipino Sign Language letter X' },
            { id: '25', letter: 'Y', imageUrl: 'Images/flashcardABC/y.png', description: 'Filipino Sign Language letter Y' },
            { id: '26', letter: 'Z', imageUrl: 'Images/flashcardABC/z.png', description: 'Filipino Sign Language letter Z' }
        ];

        // State
        let cards = [];
        let currentView = 'grid';
        let currentIndex = 0;
        let editingCardId = null;
        let touchStartX = 0;

        // Load cards from localStorage or use defaults
        function loadCards() {
            const savedCards = localStorage.getItem('fsl-cards-alphabet');
            cards = savedCards ? JSON.parse(savedCards) : defaultCards;
        }

        // Save cards to localStorage
        function saveCards() {
            localStorage.setItem('fsl-cards-alphabet', JSON.stringify(cards));
        }

        // Render grid view
        function renderGrid() {
            const gridView = document.getElementById('gridView');
            gridView.innerHTML = '';
            
            cards.forEach((card, index) => {
                const cardEl = document.createElement('div');
                cardEl.className = 'card';
                cardEl.innerHTML = `
                    <img src="${card.imageUrl}" alt="${card.description}">
                    <div class="card-label">
                        <p>${card.letter}</p>
                    </div>
                    <button class="edit-btn" data-index="${index}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                `;
                
                cardEl.addEventListener('click', (e) => {
                    if (!e.target.closest('.edit-btn')) {
                        currentIndex = index;
                        switchView('flashcard');
                    }
                });
                
                const editBtn = cardEl.querySelector('.edit-btn');
                editBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openEditModal(card.id);
                });
                
                gridView.appendChild(cardEl);
            });
        }

        // Render flashcard view
        function renderFlashcard() {
            const card = cards[currentIndex];
            document.getElementById('flashcardImage').src = card.imageUrl;
            document.getElementById('flashcardLetter').textContent = card.letter;
            document.getElementById('flashcardDescription').textContent = card.description;
            document.getElementById('cardCounter').textContent = `Card ${currentIndex + 1} of ${cards.length}`;
        }

        // Switch view
        function switchView(view) {
            currentView = view;
            const gridView = document.getElementById('gridView');
            const flashcardView = document.getElementById('flashcardView');
            const gridBtn = document.getElementById('gridViewBtn');
            const flashcardBtn = document.getElementById('flashcardViewBtn');
            
            if (view === 'grid') {
                gridView.style.display = 'grid';
                flashcardView.classList.remove('active');
                gridBtn.classList.add('active');
                flashcardBtn.classList.remove('active');
            } else {
                gridView.style.display = 'none';
                flashcardView.classList.add('active');
                gridBtn.classList.remove('active');
                flashcardBtn.classList.add('active');
                renderFlashcard();
            }
        }

        // Navigation
        function nextCard() {
            currentIndex = (currentIndex + 1) % cards.length;
            renderFlashcard();
        }

        function prevCard() {
            currentIndex = (currentIndex - 1 + cards.length) % cards.length;
            renderFlashcard();
        }

        function resetCard() {
            currentIndex = 0;
            renderFlashcard();
        }

        // Touch events
        function handleTouchStart(e) {
            touchStartX = e.touches[0].clientX;
        }

        function handleTouchEnd(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchStartX - touchEndX;
            const threshold = 75;
            
            if (diff > threshold) {
                nextCard();
            } else if (diff < -threshold) {
                prevCard();
            }
        }

        // Edit modal
        function openEditModal(cardId) {
            editingCardId = cardId;
            const card = cards.find(c => c.id === cardId);
            
            document.getElementById('modalTitle').textContent = `Edit Card - Letter ${card.letter}`;
            document.getElementById('imageUrlInput').value = card.imageUrl;
              document.getElementById('imageUrlInput').value = `Images/flashcardABC/${card.letter}.png`;
            document.getElementById('descriptionInput').value = card.description;
            document.getElementById('fileInput').value = '';
            
            updatePreview(card.imageUrl);
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            editingCardId = null;
            document.getElementById('editModal').classList.remove('active');
        }

        function updatePreview(url) {
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('previewImage');
            
            if (url) {
                previewImage.src = url;
                previewContainer.style.display = 'block';
            } else {
                previewContainer.style.display = 'none';
            }
        }

        function saveCard() {
            const imageUrl = document.getElementById('imageUrlInput').value;
            const description = document.getElementById('descriptionInput').value;
            
            cards = cards.map(card => {
                if (card.id === editingCardId) {
                    return { ...card, imageUrl, description };
                }
                return card;
            });
            
            saveCards();
            renderGrid();
            if (currentView === 'flashcard') {
                renderFlashcard();
            }
            closeEditModal();
        }

        // Event listeners
        document.getElementById('gridViewBtn').addEventListener('click', () => switchView('grid'));
        document.getElementById('flashcardViewBtn').addEventListener('click', () => switchView('flashcard'));
        document.getElementById('nextBtn').addEventListener('click', nextCard);
        document.getElementById('prevBtn').addEventListener('click', prevCard);
        document.getElementById('resetBtn').addEventListener('click', resetCard);
        document.getElementById('flashcardEditBtn').addEventListener('click', () => {
            openEditModal(cards[currentIndex].id);
        });
        document.getElementById('closeModalBtn').addEventListener('click', closeEditModal);
        document.getElementById('saveCardBtn').addEventListener('click', saveCard);
        
        document.getElementById('imageUrlInput').addEventListener('input', (e) => {
            updatePreview(e.target.value);
        });
        
        document.getElementById('fileInput').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    const dataUrl = reader.result;
                    document.getElementById('imageUrlInput').value = dataUrl;
                    updatePreview(dataUrl);
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('flashcard').addEventListener('touchstart', handleTouchStart);
        document.getElementById('flashcard').addEventListener('touchend', handleTouchEnd);
        
        document.getElementById('editModal').addEventListener('click', (e) => {
            if (e.target.id === 'editModal') {
                closeEditModal();
            }
        });

        // Initialize
        loadCards();
        renderGrid();
    </script>
</body>
</html>
