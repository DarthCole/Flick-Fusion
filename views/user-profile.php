<?php
session_start();
require_once '../db/db_connect.php';

// Get user ID from URL
$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($profile_user_id === 0) {
    header("Location: user-dashboard.php");
    exit();
}

try {
    // Get user information
    $user_query = "SELECT * FROM ffUsers WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare user query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $profile_user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();

    if (!$user) {
        header("Location: user-dashboard.php");
        exit();
    }

    // Get user's collections
    $collections_query = "
        SELECT m.movie_id, m.title, m.poster
        FROM Movies m
        JOIN Collections c ON m.movie_id = c.movie_id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
        LIMIT 6";

    $collections_stmt = $conn->prepare($collections_query);
    if (!$collections_stmt) {
        throw new Exception("Failed to prepare collections query: " . $conn->error);
    }
    $collections_stmt->bind_param("i", $profile_user_id);
    $collections_stmt->execute();
    $collections_result = $collections_stmt->get_result();

    // Get user's recent reviews
    $reviews_query = "
        SELECT r.rating, r.review_text, r.created_at,
               m.movie_id, m.title as movie_title
        FROM Reviews r
        JOIN Movies m ON r.movie_id = m.movie_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT 6";

    $reviews_stmt = $conn->prepare($reviews_query);
    if (!$reviews_stmt) {
        throw new Exception("Failed to prepare reviews query: " . $conn->error);
    }
    $reviews_stmt->bind_param("i", $profile_user_id);
    $reviews_stmt->execute();
    $reviews_result = $reviews_stmt->get_result();

} catch (Exception $e) {
    error_log("Error in user profile: " . $e->getMessage());
    echo "Error: " . $e->getMessage(); // Display error for debugging
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile - Flick Fusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #f39c12;
            --bg-dark: #121212;
            --bg-card: #1f1f1f;
            --text-light: #ffffff;
            --text-gray: #888888;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-dark);
            color: var(--text-light);
            line-height: 1.6;
        }

        .navbar {
            background-color: #333;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            color: #f39c12;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-links a:hover {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        .nav-links a.active {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 64px auto 0;
            padding: 2rem;
        }

        .profile-header {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .profile-header h1 {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
            font-size: 2.5rem;
        }

        .profile-header p {
            color: var(--text-gray);
            margin: 0;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .movie-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .movie-poster {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .movie-info {
            padding: 1.5rem;
        }

        .movie-title {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
        }

        .view-details {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .view-details:hover {
            background-color: #e67e22;
        }

        .reviews-container {
            display: grid;
            gap: 1.5rem;
        }

        .review-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .rating {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .review-text {
            color: var(--text-gray);
            margin: 1rem 0;
        }

        .review-date {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .star-rating {
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .profile-header {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="user-dashboard.php">Explore</a></li>
            <li><a href="collections.php">Collections</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="user-profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="active">Profile</a></li>
                <li><a href="../actions/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="profile-header">
            <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
            <p>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>

        <section>
            <h2 class="section-title">Movie Collection</h2>
            <div class="grid">
                <?php while ($movie = $collections_result->fetch_assoc()): ?>
                    <div class="movie-card">
                        <?php if (!empty($movie['poster'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                 class="movie-poster">
                        <?php endif; ?>
                        <div class="movie-info">
                            <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <div style="display: flex; gap: 1rem;">
                                <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>" class="view-details">View Details</a>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $profile_user_id): ?>
                                    <a href="../actions/remove_from_collection.php?movie_id=<?php echo $movie['movie_id']; ?>" 
                                       class="view-details" style="background-color: #e74c3c;">Remove</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section>
            <h2 class="section-title">Recent Reviews</h2>
            <div class="reviews-container">
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <h3 class="movie-title">
                                <a href="movie-details.php?id=<?php echo $review['movie_id']; ?>" 
                                   style="color: var(--primary-color); text-decoration: none; transition: color 0.3s;">
                                    <?php echo htmlspecialchars($review['movie_title']); ?>
                                </a>
                            </h3>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-regular'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                        <p class="review-date">Posted on <?php echo date('F j, Y', strtotime($review['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>
</body>
</html>
