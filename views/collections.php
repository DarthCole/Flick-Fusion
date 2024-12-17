<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's collections with movie details
$user_id = $_SESSION['user_id'];
$query = "SELECT m.*, g.name as genre_name
          FROM Collections c
          INNER JOIN Movies m ON c.movie_id = m.movie_id
          LEFT JOIN Genres g ON m.genre_id = g.genre_id
          WHERE c.user_id = ?
          ORDER BY c.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>My Collections - Flick Fusion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
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
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 30px;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .movie-card {
            background-color: #1f1f1f;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-poster {
            width: 100%;
            height: 375px;
            object-fit: cover;
        }

        .movie-info {
            padding: 15px;
        }

        .movie-title {
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .movie-meta {
            color: #888;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .movie-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #f39c12;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #e67e22;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .empty-collection {
            text-align: center;
            padding: 40px;
            background-color: #1f1f1f;
            border-radius: 10px;
            margin-top: 20px;
        }

        .empty-collection i {
            font-size: 48px;
            color: #f39c12;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="user-dashboard.php">Explore</a></li>
            <li><a href="collections.php" class="active">Collections</a></li>
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
            <div class="header">
                <h2>My Collection</h2>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="movies-grid">
                    <?php while($movie = $result->fetch_assoc()): ?>
                        <div class="movie-card">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" 
                                 class="movie-poster" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <div class="movie-info">
                                <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <div class="movie-meta">
                                    <?php echo $movie['release_year']; ?> • 
                                    <?php echo $movie['duration']; ?> mins • 
                                    <?php echo htmlspecialchars($movie['genre_name']); ?>
                                </div>
                                <div class="movie-actions">
                                    <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>" 
                                       class="btn btn-primary">View Details</a>
                                    <form action="../actions/remove-from-collection.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-collection">
                    <i class="fas fa-film"></i>
                    <h3>Your collection is empty</h3>
                    <p>Start adding movies from the explore page to build your collection!</p>
                    <a href="user-dashboard.php" class="btn btn-primary">Explore Movies</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
