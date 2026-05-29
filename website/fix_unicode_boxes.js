// Fix Unicode box-drawing characters rendering in code blocks
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixUnicodeBoxes);
    } else {
        fixUnicodeBoxes();
    }
    
    function fixUnicodeBoxes() {
        // Find all code blocks
        const codeBlocks = document.querySelectorAll('pre code, pre');
        
        codeBlocks.forEach(block => {
            let content = block.textContent || block.innerText;
            
            // Check if content has Unicode box-drawing characters
            if (/[┌┐└┘─│├┤┬┴┼▼▲►◄]/.test(content)) {
                // Ensure the font supports Unicode
                block.style.fontFamily = "'DejaVu Sans Mono', 'Courier New', 'Consolas', 'Monaco', monospace";
                block.style.fontSize = '14px';
                block.style.lineHeight = '1.4';
                block.style.whiteSpace = 'pre';
                block.style.letterSpacing = '0';
            }
        });
    }
})();
