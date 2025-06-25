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
    <title>Add Your Child - Daily Homework</title>
    <meta name="description" content="Add your child's information to personalize their daily worksheets.">

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

</head>

<body class="bg-gradient-to-br from-blue-50 to-purple-50">

    <!-- Navigation -->
    <nav class="navbar bg-white shadow-lg">
        <div class="navbar-start">
            <a href="/website/" class="btn btn-ghost text-xl font-bold text-primary">
                <i class="fas fa-home mr-2"></i>
                DailyHome.Work
            </a>
        </div>
        <div class="navbar-end">
            <div class="text-sm text-gray-600">
                <i class="fas fa-user-circle mr-1"></i>
                <span id="userEmail">parent@email.com</span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="min-h-screen flex justify-center items-center p-5">
        <div class="w-full max-w-2xl">
            
            <!-- Child Setup Card -->
            <div class="card bg-white shadow-2xl">
                <div class="card-body p-8">
                    
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-child text-white text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800">Tell us about your child</h1>
                        <p class="text-gray-600 mt-2">This helps us create the perfect worksheets just for them</p>
                    </div>

                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <ul class="steps steps-vertical lg:steps-horizontal w-full">
                            <li class="step step-primary">Email</li>
                            <li class="step step-primary">Child Info</li>
                            <li class="step">First Worksheet</li>
                        </ul>
                    </div>

                    <!-- Child Setup Form -->
                    <form id="childSetupForm" class="space-y-6">
                        
                        <!-- Child Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Child's First Name</span>
                                <span class="label-text-alt text-red-500">*</span>
                            </label>
                            <input type="text" id="childName" name="childName"
                                class="input input-bordered w-full" 
                                placeholder="Emma" 
                                required>
                            <label class="label">
                                <span class="label-text-alt text-gray-500">We'll include this in their worksheets</span>
                            </label>
                        </div>

                        <!-- Age Group -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Age Group / Year</span>
                                <span class="label-text-alt text-red-500">*</span>
                            </label>
                            <select id="ageGroup" name="ageGroup" class="select select-bordered w-full" required>
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

                        <!-- Interests (Multiple Select) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Favorite Topics & Interests</span>
                                <span class="label-text-alt text-gray-400">(Optional)</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="animals" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🐾 Animals</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="dinosaurs" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🦕 Dinosaurs</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="space" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🚀 Space</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="sports" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">⚽ Sports</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="princesses" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">👸 Princesses</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="cars" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🚗 Cars</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="nature" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🌺 Nature</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="adventure" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🗺️ Adventure</span>
                                </label>
                                <label class="cursor-pointer label">
                                    <input type="checkbox" name="interests[]" value="art" class="checkbox checkbox-primary checkbox-sm">
                                    <span class="label-text ml-2">🎨 Art</span>
                                </label>
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-gray-500">Select topics to make worksheets more engaging</span>
                            </label>
                        </div>

                        <!-- Subject Preferences -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Preferred Subjects</span>
                                <span class="label-text-alt text-gray-400">(Free plan gets 1 rotating subject)</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="subjects[]" value="maths" class="checkbox checkbox-primary checkbox-sm mr-2">
                                    <span class="btn btn-outline btn-sm">📊 Maths</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="subjects[]" value="english" class="checkbox checkbox-primary checkbox-sm mr-2">
                                    <span class="btn btn-outline btn-sm">📚 English</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="subjects[]" value="spanish" class="checkbox checkbox-primary checkbox-sm mr-2" disabled>
                                    <span class="btn btn-outline btn-sm">🇪🇸 Spanish</span>
                                    <div class="badge badge-warning badge-xs ml-1">Premium</div>
                                </label>
                            </div>
                        </div>

                        <!-- Message Container -->
                        <div id="messageContainer" class="hidden">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span id="messageText"></span>
                            </div>
                        </div>

                        <!-- Continue Button -->
                        <div class="text-center">
                            <button type="submit" id="continueButton" class="btn btn-primary w-full btn-lg">
                                <i class="fas fa-magic mr-2"></i>
                                Create My First Worksheet!
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Quick Tips -->
            <div class="mt-8">
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="font-semibold text-blue-800 mb-3">💡 Quick Tips:</h3>
                    <ul class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-500 mr-2 mt-0.5"></i>
                            <span>Choose interests to make worksheets more engaging</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-500 mr-2 mt-0.5"></i>
                            <span>You can always update this information later in settings</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-500 mr-2 mt-0.5"></i>
                            <span>Premium plans can add up to 3 children</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer footer-center p-4 bg-white/80 backdrop-blur-sm text-base-content">
        <nav class="grid grid-flow-col gap-4">
            <a href="/privacy.php" class="link link-hover">Privacy Policy</a>
            <a href="/terms.php" class="link link-hover">Terms of Service</a>
            <a href="mailto:support@dailyhome.work" class="link link-hover">Contact</a>
        </nav>
    </footer>

    <!-- JavaScript -->
    <script>
        // Handle form submission
        document.getElementById('childSetupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = document.getElementById('continueButton');
            const messageContainer = document.getElementById('messageContainer');
            const messageText = document.getElementById('messageText');
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Creating worksheet...';
            
            // Get form data
            const formData = new FormData(this);
            
            // Get interests array
            const interests = Array.from(document.querySelectorAll('input[name="interests[]"]:checked'))
                .map(cb => cb.value);
            
            // Get subjects array
            const subjects = Array.from(document.querySelectorAll('input[name="subjects[]"]:checked'))
                .map(cb => cb.value);
            
            const childData = {
                name: formData.get('childName'),
                age_group: formData.get('ageGroup'),
                interests: interests,
                subjects: subjects
            };
            
            try {
                // Call API to create child and first worksheet
                const response = await fetch('/api/index.php?action=createChild', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                    },
                    body: JSON.stringify(childData)
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    // Show success message
                    messageContainer.className = 'alert alert-success';
                    messageText.textContent = 'Child added successfully! Redirecting to your worksheets...';
                    messageContainer.classList.remove('hidden');
                    
                    // Redirect to worksheets page
                    setTimeout(() => {
                        window.location.href = 'worksheets.php';
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Failed to create child');
                }
                
            } catch (error) {
                console.error('Error:', error);
                messageContainer.className = 'alert alert-error';
                messageText.textContent = error.message || 'Something went wrong. Please try again.';
                messageContainer.classList.remove('hidden');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-magic mr-2"></i>Create My First Worksheet!';
            }
        });
        
        // Load user email from token if available
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('jwt_token');
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    if (payload.email) {
                        document.getElementById('userEmail').textContent = payload.email;
                    }
                } catch (e) {
                    console.log('Could not parse token');
                }
            }
        });
    </script>

</body>

</html> 