<?php 
$page_title = 'Add Your Child - Daily Homework';
include 'include/app-header.html'; 
?>

<main class="min-h-screen py-12 px-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Progress Steps -->
        <div class="steps steps-horizontal w-full mb-8">
            <div class="step step-primary">Sign Up</div>
            <div class="step step-primary">Add Child</div>
            <div class="step">Start Learning</div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-secondary rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-child text-2xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4">Tell us about your child</h1>
            <p class="text-lg text-gray-600">
                We'll personalize every worksheet to make learning more engaging
            </p>
        </div>

        <!-- Child Setup Form -->
        <div class="card card-sophisticated shadow-2xl">
            <div class="card-body p-8">
                
                <form id="childSetupForm" class="space-y-8">
                    
                    <!-- Child Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-lg">
                                <i class="fas fa-user text-primary mr-2"></i>
                                What's your child's name?
                            </span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="child_name" 
                               placeholder="Enter your child's first name" 
                               class="input input-bordered w-full input-lg" 
                               required>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">We'll include their name in every worksheet</span>
                        </label>
                    </div>

                    <!-- Age Group -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-lg">
                                <i class="fas fa-birthday-cake text-secondary mr-2"></i>
                                How old are they?
                            </span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="age_group" value="3-5" class="radio radio-primary hidden" required>
                                <div class="card card-compact border-2 border-base-300 hover:border-primary transition-colors age-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-baby text-2xl text-primary mb-2"></i>
                                        <h3 class="font-bold">Ages 3-5</h3>
                                        <p class="text-sm text-gray-600">Reception/Early Years</p>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="age_group" value="6-8" class="radio radio-primary hidden" required>
                                <div class="card card-compact border-2 border-base-300 hover:border-primary transition-colors age-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-child text-2xl text-primary mb-2"></i>
                                        <h3 class="font-bold">Ages 6-8</h3>
                                        <p class="text-sm text-gray-600">Years 1-3</p>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="age_group" value="9-11" class="radio radio-primary hidden" required>
                                <div class="card card-compact border-2 border-base-300 hover:border-primary transition-colors age-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-graduate text-2xl text-primary mb-2"></i>
                                        <h3 class="font-bold">Ages 9-11</h3>
                                        <p class="text-sm text-gray-600">Years 4-6</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Interests -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-lg">
                                <i class="fas fa-heart text-accent mr-2"></i>
                                What are they interested in?
                            </span>
                        </label>
                        <p class="text-sm text-gray-600 mb-4">Select up to 3 interests to personalize their worksheets</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="dinosaurs" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">🦕</span>
                                        <span class="font-semibold">Dinosaurs</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="space" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">🚀</span>
                                        <span class="font-semibold">Space</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="animals" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">🐱</span>
                                        <span class="font-semibold">Animals</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="sports" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">⚽</span>
                                        <span class="font-semibold">Sports</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="princesses" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">👑</span>
                                        <span class="font-semibold">Princesses</span>
                                    </div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="interests[]" value="cars" class="checkbox checkbox-accent hidden">
                                <div class="card card-compact border-2 border-base-300 hover:border-accent transition-colors interest-card">
                                    <div class="card-body text-center py-4">
                                        <span class="text-2xl mb-2">🚗</span>
                                        <span class="font-semibold">Cars</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Subject Preferences -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-lg">
                                <i class="fas fa-book text-info mr-2"></i>
                                Subject preferences
                            </span>
                        </label>
                        <div class="bg-base-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-calculator text-primary mr-3"></i>
                                        <span class="font-semibold">Mathematics</span>
                                    </div>
                                    <div class="badge badge-success">Free</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-book-open text-secondary mr-3"></i>
                                        <span class="font-semibold">English</span>
                                    </div>
                                    <div class="badge badge-success">Free</div>
                                </div>
                                <div class="flex items-center justify-between opacity-60">
                                    <div class="flex items-center">
                                        <i class="fas fa-globe text-warning mr-3"></i>
                                        <span class="font-semibold">Spanish</span>
                                    </div>
                                    <div class="badge badge-warning">Premium</div>
                                </div>
                                <div class="flex items-center justify-between opacity-60">
                                    <div class="flex items-center">
                                        <i class="fas fa-microscope text-info mr-3"></i>
                                        <span class="font-semibold">Science</span>
                                    </div>
                                    <div class="badge badge-warning">Premium</div>
                                </div>
                            </div>
                        </div>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">
                                Free plan includes rotating Maths & English. 
                                <a href="#upgrade" class="link link-primary">Upgrade for all subjects</a>
                            </span>
                        </label>
                    </div>

                    <!-- Continue Button -->
                    <button type="submit" class="btn btn-sophisticated w-full btn-lg">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Start Getting Worksheets
                    </button>

                </form>

            </div>
        </div>

        <!-- Premium Upgrade Prompt -->
        <div class="mt-8">
            <div class="card bg-gradient-to-r from-warning/10 to-orange-100 border border-warning/20">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">
                                <i class="fas fa-crown text-warning mr-2"></i>
                                Want the full experience?
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Upgrade to Premium for personalized content, all subjects, and up to 3 children
                            </p>
                        </div>
                        <button class="btn btn-warning">
                            Upgrade Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Handle card selections
document.addEventListener('DOMContentLoaded', function() {
    // Age group selection
    document.querySelectorAll('input[name="age_group"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.age-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-base-300');
            });
            
            if (this.checked) {
                const card = this.parentElement.querySelector('.age-card');
                card.classList.remove('border-base-300');
                card.classList.add('border-primary', 'bg-primary/5');
            }
        });
    });
    
    // Interest selection (max 3)
    document.querySelectorAll('input[name="interests[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('input[name="interests[]"]:checked');
            const card = this.parentElement.querySelector('.interest-card');
            
            if (this.checked) {
                if (checkedBoxes.length > 3) {
                    this.checked = false;
                    alert('You can select up to 3 interests.');
                    return;
                }
                card.classList.remove('border-base-300');
                card.classList.add('border-accent', 'bg-accent/5');
            } else {
                card.classList.remove('border-accent', 'bg-accent/5');
                card.classList.add('border-base-300');
            }
            
            // Disable/enable other checkboxes
            if (checkedBoxes.length >= 3) {
                document.querySelectorAll('input[name="interests[]"]:not(:checked)').forEach(cb => {
                    cb.disabled = true;
                    cb.parentElement.classList.add('opacity-50');
                });
            } else {
                document.querySelectorAll('input[name="interests[]"]').forEach(cb => {
                    cb.disabled = false;
                    cb.parentElement.classList.remove('opacity-50');
                });
            }
        });
    });
});

// Form submission
document.getElementById('childSetupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    
    // Get selected interests
    const interests = Array.from(document.querySelectorAll('input[name="interests[]"]:checked'))
                          .map(cb => cb.value);
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Setting Up...';
    
    try {
        const response = await fetch('/api/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'setup_child',
                child_name: formData.get('child_name'),
                age_group: formData.get('age_group'),
                interests: interests
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Success - redirect to worksheets
            window.location.href = 'worksheets.php?welcome=true';
        } else {
            alert(data.message || 'Setup failed. Please try again.');
        }
        
    } catch (error) {
        console.error('Setup error:', error);
        alert('Something went wrong. Please try again.');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i>Start Getting Worksheets';
    }
});
</script>

</body>
</html> 