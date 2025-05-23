<?php
require_once 'models/EventModel.php';

class EventController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new EventModel();
    }

    public function events() {
        // This method displays the public events page
        // Only approved events will be shown (filtered in the view)
        include 'views/events.php';
    }

    public function addEvent() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle image upload if file is provided
            $image_url = '';
            $upload_directory = 'uploads/events/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0755, true);
            }
            
            // Check if image file is uploaded
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $temp_name = $_FILES['event_image']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['event_image']['name']);
                $file_path = $upload_directory . $file_name;
                
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($temp_name, $file_path)) {
                    $image_url = $file_path;
                }
            } else if (!empty($_POST['image_url'])) {
                // Use URL if provided and no file was uploaded
                $image_url = $_POST['image_url'];
            }
            
            $event = [
                'id' => uniqid(),
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'location' => $_POST['location'],
                'image_url' => $image_url,
                'image' => $image_url,
                'status' => $_SESSION['user']['role'] === 'admin' ? 'approved' : 'pending',
                'created_by' => $_SESSION['user']['name'] ?? $_SESSION['user']['email'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            try {
                // Add the event to the database
                $this->eventModel->addEvent($event);
                
                if ($_SESSION['user']['role'] === 'admin') {
                    header('Location: index.php?controller=event&action=events&message=Event created and approved successfully.');
                } else {
                    header('Location: index.php?controller=event&action=manage&message=Event added successfully and awaiting admin approval');
                }
            } catch (Exception $e) {
                header('Location: index.php?controller=event&action=manage&error=' . urlencode($e->getMessage()));
            }
            
            return;
        } else {
            include 'views/manage_events.php';
        }
    }

    public function editEvent() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle image upload if file is provided
            $image_url = $_POST['current_image_url'] ?? ''; // Default to current image
            $upload_directory = 'uploads/events/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0755, true);
            }
            
            // Check if image file is uploaded
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $temp_name = $_FILES['event_image']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['event_image']['name']);
                $file_path = $upload_directory . $file_name;
                
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($temp_name, $file_path)) {
                    $image_url = $file_path;
                }
            } else if (!empty($_POST['image_url'])) {
                // Use URL if provided and no file was uploaded
                $image_url = $_POST['image_url'];
            }
            
            $event = [
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'location' => $_POST['location'],
                'image_url' => $image_url, // Keep for backward compatibility
                'image' => $image_url,     // Add for database column
                'status' => $_POST['status'],
                'created_by' => $_POST['created_by'],
                'created_at' => $_POST['created_at']
            ];
            
            $this->eventModel->updateEvent($_POST['id'], $event);
            header('Location: index.php?controller=event&action=manage&message=Event updated successfully');
            return;
        } else {
            $event = $this->eventModel->getEventById($_GET['id']);
            if (!$event) {
                header('Location: index.php?controller=event&action=manage&error=Event not found');
                return;
            }
            include 'views/edit_event.php';
        }
    }

    public function deleteEvent() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $this->eventModel->deleteEvent($_GET['id']);
        header('Location: index.php?controller=event&action=manage&message=Event deleted successfully');
    }

    public function registerEvent() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $eventId = $_GET['id'];
        $userId = $_SESSION['user']['id'];
        
        // Register the user for the event
        $this->eventModel->registerUserForEvent($eventId, $userId);
        
        header('Location: index.php?controller=event&action=events&message=Successfully registered for the event');
    }

    public function approveEvent() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        // Get the event to include its name in the success message
        $event = $this->eventModel->getEventById($_GET['id']);
        $this->eventModel->updateEventStatus($_GET['id'], 'approved');
        
        // Provide a more detailed success message
        $message = 'Event "' . htmlspecialchars($event['title']) . '" has been approved and is now public';
        header('Location: index.php?controller=admin&action=dashboard&message=' . urlencode($message));
    }

    public function rejectEvent() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        // Get the event to include its name in the message
        $event = $this->eventModel->getEventById($_GET['id']);
        $this->eventModel->updateEventStatus($_GET['id'], 'rejected');
        
        // Provide a more detailed message
        $message = 'Event "' . htmlspecialchars($event['title']) . '" has been rejected';
        header('Location: index.php?controller=admin&action=dashboard&message=' . urlencode($message));
    }

    public function manage() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/manage_events.php';
    }
}
?>