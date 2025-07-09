<?php

/**
 * Renders the CSS styles required for the cards and hover effect.
 */
function renderCardStyles() {
    echo "
    <style>
        body{background-color:#f7fafc;font-family:'Inter',sans-serif;margin:0;box-sizing:border-box;}
        .page-container{display:flex;flex-direction:column;align-items:center;padding:2rem;}
        *,*:before,*:after{box-sizing:inherit;}
        .card-container{position:relative;width:100%;max-width:28rem;margin-bottom:2rem;}
        .card-content{background-color:#fff;padding:1.5rem;border-radius:1.5rem;border:4px solid #9ae6b4;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05);position:relative;z-index:1;overflow:hidden;}
        .card-content::before{content:'';position:absolute;left:0;top:0;width:50px;height:100%;background-color:#f0a981;clip-path:ellipse(45% 40% at 0% 50%);z-index:0;}
        .card-content::after{content:'';position:absolute;right:0;top:0;width:50px;height:100%;background-color:#d8e1d9;clip-path:ellipse(45% 40% at 100% 50%);z-index:0;}
        .inner-content-wrapper{position:relative;z-index:1;}
        .card-header,.card-footer{display:flex;justify-content:space-between;align-items:center;}
        .card-header{margin-bottom:1rem;}
        .card-body p{color:#718096;margin:0 0 1.5rem 0; line-height: 1.5;}
        .card-buttons{display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;}
        .expo-text{color:#4299e1;font-weight:600;}
        .date-text,.user-text{font-size:0.875rem;}
        .date-text{color:#a0aec0;}
        .user-text{color:#4299e1;font-weight:500;}
        .btn{flex:1 1 0%;padding:0.5rem 1rem;border-radius:0.5rem;border:none;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1),0 1px 2px 0 rgba(0,0,0,0.06);transition:background-color 0.2s;cursor:pointer;font-family:inherit;font-size:100%; text-align:center; text-decoration: none; display:inline-block;}
        .btn-view{background-color:#e2e8f0;color:#2d3748;}.btn-view:hover{background-color:#cbd5e0;}
        .btn-edit{background-color:#000;color:#fff;}.btn-edit:hover{background-color:#2d3748;}
        .btn-copy{background-color:#f6ad55;color:#fff;}.btn-copy:hover{background-color:#ed8936;}
        .hover-window{position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(255,255,255,0.95);backdrop-filter:blur(4px);opacity:0;visibility:hidden;pointer-events:none;transition:opacity .4s ease-in-out,visibility .4s ease-in-out;overflow-y:auto;z-index:10;padding:1.5rem;border-radius:1.5rem;}
        .card-container:hover .hover-window{opacity:1;visibility:visible;pointer-events:auto;transition-delay:1.5s;}
        .hover-window h3{font-weight:700;font-size:1.125rem;margin:0 0 0.5rem 0;color:#2d3748;}
        .hover-window pre{white-space: pre-wrap; word-wrap: break-word; background-color: #f1f1f1; padding: 1rem; border-radius: 0.5rem; color: #333; font-size: 0.875rem;}
    </style>";
}

/**
 * Renders the HTML for a single card with functional buttons and hover content.
 * @param array $postData Associative array containing post data.
 * @param array|null $user The currently logged-in user array, or null if guest.
 * @return string The HTML for the card.
 */
function renderCard($postData, $user = null) {
    $formattedDate = date("M j, Y, g:i a", strtotime($postData['created_at']));
    $snippet = strlen($postData['content']) > 100 ? substr(strip_tags($postData['content']), 0, 100) . '...' : htmlspecialchars($postData['content']);

    $cardHtml = '
    <div class="card-container">
        <!-- Hidden textarea for the copy function -->
        <textarea id="paste-content-card-'. $postData['id'] .'" style="position: absolute; left: -9999px; top: 0; opacity: 0;">'. htmlspecialchars($postData['content']) .'</textarea>
        
        <div class="card-content">
            <div class="inner-content-wrapper">
                <div class="card-header">
                    <span class="expo-text">' . htmlspecialchars($postData['category']) . '</span>
                </div>
                <div class="card-body">
                    <p>' . $snippet . '</p>
                </div>
                <div class="card-buttons">
                    <a href="view.php?id='. $postData['id'] .'" class="btn btn-view">View</a>';

    // Conditionally show the Edit button based on ownership or admin status
    if ($user && ((isset($postData['user_id']) && $user['id'] === $postData['user_id']) || !empty($user['is_superadmin']))) {
        $cardHtml .= '<a href="manage_paste.php?action=edit&id='. $postData['id'] .'" class="btn btn-edit">Edit</a>';
    }

    $cardHtml .= '
                    <button class="btn btn-copy" onclick="copyPasteCard(\'paste-content-card-'. $postData['id'] .'\')">Copy</button>
                </div>
                <div class="card-footer">
                    <span class="date-text">' . htmlspecialchars($formattedDate) . '</span>
                    <span class="user-text">' . htmlspecialchars($postData['author']) . '</span>
                </div>
            </div>
        </div>
        
        <!-- Hover-over window with full content -->
        <div class="hover-window">
            <h3>' . htmlspecialchars($postData['category']) . '</h3>
            <pre>' . htmlspecialchars($postData['content']) . '</pre>
        </div>
    </div>';  
    return $cardHtml;
}

/**
 * Renders the JavaScript required for card functionality (e.g., copy button).
 */
function renderCardScripts() {
    echo '
    <!-- Toast element for copy feedback -->
    <div id="copy-toast" style="position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background-color: #28a745; color: #fff; padding: 12px 25px; border-radius: 8px; z-index: 1050; opacity: 0; visibility: hidden; transition: all 0.5s;">Copied to clipboard!</div>

    <script>
    function copyPasteCard(textareaId) {
        const contentTextarea = document.getElementById(textareaId);
        
        navigator.clipboard.writeText(contentTextarea.value).then(() => {
            const toast = document.getElementById("copy-toast");
            toast.style.opacity = 1;
            toast.style.visibility = "visible";
            toast.style.bottom = "50px";

            setTimeout(() => {
                toast.style.opacity = 0;
                toast.style.visibility = "hidden";
                toast.style.bottom = "30px";
            }, 2000);
        }).catch(err => {
            console.error("Failed to copy: ", err);
        });
    }
    </script>';
}

?>
