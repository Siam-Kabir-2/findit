<!DOCTYPE html>
<html>

<head>
    <title>FindIt Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #0f3d5e;
            color: white;
            padding: 18px 40px;
            font-size: 22px;
            font-weight: bold;
        }

        .container {
            padding: 40px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .card h2 {
            color: #0f3d5e;
            margin: 0;
            font-size: 32px;
        }

        .card p {
            margin-top: 8px;
            color: #555;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .action-box {
            border: 1px solid #ddd;
            padding: 18px;
            border-radius: 8px;
            background: #fafafa;
        }

        .action-box h4 {
            color: #0f3d5e;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #0f3d5e;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f1f6fb;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .found {
            background: #d4edda;
            color: #155724;
        }

        .claimed {
            background: #cce5ff;
            color: #004085;
        }

        .returned {
            background: #d1ecf1;
            color: #0c5460;
        }

        .rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="navbar">
        FindIt Admin Dashboard Preview
    </div>

    <div class="container">

        <div class="cards">
            <div class="card">
                <h2>10</h2>
                <p>Total Users</p>
            </div>

            <div class="card">
                <h2>20</h2>
                <p>Total Items</p>
            </div>

            <div class="card">
                <h2>8</h2>
                <p>Lost Items</p>
            </div>

            <div class="card">
                <h2>12</h2>
                <p>Found Items</p>
            </div>

            <div class="card">
                <h2>5</h2>
                <p>Pending Claims</p>
            </div>
        </div>

        <div class="section">
            <h2>Recent Users</h2>

            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user['id'] }}</td>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ $user['phone'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Recent Items</h2>

            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ $item['category'] }}</td>
                        <td>{{ $item['location'] }}</td>
                        <td>
                            <span class="badge {{ strtolower($item['status']) }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>System Modules</h2>
            <p>This dashboard represents the planned modules of the Lost & Found Item Management System.</p>

            <div class="actions">
                <div class="action-box">
                    <h4>Report Lost Item</h4>
                    <p>User submits lost item information.</p>
                </div>

                <div class="action-box">
                    <h4>Report Found Item</h4>
                    <p>User submits found item information.</p>
                </div>

                <div class="action-box">
                    <h4>Search Items</h4>
                    <p>Search by category, location, date, or status.</p>
                </div>

                <div class="action-box">
                    <h4>Claim Requests</h4>
                    <p>Admin verifies and approves item claims.</p>
                </div>
            </div>
        </div>

    </div>

</body>

</html>