<?php
class InterestDB {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "interest_db");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function save($principal, $rate, $time) {
        $interest = ($principal * $rate * $time) / 100;
        $total = $principal + $interest;

        $stmt = $this->conn->prepare("INSERT INTO interest_info (principal, rate, time, interest, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ddddd", $principal, $rate, $time, $interest, $total);
        $stmt->execute();
        $stmt->close();

        return [$interest, $total];
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM interest_info WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $principal, $rate, $time) {
        $interest = ($principal * $rate * $time) / 100;
        $total = $principal + $interest;

        $stmt = $this->conn->prepare("UPDATE interest_info SET principal=?, rate=?, time=?, interest=?, total=? WHERE id=?");
        $stmt->bind_param("dddddi", $principal, $rate, $time, $interest, $total, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM interest_info WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$db = new InterestDB();

$interest = $total = null;
$isEditing = false;
$editData = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["id"]) && $_POST["id"] !== "") {
        // Update existing record
        $db->update($_POST["id"], $_POST["principal"], $_POST["rate"], $_POST["time"]);
        header("Location: interest_view.php");
        exit;
    } else {
        // Insert new record
        list($interest, $total) = $db->save($_POST["principal"], $_POST["rate"], $_POST["time"]);
    }
}

if (isset($_GET["edit"])) {
    $isEditing = true;
    $editData = $db->getById($_GET["edit"]);
}

if (isset($_GET["delete"])) {
    $db->delete($_GET["delete"]);
    header("Location: interest_view.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Interest Calculator</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: #003399;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
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
            text-align: center;
        }

        .form-box {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #003399;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        .result {
            margin-top: 20px;
            color: green;
            font-weight: bold;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #003399;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">BD<span class="yellow-o">O</span></div>
    <nav>
        <a href="#">Kenneth</a>
        <a href="#">Aj</a>
        <a href="#">Lance</a>
        <a href="#">Angel</a>
    </nav>
</div>

<div class="container">
    <div class="form-box">
        <h2><?= $isEditing ? "Edit" : "Simple Interest Calculator" ?></h2>
        <form method="POST">
            <?php if ($isEditing): ?>
                <input type="hidden" name="id" value="<?= $editData["id"] ?>">
            <?php endif; ?>
            <input type="number" name="principal" step="0.01" placeholder="Principal Amount" required value="<?= $isEditing ? $editData['principal'] : '' ?>">
            <input type="number" name="rate" step="0.01" placeholder="Rate of Interest (%)" required value="<?= $isEditing ? $editData['rate'] : '' ?>">
            <input type="number" name="time" step="0.01" placeholder="Time in Years" required value="<?= $isEditing ? $editData['time'] : '' ?>">
            <input type="submit" value="<?= $isEditing ? "Update" : "Calculate & Save" ?>">
        </form>

        <?php if ($interest !== null): ?>
            <div class="result">
                Simple Interest: ₱<?= number_format($interest, 2) ?><br>
                Total Amount: ₱<?= number_format($total, 2) ?>
            </div>
        <?php endif; ?>

        <a href="interest_view.php">View All Saved Data</a>
    </div>
</div>

</body>
</html>
