<?php 
$page_title = 'Access Link Expired - Yes Homework';
include 'include/header.html';
?>

    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-error rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold mb-2 text-error">Link Expired</h2>
                <p class="text-gray-600">This access link is no longer active</p>
            </div>

            <!-- Error Card -->
            <div class="card card-sophisticated shadow-2xl">
                <div class="card-body p-8 text-center">
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>This login link has expired or has already been used.</span>
                    </div>
                    
                    <p class="text-gray-600 mt-4 mb-6">
                        For security reasons, login links expire after 1 hour and can only be used once.
                        Please request a new login link to access your account.
                    </p>
                    
                    <div class="space-y-3">
                        <a href="login.php" class="btn btn-sophisticated w-full">
                            <i class="fas fa-magic mr-2"></i>
                            Request New Login Link
                        </a>
                        
                        <div class="divider">Or</div>
                        
                        <a href="/" class="btn btn-outline w-full">
                            <i class="fas fa-home mr-2"></i>
                            Return to Homepage
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>

</html>
