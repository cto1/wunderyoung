<?php 
$page_title = 'Sign In - Daily Homework';
include 'include/app-header.html'; 
?>

<main class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-home text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold mb-2">Welcome back</h2>
            <p class="text-gray-600">Sign in to access your child's worksheets</p>
        </div>

        <!-- Login Form -->
        <div class="card card-sophisticated shadow-2xl">
            <div class="card-body p-8">
                <form id="loginForm" class="space-y-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Email Address</span>
                        </label>
                        <input type="email" name="email" placeholder="parent@example.com" class="input input-bordered w-full" required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="Enter your password" class="input input-bordered w-full" required>
                    </div>

                    <button type="submit" class="btn btn-sophisticated w-full btn-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                </form>
            </div>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center">
            <p class="text-gray-600">
                Don't have an account? 
                <a href="signup.php" class="link link-primary font-semibold">Get started free</a>
            </p>
        </div>

    </div>
</main>

</body>
</html> 