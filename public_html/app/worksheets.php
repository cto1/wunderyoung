<?php 
$page_title = "Worksheets - Yes Homework";
$page_description = "Manage your children and their personalized learning worksheets";
include 'include/header.html'; 
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Loading State -->
        <div id="loading-state" class="flex items-center justify-center min-h-96">
            <div class="text-center">
                <div class="loading loading-spinner loading-lg text-primary"></div>
                <p class="mt-4 text-gray-600">Loading your worksheets...</p>
            </div>
        </div>

        <!-- Main Content (Hidden initially) -->
        <div id="main-content" class="hidden">
            
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Your Children's Worksheets</h1>
                        <p class="text-gray-600 mt-2">Manage your children and track their learning progress</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button id="add-child-btn" class="btn btn-sophisticated">
                            <i class="fas fa-plus mr-2"></i>
                            Add New Child
                        </button>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div id="quick-stats" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-child text-blue-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Total Children</p>
                                <p class="text-2xl font-bold text-blue-800" id="total-children">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-green-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Worksheets Sent</p>
                                <p class="text-2xl font-bold text-green-800" id="total-worksheets">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-download text-purple-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Downloaded</p>
                                <p class="text-2xl font-bold text-purple-800" id="downloaded-count">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Children State -->
            <div id="no-children-state" class="bg-white rounded-lg shadow-sm p-8 text-center hidden">
                <i class="fas fa-child text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to Yes Homework!</h2>
                <p class="text-gray-600 mb-6">Let's start by adding your first child to create personalized worksheets</p>
                <button id="add-first-child-btn" class="btn btn-sophisticated btn-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Child
                </button>
            </div>

            <!-- Children Grid -->
            <div id="children-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Children cards will be populated here -->
            </div>

        </div>

        <!-- Add Child Modal -->
        <div id="add-child-modal" class="modal">
            <div class="modal-box w-11/12 max-w-2xl">
                <h3 class="font-bold text-2xl mb-6">Add New Child</h3>
                
                <form id="add-child-form">
                    <!-- Child Name -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Child's Name</span>
                        </label>
                        <input type="text" id="child-name" class="input input-bordered w-full" placeholder="Enter your child's name" required>
                    </div>

                    <!-- Age Group -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Age Group / Year</span>
                        </label>
                        <select id="child-age-group" class="select select-bordered w-full" required>
                            <option value="">Select age group</option>
                            <option value="3-4">Age 3-4 (Nursery)</option>
                            <option value="4-5">Age 4-5 (Reception)</option>
                            <option value="5-6">Age 5-6 (Year 1)</option>
                            <option value="6-7">Age 6-7 (Year 2)</option>
                            <option value="7-8">Age 7-8 (Year 3)</option>
                            <option value="8-9">Age 8-9 (Year 4)</option>
                            <option value="9-10">Age 9-10 (Year 5)</option>
                            <option value="10-11">Age 10-11 (Year 6)</option>
                        </select>
                    </div>

                    <!-- Interests Section -->
                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Child's Interests (Select 2)</span>
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Choose topics your child enjoys to personalize their worksheets</p>
                        
                        <!-- Interest 1 -->
                        <div class="form-control w-full mb-3">
                            <label class="label">
                                <span class="label-text">First Interest</span>
                            </label>
                            <select id="interest1" class="select select-bordered w-full" required>
                                <option value="">Select first interest</option>
                                <option value="animals">Animals & Nature</option>
                                <option value="space">Space & Planets</option>
                                <option value="dinosaurs">Dinosaurs</option>
                                <option value="cars">Cars & Vehicles</option>
                                <option value="sports">Sports & Games</option>
                                <option value="music">Music & Dancing</option>
                                <option value="art">Art & Drawing</option>
                                <option value="cooking">Cooking & Food</option>
                                <option value="books">Books & Stories</option>
                                <option value="science">Science Experiments</option>
                                <option value="princesses">Princesses & Fairy Tales</option>
                                <option value="superheroes">Superheroes</option>
                                <option value="robots">Robots & Technology</option>
                                <option value="pirates">Pirates & Adventures</option>
                                <option value="farms">Farms & Countryside</option>
                                <option value="ocean">Ocean & Sea Life</option>
                                <option value="custom1">Other (please specify below)</option>
                            </select>
                            <input type="text" id="custom-interest1" class="input input-bordered w-full mt-2 hidden" placeholder="Please specify your child's interest">
                        </div>

                        <!-- Interest 2 -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Second Interest</span>
                            </label>
                            <select id="interest2" class="select select-bordered w-full" required>
                                <option value="">Select second interest</option>
                                <option value="animals">Animals & Nature</option>
                                <option value="space">Space & Planets</option>
                                <option value="dinosaurs">Dinosaurs</option>
                                <option value="cars">Cars & Vehicles</option>
                                <option value="sports">Sports & Games</option>
                                <option value="music">Music & Dancing</option>
                                <option value="art">Art & Drawing</option>
                                <option value="cooking">Cooking & Food</option>
                                <option value="books">Books & Stories</option>
                                <option value="science">Science Experiments</option>
                                <option value="princesses">Princesses & Fairy Tales</option>
                                <option value="superheroes">Superheroes</option>
                                <option value="robots">Robots & Technology</option>
                                <option value="pirates">Pirates & Adventures</option>
                                <option value="farms">Farms & Countryside</option>
                                <option value="ocean">Ocean & Sea Life</option>
                                <option value="custom2">Other (please specify below)</option>
                            </select>
                            <input type="text" id="custom-interest2" class="input input-bordered w-full mt-2 hidden" placeholder="Please specify your child's interest">
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="child-error-message" class="alert alert-error hidden mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="child-error-text"></span>
                    </div>

                    <!-- Form Actions -->
                    <div class="modal-action">
                        <button type="button" class="btn btn-outline" onclick="closeAddChildModal()">Cancel</button>
                        <button type="submit" class="btn btn-sophisticated" id="save-child-btn">
                            <span class="loading loading-spinner loading-sm hidden" id="save-child-spinner"></span>
                            <span id="save-child-text">Save Child</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="modal">
            <div class="modal-box text-center">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="font-bold text-2xl mb-2">Child Added Successfully!</h3>
                <p class="text-gray-600 mb-4" id="success-message">We're preparing your child's first worksheet and will email it to you shortly.</p>
                <div class="modal-action justify-center">
                    <button class="btn btn-sophisticated" onclick="closeSuccessModal()">Got it!</button>
                </div>
            </div>
        </div>

        <!-- Edit Child Modal -->
        <div id="edit-child-modal" class="modal">
            <div class="modal-box w-11/12 max-w-2xl">
                <h3 class="font-bold text-2xl mb-6">Edit Child</h3>
                
                <form id="edit-child-form">
                    <!-- Child Name -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Child's Name</span>
                        </label>
                        <input type="text" id="edit-child-name" class="input input-bordered w-full" placeholder="Enter your child's name" required>
                    </div>

                    <!-- Age Group -->
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Age Group / Year</span>
                        </label>
                        <select id="edit-child-age-group" class="select select-bordered w-full" required>
                            <option value="">Select age group</option>
                            <option value="3-4">Age 3-4 (Nursery)</option>
                            <option value="4-5">Age 4-5 (Reception)</option>
                            <option value="5-6">Age 5-6 (Year 1)</option>
                            <option value="6-7">Age 6-7 (Year 2)</option>
                            <option value="7-8">Age 7-8 (Year 3)</option>
                            <option value="8-9">Age 8-9 (Year 4)</option>
                            <option value="9-10">Age 9-10 (Year 5)</option>
                            <option value="10-11">Age 10-11 (Year 6)</option>
                        </select>
                    </div>

                    <!-- Interests Section -->
                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Child's Interests (Select 2)</span>
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Choose topics your child enjoys to personalize their worksheets</p>
                        
                        <!-- Interest 1 -->
                        <div class="form-control w-full mb-3">
                            <label class="label">
                                <span class="label-text">First Interest</span>
                            </label>
                            <select id="edit-interest1" class="select select-bordered w-full" required>
                                <option value="">Select first interest</option>
                                <option value="animals">Animals & Nature</option>
                                <option value="space">Space & Planets</option>
                                <option value="dinosaurs">Dinosaurs</option>
                                <option value="cars">Cars & Vehicles</option>
                                <option value="sports">Sports & Games</option>
                                <option value="music">Music & Dancing</option>
                                <option value="art">Art & Drawing</option>
                                <option value="cooking">Cooking & Food</option>
                                <option value="books">Books & Stories</option>
                                <option value="science">Science Experiments</option>
                                <option value="princesses">Princesses & Fairy Tales</option>
                                <option value="superheroes">Superheroes</option>
                                <option value="robots">Robots & Technology</option>
                                <option value="pirates">Pirates & Adventures</option>
                                <option value="farms">Farms & Countryside</option>
                                <option value="ocean">Ocean & Sea Life</option>
                                <option value="custom1">Other (please specify below)</option>
                            </select>
                            <input type="text" id="edit-custom-interest1" class="input input-bordered w-full mt-2 hidden" placeholder="Please specify your child's interest">
                        </div>

                        <!-- Interest 2 -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Second Interest</span>
                            </label>
                            <select id="edit-interest2" class="select select-bordered w-full" required>
                                <option value="">Select second interest</option>
                                <option value="animals">Animals & Nature</option>
                                <option value="space">Space & Planets</option>
                                <option value="dinosaurs">Dinosaurs</option>
                                <option value="cars">Cars & Vehicles</option>
                                <option value="sports">Sports & Games</option>
                                <option value="music">Music & Dancing</option>
                                <option value="art">Art & Drawing</option>
                                <option value="cooking">Cooking & Food</option>
                                <option value="books">Books & Stories</option>
                                <option value="science">Science Experiments</option>
                                <option value="princesses">Princesses & Fairy Tales</option>
                                <option value="superheroes">Superheroes</option>
                                <option value="robots">Robots & Technology</option>
                                <option value="pirates">Pirates & Adventures</option>
                                <option value="farms">Farms & Countryside</option>
                                <option value="ocean">Ocean & Sea Life</option>
                                <option value="custom2">Other (please specify below)</option>
                            </select>
                            <input type="text" id="edit-custom-interest2" class="input input-bordered w-full mt-2 hidden" placeholder="Please specify your child's interest">
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="edit-child-error-message" class="alert alert-error hidden mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="edit-child-error-text"></span>
                    </div>

                    <!-- Form Actions -->
                    <div class="modal-action">
                        <button type="button" class="btn btn-outline" onclick="closeEditChildModal()">Cancel</button>
                        <button type="submit" class="btn btn-sophisticated" id="update-child-btn">
                            <span class="loading loading-spinner loading-sm hidden" id="update-child-spinner"></span>
                            <span id="update-child-text">Update Child</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-child-modal" class="modal">
            <div class="modal-box text-center">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                <h3 class="font-bold text-2xl mb-2">Delete Child?</h3>
                <p class="text-gray-600 mb-4" id="delete-child-message">Are you sure you want to delete this child? This will also delete all their worksheets and cannot be undone.</p>
                <div class="modal-action justify-center">
                    <button class="btn btn-outline" onclick="closeDeleteChildModal()">Cancel</button>
                    <button class="btn btn-error" id="confirm-delete-btn" onclick="confirmDeleteChild()">
                        <span class="loading loading-spinner loading-sm hidden" id="delete-child-spinner"></span>
                        <span id="delete-child-text">Delete Child</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="js/api-utils.js"></script>
<script>
// Global variables
let children = [];
let worksheetStats = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!api.isAuthenticated()) {
        window.location.href = 'login.php';
        return;
    }
    
    loadWorksheetPage();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Add child buttons
    document.getElementById('add-child-btn').addEventListener('click', openAddChildModal);
    document.getElementById('add-first-child-btn').addEventListener('click', openAddChildModal);
    
    // Form submissions
    document.getElementById('add-child-form').addEventListener('submit', handleAddChildSubmit);
    document.getElementById('edit-child-form').addEventListener('submit', handleEditChildSubmit);
    
    // Custom interest toggles for add form
    document.getElementById('interest1').addEventListener('change', handleInterestChange);
    document.getElementById('interest2').addEventListener('change', handleInterestChange);
    
    // Custom interest toggles for edit form
    document.getElementById('edit-interest1').addEventListener('change', handleEditInterestChange);
    document.getElementById('edit-interest2').addEventListener('change', handleEditInterestChange);
}

// Load worksheet page data
async function loadWorksheetPage() {
    try {
        // Load children and stats in parallel
        const [childrenResponse, statsResponse] = await Promise.all([
            api.makeRequest('/children', 'GET'),
            api.makeRequest('/worksheets/stats', 'GET')
        ]);
        
        children = childrenResponse.children || [];
        worksheetStats = statsResponse.stats || {};
        
        updateQuickStats();
        renderChildren();
        
        // Load individual child stats
        await loadChildrenStats();
        
        // Show main content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('main-content').classList.remove('hidden');
        
    } catch (error) {
        console.error('Error loading worksheet page:', error);
        showError('Failed to load worksheet data. Please try again.');
    }
}

// Load individual children stats
async function loadChildrenStats() {
    if (children.length === 0) return;
    
    try {
        // Load stats for each child in parallel
        const statsPromises = children.map(child => 
            api.makeRequest(`/children/${child.id}/worksheets`, 'GET')
                .then(response => ({
                    childId: child.id,
                    worksheets: response.worksheets || [],
                    total: response.total || 0
                }))
                .catch(error => ({
                    childId: child.id,
                    worksheets: [],
                    total: 0
                }))
        );
        
        const childrenStats = await Promise.all(statsPromises);
        
        // Update each child's display
        childrenStats.forEach(stats => {
            updateChildStats(stats.childId, stats.worksheets, stats.total);
        });
        
    } catch (error) {
        console.error('Error loading children stats:', error);
    }
}

// Update individual child stats display
function updateChildStats(childId, worksheets, total) {
    const worksheetCountEl = document.getElementById(`child-worksheets-${childId}`);
    const streakEl = document.getElementById(`child-streak-${childId}`);
    
    if (worksheetCountEl) {
        worksheetCountEl.textContent = total;
    }
    
    if (streakEl && worksheets.length > 0) {
        // Calculate learning streak (consecutive days with worksheets)
        const streak = calculateLearningStreak(worksheets);
        streakEl.textContent = streak;
    }
}

// Calculate learning streak from worksheets
function calculateLearningStreak(worksheets) {
    if (!worksheets || worksheets.length === 0) return 0;
    
    // Sort worksheets by date (most recent first)
    const sortedWorksheets = worksheets
        .map(w => new Date(w.date))
        .sort((a, b) => b - a);
    
    let streak = 0;
    let currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);
    
    // Check if there's a worksheet for today or yesterday
    const today = new Date(currentDate);
    const yesterday = new Date(currentDate);
    yesterday.setDate(yesterday.getDate() - 1);
    
    let checkDate = today;
    let hasWorksheetForDate = sortedWorksheets.some(date => 
        date.toDateString() === today.toDateString()
    );
    
    if (!hasWorksheetForDate) {
        // If no worksheet today, start checking from yesterday
        checkDate = yesterday;
        hasWorksheetForDate = sortedWorksheets.some(date => 
            date.toDateString() === yesterday.toDateString()
        );
    }
    
    // Count consecutive days with worksheets
    while (hasWorksheetForDate) {
        streak++;
        checkDate.setDate(checkDate.getDate() - 1);
        hasWorksheetForDate = sortedWorksheets.some(date => 
            date.toDateString() === checkDate.toDateString()
        );
    }
    
    return streak;
}

// Update quick stats display
function updateQuickStats() {
    document.getElementById('total-children').textContent = children.length;
    document.getElementById('total-worksheets').textContent = worksheetStats.total_worksheets || 0;
    document.getElementById('downloaded-count').textContent = worksheetStats.downloaded_count || 0;
}

// Render children cards
function renderChildren() {
    const grid = document.getElementById('children-grid');
    const noChildrenState = document.getElementById('no-children-state');
    
    if (children.length === 0) {
        grid.classList.add('hidden');
        noChildrenState.classList.remove('hidden');
        return;
    }
    
    noChildrenState.classList.add('hidden');
    grid.classList.remove('hidden');
    
    grid.innerHTML = children.map(child => createChildCard(child)).join('');
}

// Create child card HTML
function createChildCard(child) {
    // Calculate days since created
    const createdDate = new Date(child.created_at);
    const today = new Date();
    const daysSince = Math.floor((today - createdDate) / (1000 * 60 * 60 * 24));
    
    return `
        <div class="card bg-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="card-title text-xl font-bold text-gray-900">${child.name}</h2>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a onclick="editChild('${child.id}')"><i class="fas fa-edit mr-2"></i>Edit</a></li>
                            <li><a onclick="deleteChild('${child.id}')" class="text-red-600"><i class="fas fa-trash mr-2"></i>Delete</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-birthday-cake mr-2 text-blue-500"></i>
                        <span>${child.age_group}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-heart mr-2 text-red-500"></i>
                        <span>${child.interest1}${child.interest2 ? ', ' + child.interest2 : ''}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar mr-2 text-green-500"></i>
                        <span>Added ${daysSince === 0 ? 'today' : daysSince + ' days ago'}</span>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Worksheet Stats -->
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600" id="child-worksheets-${child.id}">0</div>
                        <div class="text-xs text-blue-600">Worksheets</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <div class="text-2xl font-bold text-green-600" id="child-streak-${child.id}">0</div>
                        <div class="text-xs text-green-600">Day Streak</div>
                    </div>
                </div>
                
                <div class="card-actions justify-end mt-4">
                    <button class="btn btn-sophisticated btn-sm" onclick="generateWorksheet('${child.id}')">
                        <i class="fas fa-magic mr-1"></i>
                        Create Download Link
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Handle interest dropdown changes (show custom input if needed)
function handleInterestChange(e) {
    const select = e.target;
    const customInput = document.getElementById(`custom-${select.id}`);
    
    if (select.value === `custom${select.id.slice(-1)}`) {
        customInput.classList.remove('hidden');
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}

// Open add child modal
function openAddChildModal() {
    document.getElementById('add-child-modal').classList.add('modal-open');
    // Reset form
    document.getElementById('add-child-form').reset();
    document.getElementById('custom-interest1').classList.add('hidden');
    document.getElementById('custom-interest2').classList.add('hidden');
    document.getElementById('child-error-message').classList.add('hidden');
}

// Close add child modal
function closeAddChildModal() {
    document.getElementById('add-child-modal').classList.remove('modal-open');
}

// Handle add child form submission
async function handleAddChildSubmit(e) {
    e.preventDefault();
    
    const btn = document.getElementById('save-child-btn');
    const spinner = document.getElementById('save-child-spinner');
    const btnText = document.getElementById('save-child-text');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Saving...';
    
    try {
        // Get form data
        const name = document.getElementById('child-name').value.trim();
        const ageGroup = document.getElementById('child-age-group').value;
        
        // Get interests (handle custom inputs)
        let interest1 = document.getElementById('interest1').value;
        let interest2 = document.getElementById('interest2').value;
        
        if (interest1 === 'custom1') {
            interest1 = document.getElementById('custom-interest1').value.trim();
        }
        if (interest2 === 'custom2') {
            interest2 = document.getElementById('custom-interest2').value.trim();
        }
        
        // Validate
        if (!name || !ageGroup || !interest1 || !interest2) {
            throw new Error('Please fill in all fields');
        }
        
        if (interest1 === interest2) {
            throw new Error('Please select two different interests');
        }
        
        // Add child
        const response = await api.makeRequest('/children', 'POST', {
            name: name,
            age_group: ageGroup,
            interest1: interest1,
            interest2: interest2
        });
        
        if (response.status === 'success') {
            closeAddChildModal();
            showSuccessModal(name);
            
            // Reload page data
            await loadWorksheetPage();
            
            // Send welcome email (placeholder for now)
            await sendWelcomeWorksheet(response.child_id, name);
        } else {
            throw new Error(response.message || 'Failed to add child');
        }
        
    } catch (error) {
        console.error('Error adding child:', error);
        showChildError(error.message);
    } finally {
        // Reset button state
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Save Child';
    }
}

// Send welcome worksheet
async function sendWelcomeWorksheet(childId, childName) {
    try {
        console.log(`Generating welcome worksheet for ${childName}...`);
        
        // Generate today's worksheet for the new child
        const today = new Date().toISOString().split('T')[0];
        
        const response = await api.makeRequest(`/children/${childId}/generate-worksheet`, 'POST', {
            date: today,
            is_welcome: true // Flag to indicate this is a welcome worksheet
        });
        
        if (response.status === 'success') {
            console.log(`Welcome worksheet generated and sent for ${childName}`);
            
            // Send a welcome email notification
            await sendWelcomeEmail(childId, childName);
        } else {
            console.warn(`Failed to generate welcome worksheet for ${childName}:`, response.message);
        }
        
    } catch (error) {
        console.error('Error sending welcome worksheet:', error);
        // Don't throw error here as this shouldn't prevent child creation
    }
}

// Send welcome email notification
async function sendWelcomeEmail(childId, childName) {
    try {
        // Get user data for personalized email
        const userData = api.getCurrentUser();
        
        // This would call an email API endpoint
        const response = await api.makeRequest('/send-welcome-email', 'POST', {
            child_id: childId,
            child_name: childName,
            parent_email: userData.email
        });
        
        if (response.status === 'success') {
            console.log(`Welcome email sent for ${childName}`);
        }
        
    } catch (error) {
        console.error('Error sending welcome email:', error);
        // Don't throw error here as this is supplementary functionality
    }
}

// Show success modal
function showSuccessModal(childName) {
    document.getElementById('success-message').textContent = 
        `${childName} has been added! We're preparing their first personalized worksheet and will email it to you shortly.`;
    document.getElementById('success-modal').classList.add('modal-open');
}

// Close success modal
function closeSuccessModal() {
    document.getElementById('success-modal').classList.remove('modal-open');
}

// Show child form error
function showChildError(message) {
    document.getElementById('child-error-text').textContent = message;
    document.getElementById('child-error-message').classList.remove('hidden');
}

// Show general error
function showError(message) {
    // You could implement a toast notification here
    alert(message);
}

// Generate worksheet for child
async function generateWorksheet(childId) {
    try {
        // Check authentication first
        if (!api.isAuthenticated()) {
            throw new Error('You are not logged in. Please refresh the page and try again.');
        }
        
        const today = new Date().toISOString().split('T')[0];
        
        // Show loading state on the button
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Generating...';
        
        // Call the worksheet generation API
        console.log('Making request to generate worksheet for child:', childId);
        console.log('User authenticated:', api.isAuthenticated());
        console.log('Current user:', api.getCurrentUser());
        
        const response = await api.makeRequest(`/children/${childId}/generate-worksheet`, 'POST', {
            date: today
        });
        
        console.log('Worksheet generation response:', response);
        
        if (response.status === 'success') {
            btn.innerHTML = '<i class="fas fa-check mr-1"></i>Download Link Ready!';
            btn.className = 'btn btn-success btn-sm';
            
            // Show success message with download instructions 
            showSuccessMessage('Download link created! Check your email for the download link.');
            
            // Reload stats to show updated counts
            await loadChildrenStats();
            await loadWorksheetPage();
            
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.className = 'btn btn-sophisticated btn-sm';
            }, 3000);
            
        } else {
            throw new Error(response.message || 'Failed to generate worksheet');
        }
        
    } catch (error) {
        console.error('Error generating worksheet:', error);
        
        // Handle specific error cases
        if (error.message.includes('already exists')) {
            showError('Today\'s download link has already been created for this child. Check your email!');
        } else if (error.message.includes('UNIQUE constraint')) {
            showError('Today\'s download link has already been created for this child. Check your email!');
        } else {
            showError('Failed to create download link. Please try again.');
        }
        
        // Reset button on error - get button reference safely
        const btn = event?.target || document.querySelector(`button[onclick*="${childId}"]`);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Create Download Link';
            btn.className = 'btn btn-sophisticated btn-sm';
        }
    }
}

// Global variables for edit/delete operations
let currentEditChildId = null;
let currentDeleteChildId = null;

// Handle edit interest dropdown changes (for edit form)
function handleEditInterestChange(e) {
    const select = e.target;
    const customInput = document.getElementById(`edit-custom-${select.id.replace('edit-', '')}`);
    
    if (select.value === `custom${select.id.slice(-1)}`) {
        customInput.classList.remove('hidden');
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}

// Open edit child modal
function editChild(childId) {
    const child = children.find(c => c.id === childId);
    if (!child) {
        showError('Child not found');
        return;
    }
    
    currentEditChildId = childId;
    
    // Populate form with current child data
    document.getElementById('edit-child-name').value = child.name;
    document.getElementById('edit-child-age-group').value = child.age_group;
    document.getElementById('edit-interest1').value = child.interest1;
    document.getElementById('edit-interest2').value = child.interest2;
    
    // Handle custom interests
    const interest1Select = document.getElementById('edit-interest1');
    const interest2Select = document.getElementById('edit-interest2');
    const customInput1 = document.getElementById('edit-custom-interest1');
    const customInput2 = document.getElementById('edit-custom-interest2');
    
    // Check if interests are custom (not in dropdown options)
    const standardInterests = ['animals', 'space', 'dinosaurs', 'cars', 'sports', 'music', 'art', 'cooking', 'books', 'science', 'princesses', 'superheroes', 'robots', 'pirates', 'farms', 'ocean'];
    
    if (!standardInterests.includes(child.interest1)) {
        interest1Select.value = 'custom1';
        customInput1.value = child.interest1;
        customInput1.classList.remove('hidden');
        customInput1.required = true;
    }
    
    if (!standardInterests.includes(child.interest2)) {
        interest2Select.value = 'custom2';
        customInput2.value = child.interest2;
        customInput2.classList.remove('hidden');
        customInput2.required = true;
    }
    
    // Clear any previous errors
    document.getElementById('edit-child-error-message').classList.add('hidden');
    
    // Open modal
    document.getElementById('edit-child-modal').classList.add('modal-open');
}

// Close edit child modal
function closeEditChildModal() {
    document.getElementById('edit-child-modal').classList.remove('modal-open');
    currentEditChildId = null;
    
    // Reset form
    document.getElementById('edit-child-form').reset();
    document.getElementById('edit-custom-interest1').classList.add('hidden');
    document.getElementById('edit-custom-interest2').classList.add('hidden');
    document.getElementById('edit-child-error-message').classList.add('hidden');
}

// Handle edit child form submission
async function handleEditChildSubmit(e) {
    e.preventDefault();
    
    if (!currentEditChildId) {
        showError('No child selected for editing');
        return;
    }
    
    const btn = document.getElementById('update-child-btn');
    const spinner = document.getElementById('update-child-spinner');
    const btnText = document.getElementById('update-child-text');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Updating...';
    
    try {
        // Get form data
        const name = document.getElementById('edit-child-name').value.trim();
        const ageGroup = document.getElementById('edit-child-age-group').value;
        
        // Get interests (handle custom inputs)
        let interest1 = document.getElementById('edit-interest1').value;
        let interest2 = document.getElementById('edit-interest2').value;
        
        if (interest1 === 'custom1') {
            interest1 = document.getElementById('edit-custom-interest1').value.trim();
        }
        if (interest2 === 'custom2') {
            interest2 = document.getElementById('edit-custom-interest2').value.trim();
        }
        
        // Validate
        if (!name || !ageGroup || !interest1 || !interest2) {
            throw new Error('Please fill in all fields');
        }
        
        if (interest1 === interest2) {
            throw new Error('Please select two different interests');
        }
        
        // Update child
        const response = await api.makeRequest(`/children/${currentEditChildId}`, 'PUT', {
            name: name,
            age_group: ageGroup,
            interest1: interest1,
            interest2: interest2
        });
        
        if (response.status === 'success') {
            closeEditChildModal();
            showSuccessMessage('Child updated successfully!');
            
            // Reload page data
            await loadWorksheetPage();
        } else {
            throw new Error(response.message || 'Failed to update child');
        }
        
    } catch (error) {
        console.error('Error updating child:', error);
        showEditChildError(error.message);
    } finally {
        // Reset button state
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Update Child';
    }
}

// Show edit child form error
function showEditChildError(message) {
    document.getElementById('edit-child-error-text').textContent = message;
    document.getElementById('edit-child-error-message').classList.remove('hidden');
}

// Open delete child modal
function deleteChild(childId) {
    const child = children.find(c => c.id === childId);
    if (!child) {
        showError('Child not found');
        return;
    }
    
    currentDeleteChildId = childId;
    
    // Update modal message with child name
    document.getElementById('delete-child-message').textContent = 
        `Are you sure you want to delete ${child.name}? This will also delete all their worksheets and cannot be undone.`;
    
    // Open modal
    document.getElementById('delete-child-modal').classList.add('modal-open');
}

// Close delete child modal
function closeDeleteChildModal() {
    document.getElementById('delete-child-modal').classList.remove('modal-open');
    currentDeleteChildId = null;
}

// Confirm delete child
async function confirmDeleteChild() {
    if (!currentDeleteChildId) {
        showError('No child selected for deletion');
        return;
    }
    
    const btn = document.getElementById('confirm-delete-btn');
    const spinner = document.getElementById('delete-child-spinner');
    const btnText = document.getElementById('delete-child-text');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Deleting...';
    
    try {
        const response = await api.makeRequest(`/children/${currentDeleteChildId}`, 'DELETE');
        
        if (response.status === 'success') {
            closeDeleteChildModal();
            showSuccessMessage('Child deleted successfully');
            
            // Reload page data
            await loadWorksheetPage();
        } else {
            throw new Error(response.message || 'Failed to delete child');
        }
        
    } catch (error) {
        console.error('Error deleting child:', error);
        showError('Failed to delete child. Please try again.');
    } finally {
        // Reset button state
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Delete Child';
    }
}

// Show success message (general)
function showSuccessMessage(message) {
    // You could implement a toast notification here
    // For now, using a simple alert
    alert(message);
}
</script>

</body>
</html> 