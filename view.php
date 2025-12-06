<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
include 'header.php'; // Includes Tailwind, Bootstrap, Custom CSS

// Validate and assign ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="container mx-auto p-4"><div class="alert alert-danger">Invalid paste ID.</div></div>';
    include 'footer.php';
    exit;
}
$id = intval($_GET['id']);

// Handle Report Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'report') {
    $reason = trim($_POST['reason']);
    if (!empty($reason)) {
        $stmt = $pdo->prepare("INSERT INTO reports (paste_id, user_id, reason) VALUES (?, ?, ?)");
        $stmt->execute([$id, $user['id'] ?? null, $reason]);
        $reportSuccess = "Report submitted successfully.";
    }
}

$stmt = $pdo->prepare("SELECT p.*, u.username AS registered_username FROM pastes p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$paste = $stmt->fetch();

if (!$paste) {
    echo '<div class="container mx-auto p-4"><div class="alert alert-danger">Paste not found.</div></div>';
    include 'footer.php';
    exit;
}

$username = $paste['user_id'] ? $paste['registered_username'] : ($paste['username'] ?? 'Anonymous');
?>

<?php include 'navbar.php'; ?>

<!-- Syntax Highlighter CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">

<div class="container mx-auto px-4 py-8">
    
    <?php if (isset($reportSuccess)): ?>
        <div class="alert alert-success mb-6"><?= $reportSuccess ?></div>
    <?php endif; ?>

    <!-- Paste Header -->
    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($paste['title']) ?></h1>
                <div class="text-slate-400 text-sm flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span>Posted by <span class="text-sky-400 font-medium"><?= htmlspecialchars($username) ?></span></span>
                    <span>•</span>
                    <span><?= htmlspecialchars($paste['created_at']) ?></span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2">
                <?php if ($user && ($user['id'] === $paste['user_id'] || !empty($user['is_superadmin']))): ?>
                    <a href="manage_paste.php?action=edit&id=<?= $paste['id'] ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    
                    <button type="button" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/40 text-red-300 border border-red-500/30 rounded-lg text-sm font-medium transition-colors" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                    
                    <!-- Feature Toggle (Admin) -->
                    <?php if (!empty($user['is_superadmin'])): ?>
                        <form method="post" action="admin_dashboard.php" class="inline-block">
                            <input type="hidden" name="action" value="toggle_featured">
                            <input type="hidden" name="paste_id" value="<?= $paste['id'] ?>">
                            <button type="submit" class="px-4 py-2 bg-yellow-500/20 hover:bg-yellow-500/40 text-yellow-300 border border-yellow-500/30 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-star me-1"></i> <?= $paste['is_featured'] ? 'Unfeature' : 'Feature' ?>
                            </button>
                        </form>
                    <?php endif; ?>

                <?php endif; ?>
                
                <button type="button" class="px-4 py-2 bg-orange-500/20 hover:bg-orange-500/40 text-orange-300 border border-orange-500/30 rounded-lg text-sm font-medium transition-colors" data-bs-toggle="modal" data-bs-target="#reportModal">
                   <i class="fas fa-flag me-1"></i> Report
                </button>

                <a href="index.php" class="px-4 py-2 bg-slate-700/50 hover:bg-slate-700 text-slate-300 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Code Content -->
    <div class="glass-panel rounded-2xl p-0 overflow-hidden mb-6 relative group">
        <div class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
            <button id="copyBtn" class="px-3 py-1 bg-slate-700 text-white rounded text-xs shadow-lg hover:bg-slate-600">Copy</button>
        </div>
         <pre class="m-0 p-6 overflow-x-auto"><code id="pasteContent" class="language-<?= htmlspecialchars($paste['syntax_language']) ?> !bg-transparent !p-0 text-sm md:text-base"><?= htmlspecialchars($paste['content']) ?></code></pre>
    </div>

    <!-- Share Section -->
    <div class="glass-panel rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Share this paste</h3>
        <div class="flex gap-3 flex-wrap">
             <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors" onclick="shareOnTwitter()">
                <i class="fab fa-twitter me-1"></i> Twitter
            </button>
            <button class="px-4 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-sm font-medium transition-colors" onclick="shareOnFacebook()">
                <i class="fab fa-facebook me-1"></i> Facebook
            </button>
             <button class="px-4 py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-lg text-sm font-medium transition-colors" onclick="shareOnLinkedIn()">
                <i class="fab fa-linkedin me-1"></i> LinkedIn
            </button>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-slate-800 text-white border-slate-700">
            <form method="post">
                <input type="hidden" name="form_action" value="report">
                <div class="modal-header border-slate-700">
                    <h5 class="modal-title">Report Paste</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-slate-300">Reason for reporting</label>
                        <textarea class="form-control bg-slate-700 text-white border-slate-600 focus:bg-slate-700 focus:text-white" name="reason" rows="3" required placeholder="Spam, abusive content, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-slate-700">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<?php if ($user && ($user['id'] === $paste['user_id'] || !empty($user['is_superadmin']))): ?>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-slate-800 text-white border-slate-700">
            <form method="post" action="manage_paste.php?action=delete&id=<?= $paste['id'] ?>">
                <input type="hidden" name="form_action" value="delete">
                <div class="modal-header border-slate-700">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     <p>Are you sure you want to delete "<strong><?= htmlspecialchars($paste['title']) ?></strong>"?</p>
                </div>
                <div class="modal-footer border-slate-700">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Increment Views
$stmt = $pdo->prepare("UPDATE pastes SET views = views + 1 WHERE id = ?");
$stmt->execute([$id]);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>
    hljs.highlightAll();
    
    // Copy Code
    document.getElementById('copyBtn').addEventListener('click', function() {
        const fullText = document.getElementById('pasteContent').innerText;
        navigator.clipboard.writeText(fullText).then(() => {
            this.textContent = 'Copied!';
            setTimeout(() => this.textContent = 'Copy', 2000);
        });
    });

    // Share Functions (Simplified)
    function shareOnTwitter() {
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=Check this out!`, '_blank');
    }
    function shareOnFacebook() {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
    }
    function shareOnLinkedIn() {
         window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}`, '_blank');
    }
</script>

<?php include 'footer.php'; ?>