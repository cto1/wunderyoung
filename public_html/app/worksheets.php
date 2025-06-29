<!DOCTYPE html>
<html>
<head>
    <title>Yes Homework - Worksheets</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .section { 
            background: white;
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .section h3 { 
            margin-top: 0; 
            color: #333; 
            border-bottom: 2px solid #3b82f6; 
            padding-bottom: 10px; 
            display: flex;
            align-items: center;
            gap: 8px;
        }
        button { 
            padding: 12px 20px; 
            margin: 5px; 
            background: #3b82f6; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500;
            transition: all 0.3s ease;
        }
        button:hover { 
            background: #2563eb; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        button.secondary { background: #10b981; }
        button.secondary:hover { background: #059669; }
        button.danger { background: #ef4444; }
        button.danger:hover { background: #dc2626; }
        .result { 
            margin-top: 15px; 
            padding: 15px; 
            background: #f8f9fa; 
            border-radius: 8px; 
            white-space: pre-wrap; 
            font-family: monospace; 
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        input, select { 
            padding: 10px; 
            margin: 5px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            width: 100%;
            box-sizing: border-box;
        }
        .form-group { 
            margin: 15px 0; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        .status { 
            padding: 15px; 
            margin: 15px 0; 
            border-radius: 8px; 
            font-weight: 500;
        }
        .status.logged-in { 
            background: #d1fae5; 
            color: #065f46; 
            border: 1px solid #a7f3d0; 
        }
        .status.logged-out { 
            background: #fee2e2; 
            color: #991b1b; 
            border: 1px solid #fecaca; 
        }
        .auth-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .children-list {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }
        .child-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .worksheet-list {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }
        .worksheet-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .hidden {
            display: none;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Yes Homework - Worksheet Generator</h1>
            <p>Generate personalized worksheets for your children based on their interests and age.</p>
        </div>
        
        <!-- Authentication Status -->
        <div id="authStatus" class="status logged-out">
            <strong>Status:</strong> <span id="authText">Not logged in</span>
        </div>
        
        <!-- Authentication Section -->
        <div class="auth-section" id="authSection">
            <h3>🔑 Authentication</h3>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="authEmail" placeholder="parent@example.com">
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" id="authPassword" placeholder="Password">
            </div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" id="authName" placeholder="Parent Name">
            </div>
            
            <button onclick="signup()">Sign Up</button>
            <button onclick="login()" class="secondary">Login</button>
            <button onclick="logout()" class="danger">Logout</button>
            
            <div id="authResult" class="result hidden"></div>
        </div>
        
        <!-- Main App Content (hidden when not logged in) -->
        <div id="appContent" class="hidden">
            <div class="main-content">
                <!-- Child Management -->
                <div class="section">
                    <h3>👶 Child Management</h3>
                    
                    <div class="form-group">
                        <label>Child Name:</label>
                        <input type="text" id="childName" placeholder="Child Name">
                    </div>
                    <div class="form-group">
                        <label>Age Group:</label>
                        <select id="childAge">
                            <option value="5">5 years</option>
                            <option value="6">6 years</option>
                            <option value="7">7 years</option>
                            <option value="8">8 years</option>
                            <option value="9">9 years</option>
                            <option value="10">10 years</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Interest 1:</label>
                        <input type="text" id="childInterest1" placeholder="e.g., dinosaurs">
                    </div>
                    <div class="form-group">
                        <label>Interest 2:</label>
                        <input type="text" id="childInterest2" placeholder="e.g., space">
                    </div>
                    
                    <button onclick="addChild()">Add Child</button>
                    <button onclick="getChildren()" class="secondary">Refresh Children</button>
                    
                    <div id="childrenList" class="children-list"></div>
                    <div id="childResult" class="result hidden"></div>
                </div>
                
                <!-- Worksheet Generation -->
                <div class="section">
                    <h3>📝 Worksheet Generation</h3>
                    
                    <div class="form-group">
                        <label>Select Child:</label>
                        <select id="worksheetChildId">
                            <option value="">Choose a child...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" id="worksheetDate" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button onclick="generateContent()">Generate New Worksheet</button>
                    <button onclick="getWorksheets()" class="secondary">Refresh Worksheets</button>
                    
                    <div id="worksheetsList" class="worksheet-list"></div>
                    <div id="worksheetResult" class="result hidden"></div>
                </div>
            </div>
            
            <!-- PDF and Email Section -->
            <div class="section">
                <h3>📄 PDF & Email</h3>
                
                <div class="form-group">
                    <label>Select Worksheet:</label>
                    <select id="pdfWorksheetId">
                        <option value="">Choose a worksheet...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Parent Email:</label>
                    <input type="email" id="parentEmail" placeholder="parent@example.com">
                </div>
                
                <button onclick="createPDF()">Create PDF</button>
                <button onclick="downloadPDF()" class="secondary">Download PDF</button>
                <button onclick="sendWorksheetEmail()" class="secondary">Email Worksheet</button>
                
                <div id="pdfResult" class="result hidden"></div>
            </div>
        </div>
    </div>

    <script>
        let authToken = localStorage.getItem('authToken');
        let currentUser = null;
        let children = [];
        let worksheets = [];
        
        // Update auth status on load
        updateAuthStatus();
        
        function updateAuthStatus() {
            const statusDiv = document.getElementById('authStatus');
            const authText = document.getElementById('authText');
            const appContent = document.getElementById('appContent');
            const authSection = document.getElementById('authSection');
            
            if (authToken) {
                statusDiv.className = 'status logged-in';
                authText.textContent = 'Logged in';
                appContent.classList.remove('hidden');
                authSection.classList.add('hidden');
                loadChildren();
                loadWorksheets();
            } else {
                statusDiv.className = 'status logged-out';
                authText.textContent = 'Not logged in';
                appContent.classList.add('hidden');
                authSection.classList.remove('hidden');
            }
        }
        
        function getHeaders() {
            const headers = { 'Content-Type': 'application/json' };
            if (authToken) {
                headers['Authorization'] = 'Bearer ' + authToken;
            }
            return headers;
        }
        
        async function signup() {
            const email = document.getElementById('authEmail').value;
            const password = document.getElementById('authPassword').value;
            const name = document.getElementById('authName').value;
            
            if (!email || !password || !name) {
                showResult('authResult', 'Please fill in all fields');
                return;
            }
            
            try {
                const response = await fetch('/api/UserAuthAPI.php?action=signup', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, name })
                });
                
                const result = await response.json();
                showResult('authResult', JSON.stringify(result, null, 2));
                
                if (result.status === 'success') {
                    authToken = result.token;
                    localStorage.setItem('authToken', authToken);
                    updateAuthStatus();
                }
            } catch (error) {
                showResult('authResult', 'Error: ' + error.message);
            }
        }
        
        async function login() {
            const email = document.getElementById('authEmail').value;
            const password = document.getElementById('authPassword').value;
            
            if (!email || !password) {
                showResult('authResult', 'Please enter email and password');
                return;
            }
            
            try {
                const response = await fetch('/api/UserAuthAPI.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                
                const result = await response.json();
                showResult('authResult', JSON.stringify(result, null, 2));
                
                if (result.status === 'success') {
                    authToken = result.token;
                    localStorage.setItem('authToken', authToken);
                    updateAuthStatus();
                }
            } catch (error) {
                showResult('authResult', 'Error: ' + error.message);
            }
        }
        
        function logout() {
            authToken = null;
            localStorage.removeItem('authToken');
            updateAuthStatus();
            showResult('authResult', 'Logged out successfully');
        }
        
        async function addChild() {
            const name = document.getElementById('childName').value;
            const age = document.getElementById('childAge').value;
            const interest1 = document.getElementById('childInterest1').value;
            const interest2 = document.getElementById('childInterest2').value;
            
            if (!name || !age || !interest1 || !interest2) {
                showResult('childResult', 'Please fill in all child fields');
                return;
            }
            
            try {
                const response = await fetch('/api/ChildAPI.php?action=add', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ name, age_group: age, interest1, interest2 })
                });
                
                const result = await response.json();
                showResult('childResult', JSON.stringify(result, null, 2));
                
                if (result.status === 'success') {
                    loadChildren();
                    clearChildForm();
                }
            } catch (error) {
                showResult('childResult', 'Error: ' + error.message);
            }
        }
        
        async function getChildren() {
            try {
                const response = await fetch('/api/ChildAPI.php?action=list', {
                    method: 'GET',
                    headers: getHeaders()
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    children = result.children;
                    displayChildren();
                    updateChildSelects();
                }
            } catch (error) {
                console.error('Error loading children:', error);
            }
        }
        
        function displayChildren() {
            const container = document.getElementById('childrenList');
            container.innerHTML = '';
            
            children.forEach(child => {
                const card = document.createElement('div');
                card.className = 'child-card';
                card.innerHTML = `
                    <strong>${child.name}</strong> (${child.age_group} years)<br>
                    Interests: ${child.interest1}, ${child.interest2}
                `;
                container.appendChild(card);
            });
        }
        
        function updateChildSelects() {
            const worksheetSelect = document.getElementById('worksheetChildId');
            worksheetSelect.innerHTML = '<option value="">Choose a child...</option>';
            
            children.forEach(child => {
                const option = document.createElement('option');
                option.value = child.id;
                option.textContent = `${child.name} (${child.age_group} years)`;
                worksheetSelect.appendChild(option);
            });
        }
        
        function clearChildForm() {
            document.getElementById('childName').value = '';
            document.getElementById('childAge').value = '5';
            document.getElementById('childInterest1').value = '';
            document.getElementById('childInterest2').value = '';
        }
        
        async function generateContent() {
            const childId = document.getElementById('worksheetChildId').value;
            const date = document.getElementById('worksheetDate').value;
            
            if (!childId || !date) {
                showResult('worksheetResult', 'Please select a child and date');
                return;
            }
            
            try {
                const response = await fetch('/api/SimpleWorksheetAPI.php?action=generate-content', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ child_id: childId, date: date })
                });
                
                const result = await response.json();
                showResult('worksheetResult', JSON.stringify(result, null, 2));
                
                if (result.status === 'success') {
                    loadWorksheets();
                }
            } catch (error) {
                showResult('worksheetResult', 'Error: ' + error.message);
            }
        }
        
        async function getWorksheets() {
            try {
                const response = await fetch('/api/SimpleWorksheetAPI.php?action=list', {
                    method: 'GET',
                    headers: getHeaders()
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    worksheets = result.worksheets;
                    displayWorksheets();
                    updateWorksheetSelects();
                }
            } catch (error) {
                console.error('Error loading worksheets:', error);
            }
        }
        
        function displayWorksheets() {
            const container = document.getElementById('worksheetsList');
            container.innerHTML = '';
            
            worksheets.forEach(worksheet => {
                const card = document.createElement('div');
                card.className = 'worksheet-card';
                card.innerHTML = `
                    <strong>Worksheet #${worksheet.id}</strong><br>
                    Child: ${worksheet.child_name}<br>
                    Date: ${worksheet.date}<br>
                    Status: ${worksheet.pdf_path ? 'PDF Ready' : 'Content Only'}
                `;
                container.appendChild(card);
            });
        }
        
        function updateWorksheetSelects() {
            const pdfSelect = document.getElementById('pdfWorksheetId');
            pdfSelect.innerHTML = '<option value="">Choose a worksheet...</option>';
            
            worksheets.forEach(worksheet => {
                const option = document.createElement('option');
                option.value = worksheet.id;
                option.textContent = `Worksheet #${worksheet.id} - ${worksheet.child_name} (${worksheet.date})`;
                pdfSelect.appendChild(option);
            });
        }
        
        async function createPDF() {
            const worksheetId = document.getElementById('pdfWorksheetId').value;
            
            if (!worksheetId) {
                showResult('pdfResult', 'Please select a worksheet');
                return;
            }
            
            try {
                const response = await fetch('/api/SimpleWorksheetAPI.php?action=create-pdf', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ worksheet_id: worksheetId })
                });
                
                const result = await response.json();
                showResult('pdfResult', JSON.stringify(result, null, 2));
                
                if (result.status === 'success') {
                    loadWorksheets();
                }
            } catch (error) {
                showResult('pdfResult', 'Error: ' + error.message);
            }
        }
        
        async function downloadPDF() {
            const worksheetId = document.getElementById('pdfWorksheetId').value;
            
            if (!worksheetId) {
                showResult('pdfResult', 'Please select a worksheet');
                return;
            }
            
            try {
                const response = await fetch(`/api/SimpleWorksheetAPI.php?action=download-pdf&worksheet_id=${worksheetId}`, {
                    method: 'GET',
                    headers: getHeaders()
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `worksheet-${worksheetId}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    showResult('pdfResult', 'PDF downloaded successfully');
                } else {
                    const result = await response.json();
                    showResult('pdfResult', JSON.stringify(result, null, 2));
                }
            } catch (error) {
                showResult('pdfResult', 'Error: ' + error.message);
            }
        }
        
        async function sendWorksheetEmail() {
            const worksheetId = document.getElementById('pdfWorksheetId').value;
            const parentEmail = document.getElementById('parentEmail').value;
            
            if (!worksheetId || !parentEmail) {
                showResult('pdfResult', 'Please select a worksheet and enter parent email');
                return;
            }
            
            // Find the selected worksheet to get child_id
            const selectedWorksheet = worksheets.find(w => w.id === worksheetId);
            if (!selectedWorksheet) {
                showResult('pdfResult', 'Selected worksheet not found');
                return;
            }
            
            try {
                const response = await fetch('/api/EmailAPI.php?action=send-worksheet', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ 
                        child_id: selectedWorksheet.child_id, 
                        worksheet_id: worksheetId, 
                        parent_email: parentEmail 
                    })
                });
                
                const result = await response.json();
                showResult('pdfResult', JSON.stringify(result, null, 2));
            } catch (error) {
                showResult('pdfResult', 'Error: ' + error.message);
            }
        }
        
        function loadChildren() {
            if (authToken) {
                getChildren();
            }
        }
        
        function loadWorksheets() {
            if (authToken) {
                getWorksheets();
            }
        }
        
        function showResult(elementId, message) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.classList.remove('hidden');
        }
    </script>
</body>
</html> 