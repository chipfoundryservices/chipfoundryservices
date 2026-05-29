<?php
/**
 * Convert Unicode box-drawing characters to ASCII for web display
 * This fixes rendering issues with Unicode characters in code blocks
 */

function convertUnicodeBoxesToAscii($content) {
    // Mapping of Unicode box-drawing characters to ASCII equivalents
    $replacements = [
        '┌' => '+',
        '┐' => '+',
        '└' => '+',
        '┘' => '+',
        '├' => '+',
        '┤' => '+',
        '┬' => '+',
        '┴' => '+',
        '┼' => '+',
        '─' => '-',
        '│' => '|',
        '▼' => 'v',
        '▲' => '^',
        '►' => '>',
        '◄' => '<',
        '■' => '#',
        '□' => 'o',
        'ö' => 'o',
    ];
    
    // Replace all Unicode box characters with ASCII
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);
    
    return $content;
}

/**
 * Process content before display - convert Unicode boxes
 */
function processContentForWeb($content) {
    // Set proper encoding
    mb_internal_encoding('UTF-8');
    
    // Convert Unicode box-drawing to ASCII
    $content = convertUnicodeBoxesToAscii($content);
    
    return $content;
}

/**
 * Apply to API response
 */
function fixApiResponse($response) {
    return convertUnicodeBoxesToAscii($response);
}
?>
