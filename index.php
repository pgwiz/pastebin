<?php
$cspHeader = "Content-Security-Policy: default-src 'self'; ".
             "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
             "script-src 'self' https://cdn.jsdelivr.net; ".
             "img-src 'self' https://images.unsplash.com data:; ".
             "font-src https://cdnjs.cloudflare.com;";

header($cspHeader);

include 'db.php';
include 'auth.php';
include 'card_view.php'; // Include the new card component file


$user = get_logged_in_user();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Pastes</title>
    <link rel="stylesheet" href="src/navbar.css">
    <?php renderCardStyles(); // Render the CSS for the cards ?>
    <style>
        /* Additional styles for page layout */
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            background-color: #fff;
            border-radius: .5rem;
            max-width: 28rem;
            margin: 0 auto;
        }
        
        /* Responsive Grid Container */
        .card-grid {
            display: grid;
            grid-template-columns: 1fr; /* 1 column for mobile */
            gap: 2rem; /* The space between cards */
            width: 100%;
            padding: 0 1rem; /* Adds some padding on the sides */
        }

        /* 2 columns for tablets */
        @media (min-width: 768px) {
            .card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* 3 columns for smaller desktops */
        @media (min-width: 992px) {
            .card-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* 4 columns for large desktops */
        @media (min-width: 1400px) {
            .card-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Override from card_view.php to allow full-width grid */
        .page-container {
            align-items: stretch;
        }
        /* Ensure individual cards don't exceed the grid cell width */
        .card-container {
            max-width: 100%;
            margin-bottom: 0; /* Let the grid gap handle spacing */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="page-container">
        <h2 class="page-title">All Pastes</h2>

        <?php
        // Fetch all pastes from the database
        $stmt = $pdo->query("
            SELECT p.id, p.title, p.content, p.username, p.created_at, u.username AS registered_username, p.user_id
            FROM pastes p
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");

        if ($stmt->rowCount() === 0): ?>
            <div class="empty-state">
                <h3>No pastes yet</h3>
                <p>Be the first to create a paste!</p>
                <a href="manage_paste.php?action=create" class="btn btn-primary mt-2">Create New Paste</a>
            </div>
        <?php else: ?>
            <div class="card-grid">
            <?php
            // Loop through the results and render a card for each paste
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                // Prepare the data in the format expected by the renderCard function
                $postData = [
                    'id'         => $row['id'],
                    'category'   => $row['title'], // Using paste title as the category/expo text
                    'content'    => $row['content'],
                    'author'     => $row['user_id'] ? htmlspecialchars($row['registered_username']) : htmlspecialchars($row['username'] ?? 'Anonymous'),
                    'created_at' => $row['created_at'],
                    'user_id'    => $row['user_id'] // Pass user_id for permission checks
                ];

                // Render the card using the component function
                echo renderCard($postData, $user);
            endwhile;
            ?>
            </div>
        <?php endif; ?>
    </div>

    <?php renderCardScripts(); // Render the JavaScript for the cards ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
