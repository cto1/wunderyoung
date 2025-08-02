document.addEventListener('DOMContentLoaded', () => {
    // --- State Management ---
    let selectedLanguage = null;
    let selectedLevel = null;
    let selectedTopics = [];

    // --- Element References ---
    const searchInput = document.getElementById('language-search-input');
    const languageCards = document.querySelectorAll('.language-card');
    const noResultsMessage = document.getElementById('no-results-message');
    const levelCards = document.querySelectorAll('.level-card');
    const topicForm = document.getElementById('topic-form');
    const topicInput = document.getElementById('topic-input');
    const addTopicBtn = document.getElementById('add-topic-btn');
    const topicListContainer = document.getElementById('topic-list-container');
    const topicErrorMessage = document.getElementById('topic-error-message');
    const createWorkbookBtn = document.getElementById('create-workbook-btn');

    // --- 1. Language Selection & Filtering ---
    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase().trim();
            let visibleCount = 0;
            languageCards.forEach(card => {
                const languageName = card.dataset.name.toLowerCase();
                if (languageName.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            if (noResultsMessage) {
                noResultsMessage.classList.toggle('hidden', visibleCount > 0);
            }
        });
    }
    languageCards.forEach(card => {
        card.addEventListener('click', (event) => {
            event.preventDefault();
            selectedLanguage = card.dataset.name;
            languageCards.forEach(c => c.classList.remove('ring-2', 'ring-primary', 'scale-105'));
            card.classList.add('ring-2', 'ring-primary', 'scale-105');
        });
    });

    // --- 2. Level Selection ---
    levelCards.forEach(card => {
        card.addEventListener('click', () => {
            selectedLevel = card.dataset.level;
            levelCards.forEach(c => c.classList.remove('ring-2', 'ring-primary', 'scale-105'));
            card.classList.add('ring-2', 'ring-primary', 'scale-105');
        });
    });

    // --- 3. Topic Selection ---
    const updateTopicList = () => {
        if (!topicListContainer) return;
        topicListContainer.innerHTML = '';
        if (selectedTopics.length === 0) {
            topicListContainer.innerHTML = `<div class="flex items-center justify-center h-full text-base-content/50 text-sm p-4"><?= __("generator_step3_no_topics"); ?></div>`;
        }
        selectedTopics.forEach((topic, index) => {
            const topicElement = document.createElement('div');
            topicElement.className = 'flex items-center justify-between rounded-md border border-primary bg-base-100/50 p-3 text-base-content';
            topicElement.innerHTML = `<span>${topic}</span><button class="remove-topic-btn text-base-content/50 hover:text-error" data-index="${index}"><i class="fa-solid fa-xmark"></i></button>`;
            topicListContainer.appendChild(topicElement);
        });
        document.querySelectorAll('.remove-topic-btn').forEach(btn => {
            btn.addEventListener('click', (event) => {
                const indexToRemove = parseInt(event.currentTarget.dataset.index, 10);
                selectedTopics.splice(indexToRemove, 1);
                updateTopicList();
            });
        });
    };
    
    const showTopicError = (message) => {
        let existingError = document.getElementById('topic-error-message');
        if (existingError) existingError.remove();
        
        const errorElement = document.createElement('p');
        errorElement.id = 'topic-error-message';
        errorElement.className = 'text-sm text-error h-5 sm:max-w-md -mt-2';
        errorElement.textContent = message;
        topicForm.insertAdjacentElement('afterend', errorElement);
        
        setTimeout(() => { errorElement.remove(); }, 3000);
    };

    if (topicForm) {
        topicInput.addEventListener('input', () => {
            const topic = topicInput.value.trim();
            addTopicBtn.disabled = (topic === '');
        });
        
        topicForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const topic = topicInput.value.trim();
            if (topic && selectedTopics.length < 3 && !selectedTopics.includes(topic)) {
                selectedTopics.push(topic);
                updateTopicList();
                topicInput.value = '';
                addTopicBtn.disabled = true;
            } else if (selectedTopics.length >= 3) {
                showTopicError('You can add a maximum of 3 topics.');
            }
        });
    }

    // --- 4. Final Submission ---
    if (createWorkbookBtn) {
        createWorkbookBtn.addEventListener('click', () => {
            console.log('--- Workbook Generation Request ---');
            console.log('Selected Language:', selectedLanguage);
            console.log('Selected Level:', selectedLevel);
            console.log('Selected Topics:', selectedTopics);
        });
    }

    // --- Initial Render ---
    updateTopicList();
    if (addTopicBtn) {
        addTopicBtn.disabled = true;
    }
});