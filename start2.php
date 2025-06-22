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

// Get counts for dashboard stats
$head_count = $conn->query("SELECT COUNT(*) as count FROM head")->fetch_assoc()['count'];
$fammem_count = $conn->query("SELECT COUNT(*) as count FROM fammem")->fetch_assoc()['count'];
$evac_count = $conn->query("SELECT COUNT(*) as count FROM evac")->fetch_assoc()['count'];

// Get records with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Get head records with evacuation info
$head_sql = "SELECT h.*, e.Region, e.Province, e.City_or_Municipality, e.Barangay, e.Evacuation_Center_Family_Members 
             FROM head h 
             LEFT JOIN evac e ON h.Serial_Number = e.Serial_Number 
             ORDER BY h.Head_of_Fam_ID DESC 
             LIMIT $records_per_page OFFSET $offset";
$head_result = $conn->query($head_sql);

// Get total pages
$total_records = $conn->query("SELECT COUNT(*) as count FROM head")->fetch_assoc()['count'];
$total_pages = ceil($total_records / $records_per_page);

// Get family members
$fammem_sql = "SELECT * FROM fammem ORDER BY Family_Member_ID DESC";
$fammem_result = $conn->query($fammem_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAFAC Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1em;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1em;
            color: #666;
        }
        
        .section {
            background: white;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .section-header {
            background: #f8f9fa;
            padding: 15px;
            font-size: 1.2em;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
            margin: 0 2px;
        }
        
        .pagination a:hover {
            background: #007bff;
            color: white;
        }
        
        .pagination .current {
            background: #007bff;
            color: white;
        }
        
        .nav-buttons {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .btn {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            margin: 0 5px;
        }
        
        .btn:hover {
            background: #218838;
        }
        
        .btn-primary {
            background: #007bff;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .status-badge {
            padding: 3px 6px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .status-yes {
            background: #d4edda;
            color: #155724;
        }
        
        .status-no {
            background: #f8d7da;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFAC Admin Dashboard</h1>
        <p>Department of Social Welfare and Development - Database Management</p>
    </div>

    <div class="container">
        <div class="nav-buttons">
            <a href="index.html" class="btn btn-primary">Back to Main Site</a>
            <a href="acts.html" class="btn">New Application</a>
        </div>

        <!-- Statistics Cards -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $head_count; ?></div>
                <div class="stat-label">Total Families</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $fammem_count; ?></div>
                <div class="stat-label">Family Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $evac_count; ?></div>
                <div class="stat-label">Evacuation Centers</div>
            </div>
        </div>

        <!-- Head of Family Records -->
        <div class="section">
            <div class="section-header">
                Head of Family Records
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Evacuation Center</th>
                            <th>4Ps</th>
                            <th>Income</th>
                            <th>Vulnerabilities</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($head_result->num_rows > 0): ?>
                            <?php while(($row = $head_result)->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['Head_of_Fam_ID']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['Head_Name']); ?></td>
                                    <td><?php echo $row['Age']; ?></td>
                                    <td><?php echo $row['Sex']; ?></td>
                                    <td><?php echo htmlspecialchars($row['Contact_Number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Barangay'] . ', ' . $row['City_or_Municipality']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Evacuation_Center_Family_Members']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($row['FourPs_Beneficiary']) == 'yes' ? 'status-yes' : 'status-no'; ?>">
                                            <?php echo $row['FourPs_Beneficiary']; ?>
                                        </span>
                                    </td>
                                    <td>₱<?php echo number_format($row['Monthly_Family_Net_Income']); ?></td>
                                    <td>
                                        <small>
                                            Elderly: <?php echo $row['Num_of_Older_Persons']; ?><br>
                                            Pregnant: <?php echo $row['Num_of_Pregnant_and_Lactating_Mothers']; ?><br>
                                            PWD: <?php echo $row['Num_of_PWDs_and_with_Medical_Conditions']; ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 40px; color: #666;">
                                    No records found. <a href="acts.html">Add the first application</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>">« Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>">Next »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Family Members -->
        <div class="section">
            <div class="section-header">
                Family Members Records
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Education</th>
                            <th>Skills</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($fammem_result->num_rows > 0): ?>
                            <?php while($row = $fammem_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['Family_Member_ID']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['Family_Members']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Relation_to_Family_Head']); ?></td>
                                    <td><?php echo $row['Age']; ?></td>
                                    <td><?php echo $row['Sex']; ?></td>
                                    <td><?php echo htmlspecialchars($row['Educational_Attainment']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Occupational_Skills']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Remarks']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                    No family member records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(function(){
            location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
$conn->close();
?>


