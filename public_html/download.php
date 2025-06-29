<?php 
$page_title = "Download Worksheet - Yes Homework";
$page_description = "Download your child's personalized worksheet";
include 'website/include/header.html'; 

// Get token from URL
$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo '<div class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center p-4">';
    echo '<div class="text-center">';
    echo '<i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>';
    echo '<h1 class="text-2xl font-bold text-gray-900 mb-2">Invalid Download Link</h1>';
    echo '<p class="text-gray-600">This download link is missing or invalid.</p>';
    echo '</div></div>';
    include 'website/include/footer.html';
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-4xl mx-auto">
        
        <!-- Loading State -->
        <div id="loading-state" class="flex items-center justify-center min-h-96">
            <div class="text-center">
                <div class="loading loading-spinner loading-lg text-primary"></div>
                <p class="mt-4 text-gray-600">Loading worksheet download...</p>
            </div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Download Error</h1>
                <p class="text-gray-600 mb-6" id="error-message"></p>
                <a href="/" class="btn btn-primary">Return Home</a>
            </div>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="hidden">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Download Worksheet for <span id="child-name" class="text-primary"></span>
                </h1>
                <p class="text-gray-600" id="worksheet-date"></p>
            </div>

            <!-- Feedback Form (if previous worksheet exists) -->
            <div id="feedback-section" class="hidden">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-comment-alt mr-2 text-blue-500"></i>
                        How did the previous worksheet go?
                    </h2>
                    <p class="text-gray-600 mb-6">Help us personalize the next worksheet by sharing feedback about the previous one</p>
                    
                    <form id="feedback-form" class="space-y-6">
                        <!-- Completion Status -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Did your child complete the previous worksheet?</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="label cursor-pointer">
                                    <input type="radio" name="completed" value="1" class="radio radio-primary mr-2" required>
                                    <span class="label-text">Yes, completed it</span>
                                </label>
                                <label class="label cursor-pointer">
                                    <input type="radio" name="completed" value="0" class="radio radio-primary mr-2" required>
                                    <span class="label-text">No, didn't finish</span>
                                </label>
                            </div>
                        </div>

                        <!-- Subject Difficulty Ratings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Math Difficulty -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Mathematics Questions</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="math_difficulty" value="easy" class="radio radio-success mr-2">
                                        <span class="label-text">😊 Too Easy</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="math_difficulty" value="just_right" class="radio radio-warning mr-2">
                                        <span class="label-text">👍 Just Right</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="math_difficulty" value="hard" class="radio radio-error mr-2">
                                        <span class="label-text">😓 Too Hard</span>
                                    </label>
                                </div>
                            </div>

                            <!-- English Difficulty -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">English Questions</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="english_difficulty" value="easy" class="radio radio-success mr-2">
                                        <span class="label-text">😊 Too Easy</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="english_difficulty" value="just_right" class="radio radio-warning mr-2">
                                        <span class="label-text">👍 Just Right</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="english_difficulty" value="hard" class="radio radio-error mr-2">
                                        <span class="label-text">😓 Too Hard</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Science Difficulty -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Science Questions</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="science_difficulty" value="easy" class="radio radio-success mr-2">
                                        <span class="label-text">😊 Too Easy</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="science_difficulty" value="just_right" class="radio radio-warning mr-2">
                                        <span class="label-text">👍 Just Right</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="science_difficulty" value="hard" class="radio radio-error mr-2">
                                        <span class="label-text">😓 Too Hard</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Other Subjects Difficulty -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Other Questions</span>
                                </label>
                                <div class="space-y-2">
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="other_difficulty" value="easy" class="radio radio-success mr-2">
                                        <span class="label-text">😊 Too Easy</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="other_difficulty" value="just_right" class="radio radio-warning mr-2">
                                        <span class="label-text">👍 Just Right</span>
                                    </label>
                                    <label class="label cursor-pointer justify-start">
                                        <input type="radio" name="other_difficulty" value="hard" class="radio radio-error mr-2">
                                        <span class="label-text">😓 Too Hard</span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <!-- Additional Feedback -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Any additional comments? (Optional)</span>
                            </label>
                            <textarea name="feedback_notes" class="textarea textarea-bordered h-24" placeholder="Tell us anything else about how the worksheet went..."></textarea>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Download Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div id="download-ready" class="hidden">
                    <i class="fas fa-download text-6xl text-primary mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Generate & Download!</h2>
                    <p class="text-gray-600 mb-6">Click the button below to generate and download your personalized worksheet</p>
                    <button id="generate-download-btn" class="btn btn-sophisticated btn-lg">
                        <i class="fas fa-magic mr-2"></i>
                        Generate & Download Worksheet
                    </button>
                </div>

                <div id="feedback-required" class="hidden">
                    <i class="fas fa-clipboard-check text-6xl text-orange-500 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Almost There!</h2>
                    <p class="text-gray-600 mb-6">Please complete the feedback form above, then we'll generate your worksheet</p>
                    <button id="submit-feedback-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Feedback & Generate Worksheet
                    </button>
                </div>

                <div id="generating-state" class="hidden">
                    <div class="loading loading-spinner loading-lg text-primary mb-4"></div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Generating Your Worksheet...</h2>
                    <p class="text-gray-600">Our AI is creating a personalized worksheet based on your feedback. This may take a moment.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Global variables
let downloadToken = '<?php echo htmlspecialchars($token); ?>';
let tokenData = null;
let previousWorksheet = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadDownloadPage();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    document.getElementById('generate-download-btn').addEventListener('click', generateAndDownload);
    document.getElementById('submit-feedback-btn').addEventListener('click', submitFeedbackAndGenerate);
}

// Load download page data
async function loadDownloadPage() {
    try {
        // Get token information
        const response = await fetch('/api/download-tokens/' + downloadToken);
        const result = await response.json();
        
        if (result.status !== 'success') {
            throw new Error(result.message || 'Invalid download token');
        }
        
        tokenData = result.token_data;
        
        // Update page content
        document.getElementById('child-name').textContent = tokenData.child_name;
        document.getElementById('worksheet-date').textContent = 
            'Worksheet for ' + formatDate(tokenData.date);
            
        // Check for previous worksheet
        await loadPreviousWorksheet();
        
        // Show main content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('main-content').classList.remove('hidden');
        
        // Show appropriate download section
        updateDownloadSection();
        
    } catch (error) {
        console.error('Error loading download page:', error);
        showError(error.message);
    }
}

// Load previous worksheet for feedback
async function loadPreviousWorksheet() {
    try {
        const response = await fetch(`/api/download-tokens/${downloadToken}/previous-worksheet`);
        const result = await response.json();
        
        if (result.status === 'success' && result.previous_worksheet) {
            previousWorksheet = result.previous_worksheet;
            
            // Show feedback section
            document.getElementById('feedback-section').classList.remove('hidden');
            
            // Pre-fill existing feedback if any
            if (previousWorksheet.completed !== null) {
                const completedRadio = document.querySelector(`input[name="completed"][value="${previousWorksheet.completed}"]`);
                if (completedRadio) completedRadio.checked = true;
            }
            
            ['math', 'english', 'science', 'other'].forEach(subject => {
                const difficulty = previousWorksheet[subject + '_difficulty'];
                if (difficulty) {
                    const difficultyRadio = document.querySelector(`input[name="${subject}_difficulty"][value="${difficulty}"]`);
                    if (difficultyRadio) difficultyRadio.checked = true;
                }
            });
            
            if (previousWorksheet.feedback_notes) {
                document.querySelector('textarea[name="feedback_notes"]').value = previousWorksheet.feedback_notes;
            }
        }
        
    } catch (error) {
        console.error('Error loading previous worksheet:', error);
        // Don't show error - just proceed without feedback form
    }
}

// Update download section based on state
function updateDownloadSection() {
    const hasNewFeedback = previousWorksheet && !hasFeedbackBeenSubmitted();
    
    if (hasNewFeedback) {
        document.getElementById('feedback-required').classList.remove('hidden');
    } else {
        document.getElementById('download-ready').classList.remove('hidden');
    }
}

// Check if feedback has been submitted for previous worksheet
function hasFeedbackBeenSubmitted() {
    return previousWorksheet && previousWorksheet.completed !== null;
}

// Submit feedback and generate worksheet
async function submitFeedbackAndGenerate() {
    try {
        // Validate feedback form
        const formData = new FormData(document.getElementById('feedback-form'));
        
        if (!formData.get('completed')) {
            throw new Error('Please indicate if the previous worksheet was completed');
        }
        
        // Show generating state
        showGeneratingState();
        
        // Submit feedback
        const feedbackData = {
            worksheet_id: previousWorksheet.id,
            child_id: tokenData.child_id,
            completed: formData.get('completed') === '1',
            math_difficulty: formData.get('math_difficulty'),
            english_difficulty: formData.get('english_difficulty'), 
            science_difficulty: formData.get('science_difficulty'),
            other_difficulty: formData.get('other_difficulty'),
            feedback_notes: formData.get('feedback_notes')
        };
        
        const feedbackResponse = await fetch('/api/feedback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(feedbackData)
        });
        
        const feedbackResult = await feedbackResponse.json();
        if (feedbackResult.status !== 'success') {
            throw new Error(feedbackResult.message || 'Failed to submit feedback');
        }
        
        // Generate and download worksheet
        await generateAndDownload();
        
    } catch (error) {
        console.error('Error submitting feedback:', error);
        showError('Failed to submit feedback: ' + error.message);
    }
}

// Generate and download worksheet
async function generateAndDownload() {
    try {
        showGeneratingState();
        
        // Generate worksheet
        const response = await fetch(`/api/download-tokens/${downloadToken}/generate`, {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.status !== 'success') {
            throw new Error(result.message || 'Failed to generate worksheet');
        }
        
        // Download the generated worksheet
        if (result.download_url) {
            window.location.href = result.download_url;
        } else {
            // Alternative download method
            const downloadResponse = await fetch(`/api/worksheets/${result.worksheet_id}/download`);
            const blob = await downloadResponse.blob();
            
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${tokenData.child_name}_worksheet_${tokenData.date}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
        
        // Show success message
        showDownloadSuccess();
        
    } catch (error) {
        console.error('Error generating worksheet:', error);
        showError('Failed to generate worksheet: ' + error.message);
    }
}

// Show generating state
function showGeneratingState() {
    document.getElementById('download-ready').classList.add('hidden');
    document.getElementById('feedback-required').classList.add('hidden');
    document.getElementById('generating-state').classList.remove('hidden');
}

// Show download success
function showDownloadSuccess() {
    document.getElementById('generating-state').classList.add('hidden');
    
    const successHtml = `
        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Worksheet Downloaded!</h2>
        <p class="text-gray-600 mb-6">Your personalized worksheet has been generated and downloaded. Enjoy learning!</p>
    `;
    
    document.querySelector('#main-content .bg-white:last-child').innerHTML = successHtml;
}

// Show error
function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('main-content').classList.add('hidden');
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-state').classList.remove('hidden');
}

// Format date helper
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}
</script>

<?php include 'website/include/footer.html'; ?> 