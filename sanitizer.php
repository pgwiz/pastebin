<?php
/**
 * Sanitizes user-submitted HTML content using HTML Purifier.
 *
 * This function is designed to clean HTML content submitted by users,
 * ensuring that it is safe and does not contain any malicious code.
 * It uses the HTML Purifier library, which is a well-known tool for
 * sanitizing HTML.
 *
 * @package MyApp
 */
/**
 * A central function to sanitize user-submitted HTML content.
 *
 * @param string $dirty_html The HTML content to clean.
 * @return string The cleaned, safe HTML.
 */
function sanitize_html($dirty_html) {
    // This check is to prevent errors if the library isn't actually installed.
    if (!class_exists('HTMLPurifier')) {
        // Fallback to the basic function if HTML Purifier is not available.
        // This ensures the site doesn't break while providing basic security.
        return htmlspecialchars($dirty_html);
    }

    $config = HTMLPurifier_Config::createDefault();

    // --- Configuration Examples ---

    // Example 1: Allow basic formatting (bold, italic) but no links or images.
    //$config->set('HTML.Allowed', 'p,b,strong,i,em,u,br');

    // Example 2: Allow links, but force them to open in a new tab and add rel="nofollow".
     $config->set('HTML.Allowed', 'p,b,strong,i,em,u,br,a[href]');
     $config->set('Attr.AllowedFrameTargets', array('_blank'));
     $config->set('HTML.Nofollow', true);

    // Example 3 (Default): Strip ALL HTML for maximum security, similar to strip_tags + htmlspecialchars.
    // $config->set('HTML.Allowed', '');


    $purifier = new HTMLPurifier($config);
    return $purifier->purify($dirty_html);
}
