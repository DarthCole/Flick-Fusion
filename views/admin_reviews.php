<?php
session_start();
require_once '../db/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has admin role (role = 1)
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

// Fetch all reviews with user and movie information
$query = "SELECT r.*, u.username, m.title as movie_title 
          FROM Reviews r 
          JOIN FFUsers u ON r.user_id = u.user_id 
          JOIN Movies m ON r.movie_id = m.movie_id 
          ORDER BY r.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Review Management - Admin</title>
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
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 2rem;
        }

        .reviews-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background-color: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
        }

        .reviews-table th,
        .reviews-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .reviews-table th {
            background-color: #2c2c2c;
            color: #f39c12;
            font-weight: bold;
        }

        .reviews-table tr:hover {
            background-color: #2c2c2c;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 0.25rem;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .rating {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background-color: #f39c12;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
        }

        .search-box {
            width: 100%;
            max-width: 300px;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2c2c2c;
            color: #fff;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">Admin Dashboard</a>
        <ul class="nav-links">
            <li><a href="admin_reviews.php" class="active">Reviews</a></li>
            <li><a href="admin_movies.php">Movies</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_collections.php">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Review Management</h1>
        <input type="text" id="searchInput" class="search-box" placeholder="Search reviews...">
        
        <table class="reviews-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Movie</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['username']); ?></td>
                        <td><?php echo htmlspecialchars($review['movie_title']); ?></td>
                        <td><span class="rating"><?php echo $review['rating']; ?>/5</span></td>
                        <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                        <td>
                            <button class="action-btn delete-btn" onclick="deleteReview(<?php echo $review['review_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.reviews-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Delete review function
        function deleteReview(reviewId) {
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                fetch('../actions/delete_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${reviewId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting review: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting review. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
