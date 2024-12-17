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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon">
    <title>Admin Dashboard - Flick Fusion</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            color: #f39c12;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .navbar .nav-links li {
            display: inline;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s;
        }

        .navbar .nav-links a:hover {
            color: #f39c12;
        }

        /* Dashboard Container */
        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
        }

        .card {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        .card h2 {
            margin-top: 0;
        }

        .list {
            list-style: none;
            padding: 0;
        }

        .list li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="#" class="logo">Admin Dashboard</a>
        <ul class="nav-links">
            <li><a href="admin_reviews.php">Reviews</a></li>
            <li><a href="admin_movies.php">Movies</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_collections.php">Collections</a></li>
            <li><a href="../actions/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard-container">
        <div class="card">
            <h2>Total Regular Users</h2>
            <canvas id="userGrowthChart"></canvas>
        </div>
        <div class="card">
            <h2>Top 5 Most Active Users</h2>
            <ul id="activeUsersList" class="list"></ul>
        </div>
        <div class="card">
            <h2>Top 3 Popular Movies</h2>
            <ul id="popularMoviesList" class="list"></ul>
        </div>
        <div class="card">
            <h2>Movie of the Week</h2>
            <p id="highestRatedMovie"></p>
        </div>
    </div>

    <script>
        // Fetch data for User Growth
        fetch('../actions/get_user_growth.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const labels = data.data.map(item => item.registration_date);
                    const counts = data.data.map(item => item.daily_count);

                    const ctx = document.getElementById('userGrowthChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'New Users',
                                data: counts,
                                borderColor: '#f39c12',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#fff'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        color: '#fff'
                                    },
                                    grid: {
                                        color: '#333'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#fff'
                                    },
                                    grid: {
                                        color: '#333'
                                    }
                                }
                            }
                        }
                    });
                }
            });

        // Fetch data for Active Users
        fetch('../actions/get_active_users.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const activeUsersList = document.getElementById('activeUsersList');
                    activeUsersList.innerHTML = data.data.map(user => 
                        `<li>${user.username} - ${user.activity_count} activities</li>`
                    ).join('');
                }
            });

        // Fetch data for Popular Movies
        fetch('../actions/get_popular_movies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const popularMoviesList = document.getElementById('popularMoviesList');
                    popularMoviesList.innerHTML = data.data.map(movie => 
                        `<li>${movie.title} - ${movie.view_count} views</li>`
                    ).join('');
                }
            });

        // Fetch data for Highest Rated Movie
        fetch('../actions/get_highest_rated_movie.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const highestRatedMovie = document.getElementById('highestRatedMovie');
                    const movie = data.data;
                    highestRatedMovie.innerHTML = `${movie.title} - Rating: ${movie.average_rating}/5`;
                }
            });
    </script>
</body>
</html>
