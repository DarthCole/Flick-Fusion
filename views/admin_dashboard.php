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
        <a href="#" class="logo">Dashboard</a>
        <ul class="nav-links">
            <li><a href="admin_reviews.html">Reviews</a></li>
            <li><a href="admin_movies.html">Movies</a></li>
            <li><a href="admin_users.html">Users</a></li>
            <li><a href="admin_collections.html">Collections</a></li>
            <li><a href="../views/login.php">Logout</a></li>
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
        fetch('actions/get_user_growth.php')
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
                                backgroundColor: 'rgba(243, 156, 18, 0.2)',
                                borderColor: '#f39c12',
                                borderWidth: 1
                            }]
                        }
                    });
                }
            });

        // Fetch data for Active Users
        fetch('actions/get_active_users.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const list = document.getElementById('activeUsersList');
                    data.data.forEach(user => {
                        const li = document.createElement('li');
                        li.textContent = `${user.username} - Reviews: ${user.review_count}, Collections: ${user.collection_count}`;
                        list.appendChild(li);
                    });
                }
            });

        // Fetch data for Popular Movies
        fetch('actions/get_popular_movies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const list = document.getElementById('popularMoviesList');
                    data.data.forEach(movie => {
                        const li = document.createElement('li');
                        li.textContent = `${movie.title} - Reviews: ${movie.review_count}`;
                        list.appendChild(li);
                    });
                }
            });

        // Fetch data for Highest Rated Movie of the Week
        fetch('actions/get_highest_rated_movie.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const movie = data.data;
                    const element = document.getElementById('highestRatedMovie');
                    element.textContent = `${movie.title} - Average Rating: ${movie.average_rating} (${movie.review_count} reviews)`;
                }
            });
    </script>
</body>
</html>
