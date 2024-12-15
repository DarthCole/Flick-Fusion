<?php
include '../actions/connect.php';

// Fetch all users
$query = "SELECT * FROM FFUsers";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Users - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: #fff; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #333; }
        th { background-color: #444; }
        tr:nth-child(even) { background-color: #222; }
        a { color: #f39c12; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role'] == 1 ? 'Admin' : 'User'; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <a href="../actions/delete_user.php?user_id=<?php echo $user['user_id']; ?>">Delete</a> | 
                        <a href="../actions/edit_user.php?user_id=<?php echo $user['user_id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
