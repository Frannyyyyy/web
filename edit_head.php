<?php
// Database configuration
$servername = "LocalHost";
$username = "fran";
$password = "QueryCode2212#"; 
$dbname = "dafac"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get head ID from URL
$head_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch head data
$head = [];
if ($head_id > 0) {
    $sql = "SELECT h.*, e.Region, e.Province, e.City_or_Municipality, e.Barangay, e.Evacuation_Center_Family_Members 
            FROM head h 
            LEFT JOIN evac e ON h.Serial_Number = e.Serial_Number 
            WHERE h.Head_of_Fam_ID = $head_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $head = $result->fetch_assoc();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $head_name = $conn->real_escape_string($_POST['head_name']);
    $age = (int)$_POST['age'];
    $contact = $conn->real_escape_string($_POST['contact']);
    $fourps = $conn->real_escape_string($_POST['fourps']);
    $income = (float)$_POST['income'];
    
    $update_sql = "UPDATE head SET 
                  Head_Name = '$head_name',
                  Age = $age,
                  Contact_Number = '$contact',
                  FourPs_Beneficiary = '$fourps',
                  Monthly_Family_Net_Income = $income
                  WHERE Head_of_Fam_ID = $head_id";
    
    if ($conn->query($update_sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Head of Family</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-cancel {
            background-color: #6c757d;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        .error {
            color: #dc3545;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Edit Head of Family</h1>
        
        <?php if (empty($head)): ?>
            <p>No family head found with this ID.</p>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="head_name">Head Name</label>
                    <input type="text" id="head_name" name="head_name" value="<?= htmlspecialchars($head['Head_Name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($head['Age']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($head['Contact_Number']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="fourps">4Ps Beneficiary</label>
                    <select id="fourps" name="fourps" required>
                        <option value="Yes" <?= $head['FourPs_Beneficiary'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                        <option value="No" <?= $head['FourPs_Beneficiary'] == 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="income">Monthly Income</label>
                    <input type="number" step="0.01" id="income" name="income" value="<?= htmlspecialchars($head['Monthly_Family_Net_Income']) ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>
<?php $conn->close(); ?>
