<?php
include '../actions/connect.php';

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
        form {
            margin-bottom: 20px;
        }
        input, select, button {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #f39c12;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #e67e22;
        }
    </style>
</head>
<body>
    <h1>Manage Movies</h1>

    <!-- Add Movie Form -->
    <form action="../actions/add_movie.php" method="POST" enctype="multipart/form-data">
        <h2>Add New Movie</h2>
        <input type="text" name="title" placeholder="Movie Title" required>
        <input type="number" name="release_year" placeholder="Release Year" required>
        <select name="genre_id" required>
            <option value="">Select Genre</option>
            <?php while ($genre = mysqli_fetch_assoc($genresResult)) : ?>
                <option value="<?php echo $genre['genre_id']; ?>"><?php echo $genre['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="description" placeholder="Movie Description"></textarea>
        <input type="file" name="poster" accept="image/*" required>
        <button type="submit">Add Movie</button>
    </form>

    <!-- Movie List -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Release Year</th>
                <th>Genre</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($movie = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $movie['movie_id']; ?></td>
                    <td><?php echo $movie['title']; ?></td>
                    <td><?php echo $movie['release_year']; ?></td>
                    <td><?php echo $movie['genre'] ?? 'No Genre'; ?></td>
                    <td><?php echo $movie['description'] ?? 'N/A'; ?></td>
                    <td><?php echo $movie['created_at']; ?></td>
                    <td>
                        <a href="../actions/delete_movie.php?movie_id=<?php echo $movie['movie_id']; ?>">Delete</a> | 
                        <a href="../actions/edit_movie.php?movie_id=<?php echo $movie['movie_id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
