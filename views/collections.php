<?php
include '../actions/connect.php';

// Fetch collections with user and movie details
$query = "SELECT Collections.collection_id, FFUsers.username, Movies.title, Collections.created_at 
          FROM Collections 
          INNER JOIN FFUsers ON Collections.user_id = FFUsers.user_id 
          INNER JOIN Movies ON Collections.movie_id = Movies.movie_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections - Admin</title>
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
    <h1>Manage Collections</h1>

    <table>
        <thead>
            <tr>
                <th>Collection ID</th>
                <th>User</th>
                <th>Movie</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($collection = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $collection['collection_id']; ?></td>
                    <td><?php echo $collection['username']; ?></td>
                    <td><?php echo $collection['title']; ?></td>
                    <td><?php echo $collection['created_at']; ?></td>
                    <td>
                        <a href="../actions/delete_collection.php?collection_id=<?php echo $collection['collection_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
