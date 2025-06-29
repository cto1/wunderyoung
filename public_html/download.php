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

<script src="/app/js/config-env.js"></script>
<script src="/app/js/api-utils.js"></script>

<script>
// Global variables
let downloadToken = '<?php echo htmlspecialchars($token); ?>';
let worksheetData = null;
let feedbackSubmitted = false;
</script>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Loading State -->
        <div id="loading-state" class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading worksheet information...</p>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="hidden">
            
            <!-- Worksheet Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">📚 Worksheet Ready!</h1>
                    <p class="text-gray-600">Your personalized worksheet is ready for download</p>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-blue-900" id="child-name">Loading...</h3>
                            <p class="text-blue-700 text-sm" id="worksheet-date">Loading...</p>
                        </div>
                        <div class="text-blue-600">
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Form -->
            <div id="feedback-form" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Quick Feedback (Optional)
                </h2>
                <p class="text-gray-600 mb-6">Help us create better worksheets by sharing how your child did with their previous worksheet:</p>
                
                <form id="feedback-form-element" class="space-y-6">
                    
                    <!-- Completion Question -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Did your child complete their previous worksheet?
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="completion" value="completed" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">✅ Yes, completed it all</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="completion" value="mostly" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">📝 Completed most of it</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="completion" value="some" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">📚 Completed some of it</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="completion" value="none" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">❌ Didn't complete it</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="completion" value="first_time" class="text-blue-600 focus:ring-blue-500" checked>
                                <span class="ml-2">🎉 This is our first worksheet!</span>
                            </label>
                        </div>
                    </div>

                    <!-- Math Difficulty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            How did your child find the math problems?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors">
                                <input type="radio" name="math_difficulty" value="easy" class="sr-only">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">😊</div>
                                    <div class="text-sm font-medium">Too Easy</div>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors border-blue-300 bg-blue-50">
                                <input type="radio" name="math_difficulty" value="just_right" class="sr-only" checked>
                                <div class="text-center">
                                    <div class="text-2xl mb-1">👍</div>
                                    <div class="text-sm font-medium">Just Right</div>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                                <input type="radio" name="math_difficulty" value="hard" class="sr-only">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">😅</div>
                                    <div class="text-sm font-medium">Too Hard</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Other Subjects Difficulty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            How about reading, science, and other activities?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors">
                                <input type="radio" name="other_difficulty" value="easy" class="sr-only">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">😊</div>
                                    <div class="text-sm font-medium">Too Easy</div>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors border-blue-300 bg-blue-50">
                                <input type="radio" name="other_difficulty" value="just_right" class="sr-only" checked>
                                <div class="text-center">
                                    <div class="text-2xl mb-1">👍</div>
                                    <div class="text-sm font-medium">Just Right</div>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                                <input type="radio" name="other_difficulty" value="hard" class="sr-only">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">😅</div>
                                    <div class="text-sm font-medium">Too Hard</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Optional Comment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Any additional comments? (Optional)
                        </label>
                        <textarea name="comments" rows="3" 
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="e.g., My child loved the dinosaur theme! or The math was perfect but reading was too advanced..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="button" onclick="skipFeedbackAndDownload()" 
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors">
                            Skip & Download
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            <span id="submit-text">Submit & Download</span>
                            <span id="submit-spinner" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Download Success -->
            <div id="download-success" class="hidden bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <i class="fas fa-check-circle text-green-600 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-green-900 mb-2">Thank you for your feedback!</h3>
                <p class="text-green-700 mb-4">Your worksheet should start downloading automatically.</p>
                <button onclick="downloadPDF()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download Again
                </button>
            </div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <i class="fas fa-exclamation-circle text-red-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-red-900 mb-2">Download Not Available</h3>
            <p class="text-red-700 mb-4" id="error-message">This download link is invalid or has expired.</p>
            <a href="/" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                Back to Home
            </a>
        </div>
    </div>
</div>

<script>
// Initialize page when loaded
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
    setupFormHandlers();
});

async function initializePage() {
    try {
        // Get token info using proxy server
        const response = await fetch(`/app/proxy-server/proxy.php?api=get_token_info&token=${downloadToken}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        console.log('Token validation result:', result);
        
        if (result.status === 'success') {
            const tokenData = result.token_data;
            worksheetData = {
                child_name: tokenData.child_name,
                date: tokenData.date,
                child_id: tokenData.child_id,
                token: downloadToken
            };
            
            // Update UI with worksheet information
            document.getElementById('child-name').textContent = worksheetData.child_name + "'s Worksheet";
            document.getElementById('worksheet-date').textContent = new Date(worksheetData.date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Show main content
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('main-content').classList.remove('hidden');
        } else {
            throw new Error(result.message || 'Invalid download token');
        }
        
    } catch (error) {
        console.error('Error loading worksheet:', error);
        showError(error.message || 'Failed to load worksheet information. Please check your download link.');
    }
}

function setupFormHandlers() {
    // Handle radio button visual feedback
    const radioGroups = ['math_difficulty', 'other_difficulty'];
    radioGroups.forEach(groupName => {
        const radios = document.querySelectorAll(`input[name="${groupName}"]`);
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selection styling from all labels in this group
                radios.forEach(r => {
                    const label = r.closest('label');
                    label.classList.remove('border-blue-300', 'bg-blue-50', 'border-green-300', 'bg-green-50', 'border-red-300', 'bg-red-50');
                    label.classList.add('border-gray-300');
                });
                
                // Add selection styling to chosen option
                const selectedLabel = this.closest('label');
                selectedLabel.classList.remove('border-gray-300');
                if (this.value === 'easy') {
                    selectedLabel.classList.add('border-green-300', 'bg-green-50');
                } else if (this.value === 'just_right') {
                    selectedLabel.classList.add('border-blue-300', 'bg-blue-50');
                } else if (this.value === 'hard') {
                    selectedLabel.classList.add('border-red-300', 'bg-red-50');
                }
            });
        });
    });

    // Handle form submission
    document.getElementById('feedback-form-element').addEventListener('submit', handleFeedbackSubmit);
}

async function handleFeedbackSubmit(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitSpinner.classList.remove('hidden');
    
    try {
        // Collect form data
        const formData = new FormData(e.target);
        const feedbackData = {
            token: downloadToken,
            completion: formData.get('completion'),
            math_difficulty: formData.get('math_difficulty'),
            other_difficulty: formData.get('other_difficulty'),
            comments: formData.get('comments') || ''
        };
        
        console.log('Submitting feedback:', feedbackData);
        
        // TODO: Submit feedback to API
        await submitFeedback(feedbackData);
        
        // Hide form and show success
        document.getElementById('feedback-form').classList.add('hidden');
        document.getElementById('download-success').classList.remove('hidden');
        
        // Start download automatically
        setTimeout(() => {
            downloadPDF();
        }, 1000);
        
    } catch (error) {
        console.error('Error submitting feedback:', error);
        alert('Failed to submit feedback. Your download will start anyway.');
        downloadPDF();
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitSpinner.classList.add('hidden');
    }
}

async function submitFeedback(feedbackData) {
    try {
        const response = await fetch('/app/proxy-server/proxy.php?api=submit_feedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(feedbackData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            console.log('Feedback submitted successfully:', result);
            feedbackSubmitted = true;
        } else {
            throw new Error(result.message || 'Failed to submit feedback');
        }
        
    } catch (error) {
        console.error('Error submitting feedback:', error);
        throw error;
    }
}

function skipFeedbackAndDownload() {
    if (confirm('Skip feedback and download worksheet?')) {
        downloadPDF();
    }
}

function downloadPDF() {
    if (!worksheetData) {
        alert('Worksheet data not available');
        return;
    }
    
    // Use proxy server to generate and download PDF on-demand
    const downloadUrl = `/app/proxy-server/proxy.php?api=download_pdf&token=${downloadToken}`;
    console.log('Generating and downloading PDF from:', downloadUrl);
    
    // First test the URL to see what we get back
    console.log('Testing download URL response...');
    fetch(downloadUrl)
        .then(response => {
            console.log('Download response status:', response.status);
            console.log('Download response headers:', {
                contentType: response.headers.get('content-type'),
                contentLength: response.headers.get('content-length'),
                contentDisposition: response.headers.get('content-disposition')
            });
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Download URL error response:', text);
                    throw new Error(`Server returned ${response.status}: ${text}`);
                });
            }
            
            // Check if we got a PDF or JSON error
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(json => {
                    console.error('Got JSON response instead of PDF:', json);
                    throw new Error(json.message || 'Server returned JSON instead of PDF');
                });
            }
            
            // If we get here, the server is returning a PDF, so try direct download
            console.log('Server returned PDF, attempting direct download...');
            tryDirectDownload();
        })
        .catch(error => {
            console.error('Download test failed:', error);
            alert('Download failed: ' + error.message);
        });
    
    function tryDirectDownload() {
        // Try creating a blob and downloading it
        fetch(downloadUrl)
            .then(response => response.blob())
            .then(blob => {
                console.log('Got PDF blob, size:', blob.size);
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `${worksheetData.child_name}_Worksheet_${worksheetData.date}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                console.log('Download triggered via blob method');
            })
            .catch(error => {
                console.error('Blob download failed:', error);
                // Fallback to window.open
                console.log('Trying window.open fallback...');
                const downloadWindow = window.open(downloadUrl, '_blank');
                if (!downloadWindow) {
                    alert('Download blocked. Please disable popup blocker and try again.');
                }
            });
    }
}

function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('main-content').classList.add('hidden');
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-state').classList.remove('hidden');
}
</script>

<?php include 'website/include/footer.html'; ?>
