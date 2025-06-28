<?php 
$page_title = 'Privacy Policy - Yes Homework';
$page_description = 'Learn how Yes Homework protects your privacy and handles your personal information.';
$canonical_url = 'https://yeshomework.com/privacy.php';
include 'website/include/header.html'; 
?>
    <!-- Navigation -->
    <div class="navbar bg-base-100 shadow-lg">
        <div class="navbar-start">
            <a href="/" class="btn btn-ghost text-xl font-bold">Yes Homework</a>
        </div>
        <div class="navbar-end">
            <a href="/" class="btn btn-primary">Back to Home</a>
        </div>
    </div>

    <!-- Privacy Policy Content -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="prose prose-lg max-w-none">
            <h1 class="text-4xl font-bold mb-6">Privacy Policy</h1>
            <p class="text-gray-600 mb-8">Last updated: <?php echo date('d F Y'); ?></p>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <p class="text-lg leading-relaxed">
                    Your privacy is important to us. It is Yes Homework's policy to respect your privacy regarding any information we may collect from you across our website, and other sites we own and operate.
                </p>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Information We Collect</h2>
                <p class="mb-4">We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we're collecting it and how it will be used.</p>
                
                <h3 class="text-xl font-semibold mb-2">Personal Information</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Email Address:</strong> Required for account creation and worksheet delivery</li>
                    <li><strong>Child's Information:</strong> Name, age group, and interests for personalisation</li>
                    <li><strong>Payment Information:</strong> Processed securely through Stripe for premium subscriptions</li>
                    <li><strong>Usage Data:</strong> How you interact with our service to improve functionality</li>
                </ul>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Magic Link Authentication</h2>
                <p>We use passwordless authentication via magic links sent to your email. This means:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li>No passwords are stored on our servers</li>
                    <li>Access tokens are temporary and expire automatically</li>
                    <li>Each login link is unique and single-use</li>
                    <li>Enhanced security through email verification</li>
                </ul>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">How We Use Your Information</h2>
                <p>We use the collected information for the following purposes:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li><strong>Service Delivery:</strong> Creating and sending personalised worksheets</li>
                    <li><strong>Account Management:</strong> Managing your subscription and preferences</li>
                    <li><strong>Communication:</strong> Sending service-related emails and updates</li>
                    <li><strong>Improvement:</strong> Analysing usage to enhance our service</li>
                    <li><strong>Legal Compliance:</strong> Meeting regulatory and legal requirements</li>
                </ul>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Data Storage and Security</h2>
                <p>We take data security seriously:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li><strong>Secure Storage:</strong> Data is stored in encrypted SQLite databases</li>
                    <li><strong>Limited Retention:</strong> We only retain information as long as necessary to provide our service</li>
                    <li><strong>Access Controls:</strong> Strict access controls and authentication measures</li>
                    <li><strong>Regular Backups:</strong> Secure, encrypted backups of essential data</li>
                    <li><strong>UK/EU Servers:</strong> Data is stored within the UK/EU jurisdiction</li>
                </ul>
                <p class="mt-4">What data we store, we'll protect within commercially acceptable means to prevent loss and theft, as well as unauthorised access, disclosure, copying, use or modification.</p>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Data Sharing and Third Parties</h2>
                <p>We don't share any personally identifying information publicly or with third-parties, except when required by law.</p>
                <p class="mt-4"><strong>Third-party services we use:</strong></p>
                <ul class="list-disc list-inside mt-2 space-y-2">
                    <li><strong>Stripe:</strong> For secure payment processing (premium subscriptions)</li>
                    <li><strong>Mailgun:</strong> For reliable email delivery of worksheets</li>
                    <li><strong>OpenAI:</strong> For generating personalised worksheet content</li>
                </ul>
                <p class="mt-4">These services have their own privacy policies and we ensure they meet appropriate data protection standards.</p>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">GDPR Compliance</h2>
                <p>We act in the capacity of a data controller and a data processor with regard to the personal data processed through Yes Homework and our services in terms of the applicable data protection laws, including the EU General Data Protection Regulation (GDPR).</p>
                
                <h3 class="text-xl font-semibold mb-2 mt-4">Your Rights Under GDPR</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Right to Access:</strong> Request a copy of your personal data</li>
                    <li><strong>Right to Rectification:</strong> Correct inaccurate personal data</li>
                    <li><strong>Right to Erasure:</strong> Request deletion of your personal data</li>
                    <li><strong>Right to Portability:</strong> Receive your data in a portable format</li>
                    <li><strong>Right to Object:</strong> Object to processing of your personal data</li>
                    <li><strong>Right to Withdraw Consent:</strong> Withdraw consent at any time</li>
                </ul>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Children's Privacy</h2>
                <p>Our service is designed for children aged 3-11 with parental supervision:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li>Parent/guardian consent is required for all child accounts</li>
                    <li>We collect minimal information about children (name, age, interests)</li>
                    <li>Child data is used solely for educational content personalisation</li>
                    <li>Parents can request deletion of child data at any time</li>
                    <li>We comply with UK and EU regulations regarding children's data</li>
                </ul>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Cookies and Tracking</h2>
                <p>We use minimal cookies for essential functionality:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li><strong>Authentication Cookies:</strong> To keep you logged in</li>
                    <li><strong>Preference Cookies:</strong> To remember your settings</li>
                    <li><strong>Security Cookies:</strong> To protect against fraud</li>
                </ul>
                <p class="mt-4">We do not use tracking cookies for advertising or analytics without your explicit consent.</p>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">External Links</h2>
                <p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Your Choices</h2>
                <p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.</p>
                <p class="mt-4">You can:</p>
                <ul class="list-disc list-inside mt-2 space-y-2">
                    <li>Update your account information at any time</li>
                    <li>Cancel your subscription without penalty</li>
                    <li>Request deletion of your account and data</li>
                    <li>Opt out of non-essential communications</li>
                </ul>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Data Breach Notification</h2>
                <p>In the unlikely event of a data breach that poses a risk to your rights and freedoms, we will:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li>Notify the relevant supervisory authority within 72 hours</li>
                    <li>Inform affected users without undue delay</li>
                    <li>Provide clear information about the breach and remedial actions</li>
                    <li>Take immediate steps to secure the affected systems</li>
                </ul>
            </div>

            <div class="bg-base-200 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Changes to This Policy</h2>
                <p>We may update this privacy policy from time to time. We will notify you of any significant changes by:</p>
                <ul class="list-disc list-inside mt-4 space-y-2">
                    <li>Sending an email to your registered address</li>
                    <li>Posting a notice on our website</li>
                    <li>Updating the "Last updated" date at the top of this policy</li>
                </ul>
                <p class="mt-4">Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information.</p>
            </div>

            <div class="bg-base-100 p-6 rounded-lg mb-8">
                <h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
                <p>If you have any questions about how we handle user data and personal information, feel free to contact us:</p>
                <div class="mt-4">
                    <p><strong>Email:</strong> privacy@Yeshome.work</p>
                    <p><strong>Data Protection Officer:</strong> legal@yeshomework.com</p>
                    <p><strong>Website:</strong> yeshomework.com</p>
                    <p><strong>Address:</strong> Yes Homework Ltd, United Kingdom</p>
                </div>
            </div>

            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold">Effective Date</h3>
                    <div class="text-xs">This policy is effective as of <?php echo date('d F Y'); ?>. By using our service, you acknowledge that you have read and understood this privacy policy.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-neutral text-neutral-content mt-16">
        <aside>
            <p>&copy; <?php echo date('Y'); ?> Yes Homework</p>
        </aside>
    </footer>
</body>
</html>