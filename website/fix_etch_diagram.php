<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get current content
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 10717");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $content = $row['response'];
        
        // Remove extra dash lines from tables
        $content = preg_replace('/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/', '', $content);
        
        // Replace the ASCII diagram section with one giant diagram on grey background
        $oldDiagram = "9.1 Complete Flow

                  Plasma Parameters
                          ↓
              Ion/Neutral Energy-Angle Distributions
                          ↓
    ┌─────────────────────┴─────────────────────┐
    ↓                                           ↓
Transport in Feature                    Surface Chemistry
(Knudsen, charging)                   (coverage, reactions)
    ↓                                           ↓
    └─────────────────────┬─────────────────────┘
                          ↓
                  Local Etch Velocity
                    Vn(x, θ, Γ, T)
                          ↓
              Surface Evolution Equation
              ∂φ/∂t + Vn|∇φ| = 0
                          ↓
                   Etch Profile";

        $newDiagram = "9.1 Complete Flow

$$
\\begin{array}{c}
\\text{Plasma Parameters} \\\\
\\downarrow \\\\
\\text{Ion/Neutral Energy-Angle Distributions} \\\\
\\downarrow \\\\
\\begin{array}{|c|}
\\hline
\\begin{array}{cc}
\\text{Transport in Feature} & \\text{Surface Chemistry} \\\\
\\text{(Knudsen, charging)} & \\text{(coverage, reactions)}
\\end{array} \\\\
\\hline
\\end{array} \\\\
\\downarrow \\\\
\\text{Local Etch Velocity} \\\\
V_n(x, \\theta, \\Gamma, T) \\\\
\\downarrow \\\\
\\text{Surface Evolution Equation} \\\\
\\frac{\\partial\\phi}{\\partial t} + V_n|\\nabla\\phi| = 0 \\\\
\\downarrow \\\\
\\text{Etch Profile}
\\end{array}
$$";

        $content = str_replace($oldDiagram, $newDiagram, $content);
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 10717");
        $updateStmt->execute([$content]);
        
        echo "Successfully updated etch profile modeling!\n";
        echo "- Removed extra dash lines from tables\n";
        echo "- Replaced ASCII diagram with unified grey background diagram\n";
        echo "Content length: " . strlen($content) . " characters\n";
        
    } else {
        echo "Entry not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>