<?php

error_reporting(E_ERROR | E_PARSE);

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

// Process form data when form is submitted
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true; 
    $error_message = "";
    
    // Insert into evac table first (since head table references it)
    $region = $_POST['region'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $evaccenter = $_POST['evaccenter'];
    
    $evac_sql = "INSERT INTO evac (Region, Province, District, City_or_Municipality, Barangay, Evacuation_Center_Family_Members) 
                VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($evac_sql);
    $stmt->bind_param("ssssss", $region, $province, $district, $city, $barangay, $evaccenter);
    
    if ($stmt->execute()) {
        $evac_id = $stmt->insert_id; // Get the auto-incremented Serial_Number
    } else {
        $success = false;
        echo "Error: " . $evac_sql . "<br>" . $conn->error;
    }
    $stmt->close();
    
    if($success){
        // Insert into head table
        $head_name = $_POST['head'];
        $birthdate = $_POST['birthdate'];
        $age = (int)$_POST['head_age']; 
        
        if ($age <= 0 || $age > 150) {
            $birth_date = new DateTime($birthdate);
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;
        }
        
        $birthplace = $_POST['birthplace'];
        $sex = $_POST['sex1'];
        $mother = $_POST['mother'];
        $job = $_POST['job'];
        $income = $_POST['income'];
        $idtype = $_POST['idtype'];
        $idnum = $_POST['idnum'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $fourps = isset($_POST['response']) ? $_POST['response'] : 'No';
        $iptype = $_POST['iptype'];
        $old = $_POST['old'];
        $pregnant = $_POST['pregnant'];
        $pwd = $_POST['pwd'];
        
        $head_sql = "INSERT INTO head (Head_Name, Birthdate, Age, Birthplace, Sex, Mother_Maiden_Name, Occupation, 
                    Monthly_Family_Net_Income, ID_Presented, ID_Card_Number, Address, Contact_Number, 
                    FourPs_Beneficiary, Type_of_Ethnicity, Num_of_Older_Persons, 
                    Num_of_Pregnant_and_Lactating_Mothers, Num_of_PWDs_and_with_Medical_Conditions, Serial_Number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($head_sql);
        $stmt->bind_param("ssissssdssssssiiii", $head_name, $birthdate, $age, $birthplace, $sex, $mother, $job, 
                          $income, $idtype, $idnum, $address, $contact, $fourps, $iptype, $old, $pregnant, $pwd, $evac_id);
        
        if ($stmt->execute()) {
            $head_id = $stmt->insert_id; // Get the auto-incremented Head_of_Fam_ID
        } else {
            $success = false;
            echo "Error: " . $head_sql . "<br>" . $conn->error;
        }
        $stmt->close();
    }
    
    if ($success && isset($_POST['fullName']) && !empty($_POST['fullName'])) {
        $fullNames = $_POST['fullName'];
        $relations = $_POST['relation'];
        $ages = $_POST['age'];
        $sexes = $_POST['sex'];
        $educs = $_POST['educ'];
        $skills = $_POST['skills'];
        $remarks = $_POST['remarks'];
        
        // Get next member ID for this family
        $result = $conn->query("SELECT IFNULL(MAX(Family_Member_ID), 0) + 1 FROM fammem WHERE Head_of_Fam_ID = $head_id");
        $base_member_id = $result->fetch_row()[0];
        
        $stmt = $conn->prepare("INSERT INTO fammem (Family_Member_ID, Head_of_Fam_ID, Family_Members, Relation_to_Family_Head, Age, Sex, Educational_Attainment, Occupational_Skills, Remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($fullNames as $i => $fullName) {
            if (empty($fullName) || empty($relations[$i]) || empty($ages[$i])) continue;
            
    
            $current_member_id = $base_member_id + $i;
            $current_head_id = $head_id;
            $current_fullname = $fullName;
            $current_relation = $relations[$i];
            $current_age = (int)$ages[$i]; 
            $current_sex = $sexes[$i];
            $current_educ = $educs[$i];
            $current_skill = $skills[$i];
            $current_remark = $remarks[$i];
            
            $stmt->bind_param("iississss", 
                $current_member_id,      // i - integer
                $current_head_id,        // i - integer  
                $current_fullname,       // s - string
                $current_relation,       // s - string
                $current_age,            // i - integer
                $current_sex,            // s - string
                $current_educ,           // s - string
                $current_skill,          // s - string
                $current_remark          // s - string
            );
            
            if (!$stmt->execute()) {
                $success = false;
                $error_message = "Error inserting family member: " . $stmt->error;
                break;
            }
        }
        $stmt->close();
    }
     
    if ($success) {
        // Success message
        $success_message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>DAFAC - Success</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f0f8ff;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .success-icon {
                    font-size: 80px;
                    color: #28a745;
                    margin-bottom: 20px;
                }
                .success-title {
                    color: #28a745;
                    font-size: 28px;
                    margin-bottom: 15px;
                    font-weight: bold;
                }
                .success-message {
                    color: #333;
                    font-size: 16px;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .details {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 5px;
                    margin-bottom: 30px;
                    text-align: left;
                }
                .btn {
                    background-color: #007bff;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 5px;
                    font-size: 16px;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-block;
                    margin: 10px;
                    transition: background-color 0.3s;
                }
                .btn:hover {
                    background-color: #0056b3;
                }
                .btn-secondary {
                    background-color: #6c757d;
                }
                .btn-secondary:hover {
                    background-color: #545b62;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='success-icon'>✓</div>
                <h1 class='success-title'>Successfully Submitted!</h1>
                <p class='success-message'>
                    Your DAFAC (Disaster Assistance Family Access Card) application has been successfully saved to our system. 
                    Your information is now securely stored and will be processed by the Department of Social Welfare and Development.
                </p>
                <div class='details'>
                    <h3>Submission Details:</h3>
                    <p><strong>Head of Family:</strong> " . htmlspecialchars($head_name) . "</p>
                    <p><strong>Evacuation Center:</strong> " . htmlspecialchars($evaccenter) . "</p>
                    <p><strong>Location:</strong> " . htmlspecialchars($barangay . ', ' . $city . ', ' . $province) . "</p>
                    <p><strong>Contact:</strong> " . htmlspecialchars($contact) . "</p>
                    <p><strong>Submission Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                    <p><strong>Reference Number:</strong> DAFAC-" . str_pad($head_id, 6, '0', STR_PAD_LEFT) . "</p>
                </div>
                <p style='font-size: 14px; color: #666; margin-bottom: 30px;'>
                    Please keep your reference number for future inquiries. You will be contacted if additional information is needed.
                </p>
                <a href='acts.html' class='btn'>Submit Another Application</a>
                <a href='index.html' class='btn btn-secondary'>Return to Home</a>
            </div>
        </body>
        </html>";
        
        echo $success_message;
        
    } else {
        $error_message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>DAFAC - Error</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #fff5f5; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); text-align: center; }
                .error-icon { font-size: 80px; color: #dc3545; margin-bottom: 20px; }
                .error-title { color: #dc3545; font-size: 28px; margin-bottom: 15px; font-weight: bold; }
                .btn { background-color: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error-icon'>✗</div>
                <h1 class='error-title'>Submission Failed</h1>
                <p>There was an error processing your application. Please try again.</p>
                <p style='font-size: 14px; color: #666;'>Error: " . htmlspecialchars($error_message) . "</p>
                <a href='acts.html' class='btn'>Try Again</a>
            </div>
        </body>
        </html>";
        
        echo $error_message;
    }
} else {
    die("Please submit the form to access this page.");
}

// Close connection
$conn->close();
?>
