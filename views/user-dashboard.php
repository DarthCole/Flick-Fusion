<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get genres for filter
$genres_query = "SELECT * FROM Genres ORDER BY name";
$genres_result = $conn->query($genres_query);

// Handle search and filter
$where_clause = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause .= " AND (m.title LIKE '%$search%' OR m.description LIKE '%$search%' OR m.director LIKE '%$search%')";
}
if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $genre = intval($_GET['genre']);
    $where_clause .= " AND m.genre_id = $genre";
}

// Get movies with genre information
$movies_query = "
    SELECT m.*, g.name as genre_name, 
           (SELECT AVG(rating) FROM Reviews WHERE movie_id = m.movie_id) as avg_rating
    FROM Movies m
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    WHERE $where_clause
    ORDER BY m.created_at DESC";
$movies_result = $conn->query($movies_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Explore Movies - Flick Fusion</title>
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
            min-height: 100vh;
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .search-form input {
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #1f1f1f;
            color: #fff;
        }

        .search-form button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #f39c12;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #e67e22;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .genre-select {
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #1f1f1f;
            color: #fff;
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

        .movie-rating {
            color: #f39c12;
            margin-bottom: 15px;
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

        .btn-outline {
            background-color: transparent;
            border: 1px solid #f39c12;
            color: #f39c12;
        }

        .btn-outline:hover {
            background-color: #f39c12;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo">Flick Fusion</a>
        <ul class="nav-links">
            <li><a href="user-dashboard.php" class="active">Explore</a></li>
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
            <div class="header">
                <h2>Explore Movies</h2>
                <div class="filters">
                    <form class="search-form" method="GET">
                        <select class="genre-select" name="genre" onchange="this.form.submit()">
                            <option value="">All Genres</option>
                            <?php while($genre = $genres_result->fetch_assoc()): ?>
                                <option value="<?php echo $genre['genre_id']; ?>" 
                                        <?php echo (isset($_GET['genre']) && $_GET['genre'] == $genre['genre_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($genre['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="search" name="search" placeholder="Search movies..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>
            </div>

            <div class="movies-grid">
                <?php while($movie = $movies_result->fetch_assoc()): ?>
                    <div class="movie-card">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" 
                             class="movie-poster" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="movie-info">
                            <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <div class="movie-meta">
                                <?php echo $movie['release_year']; ?> • 
                                <?php echo isset($movie['duration']) ? $movie['duration'] . ' mins' : 'N/A'; ?> • 
                                <?php echo htmlspecialchars($movie['genre_name']); ?>
                            </div>
                            <div class="movie-rating">
                                <?php
                                $avg_rating = isset($movie['avg_rating']) ? round($movie['avg_rating']) : 0;
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $avg_rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="movie-actions">
                                <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>" 
                                   class="btn btn-primary">View Details</a>
                                <form action="../actions/add_collection.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                                    <button type="submit" class="btn btn-outline">
                                        <i class="fas fa-plus"></i> Add to Collection
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</body>
</html>