<!DOCTYPE html>
<html>
<head>
    <title>Worksheet Feedback - Yes Homework</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #3b82f6; text-align: center; margin-bottom: 30px; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input[type="text"], input[type="email"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        textarea { height: 100px; resize: vertical; }
        button { background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
        button:hover { background: #2563eb; }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .worksheet-info { background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Worksheet Feedback</h1>
        
        <?php
        require_once 'api/conf.php';
        require_once 'api/env.php';
        loadEnv();
        
        $token = $_GET['token'] ?? '';
        $worksheetInfo = null;
        $error = null;
        
        if ($token) {
            try {
                $pdo = Database::getInstance()->getPDO();
                
                // Extract worksheet ID from token (format: fb_xxx_ws_123)
                $parts = explode('_', $token);
                if (count($parts) >= 3) {
                    $worksheetId = end($parts);
                    
                    // Get worksheet info
                    $stmt = $pdo->prepare("
                        SELECT w.*, c.name as child_name 
                        FROM worksheets w 
                        JOIN children c ON w.child_id = c.id 
                        WHERE w.id = ?
                    ");
                    $stmt->execute([$worksheetId]);
                    $worksheetInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$worksheetInfo) {
                        $error = 'Worksheet not found';
                    }
                } else {
                    $error = 'Invalid token format';
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'No token provided';
        }
        ?>
        
        <?php if ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($worksheetInfo): ?>
            <div class="worksheet-info">
                <h3>Worksheet Information</h3>
                <p><strong>Child:</strong> <?php echo htmlspecialchars($worksheetInfo['child_name']); ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($worksheetInfo['date'])); ?></p>
            </div>
            
            <form id="feedbackForm">
                <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="parentName">Your Name:</label>
                    <input type="text" id="parentName" name="parentName" required>
                </div>
                
                <div class="form-group">
                    <label for="parentEmail">Your Email:</label>
                    <input type="email" id="parentEmail" name="parentEmail" required>
                </div>
                
                <div class="form-group">
                    <label for="difficulty">How difficult was this worksheet for your child?</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="">Select difficulty level</option>
                        <option value="too_easy">Too Easy</option>
                        <option value="just_right">Just Right</option>
                        <option value="challenging">Challenging</option>
                        <option value="too_hard">Too Hard</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="engagement">How engaged was your child with this worksheet?</label>
                    <select id="engagement" name="engagement" required>
                        <option value="">Select engagement level</option>
                        <option value="very_engaged">Very Engaged</option>
                        <option value="somewhat_engaged">Somewhat Engaged</option>
                        <option value="neutral">Neutral</option>
                        <option value="not_engaged">Not Engaged</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="completion">Did your child complete the worksheet?</label>
                    <select id="completion" name="completion" required>
                        <option value="">Select completion status</option>
                        <option value="completed">Completed</option>
                        <option value="partially">Partially Completed</option>
                        <option value="not_completed">Not Completed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="favoritePart">What was your child's favorite part?</label>
                    <textarea id="favoritePart" name="favoritePart" placeholder="Tell us what your child enjoyed most..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="challengingPart">What was most challenging?</label>
                    <textarea id="challengingPart" name="challengingPart" placeholder="Tell us what was difficult..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="suggestions">Any suggestions for improvement?</label>
                    <textarea id="suggestions" name="suggestions" placeholder="We'd love to hear your ideas..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="wouldRecommend">Would you recommend this type of worksheet to other parents?</label>
                    <select id="wouldRecommend" name="wouldRecommend" required>
                        <option value="">Select recommendation</option>
                        <option value="definitely">Definitely Yes</option>
                        <option value="probably">Probably Yes</option>
                        <option value="maybe">Maybe</option>
                        <option value="probably_not">Probably Not</option>
                        <option value="definitely_not">Definitely Not</option>
                    </select>
                </div>
                
                <button type="submit">Submit Feedback</button>
            </form>
            
            <div id="result"></div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('feedbackForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            data.token = document.getElementById('token').value;
            
            try {
                const response = await fetch('/api/FeedbackAPI.php?action=submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    document.getElementById('result').innerHTML = `
                        <div class="success">
                            <strong>Thank you!</strong> Your feedback has been submitted successfully. 
                            We appreciate your input and will use it to create better worksheets for your child.
                        </div>
                    `;
                    document.getElementById('feedbackForm').style.display = 'none';
                } else {
                    document.getElementById('result').innerHTML = `
                        <div class="error">
                            <strong>Error:</strong> ${result.message}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('result').innerHTML = `
                    <div class="error">
                        <strong>Error:</strong> Failed to submit feedback. Please try again.
                    </div>
                `;
            }
        });
    </script>
</body>
</html> 