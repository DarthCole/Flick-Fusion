<?php
include '../actions/connect.php'; // Include database connection

// Fetch all reviews
$query = "
    SELECT Reviews.review_id, FFUsers.username, Movies.title, Reviews.rating, Reviews.review_text, Reviews.created_at 
    FROM Reviews 
    INNER JOIN FFUsers ON Reviews.user_id = FFUsers.user_id 
    INNER JOIN Movies ON Reviews.movie_id = Movies.movie_id";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - Admin</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #333;
        }
        th {
            background-color: #444;
        }
        tr:nth-child(even) {
            background-color: #222;
        }
        a {
            color: #f39c12;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Manage Reviews</h1>
    <table>
        <thead>
            <tr>
                <th>Review ID</th>
                <th>User</th>
                <th>Movie</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['review_id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['rating']; ?></td>
                    <td><?php echo $row['review_text']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="../actions/delete_review.php?review_id=<?php echo $row['review_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
