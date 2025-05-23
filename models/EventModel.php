<?php
require_once 'database.php';

/**
 * Event Model
 * Handles all event-related data operations
 */
class EventModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get upcoming events
     * @param int $limit Number of events to return
     * @return array Array of upcoming events
     */
    public function getUpcomingEvents($limit = 3) {
        // When using database, use:
        // $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE event_date > NOW() AND status = 'approved' ORDER BY event_date LIMIT :limit");
        // $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        // $stmt->execute();
        // return $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // For now, use session data
        $events = $_SESSION['events'] ?? [];
        $approvedEvents = array_filter($events, function($event) {
            return $event['status'] === 'approved';
        });
        return array_slice($approvedEvents, 0, $limit);
    }
    
    /**
     * Get all events
     * @return array Array of all events
     */
    public function getAllEvents() {
        // When using database, use:
        // return $this->findAll();
        
        // For now, use session data
        return $_SESSION['events'] ?? [];
    }
    
    /**
     * Get event by ID
     * @param int $id Event ID
     * @return array|false Event data or false if not found
     */
    public function getEventById($id) {
        try {
            $sql = "SELECT * FROM event_requests WHERE id = '" . addslashes($id) . "'";
            $event = $this->db->fetchOne($sql);
            
            // Map the database column names to the keys expected by the views
            if ($event) {
                if (isset($event['image'])) {
                    $event['image_url'] = $event['image'];
                }
                if (isset($event['event_date'])) {
                    $event['date'] = $event['event_date'];
                }
            }
            
            return $event;
        } catch (Exception $e) {
            // Fallback to session data if database query fails
            $events = $_SESSION['events'] ?? [];
            foreach ($events as $event) {
                if ($event['id'] == $id) {
                    return $event;
                }
            }
            return false;
        }
    }
    
    /**
     * Add event
     * @param array $eventData Event data
     * @return int|bool Event ID on success, false on failure
     */
    public function addEvent($eventData) {
        try {
            // Create the event_requests table if it doesn't exist
            $createTableSQL = "CREATE TABLE IF NOT EXISTS event_requests (
                id VARCHAR(100) PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                event_date DATE,
                time VARCHAR(100),
                location VARCHAR(255),
                image VARCHAR(255),
                status VARCHAR(20) DEFAULT 'pending',
                created_by VARCHAR(100),
                created_at DATETIME
            )";
            $this->db->query($createTableSQL);
            
            // Insert the event data
            $sql = "INSERT INTO event_requests (id, title, description, event_date, time, location, image, status, created_by, created_at) 
                    VALUES (
                        '{$eventData['id']}', 
                        '{$eventData['title']}', 
                        '{$eventData['description']}', 
                        '{$eventData['date']}', 
                        '{$eventData['time']}', 
                        '{$eventData['location']}', 
                        '{$eventData['image_url']}', 
                        '{$eventData['status']}', 
                        '{$eventData['created_by']}', 
                        '{$eventData['created_at']}'
                    )";
            
            $this->db->query($sql);
            
            // If database insertion is successful, remove any session-based events to avoid duplication
            if (isset($_SESSION['events'])) {
                unset($_SESSION['events']);
            }
            
            return $eventData['id'];
        } catch (Exception $e) {
            // If database fails, use session as fallback
            if (!isset($_SESSION['events'])) {
                $_SESSION['events'] = [];
            }
            $_SESSION['events'][] = $eventData;
            return $eventData['id'];
        }
    }
    
    /**
     * Update event
     * @param int $id Event ID
     * @param array $eventData Event data
     * @return bool True on success, false on failure
     */
    public function updateEvent($id, $eventData) {
        try {
            // Create the event_requests table if it doesn't exist (safety check)
            $createTableSQL = "CREATE TABLE IF NOT EXISTS event_requests (
                id VARCHAR(100) PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                event_date DATE,
                time VARCHAR(100),
                location VARCHAR(255),
                image VARCHAR(255),
                status VARCHAR(20) DEFAULT 'pending',
                created_by VARCHAR(100),
                created_at DATETIME
            )";
            $this->db->query($createTableSQL);
            
            // Update the event in the database
            $sql = "UPDATE event_requests SET 
                title = '" . addslashes($eventData['title']) . "',
                description = '" . addslashes($eventData['description']) . "',
                event_date = '" . addslashes($eventData['date']) . "',
                time = '" . addslashes($eventData['time']) . "',
                location = '" . addslashes($eventData['location']) . "',
                image = '" . addslashes($eventData['image_url']) . "',
                status = '" . addslashes($eventData['status']) . "',
                created_by = '" . addslashes($eventData['created_by']) . "',
                created_at = '" . addslashes($eventData['created_at']) . "'
                WHERE id = '" . addslashes($id) . "'";
            
            $this->db->query($sql);
            return true;
        } catch (Exception $e) {
            // Fallback to session update if database fails
            $events = &$_SESSION['events'];
            
            foreach ($events as $key => $event) {
                if ($event['id'] == $id) {
                    // Preserve ID
                    $eventData['id'] = $id;
                    
                    // Update event
                    $events[$key] = $eventData;
                    return true;
                }
            }
            
            return false;
        }
    }
    
    /**
     * Delete event
     * @param int $id Event ID
     * @return bool True on success, false on failure
     */
    public function deleteEvent($id) {
        try {
            // Delete event from database
            $sql = "DELETE FROM event_requests WHERE id = '" . addslashes($id) . "'";
            $this->db->query($sql);
            
            // Also remove any image files if they're in our uploads directory
            $event = $this->getEventById($id);
            if ($event && isset($event['image_url']) && strpos($event['image_url'], 'uploads/events/') === 0) {
                if (file_exists($event['image_url'])) {
                    unlink($event['image_url']);
                }
            }
            
            return true;
        } catch (Exception $e) {
            // Fallback to session data if database query fails
            $events = &$_SESSION['events'];
            
            foreach ($events as $key => $event) {
                if ($event['id'] == $id) {
                    unset($events[$key]);
                    $_SESSION['events'] = array_values($events); // Re-index array
                    return true;
                }
            }
            
            return false;
        }
    }
    
    /**
     * Approve event
     * @param int $id Event ID
     * @return bool True on success, false on failure
     */
    public function approveEvent($id) {
        // When using database, use:
        // return $this->update($id, ['status' => 'approved']);
        
        // For now, use session data
        $events = &$_SESSION['events'];
        
        foreach ($events as $key => $event) {
            if ($event['id'] == $id) {
                $events[$key]['status'] = 'approved';
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Reject event
     * @param int $id Event ID
     * @return bool True on success, false on failure
     */
    public function rejectEvent($id) {
        // When using database, use:
        // return $this->update($id, ['status' => 'rejected']);
        
        // For now, use session data
        $events = &$_SESSION['events'];
        
        foreach ($events as $key => $event) {
            if ($event['id'] == $id) {
                $events[$key]['status'] = 'rejected';
                return true;
            }
        }
        
        return false;
    }

    // Update event status (pending, approved, rejected)
    public function updateEventStatus($id, $status) {
        $sql = "UPDATE event_requests 
                SET status = '$status' 
                WHERE id = '$id'";
        $this->db->query($sql);
    }

    // Get events, optionally filtered by status
    public function getEvents($status = null) {
        try {
            // Create the table if it doesn't exist
            $createTableSQL = "CREATE TABLE IF NOT EXISTS event_requests (
                id VARCHAR(100) PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                event_date DATE,
                time VARCHAR(100),
                location VARCHAR(255),
                image VARCHAR(255),
                status VARCHAR(20) DEFAULT 'pending',
                created_by VARCHAR(100),
                created_at DATETIME
            )";
            $this->db->query($createTableSQL);
            
            if ($status) {
                $sql = "SELECT * FROM event_requests WHERE status = '$status' ORDER BY event_date DESC, time ASC";
            } else {
                $sql = "SELECT * FROM event_requests ORDER BY event_date DESC, time ASC";
            }
            
            $events = $this->db->fetchAll($sql);
            
            // Map the database column names to the keys expected by the views
            foreach ($events as &$event) {
                if (isset($event['image'])) {
                    $event['image_url'] = $event['image'];
                }
                if (isset($event['event_date'])) {
                    $event['date'] = $event['event_date'];
                }
            }
            
            // If database retrieval was successful, clear the session-based events
            // to prevent duplication between database and session storage
            if (isset($_SESSION['events'])) {
                unset($_SESSION['events']);
            }
            
            // Only use session as fallback if no events found in database
            if (empty($events) && isset($_SESSION['events'])) {
                if ($status) {
                    return array_filter($_SESSION['events'], function($event) use ($status) {
                        return $event['status'] === $status;
                    });
                }
                return $_SESSION['events'];
            }
            
            return $events;
        } catch (Exception $e) {
            // Fallback to session data if database query fails
            if (isset($_SESSION['events'])) {
                if ($status) {
                    return array_filter($_SESSION['events'], function($event) use ($status) {
                        return $event['status'] === $status;
                    });
                }
                return $_SESSION['events'];
            }
            return [];
        }
    }
    
    // Register a user for an event
    public function registerUserForEvent($eventId, $userId) {
        $registrationId = uniqid();
        $registrationDate = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO event_registrations (id, event_id, user_id, registration_date) 
                VALUES ('$registrationId', '$eventId', '$userId', '$registrationDate')";
                
        $this->db->query($sql);
    }
    
    // Get registered users for an event
    public function getRegisteredUsers($eventId) {
        $sql = "SELECT u.* FROM users u 
                JOIN event_registrations er ON u.id = er.user_id 
                WHERE er.event_id = '$eventId'";
                
        return $this->db->fetchAll($sql);
    }
    
    // Check if a user is registered for an event
    public function isUserRegistered($eventId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM event_registrations 
                WHERE event_id = '$eventId' AND user_id = '$userId'";
                
        $result = $this->db->fetchOne($sql);
        return $result['count'] > 0;
    }
}
?>