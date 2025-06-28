<?php 
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include 'include/translations.php'; 
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Worksheets - Yes Homework</title>
    <meta name="description" content="Manage your child's daily worksheets and track their learning progress.">

    <!--  Favicons -->
    <link rel="icon" type="image/png" href="assets/favicons/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicons/apple-touch-icon.png">
    <link rel="shortcut icon" href="assets/favicons/favicon.ico">

    <!-- Font-Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- DaisyUI & TailwindCSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- API Utils & Authentication -->
    <script src="js/api-utils.js"></script>
    <script src="js/localstorage-data.js"></script>

</head>

<body class="bg-gradient-to-br from-blue-50 to-purple-50">

    <!-- Navigation -->
    <nav class="navbar bg-white shadow-lg sticky top-0 z-50">
        <div class="navbar-start">
            <a href="/website/" class="btn btn-ghost text-xl font-bold text-primary">
                <i class="fas fa-home mr-2"></i>
                Yes! Homework
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li><a href="worksheets.php" class="active">Worksheets</a></li>
                <li><a href="settings.php">Settings</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full bg-primary text-white flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="settings.php"><i class="fas fa-cog mr-2"></i>Settings</a></li>
                    <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 max-w-6xl">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">My Worksheets</h1>
                    <p class="text-gray-600" id="welcomeMessage">Welcome back! Here are your child's learning materials.</p>
                </div>
                <div class="mt-4 lg:mt-0">
                    <div class="flex flex-wrap gap-2">
                        <button id="generateWorksheetBtn" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Generate New Worksheet
                        </button>
                        <button id="upgradeBtn" class="btn btn-warning">
                            <i class="fas fa-crown mr-2"></i>
                            Upgrade to Premium
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="stat bg-white rounded-lg shadow-lg">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-user-circle text-3xl"></i>
                    </div>
                    <div class="stat-title">Account Type</div>
                    <div class="stat-value text-primary" id="accountType">Free Plan</div>
                    <div class="stat-desc" id="accountDesc">1 child, rotating subjects</div>
                </div>
                
                <div class="stat bg-white rounded-lg shadow-lg">
                    <div class="stat-figure text-secondary">
                        <i class="fas fa-file-alt text-3xl"></i>
                    </div>
                    <div class="stat-title">Worksheets This Month</div>
                    <div class="stat-value text-secondary" id="worksheetCount">0</div>
                    <div class="stat-desc" id="worksheetDesc">Generated this month</div>
                </div>
                
                <div class="stat bg-white rounded-lg shadow-lg">
                    <div class="stat-figure text-accent">
                        <i class="fas fa-calendar-check text-3xl"></i>
                    </div>
                    <div class="stat-title">Streak</div>
                    <div class="stat-value text-accent" id="streakCount">0</div>
                    <div class="stat-desc">Days active</div>
                </div>
            </div>
        </div>

        <!-- Children Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Children</h2>
                <button id="addChildBtn" class="btn btn-outline btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Add Child
                </button>
            </div>
            
            <div id="childrenContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Children will be loaded here -->
                <div class="loading loading-spinner loading-lg mx-auto"></div>
            </div>
        </div>

        <!-- Recent Worksheets -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Recent Worksheets</h2>
                <div class="flex gap-2">
                    <select id="filterChild" class="select select-bordered select-sm">
                        <option value="">All Children</option>
                    </select>
                    <select id="filterSubject" class="select select-bordered select-sm">
                        <option value="">All Subjects</option>
                        <option value="maths">Maths</option>
                        <option value="english">English</option>
                        <option value="spanish">Spanish</option>
                    </select>
                </div>
            </div>
            
            <div id="worksheetsContainer">
                <!-- Worksheets will be loaded here -->
                <div class="loading loading-spinner loading-lg mx-auto"></div>
            </div>
        </div>

        <!-- Upgrade Prompt (shown for free users) -->
        <div id="upgradePrompt" class="hidden">
            <div class="card bg-gradient-to-r from-purple-500 to-blue-600 text-white shadow-2xl">
                <div class="card-body text-center">
                    <h2 class="card-title justify-center text-3xl mb-4">
                        <i class="fas fa-crown mr-2"></i>
                        Unlock Premium Features
                    </h2>
                    <p class="text-lg mb-6 opacity-90">
                        Get the most out of Yes Homework with personalized content, multiple children, and all subjects.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="font-bold text-xl mb-4">Free Plan</h3>
                            <ul class="text-left space-y-2 text-sm">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-300 mr-2"></i>
                                    1 child only
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-300 mr-2"></i>
                                    1 rotating subject
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-times text-red-300 mr-2"></i>
                                    Basic worksheets
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-times text-red-300 mr-2"></i>
                                    No personalization
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                            <h3 class="font-bold text-xl mb-4">Premium Plan - €9/month</h3>
                            <ul class="text-left space-y-2 text-sm">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-300 mr-2"></i>
                                    Up to 3 children
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-300 mr-2"></i>
                                    All subjects (Maths, English, Spanish)
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-300 mr-2"></i>
                                    Fully personalized content
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-300 mr-2"></i>
                                    Worksheet backlog access
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <button id="upgradeNowBtn" class="btn btn-warning btn-lg">
                        <i class="fas fa-rocket mr-2"></i>
                        Upgrade Now - €9/month
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Modals -->
    
    <!-- Generate Worksheet Modal -->
    <dialog id="generateModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Generate New Worksheet</h3>
            <form id="generateForm" class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Select Child</span>
                    </label>
                    <select id="generateChildSelect" class="select select-bordered w-full" required>
                        <option value="">Choose child...</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Subject</span>
                    </label>
                    <select id="generateSubjectSelect" class="select select-bordered w-full" required>
                        <option value="">Choose subject...</option>
                        <option value="maths">📊 Maths</option>
                        <option value="english">📚 English</option>
                        <option value="spanish">🇪🇸 Spanish (Premium)</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Difficulty Level</span>
                    </label>
                    <select id="generateDifficulty" class="select select-bordered w-full">
                        <option value="age_appropriate">Age Appropriate (Recommended)</option>
                        <option value="easy">Easier</option>
                        <option value="challenging">More Challenging</option>
                    </select>
                </div>
                
                <div class="modal-action">
                    <button type="button" class="btn" onclick="generateModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-magic mr-2"></i>
                        Generate Worksheet
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Add Child Modal -->
    <dialog id="addChildModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Add New Child</h3>
            <p class="text-sm text-gray-600 mb-4">
                <span id="childLimitText">Free plan: 1 child limit</span>
            </p>
            <form id="addChildForm" class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Child's Name</span>
                    </label>
                    <input type="text" id="newChildName" class="input input-bordered w-full" placeholder="Emma" required>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Age Group</span>
                    </label>
                    <select id="newChildAge" class="select select-bordered w-full" required>
                        <option value="">Select age group...</option>
                        <option value="nursery">Nursery (Ages 3-4)</option>
                        <option value="reception">Reception (Ages 4-5)</option>
                        <option value="year1">Year 1 (Ages 5-6)</option>
                        <option value="year2">Year 2 (Ages 6-7)</option>
                        <option value="year3">Year 3 (Ages 7-8)</option>
                        <option value="year4">Year 4 (Ages 8-9)</option>
                        <option value="year5">Year 5 (Ages 9-10)</option>
                        <option value="year6">Year 6 (Ages 10-11)</option>
                    </select>
                </div>
                
                <div class="modal-action">
                    <button type="button" class="btn" onclick="addChildModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Add Child
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- JavaScript -->
    <script>
        // Global variables
        let userData = {};
        let children = [];
        let worksheets = [];
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadUserData();
            loadChildren();
            loadWorksheets();
            setupEventListeners();
        });
        
        // Load user data and account info
        async function loadUserData() {
            try {
                const response = await fetch('/api/index.php?action=getUserInfo', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    }
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    userData = result.user;
                    updateAccountInfo();
                } else if (response.status === 401) {
                    // Unauthorized - redirect to login
                    console.warn('Authentication failed, redirecting to login');
                    window.location.href = './login.php';
                    return;
                }
            } catch (error) {
                console.error('Error loading user data:', error);
                if (error.message.includes('401') || error.message.includes('unauthorized')) {
                    window.location.href = './login.php';
                }
            }
        }
        
        // Load children
        async function loadChildren() {
            try {
                const response = await fetch('/api/index.php?action=getChildren', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    }
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    children = result.children || [];
                    renderChildren();
                    updateChildrenDropdowns();
                } else if (response.status === 401) {
                    console.warn('Authentication failed, redirecting to login');
                    window.location.href = './login.php';
                    return;
                }
            } catch (error) {
                console.error('Error loading children:', error);
                if (error.message.includes('401') || error.message.includes('unauthorized')) {
                    window.location.href = './login.php';
                } else {
                    document.getElementById('childrenContainer').innerHTML = '<p class="text-red-500">Error loading children</p>';
                }
            }
        }
        
        // Load worksheets
        async function loadWorksheets() {
            try {
                const response = await fetch('/api/index.php?action=getWorksheets', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    }
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    worksheets = result.worksheets || [];
                    renderWorksheets();
                    updateStats();
                } else if (response.status === 401) {
                    console.warn('Authentication failed, redirecting to login');
                    window.location.href = './login.php';
                    return;
                }
            } catch (error) {
                console.error('Error loading worksheets:', error);
                if (error.message.includes('401') || error.message.includes('unauthorized')) {
                    window.location.href = './login.php';
                } else {
                    document.getElementById('worksheetsContainer').innerHTML = '<p class="text-red-500">Error loading worksheets</p>';
                }
            }
        }
        
        // Update account info display
        function updateAccountInfo() {
            const isPremium = userData.subscription_type === 'premium';
            
            document.getElementById('accountType').textContent = isPremium ? 'Premium Plan' : 'Free Plan';
            document.getElementById('accountDesc').textContent = isPremium ? 'Up to 3 children, all subjects' : '1 child, rotating subjects';
            
            // Show/hide upgrade prompt
            document.getElementById('upgradePrompt').classList.toggle('hidden', isPremium);
        }
        
        // Render children cards
        function renderChildren() {
            const container = document.getElementById('childrenContainer');
            
            if (children.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-child text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No children added yet</h3>
                        <p class="text-gray-500 mb-4">Add your first child to start generating worksheets</p>
                        <button class="btn btn-primary" onclick="addChildModal.showModal()">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Child
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = children.map(child => `
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-4">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-white rounded-full w-12 h-12">
                                    <span class="text-xl">${child.name.charAt(0).toUpperCase()}</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-end">
                                <button class="btn btn-ghost btn-sm btn-circle">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-40">
                                    <li><a onclick="editChild('${child.id}')"><i class="fas fa-edit mr-2"></i>Edit</a></li>
                                    <li><a onclick="deleteChild('${child.id}')" class="text-red-500"><i class="fas fa-trash mr-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <h3 class="card-title text-lg">${child.name}</h3>
                        <p class="text-sm text-gray-600 mb-2">${child.age_group || 'Age not set'}</p>
                        
                        <div class="flex flex-wrap gap-1 mb-4">
                            ${(child.interests || []).slice(0, 3).map(interest => 
                                `<div class="badge badge-outline badge-sm">${interest}</div>`
                            ).join('')}
                            ${(child.interests || []).length > 3 ? `<div class="badge badge-outline badge-sm">+${(child.interests || []).length - 3}</div>` : ''}
                        </div>
                        
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm" onclick="generateWorksheetFor('${child.id}')">
                                <i class="fas fa-plus mr-1"></i>
                                Generate
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Render worksheets
        function renderWorksheets() {
            const container = document.getElementById('worksheetsContainer');
            
            if (worksheets.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-file-alt text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No worksheets yet</h3>
                        <p class="text-gray-500 mb-4">Generate your first worksheet to get started</p>
                        <button class="btn btn-primary" onclick="generateModal.showModal()">
                            <i class="fas fa-magic mr-2"></i>
                            Generate First Worksheet
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${worksheets.map(worksheet => `
                        <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                            <div class="card-body">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="badge badge-primary badge-sm">${worksheet.subject || 'General'}</div>
                                    <div class="text-xs text-gray-500">${formatDate(worksheet.date)}</div>
                                </div>
                                
                                <h3 class="card-title text-lg">${worksheet.child_name}</h3>
                                <p class="text-sm text-gray-600 mb-4">${worksheet.age_group || 'Age not specified'}</p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span><i class="fas fa-download mr-1"></i>${worksheet.downloaded ? 'Downloaded' : 'Not downloaded'}</span>
                                    <span><i class="fas fa-clock mr-1"></i>${formatTime(worksheet.created_at)}</span>
                                </div>
                                
                                <div class="card-actions justify-end">
                                    <button class="btn btn-outline btn-sm" onclick="previewWorksheet('${worksheet.id}')">
                                        <i class="fas fa-eye mr-1"></i>
                                        Preview
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="downloadWorksheet('${worksheet.id}')">
                                        <i class="fas fa-download mr-1"></i>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        
        // Update statistics
        function updateStats() {
            const thisMonth = new Date().getMonth();
            const thisMonthWorksheets = worksheets.filter(w => new Date(w.date).getMonth() === thisMonth);
            
            document.getElementById('worksheetCount').textContent = thisMonthWorksheets.length;
            document.getElementById('worksheetDesc').textContent = `Generated this month`;
            
            // Calculate streak (simplified)
            const today = new Date();
            const recentDays = 7;
            let streak = 0;
            
            for (let i = 0; i < recentDays; i++) {
                const checkDate = new Date(today);
                checkDate.setDate(checkDate.getDate() - i);
                const hasWorksheet = worksheets.some(w => {
                    const wDate = new Date(w.date);
                    return wDate.toDateString() === checkDate.toDateString();
                });
                
                if (hasWorksheet) {
                    streak++;
                } else {
                    break;
                }
            }
            
            document.getElementById('streakCount').textContent = streak;
        }
        
        // Update dropdowns
        function updateChildrenDropdowns() {
            const selects = ['generateChildSelect', 'filterChild'];
            
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                if (select) {
                    const options = children.map(child => 
                        `<option value="${child.id}">${child.name}</option>`
                    ).join('');
                    
                    if (selectId === 'filterChild') {
                        select.innerHTML = '<option value="">All Children</option>' + options;
                    } else {
                        select.innerHTML = '<option value="">Choose child...</option>' + options;
                    }
                }
            });
        }
        
        // Event listeners
        function setupEventListeners() {
            // Generate worksheet button
            document.getElementById('generateWorksheetBtn').addEventListener('click', () => {
                if (children.length === 0) {
                    alert('Please add a child first');
                    addChildModal.showModal();
                } else {
                    generateModal.showModal();
                }
            });
            
            // Add child button
            document.getElementById('addChildBtn').addEventListener('click', () => {
                addChildModal.showModal();
            });
            
            // Upgrade buttons
            document.getElementById('upgradeBtn').addEventListener('click', handleUpgrade);
            document.getElementById('upgradeNowBtn').addEventListener('click', handleUpgrade);
            
            // Form submissions
            document.getElementById('generateForm').addEventListener('submit', handleGenerateWorksheet);
            document.getElementById('addChildForm').addEventListener('submit', handleAddChild);
        }
        
        // Handle worksheet generation
        async function handleGenerateWorksheet(e) {
            e.preventDefault();
            
            const childId = document.getElementById('generateChildSelect').value;
            const subject = document.getElementById('generateSubjectSelect').value;
            const difficulty = document.getElementById('generateDifficulty').value;
            
            try {
                const response = await fetch('/api/index.php?action=generateWorksheet', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    },
                    body: JSON.stringify({
                        child_id: childId,
                        subject: subject,
                        difficulty: difficulty,
                        date: new Date().toISOString().split('T')[0]
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    generateModal.close();
                    loadWorksheets(); // Refresh worksheets
                    alert('Worksheet generated successfully!');
                } else {
                    alert(result.message || 'Failed to generate worksheet');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        }
        
        // Handle add child
        async function handleAddChild(e) {
            e.preventDefault();
            
            const name = document.getElementById('newChildName').value;
            const ageGroup = document.getElementById('newChildAge').value;
            
            try {
                const response = await fetch('/api/index.php?action=createChild', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    },
                    body: JSON.stringify({
                        name: name,
                        age_group: ageGroup
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    addChildModal.close();
                    loadChildren(); // Refresh children
                    document.getElementById('addChildForm').reset();
                } else {
                    alert(result.message || 'Failed to add child');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        }
        
        // Handle upgrade
        function handleUpgrade() {
            // Redirect to payment page or show payment modal
            window.open('https://buy.stripe.com/your-payment-link', '_blank');
        }
        
        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }
        
        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString();
        }
        
        function generateWorksheetFor(childId) {
            document.getElementById('generateChildSelect').value = childId;
            generateModal.showModal();
        }
        
        function downloadWorksheet(worksheetId) {
            window.open(`/api/index.php?action=downloadWorksheet&id=${worksheetId}`, '_blank');
        }
        
        function previewWorksheet(worksheetId) {
            window.open(`/api/index.php?action=previewWorksheet&id=${worksheetId}`, '_blank');
        }
        
        function logout() {
            localStorage.removeItem('jwt_token');
            window.location.href = '/app/login.php';
        }
    </script>

</body>

</html> 