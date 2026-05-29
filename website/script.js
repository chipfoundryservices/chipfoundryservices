// Simple Google-like functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchButtons = document.querySelectorAll('.search-btn');
    
    // Search suggestions related to chip foundry services
    const suggestions = [
        'AI optimization services',
        'Semiconductor manufacturing',
        'Wet processing equipment',
        'Chemical delivery systems',
        'Multi-agent AI frameworks',
        'LLM optimization techniques',
        'Marangoni drying technology',
        'Manufacturing operations TPM',
        'Quality systems CAPA',
        'Database management services'
    ];
    
    // Handle search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch(this.value);
        }
    });
    
    // Handle search buttons
    searchButtons.forEach(button => {
        button.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                performSearch(query);
            } else if (this.textContent.includes('Technical')) {
                // "I'm Feeling Technical" - show random suggestion
                const randomSuggestion = suggestions[Math.floor(Math.random() * suggestions.length)];
                searchInput.value = randomSuggestion;
                performSearch(randomSuggestion);
            } else {
                performSearch('chip foundry services');
            }
        });
    });
    
    // Simple search function
    function performSearch(query) {
        console.log('Searching for:', query);
        
        // Create a simple results page
        document.body.innerHTML = `
            <div style="font-family: arial, sans-serif; background: #202124; color: #e8eaed; min-height: 100vh; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto;">
                    <div style="margin-bottom: 30px;">
                        <a href="/" style="color: #8ab4f8; text-decoration: none; font-size: 24px;">← Back to Chip Foundry Services</a>
                    </div>
                    
                    <h1 style="color: #8ab4f8; margin-bottom: 20px;">Search Results for "${query}"</h1>
                    
                    <div style="background: #303134; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="color: #8ab4f8; margin-bottom: 10px;">🤖 AI & Machine Learning Services</h3>
                        <p style="color: #bdc1c6; line-height: 1.5;">
                            Multi-agent systems, LLM optimization, speculative decoding, KV caching, 
                            state-space models (Mamba, S4, RWKV, RetNet), differential privacy, 
                            federated learning, and advanced RAG techniques.
                        </p>
                    </div>
                    
                    <div style="background: #303134; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="color: #8ab4f8; margin-bottom: 10px;">🔬 Semiconductor Manufacturing</h3>
                        <p style="color: #bdc1c6; line-height: 1.5;">
                            Wet processing equipment, wet benches, Marangoni drying, megasonic cleaning, 
                            chemical handling & delivery systems, manufacturing operations (TPM), 
                            visual management, quality systems & CAPA.
                        </p>
                    </div>
                    
                    <div style="background: #303134; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="color: #8ab4f8; margin-bottom: 10px;">🗄️ Database Services</h3>
                        <p style="color: #bdc1c6; line-height: 1.5;">
                            Database: chipfoundry.qa_responses with 3,406+ entries and 2,135 recent updates. 
                            MariaDB optimization, data import/export, backup solutions.
                        </p>
                    </div>
                    
                    <div style="background: #303134; padding: 20px; border-radius: 8px;">
                        <h3 style="color: #8ab4f8; margin-bottom: 10px;">🖥️ Infrastructure</h3>
                        <p style="color: #bdc1c6; line-height: 1.5;">
                            AWS Lightsail ChipFoundryServicesNginx-1 (98.86.220.145)<br>
                            Stack: Nginx 1.28.0 + MariaDB + PHP-FPM (Bitnami)<br>
                            24/7 availability with monitoring and backup systems.
                        </p>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Add some hover effects
    document.querySelectorAll('.search-btn, .mic-icon, .camera-icon, .apps-menu').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Focus search input on page load
    setTimeout(() => {
        searchInput.focus();
    }, 100);
    
    console.log('Chip Foundry Services - Google-style interface loaded');
});