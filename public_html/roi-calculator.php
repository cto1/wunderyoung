<?php include 'website/include/header.html'; ?>

<body>
  
  <!-- Navbar -->
  <?php include 'website/include/navbar.html'; ?>
  
  <!-- ROI Calculator Hero -->
  <section class="w-full bg-gradient-to-br from-warning/10 to-secondary/10">
      <div class="w-full">
          <section class="relative z-20 w-full max-w-[90rem] mx-auto px-[5%]">
              <div class="pt-16 pb-12 text-center space-y-8 max-w-4xl mx-auto">
                  
                  <span class="bg-warning text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                      ROI Calculator
                  </span>
                  
                  <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold leading-tight">
                      Calculate your potential cost savings
                  </h1>
                  
                  <p class="text-xl md:text-2xl text-base-content/80 max-w-3xl mx-auto leading-relaxed">
                                              Discover how much time and money you could save by using Yes Homework, 
                      the screen-free homework solution that eliminates planning stress.
                  </p>
                  
              </div>
          </section>
      </div>
  </section>

  <main>

    <!-- ROI Calculator Tool -->
    <section class="w-full max-w-[90rem] mx-auto py-[80px] px-[5%]">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <!-- Calculator Form -->
            <div class="card card-sophisticated shadow-2xl">
                <div class="card-body p-8">
                    <h2 class="text-3xl font-bold mb-8 text-center">
                        <i class="fas fa-calculator text-primary mr-3"></i>
                        Your Savings Calculator
                    </h2>
                    
                    <form id="roiCalculator" class="space-y-6">
                        
                        <!-- Number of Children -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-lg">How many children do you have?</span>
                            </label>
                            <select class="select select-bordered w-full text-lg" id="numChildren">
                                <option value="1">1 child</option>
                                <option value="2">2 children</option>
                                <option value="3">3 children</option>
                                <option value="4">4+ children</option>
                            </select>
                        </div>

                        <!-- Time Spent Planning -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-lg">How much time do you spend planning homework per week?</span>
                            </label>
                            <select class="select select-bordered w-full text-lg" id="planningTime">
                                <option value="30">30 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="180">3+ hours</option>
                            </select>
                        </div>

                        <!-- Hourly Value -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-lg">What's your time worth per hour?</span>
                            </label>
                            <select class="select select-bordered w-full text-lg" id="hourlyValue">
                                <option value="15">£15/hour</option>
                                <option value="25">£25/hour</option>
                                <option value="35">£35/hour</option>
                                <option value="50">£50/hour</option>
                                <option value="75">£75/hour</option>
                            </select>
                        </div>

                        <!-- Stress Factor -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-lg">How stressful is homework planning for you?</span>
                            </label>
                            <div class="rating rating-lg">
                                <input type="radio" name="stress" value="1" class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="stress" value="2" class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="stress" value="3" class="mask mask-star-2 bg-orange-400" checked />
                                <input type="radio" name="stress" value="4" class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="stress" value="5" class="mask mask-star-2 bg-orange-400" />
                            </div>
                            <label class="label">
                                <span class="label-text-alt">1 = No stress, 5 = Very stressful</span>
                            </label>
                        </div>

                        <!-- Calculate Button -->
                        <button type="button" onclick="calculateROI()" class="btn btn-sophisticated w-full btn-lg text-lg">
                            <i class="fas fa-chart-line mr-2"></i>
                            Calculate My Savings
                        </button>

                    </form>
                </div>
            </div>

            <!-- Results Display -->
            <div class="space-y-6">
                
                <!-- Results Card -->
                <div id="resultsCard" class="card bg-gradient-to-br from-primary/10 to-secondary/10 shadow-xl hidden">
                    <div class="card-body p-8">
                        <h3 class="text-3xl font-bold mb-6 text-center">
                            <i class="fas fa-trophy text-warning mr-3"></i>
                            Your Potential Savings
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div class="stat bg-base-100 rounded-lg p-6">
                                <div class="stat-title text-lg">Time Saved Per Week</div>
                                <div class="stat-value text-primary text-4xl" id="timeSaved">0 hours</div>
                                <div class="stat-desc text-base">No more homework planning stress</div>
                            </div>
                            
                            <div class="stat bg-base-100 rounded-lg p-6">
                                <div class="stat-title text-lg">Money Saved Per Month</div>
                                <div class="stat-value text-secondary text-4xl" id="moneySaved">£0</div>
                                <div class="stat-desc text-base">Based on your time value</div>
                            </div>
                            
                            <div class="stat bg-base-100 rounded-lg p-6">
                                <div class="stat-title text-lg">Annual Savings</div>
                                <div class="stat-value text-accent text-4xl" id="annualSavings">£0</div>
                                <div class="stat-desc text-base">Total yearly value</div>
                            </div>
                        </div>

                        <div class="mt-8 text-center">
                            <div class="bg-warning/20 rounded-lg p-6 mb-6">
                                <h4 class="font-bold text-xl mb-2">
                                    <i class="fas fa-lightbulb text-warning mr-2"></i>
                                    Yes Homework Premium: €9/month
                                </h4>
                                <p class="text-lg" id="roiMessage">See your ROI calculation above</p>
                            </div>
                            
                            <a href="/app/signup.php" class="btn btn-warning btn-lg text-lg">
                                <i class="fas fa-rocket mr-2"></i>
                                Start Saving Now - Free Trial
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Benefits List -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-8">
                        <h3 class="text-2xl font-bold mb-6">
                            <i class="fas fa-star text-primary mr-3"></i>
                            Beyond Time Savings
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-check-circle text-success text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-lg">Eliminate Planning Stress</h4>
                                    <p class="text-base text-base-content/70">No more last-minute scrambling for homework activities</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-check-circle text-success text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-lg">Consistent Learning</h4>
                                    <p class="text-base text-base-content/70">Daily worksheets ensure regular educational progress</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-check-circle text-success text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-lg">Screen-Free Peace of Mind</h4>
                                    <p class="text-base text-base-content/70">Pure pencil-and-paper learning, no digital distractions</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-check-circle text-success text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-lg">Personalized Content</h4>
                                    <p class="text-base text-base-content/70">Worksheets tailored to your child's interests and level</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Security Section -->
    <section class="w-full bg-base-200">
        <div class="w-full max-w-[90rem] mx-auto py-[80px] px-[5%]">
            <div class="text-center max-w-4xl mx-auto">
                
                <span class="bg-success text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Security & Privacy
                </span>
                
                <h2 class="text-4xl md:text-5xl font-bold mt-8 mb-6">
                    Your family's privacy is paramount
                </h2>
                
                <p class="text-xl md:text-2xl text-base-content/80 leading-relaxed mb-12">
                    When handling your family's information and your child's learning data, security is paramount. 
                    We employ state-of-the-art security measures and undergo regular security reviews from 
                    third-party security firms to ensure the highest level of data protection.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-6 text-center">
                            <i class="fas fa-shield-alt text-success text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold mb-2">Bank-Level Encryption</h3>
                            <p class="text-base">All data encrypted with industry-standard SSL/TLS protocols</p>
                        </div>
                    </div>
                    
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-6 text-center">
                            <i class="fas fa-user-shield text-info text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold mb-2">GDPR Compliant</h3>
                            <p class="text-base">Full compliance with European data protection regulations</p>
                        </div>
                    </div>
                    
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-6 text-center">
                            <i class="fas fa-eye-slash text-warning text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold mb-2">Zero Data Sharing</h3>
                            <p class="text-base">We never share your family's information with third parties</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

  </main>
  
  <!-- Footer -->
  <?php include 'website/include/footer.html'; ?>
  
  <script>
    function calculateROI() {
        const numChildren = parseInt(document.getElementById('numChildren').value);
        const planningTime = parseInt(document.getElementById('planningTime').value); // minutes per week
        const hourlyValue = parseInt(document.getElementById('hourlyValue').value);
        const stress = parseInt(document.querySelector('input[name="stress"]:checked').value);
        
        // Calculate time saved (assume we save 80% of planning time)
        const timeSavedMinutes = planningTime * 0.8;
        const timeSavedHours = timeSavedMinutes / 60;
        
        // Calculate money saved per week
        const moneySavedWeekly = timeSavedHours * hourlyValue;
        const moneySavedMonthly = moneySavedWeekly * 4.33; // average weeks per month
        const moneySavedAnnually = moneySavedMonthly * 12;
        
        // Display results
        document.getElementById('timeSaved').textContent = timeSavedHours.toFixed(1) + ' hours';
        document.getElementById('moneySaved').textContent = '£' + Math.round(moneySavedMonthly);
        document.getElementById('annualSavings').textContent = '£' + Math.round(moneySavedAnnually);
        
        // Calculate ROI vs Premium cost (€9/month = ~£8/month)
        const premiumCostMonthly = 8;
        const netSavingsMonthly = moneySavedMonthly - premiumCostMonthly;
        const roiPercentage = ((netSavingsMonthly / premiumCostMonthly) * 100).toFixed(0);
        
        let roiMessage = '';
        if (netSavingsMonthly > 0) {
            roiMessage = `You save £${Math.round(netSavingsMonthly)} per month after Premium costs - that's a ${roiPercentage}% ROI!`;
        } else {
            roiMessage = `Premium pays for itself in reduced stress and time savings!`;
        }
        
        document.getElementById('roiMessage').textContent = roiMessage;
        document.getElementById('resultsCard').classList.remove('hidden');
        
        // Scroll to results
        document.getElementById('resultsCard').scrollIntoView({ behavior: 'smooth' });
    }
  </script>

</body>

</html> 