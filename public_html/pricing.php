<?php 
$page_title = 'Pricing Plans - Yes Homework';
$page_description = 'Choose the perfect Yes Homework plan for your family. Free plan available, Family Plus for £6/month, or Classroom for educators at £25/month.';
$canonical_url = 'https://yeshomework.com/pricing.php';
include 'website/include/header.html'; 
?>

<main>
    <!-- Hero Section -->
    <section class="py-16 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="container mx-auto max-w-6xl px-4 text-center">
            <h1 class="text-5xl font-bold mb-6 text-gray-800">
                Final Pricing Plans
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Screen-free learning that fits every family. Start free, upgrade when you're ready.
            </p>
            <div class="flex justify-center items-center gap-4 mb-8">
                <span class="text-gray-600">Monthly</span>
                <div class="badge badge-success">No Annual Lock-in</div>
                <span class="text-gray-600">Cancel Anytime</span>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16 bg-white">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                
                <!-- Family Free Plan -->
                <div class="card bg-white shadow-xl border-2 border-gray-200 hover:shadow-2xl transition-all">
                    <div class="card-body p-8">
                        <div class="text-center mb-6">
                            <div class="text-4xl mb-4">🆓</div>
                            <h3 class="text-2xl font-bold mb-2">Family Free</h3>
                            <p class="text-gray-600">"Perfect for getting started"</p>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="text-4xl font-bold text-gray-800">£0</div>
                            <div class="text-gray-600">/month</div>
                        </div>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Up to 2 children</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Personalized worksheets (names + interests)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>1 subject daily (Maths → English → Spanish rotating)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Daily email delivery at 7am</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>15-minute worksheets</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Reception to Year 6 content</span>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-6 p-3 bg-gray-50 rounded-lg">
                            <strong>Perfect for:</strong> Small families wanting to try the service
                        </div>
                        
                        <a href="/app/signup.php" class="btn btn-outline btn-primary w-full btn-lg">
                            <i class="fas fa-rocket mr-2"></i>
                            Start Free Now
                        </a>
                    </div>
                </div>

                <!-- Family Plus Plan -->
                <div class="card bg-white shadow-xl border-4 border-primary hover:shadow-2xl transition-all relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <div class="badge badge-primary badge-lg px-4 py-2 text-white font-bold">
                            MOST POPULAR
                        </div>
                    </div>
                    <div class="card-body p-8">
                        <div class="text-center mb-6">
                            <div class="text-4xl mb-4">💎</div>
                            <h3 class="text-2xl font-bold mb-2 text-primary">Family Plus</h3>
                            <p class="text-gray-600">"Most families choose this!"</p>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="text-4xl font-bold text-primary">£6</div>
                            <div class="text-gray-600">/month</div>
                            <div class="text-sm text-green-600 font-semibold mt-1">
                                Less than £2 per week - cheaper than a coffee!
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Up to 5 children</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>All 3 subjects every day (Maths + English + Spanish)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Fully personalized worksheets</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Download archive access</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Bonus themed packs (holidays, seasons)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Priority email support</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Completion tracking</span>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <strong>Perfect for:</strong> Most UK families (2-4 kids)
                        </div>
                        
                        <a href="/app/signup.php?plan=plus" class="btn btn-primary w-full btn-lg">
                            <i class="fas fa-crown mr-2"></i>
                            Start Family Plus
                        </a>
                    </div>
                </div>

                <!-- Classroom Plan -->
                <div class="card bg-white shadow-xl border-2 border-gray-200 hover:shadow-2xl transition-all">
                    <div class="card-body p-8">
                        <div class="text-center mb-6">
                            <div class="text-4xl mb-4">🏫</div>
                            <h3 class="text-2xl font-bold mb-2">Classroom</h3>
                            <p class="text-gray-600">"For teachers & educators"</p>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="text-4xl font-bold text-gray-800">£25</div>
                            <div class="text-gray-600">/month</div>
                            <div class="text-sm text-green-600 font-semibold mt-1">
                                Just 83p per child per month
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Up to 30 children</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Teacher dashboard with class overview</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Bulk download options</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Individual progress tracking</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>All subjects + curriculum alignment</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Class themes coordination</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Dedicated teacher support</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Invoicing for schools</span>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-6 p-3 bg-gray-50 rounded-lg">
                            <strong>Perfect for:</strong> Teachers, homeschool groups, tutoring centers
                        </div>
                        
                        <a href="/app/signup.php?plan=classroom" class="btn btn-outline btn-secondary w-full btn-lg">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Start Classroom
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Table -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto max-w-6xl px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Plan Comparison Summary</h2>
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full bg-white shadow-lg rounded-lg">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left font-bold">Feature</th>
                            <th class="text-center font-bold">Family Free</th>
                            <th class="text-center font-bold text-primary">Family Plus</th>
                            <th class="text-center font-bold">Classroom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-semibold">Children</td>
                            <td class="text-center">2</td>
                            <td class="text-center text-primary font-bold">5</td>
                            <td class="text-center">30</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Subjects</td>
                            <td class="text-center">1 (rotating)</td>
                            <td class="text-center text-primary font-bold">All 3 daily</td>
                            <td class="text-center">All 3 daily</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Personalization</td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Archive</td>
                            <td class="text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Teacher Tools</td>
                            <td class="text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="font-bold text-lg">Price</td>
                            <td class="text-center font-bold text-lg">Free</td>
                            <td class="text-center font-bold text-lg text-primary">£6/month</td>
                            <td class="text-center font-bold text-lg">£25/month</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto max-w-4xl px-4 text-center text-white">
            <h2 class="text-4xl font-bold mb-6">Ready to Start Screen-Free Learning?</h2>
            <p class="text-xl mb-8 opacity-90">
                Join thousands of families already using Yes Homework. Start free, no credit card required.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/app/signup.php" class="btn btn-white btn-lg">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Free Today
                </a>
                <a href="/#how-it-works" class="btn btn-outline btn-white btn-lg">
                    <i class="fas fa-play mr-2"></i>
                    See How It Works
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include 'website/include/footer.html'; ?>

<script>
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>

</body>
</html> 