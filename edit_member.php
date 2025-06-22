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

// Get member and head IDs from URL
$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$head_id = isset($_GET['head_id']) ? (int)$_GET['head_id'] : 0;

// Fetch member data
$member = [];
if ($member_id > 0 && $head_id > 0) {
    $sql = "SELECT * FROM fammem WHERE Family_Member_ID = $member_id AND Head_of_Fam_ID = $head_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $relation = $conn->real_escape_string($_POST['relation']);
    $age = (int)$_POST['age'];
    $sex = $conn->real_escape_string($_POST['sex']);
    $education = $conn->real_escape_string($_POST['education']);
    
    $update_sql = "UPDATE fammem SET 
                  Family_Members = '$name',
                  Relation_to_Family_Head = '$relation',
                  Age = $age,
                  Sex = '$sex',
                  Educational_Attainment = '$education'
                  WHERE Family_Member_ID = $member_id AND Head_of_Fam_ID = $head_id";
    
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
    <title>Edit Family Member</title>
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
        <h1><i class="fas fa-user-edit"></i> Edit Family Member</h1>
        
        <?php if (empty($member)): ?>
            <p>No family member found with this ID.</p>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Member Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($member['Family_Members']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="relation">Relation to Head</label>
                    <input type="text" id="relation" name="relation" value="<?= htmlspecialchars($member['Relation_to_Family_Head']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($member['Age']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="sex">Sex</label>
                    <select id="sex" name="sex" required>
                        <option value="Male" <?= $member['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $member['Sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $member['Sex'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="education">Educational Attainment</label>
                    <input type="text" id="education" name="education" value="<?= htmlspecialchars($member['Educational_Attainment']) ?>">
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
