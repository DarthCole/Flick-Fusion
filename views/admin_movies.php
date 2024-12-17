<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    header("Location: user-dashboard.php");
    exit();
}

// Fetch all movies
$query = "SELECT Movies.movie_id, Movies.title, Movies.release_year, Genres.name AS genre, Movies.description, Movies.created_at 
          FROM Movies 
          LEFT JOIN Genres ON Movies.genre_id = Genres.genre_id";
$result = mysqli_query($conn, $query);

// Fetch all genres for dropdown
$genresQuery = "SELECT * FROM Genres";
$genresResult = mysqli_query($conn, $genresQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Movies - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
        }

        /* Navigation Bar Styling */
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

        /* Content Styles */
        .container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2c2c2c;
            font-weight: bold;
        }

        tr:hover {
            background-color: #2c2c2c;
        }

        .actions a {
            color: #f39c12;
            text-decoration: none;
            margin-right: 10px;
        }

        .actions a:hover {
            text-decoration: underline;
        }
        
        .add-btn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .add-btn:hover {
            background-color: #444;
        }
        
        .movies-table {
            margin-top: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .submit-btn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .submit-btn:hover {
            background-color: #444;
        }
        
        .edit-btn, .delete-btn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .edit-btn:hover, .delete-btn:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">Admin Dashboard</a>
        <ul class="nav-links">
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="admin_movies.php" class="active">Movies</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_collections.php">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Movie Management</h1>
        <button class="add-btn" onclick="openAddMovieModal()">Add New Movie</button>
        
        <div class="movies-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Release Year</th>
                        <th>Genre</th>
                        <th>Description</th>
                        <th>Added Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($movie = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo $movie['release_year']; ?></td>
                            <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                            <td><?php echo htmlspecialchars($movie['description']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($movie['created_at'])); ?></td>
                            <td>
                                <a href="../actions/edit_movie.php?movie_id=<?php echo $movie['movie_id']; ?>" class="edit-btn">Edit</a>
                                <button class="delete-btn" onclick="deleteMovie(<?php echo $movie['movie_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Movie Modal -->
    <div id="addMovieModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Movie</h2>
            <form action="../actions/add_movie.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="release_year">Release Year:</label>
                    <input type="number" id="release_year" name="release_year" required>
                </div>
                <div class="form-group">
                    <label for="genre_id">Genre:</label>
                    <select id="genre_id" name="genre_id" required>
                        <option value="">Select Genre</option>
                        <?php while ($genre = mysqli_fetch_assoc($genresResult)): ?>
                            <option value="<?php echo $genre['genre_id']; ?>">
                                <?php echo htmlspecialchars($genre['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="poster">Movie Poster:</label>
                    <input type="file" id="poster" name="poster" accept="image/*" required>
                </div>
                <button type="submit" class="submit-btn">Add Movie</button>
            </form>
        </div>
    </div>

    <script>
        // Delete movie function
        function deleteMovie(movieId) {
            if (confirm('Are you sure you want to delete this movie? This action cannot be undone.')) {
                fetch('../actions/delete_movie.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `movie_id=${movieId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting movie: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting movie. Please try again.');
                });
            }
        }

        // Modal functionality
        const modal = document.getElementById('addMovieModal');
        const closeBtn = document.getElementsByClassName('close')[0];

        function openAddMovieModal() {
            modal.style.display = 'block';
        }

        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
