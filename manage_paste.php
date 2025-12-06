<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
include 'header.php';

$languages = ['plaintext', 'php', 'javascript', 'html', 'css', 'python', 'sql', 'bash', 'json', 'xml']; 

$action = $_GET['action'] ?? 'create';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$paste = null;
$pageTitle = 'Create Paste';
$submitLabel = 'Create Paste';
$shared_users = [];
$share_message = '';

// Auth Logic (Simplified for brevity but preserving security)
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pastes WHERE id = ?");
    $stmt->execute([$id]);
    $paste = $stmt->fetch();
    
    if(!$paste) {
        echo "<div class='container p-4 alert alert-danger'>Paste not found.</div>";
        include 'footer.php'; exit;
    }

    // Check permissions
    $isOwner = ($user && $user['id'] === $paste['user_id']);
    $isAdmin = ($user && !empty($user['is_superadmin']));
    
    // For edit/share/delete, check rights
    if (in_array($action, ['edit', 'share', 'delete'])) {
        if (!$isOwner && !$isAdmin) {
             // Check shared permissions for edit
             $permStmt = $pdo->prepare("SELECT COUNT(*) FROM paste_permissions WHERE paste_id = ? AND user_id = ? AND permission_type = 'edit'");
             $permStmt->execute([$id, $user['id']]);
             if ($permStmt->fetchColumn() == 0) {
                 echo "<div class='container p-4 alert alert-danger'>Unauthorized.</div>";
                 include 'footer.php'; exit;
             }
        }
    }
    
    if($action === 'edit') {
        $pageTitle = 'Edit Paste';
        $submitLabel = 'Update Paste';
        
        // Fetch shared users
        $sStmt = $pdo->prepare("SELECT u.username FROM users u JOIN paste_permissions pp ON u.id = pp.user_id WHERE pp.paste_id = ?");
        $sStmt->execute([$id]);
        $shared_users = $sStmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_action = $_POST['form_action'] ?? $action;
    
    if ($form_action === 'create') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $syntax = $_POST['syntax'];
        
        if ($user) {
            $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $syntax, $user['id']]);
        } else {
             $guestUser = 'Guest_'.substr(md5(uniqid()), 0, 6);
             $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, username) VALUES (?, ?, ?, ?)");
             $stmt->execute([$title, $content, $syntax, $guestUser]);
        }
        header("Location: index.php"); exit;
    }
    elseif ($form_action === 'edit') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $syntax = $_POST['syntax'];
        $stmt = $pdo->prepare("UPDATE pastes SET title=?, content=?, syntax_language=? WHERE id=?");
        $stmt->execute([$title, $content, $syntax, $id]);
        header("Location: view.php?id=$id"); exit;
    }
    elseif ($form_action === 'share') {
        $targetUser = trim($_POST['username_to_share']);
        $uStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $uStmt->execute([$targetUser]);
        $tIdx = $uStmt->fetch();
        
        if ($tIdx) {
            if ($tIdx['id'] == $paste['user_id']) {
                $share_message = '<div class="alert alert-warning mt-2">Cannot share with owner.</div>';
            } else {
                $pStmt = $pdo->prepare("INSERT INTO paste_permissions (paste_id, user_id, permission_type) VALUES (?, ?, 'edit') ON DUPLICATE KEY UPDATE permission_type='edit'");
                $pStmt->execute([$id, $tIdx['id']]);
                $share_message = '<div class="alert alert-success mt-2">Shared with '.htmlspecialchars($targetUser).'</div>';
                // Refresh shared list
                $sStmt = $pdo->prepare("SELECT u.username FROM users u JOIN paste_permissions pp ON u.id = pp.user_id WHERE pp.paste_id = ?");
                $sStmt->execute([$id]);
                $shared_users = $sStmt->fetchAll(PDO::FETCH_COLUMN);
            }
        } else {
            $share_message = '<div class="alert alert-danger mt-2">User not found.</div>';
        }
        $action = 'edit'; // Stay on edit page
    }
    elseif ($form_action === 'delete') {
         $stmt = $pdo->prepare("DELETE FROM pastes WHERE id = ?");
         $stmt->execute([$id]);
         header("Location: index.php"); exit;
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="glass-panel p-8 rounded-2xl max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6"><?= htmlspecialchars($pageTitle) ?></h1>
        
        <form method="post" action="manage_paste.php?action=<?= $action ?><?= $id ? '&id='.$id : '' ?>">
            <input type="hidden" name="form_action" value="<?= $action ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="col-span-2">
                    <label class="block text-slate-300 mb-2 font-medium">Title</label>
                    <input type="text" name="title" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" value="<?= htmlspecialchars($paste['title'] ?? '') ?>" required placeholder="My Amazing Paste">
                </div>
                <div>
                     <label class="block text-slate-300 mb-2 font-medium">Language</label>
                     <select name="syntax" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <?php foreach($languages as $lang): ?>
                            <option value="<?= $lang ?>" <?= ($paste['syntax_language'] ?? '') === $lang ? 'selected' : '' ?>><?= ucfirst($lang) ?></option>
                        <?php endforeach; ?>
                     </select>
                </div>
            </div>
            
            <div class="mb-6">
                 <label class="block text-slate-300 mb-2 font-medium">Content</label>
                 <textarea name="content" rows="15" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white font-mono text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" required placeholder="// Paste your code here..."><?= htmlspecialchars($paste['content'] ?? '') ?></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5"><?= $submitLabel ?></button>
                <a href="<?= $id ? 'view.php?id='.$id : 'index.php' ?>" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium rounded-lg transition-colors">Cancel</a>
            </div>
        </form>
    </div>
    
    <!-- Sharing UI -->
    <?php if ($action === 'edit' && $id): ?>
    <div class="glass-panel p-6 rounded-2xl max-w-4xl mx-auto mt-6">
        <h3 class="text-xl font-bold text-white mb-4">Share with other users</h3>
        <?= $share_message ?>
        <form method="post" class="flex gap-4 mb-4">
            <input type="hidden" name="form_action" value="share">
            <input type="text" name="username_to_share" placeholder="Enter username" class="flex-grow bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-lg">Share</button>
        </form>
        
        <div>
            <h4 class="text-slate-400 text-sm font-semibold mb-2">Users with access:</h4>
            <?php if(empty($shared_users)): ?>
                <p class="text-slate-500 text-sm">Not shared with anyone yet.</p>
            <?php else: ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($shared_users as $su): ?>
                        <span class="px-3 py-1 bg-slate-700 text-slate-200 rounded-full text-sm"><?= htmlspecialchars($su) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <hr class="border-slate-700 my-4">
        
        <!-- Public Link -->
        <div>
            <h4 class="text-slate-400 text-sm font-semibold mb-2">Public Link:</h4>
            <div class="flex gap-2">
                <input type="text" id="shareLink" value="<?= 'http://'.$_SERVER['HTTP_HOST'].'/pastebin/view.php?id='.$id ?>" class="flex-grow bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-2 text-slate-400 text-sm" readonly>
                <button onclick="copyLink()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg">Copy</button>
            </div>
        </div>
    </div>
    
    <script>
    function copyLink() {
        var copyText = document.getElementById("shareLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(copyText.value);
        alert("Link copied: " + copyText.value);
    }
    </script>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
