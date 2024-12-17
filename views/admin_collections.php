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

// Fetch all collections with user information and movie count
$query = "SELECT c.*, u.username, 
          COUNT(DISTINCT cm.movie_id) as movie_count,
          GROUP_CONCAT(DISTINCT m.title SEPARATOR ', ') as movies
          FROM Collections c 
          JOIN FFUsers u ON c.user_id = u.user_id
          LEFT JOIN CollectionMovies cm ON c.collection_id = cm.collection_id
          LEFT JOIN Movies m ON cm.movie_id = m.movie_id
          GROUP BY c.collection_id
          ORDER BY c.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Collection Management - Admin</title>
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

        .collections-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background-color: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
        }

        .collections-table th,
        .collections-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .collections-table th {
            background-color: #2c2c2c;
            color: #f39c12;
            font-weight: bold;
        }

        .collections-table tr:hover {
            background-color: #2c2c2c;
        }

        .movie-list {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        .view-btn {
            background-color: #3498db;
            color: #fff;
        }

        .view-btn:hover {
            background-color: #2980b9;
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

        .visibility-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
        }

        .visibility-public {
            background-color: #2ecc71;
            color: #fff;
        }

        .visibility-private {
            background-color: #95a5a6;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">Admin Dashboard</a>
        <ul class="nav-links">
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="admin_movies.php">Movies</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_collections.php" class="active">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Collection Management</h1>
        <input type="text" id="searchInput" class="search-box" placeholder="Search collections...">
        
        <table class="collections-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>User</th>
                    <th>Visibility</th>
                    <th>Movies</th>
                    <th>Movie Count</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0):
                    while ($collection = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($collection['name'] ?? 'Unnamed Collection'); ?></td>
                        <td><?php echo htmlspecialchars($collection['username'] ?? 'Unknown User'); ?></td>
                        <td>
                            <span class="visibility-badge <?php echo ($collection['is_public'] ?? true) ? 'visibility-public' : 'visibility-private'; ?>">
                                <?php echo ($collection['is_public'] ?? true) ? 'Public' : 'Private'; ?>
                            </span>
                        </td>
                        <td class="movie-list" title="<?php echo htmlspecialchars($collection['movies'] ?? ''); ?>">
                            <?php echo htmlspecialchars($collection['movies'] ?? 'No movies'); ?>
                        </td>
                        <td><?php echo intval($collection['movie_count'] ?? 0); ?></td>
                        <td><?php echo date('M d, Y', strtotime($collection['created_at'])); ?></td>
                        <td>
                            <button class="delete-btn" onclick="deleteCollection(<?php echo $collection['collection_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="7" class="no-data">No collections found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.collections-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Delete collection function
        function deleteCollection(collectionId) {
            if (confirm('Are you sure you want to delete this collection? This action cannot be undone.')) {
                fetch('../actions/delete_collection.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `collection_id=${collectionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting collection: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting collection. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
