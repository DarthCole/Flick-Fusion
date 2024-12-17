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

// Fetch all users except current admin
$query = "SELECT u.*, 
          COUNT(DISTINCT r.review_id) as review_count,
          COUNT(DISTINCT c.collection_id) as collection_count
          FROM FFUsers u 
          LEFT JOIN Reviews r ON u.user_id = r.user_id
          LEFT JOIN Collections c ON u.user_id = c.user_id
          WHERE u.user_id != ?
          GROUP BY u.user_id
          ORDER BY u.created_at DESC";
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
    <title>User Management - Admin</title>
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

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background-color: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .users-table th {
            background-color: #2c2c2c;
            color: #f39c12;
            font-weight: bold;
        }

        .users-table tr:hover {
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

        .promote-btn {
            background-color: #2ecc71;
            color: #fff;
        }

        .promote-btn:hover {
            background-color: #27ae60;
        }

        .demote-btn {
            background-color: #f1c40f;
            color: #fff;
        }

        .demote-btn:hover {
            background-color: #f39c12;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
        }

        .role-admin {
            background-color: #2ecc71;
            color: #fff;
        }

        .role-user {
            background-color: #3498db;
            color: #fff;
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
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="admin_movies.php">Movies</a></li>
            <li><a href="admin_users.php" class="active">Users</a></li>
            <li><a href="admin_collections.php">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>User Management</h1>
        <input type="text" id="searchInput" class="search-box" placeholder="Search users...">
        
        <table class="users-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Reviews</th>
                    <th>Collections</th>
                    <th>Join Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge <?php echo $user['role'] == 1 ? 'role-admin' : 'role-user'; ?>">
                                <?php echo $user['role'] == 1 ? 'Admin' : 'User'; ?>
                            </span>
                        </td>
                        <td><?php echo $user['review_count']; ?></td>
                        <td><?php echo $user['collection_count']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['role'] == 0): ?>
                                <button class="action-btn promote-btn" onclick="updateUserRole(<?php echo $user['user_id']; ?>, 1)">Make Admin</button>
                            <?php else: ?>
                                <button class="action-btn demote-btn" onclick="updateUserRole(<?php echo $user['user_id']; ?>, 0)">Remove Admin</button>
                            <?php endif; ?>
                            <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
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
            const rows = document.querySelectorAll('.users-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Update user role function
        function updateUserRole(userId, newRole) {
            const action = newRole === 1 ? 'promote' : 'demote';
            const confirmMessage = newRole === 1 
                ? 'Are you sure you want to make this user an admin?' 
                : 'Are you sure you want to remove admin privileges from this user?';

            if (confirm(confirmMessage)) {
                fetch('../actions/update_user_role.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&role=${newRole}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(`Error ${action}ing user: ` + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`Error ${action}ing user. Please try again.`);
                });
            }
        }

        // Delete user function
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all associated reviews and collections.')) {
                fetch('../actions/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting user: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
