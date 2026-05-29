/**
 * Math Diagram Interactive Script
 * Provides hover effects, tooltips, and field information popups
 */

document.addEventListener('DOMContentLoaded', function() {
    const svg = document.getElementById('mathDiagram');
    const boxes = svg.querySelectorAll('.box');
    const connections = svg.querySelectorAll('[class*="connection"]');
    
    // Field information database
    const fieldInfo = {
        algebra: {
            title: 'Algebra',
            description: 'Studies structure, operations, and their properties',
            subfields: ['Linear Algebra', 'Group Theory', 'Ring Theory', 'Field Theory', 'Representation Theory'],
            connections: ['analysis', 'geometry']
        },
        analysis: {
            title: 'Analysis',
            description: 'Rigorous study of continuous change, limits, and infinity',
            subfields: ['Real Analysis', 'Complex Analysis', 'Functional Analysis', 'Measure Theory', 'Differential Equations'],
            connections: ['algebra', 'geometry']
        },
        geometry: {
            title: 'Geometry & Topology',
            description: 'Study of space, shape, and structure',
            subfields: ['Euclidean Geometry', 'Non-Euclidean Geometries', 'Differential Geometry', 'Algebraic Topology'],
            connections: ['algebra', 'analysis']
        }
    };

    // Add interactivity to boxes
    boxes.forEach(box => {
        // Hover effects
        box.addEventListener('mouseenter', function(e) {
            highlightConnected(this);
            showTooltip(e, this);
        });
        
        box.addEventListener('mouseleave', function() {
            clearHighlights();
            hideTooltip();
        });
        
        // Click to show detailed info
        box.addEventListener('click', function(e) {
            e.stopPropagation();
            const fieldType = this.getAttribute('data-field');
            if (fieldType && fieldInfo[fieldType]) {
                showFieldInfo(fieldInfo[fieldType]);
            }
        });
    });

    // Clear highlights on background click
    svg.addEventListener('click', function() {
        clearHighlights();
    });

    // Highlight connected elements
    function highlightConnected(element) {
        const fieldType = element.getAttribute('data-field');
        
        // Highlight the element
        element.style.opacity = '1';
        element.classList.add('active');
        
        // Highlight connected elements
        boxes.forEach(box => {
            const boxField = box.getAttribute('data-field');
            if (fieldType && fieldInfo[fieldType] && 
                fieldInfo[fieldType].connections.includes(boxField)) {
                box.style.opacity = '1';
            } else if (element !== box) {
                box.style.opacity = '0.5';
            }
        });
        
        // Dim connections
        connections.forEach(conn => {
            conn.style.opacity = '0.3';
        });
    }

    // Clear all highlights
    function clearHighlights() {
        boxes.forEach(box => {
            box.style.opacity = '1';
            box.classList.remove('active');
        });
        
        connections.forEach(conn => {
            conn.style.opacity = conn.classList.contains('connection-primary') ? '0.6' : '0.4';
        });
    }

    // Show tooltip on hover
    function showTooltip(event, element) {
        const rect = element.getBoundingClientRect();
        const text = element.querySelector('text');
        
        if (text) {
            const tooltip = document.createElement('div');
            tooltip.id = 'tooltip';
            tooltip.style.cssText = `
                position: fixed;
                left: ${rect.right + 10}px;
                top: ${rect.top}px;
                background: rgba(44, 62, 80, 0.95);
                color: #ecf0f1;
                padding: 10px 15px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 500;
                pointer-events: none;
                z-index: 1000;
                max-width: 250px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            `;
            
            const fieldType = element.getAttribute('data-field');
            if (fieldType && fieldInfo[fieldType]) {
                tooltip.textContent = fieldInfo[fieldType].description;
            } else {
                tooltip.textContent = text.textContent || element.getAttribute('data-info') || 'Mathematical Field';
            }
            
            document.body.appendChild(tooltip);
        }
    }

    // Hide tooltip
    function hideTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Show detailed field information
    function showFieldInfo(info) {
        const modal = document.createElement('div');
        modal.id = 'field-info-modal';
        modal.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 2000;
            max-width: 400px;
            font-family: 'Segoe UI', sans-serif;
            color: #2c3e50;
        `;
        
        modal.innerHTML = `
            <button id="close-modal" style="
                position: absolute;
                top: 10px;
                right: 10px;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #7f8c8d;
            ">×</button>
            <h2 style="margin: 0 0 15px 0; color: #2980b9;">${info.title}</h2>
            <p style="margin: 0 0 15px 0; line-height: 1.6; color: #34495e;">${info.description}</p>
            <h3 style="margin: 15px 0 10px 0; font-size: 14px; color: #2c3e50;">Subfields:</h3>
            <ul style="margin: 0 0 15px 0; padding-left: 20px; color: #34495e;">
                ${info.subfields.map(sf => `<li style="margin-bottom: 5px;">${sf}</li>`).join('')}
            </ul>
            <p style="margin: 0; font-size: 12px; color: #7f8c8d; font-style: italic;">
                Click elsewhere or the × button to close
            </p>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal
        const closeBtn = modal.querySelector('#close-modal');
        closeBtn.addEventListener('click', () => modal.remove());
        
        // Close on background click
        modal.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => {
            if (document.getElementById('field-info-modal')) {
                document.getElementById('field-info-modal').remove();
            }
        });
    }

    // Responsive SVG scaling
    function scaleSVG() {
        const container = document.querySelector('.diagram-container');
        const svg = document.getElementById('mathDiagram');
        
        if (window.innerWidth < 768) {
            svg.setAttribute('viewBox', '0 0 1000 1000');
        } else {
            svg.setAttribute('viewBox', '0 0 1000 900');
        }
    }

    // Handle window resize
    window.addEventListener('resize', scaleSVG);
    
    // Initial scale
    scaleSVG();

    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('field-info-modal');
            if (modal) {
                modal.remove();
            }
            clearHighlights();
        }
    });

    // Add accessibility attributes
    boxes.forEach((box, index) => {
        box.setAttribute('role', 'button');
        box.setAttribute('tabindex', index);
        box.setAttribute('aria-label', 'Mathematical field');
    });

    // Performance optimization: debounce resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(scaleSVG, 250);
    });

    console.log('Math Diagram initialized successfully');
});
