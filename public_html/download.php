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

<script>
// Global variables
let downloadToken = '<?php echo htmlspecialchars($token); ?>';
console.log('Download token:', downloadToken);
</script>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Worksheet Download</h1>
            <p class="text-gray-600">This page will implement the feedback system and on-demand worksheet generation.</p>
            <p class="text-sm text-gray-500 mt-4">Token: <?php echo htmlspecialchars($token); ?></p>
        </div>
    </div>
</div>

<?php include 'website/include/footer.html'; ?>
