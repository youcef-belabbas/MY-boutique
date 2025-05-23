<?php
require_once 'models/database.php';

// Create database connection
$db = new Database();
$conn = $db->connect();

// SQL to create the event_requests table
$sql = "
CREATE TABLE IF NOT EXISTS event_requests (
    id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATE,
    time TIME,
    location VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_by VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the SQL
if (mysqli_query($conn, $sql)) {
    echo "Table 'event_requests' created successfully!<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// SQL to create the event_registrations table
$registrationsSql = "
CREATE TABLE IF NOT EXISTS event_registrations (
    id VARCHAR(255) PRIMARY KEY,
    event_id VARCHAR(255) NOT NULL,
    user_id VARCHAR(255) NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event_requests(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (event_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the SQL
if (mysqli_query($conn, $registrationsSql)) {
    echo "Table 'event_registrations' created successfully!<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Insert sample data (optional)
$checkSql = "SELECT COUNT(*) as count FROM event_requests";
$result = mysqli_query($conn, $checkSql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sampleSql = "
    INSERT INTO event_requests (id, title, description, date, time, location, image, status, created_by, created_at) 
    VALUES 
    ('event1', 'Summer Fashion Show 2025', 'Join us for our exclusive summer collection reveal!', '2025-06-15', '18:00:00', 'MY Boutique Main Store', 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8ZmFzaGlvbiUyMGV2ZW50fGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60', 'approved', 'admin', NOW());
    ";
    
    if (mysqli_query($conn, $sampleSql)) {
        echo "Sample event data added successfully!";
    } else {
        echo "Error adding sample data: " . mysqli_error($conn);
    }
} else {
    echo "Sample data not added because the table already has data.";
}

// Close connection
mysqli_close($conn);
?> 