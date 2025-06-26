<?php 
$page_title = 'Get Started - Daily Homework';
include 'include/app-header.html'; 
?>

<main class="min-h-screen py-12 px-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Progress Steps -->
        <div class="steps steps-horizontal w-full mb-8">
            <div class="step step-primary">Sign Up</div>
            <div class="step">Add Child</div>
            <div class="step">Start Learning</div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-rocket text-2xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4">Start your free trial</h1>
            <p class="text-lg text-gray-600">
                Join hundreds of families enjoying screen-free homework
            </p>
        </div>

        <!-- Signup Form -->
        <div class="card card-sophisticated shadow-2xl">
            <div class="card-body p-8">
                
                <!-- Benefits Preview -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-lg p-6 mb-8">
                    <h3 class="font-bold text-lg mb-4 text-center">
                        <i class="fas fa-gift text-primary mr-2"></i>
                        What you'll get free forever
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <i class="fas fa-calendar-check text-primary text-2xl mb-2"></i>
                            <h4 class="font-semibold">Daily Worksheets</h4>
                            <p class="text-sm text-gray-600">Fresh content every morning</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-child text-secondary text-2xl mb-2"></i>
                            <h4 class="font-semibold">1 Child</h4>
                            <p class="text-sm text-gray-600">Perfect for getting started</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-book text-accent text-2xl mb-2"></i>
                            <h4 class="font-semibold">Rotating Subjects</h4>
                            <p class="text-sm text-gray-600">Maths & English weekly</p>
                        </div>
                    </div>
                </div>

                <form id="signupForm" class="space-y-6">
                    
                    <!-- Email Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Email Address</span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               placeholder="parent@example.com" 
                               class="input input-bordered w-full" 
                               required>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">We'll send your worksheets here</span>
                        </label>
                    </div>

                    <!-- Password Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Create Password</span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               placeholder="Choose a secure password" 
                               class="input input-bordered w-full" 
                               required>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">At least 8 characters</span>
                        </label>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" name="terms" class="checkbox checkbox-primary mr-3" required>
                            <span class="label-text">
                                I agree to the 
                                <a href="/terms.php" target="_blank" class="link link-primary">Terms of Service</a> 
                                and 
                                <a href="/privacy.php" target="_blank" class="link link-primary">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Marketing Opt-in -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" name="marketing" class="checkbox checkbox-secondary mr-3" checked>
                            <span class="label-text">
                                Send me tips and updates about Daily Homework (optional)
                            </span>
                        </label>
                    </div>

                    <!-- Sign Up Button -->
                    <button type="submit" class="btn btn-sophisticated w-full btn-lg">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Free Account
                    </button>

                </form>

                <!-- Divider -->
                <div class="divider my-6">Already have an account?</div>

                <!-- Sign In Link -->
                <div class="text-center">
                    <a href="login.php" class="btn btn-outline btn-primary">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In Instead
                    </a>
                </div>

            </div>
        </div>

        <!-- Trust Signals -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-shield-alt text-success"></i>
                <span class="text-sm">100% Secure</span>
            </div>
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-credit-card text-info"></i>
                <span class="text-sm">No Credit Card</span>
            </div>
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-times-circle text-warning"></i>
                <span class="text-sm">Cancel Anytime</span>
            </div>
        </div>

        <!-- Upgrade Preview -->
        <div class="mt-12">
            <div class="card bg-gradient-to-r from-warning/10 to-orange-100 border border-warning/20">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">
                                <i class="fas fa-star text-warning mr-2"></i>
                                Want more? Upgrade to Premium
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                3 children • All subjects • Full personalization • €9/month
                            </p>
                        </div>
                        <div class="badge badge-warning">
                            Most Popular
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Form handling
document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Creating Account...';
    
    try {
        const response = await fetch('/api/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'signup',
                email: formData.get('email'),
                password: formData.get('password'),
                terms: formData.get('terms') ? true : false,
                marketing: formData.get('marketing') ? true : false
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Success - redirect to child setup
            window.location.href = 'child-setup.php';
        } else {
            // Show error
            alert(data.message || 'Signup failed. Please try again.');
        }
        
    } catch (error) {
        console.error('Signup error:', error);
        alert('Something went wrong. Please try again.');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Free Account';
    }
});

// Pre-fill email from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const email = urlParams.get('email');
if (email) {
    document.querySelector('input[name="email"]').value = email;
}
</script>

</body>
</html> 