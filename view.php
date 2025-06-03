<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();

// Validate and assign ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid paste ID.");
}
$id = intval($_GET['id']);

// Fetch paste with optional user join
$stmt = $pdo->prepare("
    SELECT p.*, u.username AS registered_username 
    FROM pastes p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$paste = $stmt->fetch();

if (!$paste) {
    die("Paste not found.");
}

// Determine username (registered user vs anonymous)
$username = $paste['user_id'] ? $paste['registered_username'] : ($paste['username'] ?? 'Anonymous');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($paste['title']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Highlight.js for Syntax Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <!-- Custom view.css (defines .card, .container, etc.) -->
    <link rel="stylesheet" href="src/view.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .status-ind {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd;
            margin-top: 5px;
        }
        .text-wrap {
            flex-grow: 1;
        }
        .text-content {
            margin: 0;
            line-height: 1.6;
        }
        .text-link {
            text-decoration: none;
            font-weight: 500;
            color: #0d6efd;
        }
        .button-wrap {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        .primary-cta, .secondary-cta {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .primary-cta {
            background-color: #0d6efd;
            color: white;
            border: none;
        }
        .primary-cta:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .secondary-cta {
            background-color: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .secondary-cta:hover {
            background-color: #dc3545;
            color: white;
        }
        .code-block {
            background-color: #212529;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 0;
            position: relative;
            word-wrap: break-word;
        }
        pre code {
            color: #f8f9fa;
            white-space: pre-wrap;
            font-size: 0.95rem;
        }
        #copyBtn {
            margin-top: 0.75rem;
            align-self: flex-start;
        }
        .posted-date {
            color: #555;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .flex-column-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5rem;
            max-width: 1200px;
        }
        .back-link {
            margin-top: 1rem;
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container flex-column-container mt-4">
        <!-- Paste Card with Vertical Layout -->
        <div class="card shadow-sm">
            <!-- Header Section -->
            <div class="card-header">
                <div class="status-ind"></div>
                <div class="text-wrap">
                    <p class="text-content">
                        <a class="text-link" href="#">
                            <?= htmlspecialchars($username) ?>
                        </a>
                        posted
                        <a class="text-link" href="#">
                            <?= htmlspecialchars($paste['title']) ?>
                        </a>
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="button-wrap">
                         <?php if ($user && $user['id'] === $paste['user_id']): ?>
    <form method="post">
        <input type="hidden" name="paste_id" value="<?= $paste['id'] ?>">
        <label class="form-check-label">
            <input type="checkbox" name="is_featured" <?= $paste['is_featured'] ? 'checked' : '' ?> class="form-check-input">
            Mark as Featured
        </label>
        <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
    </form>

                            <a href="edit.php?id=<?= $paste['id'] ?>" class="primary-cta">Edit Paste</a>
                            <button class="secondary-cta" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete Paste
                            </button>
                        <?php else: ?>
                            <a href="index.php" class="primary-cta">Back to All Pastes</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Code Section -->
            <div class="d-flex flex-column">
                <div class="code-block">
                    <pre><code id="pasteContent" class="language-<?= htmlspecialchars($paste['syntax_language']) ?>">
<?= htmlspecialchars($paste['content']) ?>
                    </code></pre>
                </div>
                <button id="copyBtn" class="btn btn-primary">Copy to Clipboard</button>
            </div>
          
<div class="share-section">
    <!--enable this count at will>
    <button class="btn btn-primary" onclick="sharePaste()"-->
        <!--and remove the line below-->
        <button class="btn btn-primary">
        <i class="fas fa-share-alt"></i> Share (<?= $paste['shares'] ?>)
    </button>

    <!-- Social Media Buttons -->
    <div class="social-share mt-3">
        <a href="#" class="btn btn-twitter" onclick="shareOnTwitter()">
            <i class="fab fa-twitter"></i> Twitter
        </a>
        <a href="#" class="btn btn-facebook" onclick="shareOnFacebook()">
            <i class="fab fa-facebook"></i> Facebook
        </a>
        <a href="#" class="btn btn-linkedin" onclick="shareOnLinkedIn()">
            <i class="fab fa-linkedin"></i> LinkedIn
        </a>
    </div>
</div>
        </div>

        <!-- Metadata Section -->
        <div class="d-flex flex-column back-link">
            <p class="posted-date">Posted on: <?= htmlspecialchars($paste['created_at']) ?></p>
            <a href="index.php" class="btn btn-secondary align-self-start">&larr; Back to All Pastes</a>
        </div>
       
    </div>

    <!-- Delete Confirmation Modal -->
    <?php if ($user && $user['id'] === $paste['user_id']): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="delete.php">
                <input type="hidden" name="id" value="<?= $paste['id'] ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this paste?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $stmt = $pdo->prepare("UPDATE pastes SET views = views + 1 WHERE id = ?");
$stmt->execute([$id]);
    ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        // Initialize syntax highlighting
        hljs.highlightAll();

        // Copy to clipboard functionality
        document.getElementById('copyBtn').addEventListener('click', function() {
            const codeElement = document.getElementById('pasteContent');
            const codeText = codeElement.innerText.trim();

            navigator.clipboard.writeText(codeText)
                .then(() => {
                    const originalText = this.textContent;
                    this.textContent = 'Copied!';
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    this.textContent = 'Error!';
                    setTimeout(() => {
                        this.textContent = 'Copy to Clipboard';
                    }, 2000);
                });
        });
 
function sharePaste() {
    const pasteId = <?= $paste['id'] ?>;
    fetch(`share.php?id=${pasteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update share count in UI
                document.querySelector('.share-section button').innerHTML = 
                    `<i class="fas fa-share-alt"></i> Share (${data.shares})`;
                //alert('Share count updated!');
            }
        })
        .catch(error => console.error('Error sharing:', error));
}

function shareOnTwitter() {
    sharePaste(); // Increment share count first
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent("Check out this code snippet!");
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareOnFacebook() {
     sharePaste(); // Increment share count first
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareOnLinkedIn() {
     sharePaste(); // Increment share count first
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("<?= $paste['title'] ?>");
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}`, '_blank');
}
</script>
</body>
</html>