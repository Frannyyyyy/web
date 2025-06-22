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

// handles del8 all recs
if (isset($_GET['delete_all'])) {
    $conn->query("DELETE FROM fammem");
    $conn->query("DELETE FROM head");
    $conn->query("DELETE FROM evac");
    header("Location: dashboard.php");
    exit;
}

// delete operations
if (isset($_GET['delete_head'])) {
    $head_id = (int)$_GET['delete_head'];
    $conn->query("DELETE FROM fammem WHERE Head_of_Fam_ID = $head_id");
    $conn->query("DELETE FROM head WHERE Head_of_Fam_ID = $head_id");
    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['delete_member'])) {
    $member_id = (int)$_GET['delete_member'];
    $head_id = (int)$_GET['head_id'];
    $conn->query("DELETE FROM fammem WHERE Family_Member_ID = $member_id AND Head_of_Fam_ID = $head_id");
    header("Location: dashboard.php");
    exit;
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

// Get all family members with head location info
$fammem_sql = "SELECT f.*, e.Region, e.Province, e.City_or_Municipality, e.Barangay, e.Evacuation_Center_Family_Members
               FROM fammem f
               JOIN head h ON f.Head_of_Fam_ID = h.Head_of_Fam_ID
               LEFT JOIN evac e ON h.Serial_Number = e.Serial_Number
               ORDER BY f.Head_of_Fam_ID, f.Family_Member_ID";
$fammem_result = $conn->query($fammem_sql);

// Create an array of family members grouped by head
$family_members = [];
while($member = $fammem_result->fetch_assoc()) {
    $family_members[$member['Head_of_Fam_ID']][] = $member;
}
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 25px 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            padding: 25px;
            border-radius: 8px;
            border: none;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-label {
            font-size: 1.1em;
            color: #555;
            font-weight: 500;
        }
        
        .btn-delete-all {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
        }

        .return {
            background-color:rgb(98, 53, 220);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
        }

        .return a{
            text-decoration:none;
            color:inherit;
        }

        .return:hover{
            background-color:rgb(45, 22, 253);
        }

        .btn-delete-all:hover {
            background-color: #c82333;
        }
        
        .section {
            background: white;
            margin-bottom: 25px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 18px;
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
        }
        
        .table-container {
            overflow-x: auto;
            padding: 0 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }
        
        .family-head {
            background-color: #e7f3ff !important;
            font-weight: bold;
            border-left: 4px solid #007bff;
        }
        
        .family-member {
            background-color: #f9f9f9;
        }
        
        .family-member td:first-child {
            padding-left: 50px;
            position: relative;
        }
        
        .family-member td:first-child::before {
            content: "→";
            position: absolute;
            left: 25px;
            color: #6c757d;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 70px;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-edit {
            background-color: #ffc107;
        }
        
        .btn-edit:hover {
            background-color: #e0a800;
        }
        
        .btn-delete {
            background-color: #dc3545;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 25px 0;
        }
        
        .pagination a, .pagination span {
            padding: 10px 16px;
            background: white;
            border: 1px solid #dee2e6;
            text-decoration: none;
            color: #007bff;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination .current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .no-records {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 1.1em;
        }
        
        .location-info {
            line-height: 1.5;
        }
        
        .member-details {
            line-height: 1.6;
        }
        
        .empty-relationship {
            color: #999;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .btn {
                padding: 6px 10px;
                font-size: 0.8em;
                min-width: 60px;
            }
            .homebttn{
            display:flex;
            background-color:rgb(65, 62, 239);
            height: 80px;
            align-items: center;
            
            }

            .homebttn a{
                margin-top:10px;
                margin-left:40px;
                background-color: white;
                border-radius: 8px;
                padding: 8px;
                color:rgb(41, 22, 251);
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            a{
                color:inherit;
                text-decoration: none;
            }

            .homebttn a:hover{
            background-color: blue;  
            color: white;
            }

        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-home"></i> DAFAC Admin Dashboard</h1>
        <p>Department of Social Welfare and Development - Database Management</p>
    </div>

    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($head_count) ?></div>
                <div class="stat-label"><i class="fas fa-users"></i> Family Heads</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($fammem_count) ?></div>
                <div class="stat-label"><i class="fas fa-user-friends"></i> Family Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($evac_count) ?></div>
                <div class="stat-label"><i class="fas fa-building"></i> Evacuation Centers</div>
            </div>
        </div>

        <!-- Delete All Records Button -->
        <div style="text-align: center; margin: 20px 0;">
            <button onclick="deleteAllRecords()" class="btn-delete-all">
                Delete All Records
            </button>
        </div>

        <!-- Family Records -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-list"></i> Family Records
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Details</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($head_result->num_rows > 0): ?>
                            <?php while($head = $head_result->fetch_assoc()): ?>
                                <tr class="family-head">
                                    <td>#<?= $head['Head_of_Fam_ID'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($head['Head_Name']) ?></strong>
                                        <div class="member-details">
                                            Age: <?= $head['Age'] ?> | 
                                            Sex: <?= $head['Sex'] ?><br>
                                            Contact: <?= htmlspecialchars($head['Contact_Number']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="member-details">
                                            4Ps: <?= $head['FourPs_Beneficiary'] ?><br>
                                            Income: ₱<?= number_format($head['Monthly_Family_Net_Income'], 2) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="location-info">
                                            <strong><?= htmlspecialchars($head['Evacuation_Center_Family_Members']) ?></strong><br>
                                            <?= htmlspecialchars($head['Barangay'] . ', ' . $head['City_or_Municipality']) ?><br>
                                            <?= htmlspecialchars($head['Province'] . ' (' . $head['Region'] . ')') ?>
                                        </div>
                                    </td>
                                    <td class="action-btns">
                                        <a href="edit_head.php?id=<?= $head['Head_of_Fam_ID'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?delete_head=<?= $head['Head_of_Fam_ID'] ?>" class="btn btn-delete" onclick="return confirm('Delete this family and all members?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>
                                
                                <?php if (isset($family_members[$head['Head_of_Fam_ID']])): ?>
                                    <?php foreach($family_members[$head['Head_of_Fam_ID']] as $member): ?>
                                        <tr class="family-member">
                                            <td>#<?= $member['Family_Member_ID'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($member['Family_Members']) ?>
                                                <div class="member-details">
                                                    Age: <?= $member['Age'] ?> | 
                                                    Sex: <?= $member['Sex'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="member-details">
                                                    <?php if (!empty($member['Relation_to_Family_Head']) && $member['Relation_to_Family_Head'] != '0'): ?>
                                                        Relation: <strong><?= htmlspecialchars($member['Relation_to_Family_Head']) ?></strong><br>
                                                    <?php else: ?>
                                                        <span class="empty-relationship">No relationship specified</span><br>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($member['Educational_Attainment']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="location-info">
                                                    <strong><?= htmlspecialchars($member['Evacuation_Center_Family_Members']) ?></strong><br>
                                                    <?= htmlspecialchars($member['Barangay'] . ', ' . $member['City_or_Municipality']) ?><br>
                                                    <?= htmlspecialchars($member['Province'] . ' (' . $member['Region'] . ')') ?>
                                                </div>
                                            </td>
                                            <td class="action-btns">
                                                <a href="edit_member.php?member_id=<?= $member['Family_Member_ID'] ?>&head_id=<?= $head['Head_of_Fam_ID'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="?delete_member=<?= $member['Family_Member_ID'] ?>&head_id=<?= $head['Head_of_Fam_ID'] ?>" class="btn btn-delete" onclick="return confirm('Delete this family member?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-records">
                                    <i class="fas fa-info-circle" style="font-size: 2em; margin-bottom: 10px;"></i><br>
                                    No records found. <a href="acts.html" style="color: #007bff; text-decoration: none; font-weight: bold;">Add the first application</a>
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
                    <a href="?page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page+1 ?>">Next <i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin: 20px 0;">
        <button class="return"><a href="index.html">Return Home</a></button>
    </div>
    <script>
        function deleteAllRecords() {
            if (confirm('Are you sure you want to delete all records? This cannot be undone.')) {
                window.location.href = '?delete_all=1';
            }
        }

        // Auto refresh every 30 seconds
        setTimeout(function(){ 
            location.reload(); 
        }, 30000);

        // confrimation delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this record?');
        }
    </script>
</body>
</html>
