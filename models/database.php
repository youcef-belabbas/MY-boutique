<?php
// Database connection configuration
class Database {
    private $host = 'localhost';
    private $dbname = 'my_clothing_store';
    private $username = 'root'; // Update with your MySQL username
    private $password = ''; // Update with your MySQL password
    private $conn;
    private $socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; // XAMPP Mac socket path

    // Connect to the database
    public function connect() {
        $this->conn = null;
        try {
            // Try connecting using socket for XAMPP on Mac
            $this->conn = mysqli_connect(
                $this->host, 
                $this->username, 
                $this->password, 
                $this->dbname, 
                null, 
                $this->socket
            );
            
            // If that fails, try with default port
            if (!$this->conn) {
                $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->dbname, 3306);
            }
            
            if (!$this->conn) {
                throw new Exception("Connection failed: " . mysqli_connect_error());
            }
        } catch (Exception $e) {
            // Log error but don't display it to users
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection error. Please try again later.");
        }
        return $this->conn;
    }

    // Execute a query and return results
    public function query($sql) {
        try {
            $conn = $this->connect();
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                throw new Exception("Query error: " . mysqli_error($conn));
            }
            return $result;
        } catch (Exception $e) {
            error_log("Query error in: $sql - " . $e->getMessage());
            throw $e; // Re-throw the exception for the controller to handle
        }
    }

    // Fetch all rows from a query
    public function fetchAll($sql) {
        try {
            $result = $this->query($sql);
            $rows = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            return $rows;
        } catch (Exception $e) {
            error_log("FetchAll error: " . $e->getMessage());
            return []; // Return empty array on error
        }
    }

    // Fetch a single row from a query
    public function fetchOne($sql) {
        try {
            $result = $this->query($sql);
            return mysqli_fetch_assoc($result);
        } catch (Exception $e) {
            error_log("FetchOne error: " . $e->getMessage());
            return null; // Return null on error
        }
    }
}
?>