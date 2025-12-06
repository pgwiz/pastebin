<?php
include_once 'sanitizer.php';

function renderCardStyles() {
    // Styles handled by Tailwind/Global CSS now. Function kept for compatibility to avoid breaking calls in other files if any.
}

function renderCard($postData, $user = null) {
    // Sanitize
    $id = htmlspecialchars($postData['id']);
    $category = htmlspecialchars($postData['category']);
    $content = $postData['content'];
    $author = htmlspecialchars($postData['author']);
    $userId = isset($postData['user_id']) ? htmlspecialchars($postData['user_id']) : null;
    $created_at = strtotime($postData['created_at']);
    $formattedDate = htmlspecialchars(date("M j, Y, g:i a", $created_at));

    $snippet = htmlspecialchars(substr(strip_tags($content), 0, 100));
    if (strlen(strip_tags($content)) > 100) $snippet .= '...';

    // Hover content
    $sanitized_hover_content = function_exists('sanitize_html') ? sanitize_html($content) : htmlspecialchars($content);

    $cardHtml = '
    <div class="relative w-full h-full group perspective-1000">
        <textarea hidden id="paste-content-card-'. $id .'">' . htmlspecialchars($content) . '</textarea>
        
        <!-- Card Body -->
        <div class="glass-panel rounded-2xl p-6 h-full flex flex-col relative overflow-hidden transition-all duration-300 group-hover:shadow-[0_0_25px_rgba(56,189,248,0.3)] group-hover:-translate-y-1">
            
            <!-- Decor shapes -->
            <div class="absolute -top-10 -left-10 w-20 h-20 bg-blue-500/20 rounded-full blur-xl"></div>
            <div class="absolute -bottom-10 -right-10 w-20 h-20 bg-purple-500/20 rounded-full blur-xl"></div>
            
            <!-- Custom SVG Pattern -->
            <div class="absolute inset-0 opacity-10 pointer-events-none z-0">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid-pattern-'. $id .'" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="white" stroke-width="2" fill="none"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid-pattern-'. $id .')" />
                </svg>
            </div>';

    // Admin Info Button
    if (isset($user['is_superadmin']) && $user['is_superadmin']) {
        $cardHtml .= '
            <button onclick="openAdminSummary('. $id .'); event.preventDefault();" 
                    class="absolute top-4 right-4 z-20 w-8 h-8 flex items-center justify-center rounded-full bg-slate-800/50 text-slate-400 hover:bg-blue-500 hover:text-white transition-all backdrop-blur-sm border border-slate-700/50 translate-y-0"
                    title="Admin Summary">
                <i class="fas fa-info"></i>
            </button>';
    }

    $cardHtml .= '
            <div class="relative z-10 flex flex-col h-full">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-sky-400 truncate w-full" title="'. $category .'">'. $category .'</h3>
                </div>

                <!-- Content Snippet -->
                <div class="mb-6 flex-grow">
                    <p class="text-slate-300 text-sm leading-relaxed line-clamp-3">'. $snippet .'</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 mb-4">
                    <a href="view.php?id='. $id .'" class="flex-1 text-center px-3 py-2 bg-slate-700/50 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition-colors border border-slate-600/50">View</a>';

    if ($user && (($userId && $user['id'] === $userId) || !empty($user['is_superadmin']))) {
        $cardHtml .= '<a href="manage_paste.php?action=edit&id='. $id .'" class="flex-1 text-center px-3 py-2 bg-blue-600/20 hover:bg-blue-600/40 text-blue-300 border border-blue-500/30 rounded-lg text-sm font-medium transition-colors">Edit</a>';
    }

    $cardHtml .= '
                    <button onclick="copyRawContent('. $id .')" class="flex-1 px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600/40 text-emerald-300 border border-emerald-500/30 rounded-lg text-sm font-medium transition-colors">Copy</button>
                </div>

                <!-- Footer -->
                <div class="flex justify-between items-center text-xs text-slate-500 border-t border-slate-700/50 pt-3 mt-auto">
                    <span>'. $formattedDate .'</span>
                    <span class="text-sky-300/80 font-medium">@'. $author .'</span>
                </div>
            </div>
        </div>
    </div>';

    return $cardHtml;
}

function renderCardScripts() {
    echo '
    <div id="copy-toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 opacity-0 invisible z-50 flex items-center space-x-2">
        <i class="fas fa-check-circle"></i>
        <span>Copied to clipboard!</span>
    </div>

    <script>
    function copyRawContent(pasteId) {
        const toast = document.getElementById("copy-toast");
        fetch(`get_raw_paste.php?id=${pasteId}`)
            .then(res => res.text())
            .then(text => {
                navigator.clipboard.writeText(text).then(() => {
                    toast.classList.remove("opacity-0", "invisible", "translate-y-4");
                    setTimeout(() => {
                        toast.classList.add("opacity-0", "invisible", "translate-y-4");
                    }, 2000);
                });
            })
            .catch(console.error);
    }
    </script>';
}
?>
