<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Check admin role
$user_id = $_SESSION['user_id'];
$admin_check_query = "SELECT role FROM FFUsers WHERE user_id = ?";
$stmt = $conn->prepare($admin_check_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] != 1) {
    header("Location: ../views/user-dashboard.php");
    exit();
}

// Get movie data if movie_id is provided
$movie = null;
$genres = [];

if (isset($_GET['movie_id'])) {
    $movie_id = intval($_GET['movie_id']);
    $query = "SELECT * FROM Movies WHERE movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();

    if (!$movie) {
        header("Location: ../views/admin_movies.php");
        exit();
    }

    // Get all genres
    $genres_query = "SELECT * FROM Genres ORDER BY name";
    $genres_result = $conn->query($genres_query);
    while ($genre = $genres_result->fetch_assoc()) {
        $genres[] = $genre;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = intval($_POST['movie_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $release_year = intval($_POST['release_year']);
    $genre_id = intval($_POST['genre_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Handle poster upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster = file_get_contents($_FILES['poster']['tmp_name']);
        $query = "UPDATE Movies SET title = ?, release_year = ?, genre_id = ?, description = ?, poster = ? WHERE movie_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siissi", $title, $release_year, $genre_id, $description, $poster, $movie_id);
    } else {
        $query = "UPDATE Movies SET title = ?, release_year = ?, genre_id = ?, description = ? WHERE movie_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siisi", $title, $release_year, $genre_id, $description, $movie_id);
    }

    if ($stmt->execute()) {
        header("Location: ../views/movies.php?success=1");
        exit();
    } else {
        $error = $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Edit Movie - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
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

        .navbar .logo {
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

        .nav-links a:hover, .nav-links a.active {
            color: #f39c12;
            background-color: rgba(243, 156, 18, 0.1);
        }

        .container {
            max-width: 800px;
            margin: 80px auto 0;
            padding: 2rem;
        }

        .edit-form {
            background-color: #1f1f1f;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #f39c12;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2c2c2c;
            color: #fff;
            font-size: 1rem;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .current-poster {
            margin: 1rem 0;
            max-width: 200px;
        }

        .current-poster img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"] {
            background-color: #f39c12;
            color: #fff;
        }

        button[type="submit"]:hover {
            background-color: #e67e22;
        }

        .cancel-btn {
            background-color: #666;
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
        }

        .cancel-btn:hover {
            background-color: #777;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../views/admin_dashboard.php" class="logo">Admin Dashboard</a>
        <ul class="nav-links">
            <li><a href="../views/admin_reviews.php">Reviews</a></li>
            <li><a href="../views/admin_movies.php" class="active">Movies</a></li>
            <li><a href="../views/admin_users.php">Users</a></li>
            <li><a href="../views/admin_collections.php">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="edit-form">
            <h1>Edit Movie</h1>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="edit_movie.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                
                <div class="form-group">
                    <label for="title">Movie Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="release_year">Release Year</label>
                    <input type="number" id="release_year" name="release_year" value="<?php echo $movie['release_year']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="genre_id">Genre</label>
                    <select id="genre_id" name="genre_id" required>
                        <option value="">Select Genre</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?php echo $genre['genre_id']; ?>" <?php echo ($genre['genre_id'] == $movie['genre_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($genre['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($movie['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Current Poster</label>
                    <div class="current-poster">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> poster">
                    </div>
                </div>

                <div class="form-group">
                    <label for="poster">New Poster (optional)</label>
                    <input type="file" id="poster" name="poster" accept="image/*">
                </div>

                <div class="button-group">
                    <button type="submit">Update Movie</button>
                    <a href="../views/movies.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
