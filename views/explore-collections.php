<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get collections with user info and movie counts
$collections_query = "
    SELECT 
        u.username,
        u.user_id,
        COUNT(DISTINCT c.movie_id) as movie_count,
        GROUP_CONCAT(DISTINCT g.name) as genres,
        MAX(c.created_at) as last_updated
    FROM Collections c
    INNER JOIN FFUsers u ON c.user_id = u.user_id
    INNER JOIN Movies m ON c.movie_id = m.movie_id
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    WHERE c.user_id != ?
    GROUP BY u.user_id, u.username
    ORDER BY movie_count DESC";

$stmt = $conn->prepare($collections_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$collections_result = $stmt->get_result();

// Get some sample movies from each collection
function getCollectionPreview($conn, $user_id, $limit = 4) {
    $preview_query = "
        SELECT m.*, g.name as genre_name
        FROM Collections c
        INNER JOIN Movies m ON c.movie_id = m.movie_id
        LEFT JOIN Genres g ON m.genre_id = g.genre_id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
        LIMIT ?";
    
    $stmt = $conn->prepare($preview_query);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Explore Collections - Flick Fusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styles */
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

        /* Main Content Padding */
        main {
            padding-top: 80px; /* Account for fixed navbar */
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .collections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .collection-card {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .collection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
        }

        .collector-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .collector-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e50914;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .collector-name {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .collection-stats {
            color: #999;
            font-size: 0.9rem;
        }

        .movie-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 2/3;
            border-radius: 4px;
            overflow: hidden;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item:hover .movie-title {
            opacity: 1;
        }

        .movie-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            padding: 0.5rem;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .collection-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #999;
            font-size: 0.9rem;
        }

        .genres {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .genre-tag {
            background-color: #333;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .view-all {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #e50914;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: opacity 0.3s;
        }

        .view-all:hover {
            opacity: 0.9;
        }

        .search-bar {
            margin: 2rem 0;
            display: flex;
            gap: 1rem;
        }

        .search-bar input {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }

        .search-bar button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            background-color: #e50914;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .search-bar button:hover {
            opacity: 0.9;
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
                <li><a href="user-profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a></li>
                <li><a href="../actions/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="container">
            <h1>Discover Movie Collections</h1>
            
            <div class="search-bar">
                <input type="text" placeholder="Search collections by username or genre...">
                <button type="button"><i class="fas fa-search"></i> Search</button>
            </div>

            <div class="collections-grid">
                <?php while ($collection = $collections_result->fetch_assoc()): 
                    $preview_movies = getCollectionPreview($conn, $collection['user_id']);
                    $genres = explode(',', $collection['genres']);
                    $genres = array_unique($genres);
                ?>
                    <div class="collection-card">
                        <div class="collection-header">
                            <div class="collector-info">
                                <div class="collector-avatar">
                                    <?php echo strtoupper(substr($collection['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="collector-name"><?php echo htmlspecialchars($collection['username']); ?></div>
                                    <div class="collection-stats">
                                        <?php echo $collection['movie_count']; ?> movies
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="movie-preview">
                            <?php while ($movie = $preview_movies->fetch_assoc()): ?>
                                <div class="preview-item">
                                    <?php if ($movie['poster']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" 
                                             alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                    <?php else: ?>
                                        <img src="../assets/images/default-movie.jpg" 
                                             alt="Default Movie Poster">
                                    <?php endif; ?>
                                    <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="collection-footer">
                            <div class="genres">
                                <?php foreach (array_slice($genres, 0, 3) as $genre): ?>
                                    <span class="genre-tag"><?php echo htmlspecialchars($genre); ?></span>
                                <?php endforeach; ?>
                                <?php if (count($genres) > 3): ?>
                                    <span class="genre-tag">+<?php echo count($genres) - 3; ?> more</span>
                                <?php endif; ?>
                            </div>
                            <a href="user-collection.php?user_id=<?php echo $collection['user_id']; ?>" class="view-all">
                                View All
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</body>
</html>
