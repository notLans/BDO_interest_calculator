<?php
class InterestDB {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "interest_db");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM interest_info ORDER BY id DESC");
        return $result;
    }
}

$db = new InterestDB();
$data = $db->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved Interest Data</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #003399;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .yellow-o {
            color: #FFD700;
        }

        .container {
            padding: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #003399;
            color: white;
        }

        a {
            text-decoration: none;
            color: #003399;
            font-weight: bold;
        }

        .actions a {
            margin: 0 5px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">BD<span class="yellow-o">O</span></div>
</div>

<div class="container">
    <h2>All Saved Interest Data</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Principal</th>
            <th>Rate (%)</th>
            <th>Time (Years)</th>
            <th>Interest</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>₱<?= number_format($row['principal'], 2) ?></td>
                <td><?= $row['rate'] ?></td>
                <td><?= $row['time'] ?></td>
                <td>₱<?= number_format($row['interest'], 2) ?></td>
                <td>₱<?= number_format($row['total'], 2) ?></td>
                <td class="actions">
                    <a href="interest_form.php?edit=<?= $row['id'] ?>">Edit</a>
                    <a href="interest_form.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="interest_form.php">← Back to Form</a>
</div>

</body>
</html>
