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
echo "Connected successfully to the database.\n";

// Process form data when form is submitted
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
        echo "New evacuation center record created successfully. ID: " . $evac_id . "\n";
    } else {
        echo "Error: " . $evac_sql . "<br>" . $conn->error;
    }
    $stmt->close();

    // Insert into head table
    $head_name = $_POST['head'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $birthplace = $_POST['birthplace'];
    $sex = $_POST['sex'];
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
        echo "New head of family record created successfully. ID: " . $head_id . "\n";
    } else {
        echo "Error: " . $head_sql . "<br>" . $conn->error;
    }
    $stmt->close();

    
    if (isset($_POST['fullName']) && !empty($_POST['fullName'])) {
        $fullNames = $_POST['fullName'];
        $relations = $_POST['relation'];
        $ages = $_POST['age'];
        $sexes = $_POST['sex'];
        $educs = $_POST['educ'];
        $skills = $_POST['skills'];
        $remarks = $_POST['remarks'];
        
        $fammem_sql = "INSERT INTO fammem (Family_Members, Relation_to_Family_Head, Age, Sex, 
                      Educational_Attainment, Occupational_Skills, Remarks) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($fammem_sql);
        
        // Loop through each family member
      foreach ($fullNames as $i => $fullName) {
            // Skip if any required field is empty
            if (empty($fullName) || empty($relations[$i]) || empty($ages[$i])) {
                continue;
            }
            
            $stmt->bind_param("ssissss", 
                $fullName,
                $relations[$i],
                $ages[$i],
                $sexes[$i],
                $educs[$i],
                $skills[$i],
                $remarks[$i]
            );
            
            if ($stmt->execute()) {
                echo "New family member record created successfully. ID: " . $stmt->insert_id . "\n";
            } else {
                echo "Error inserting family member: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }

    echo "All data processed successfully!";
}
else {
   
    die("Please submit the form to access this page.");
}
// Close connection
$conn->close();
?>

