<?php
require_once 'models/EventModel.php';
$eventModel = new EventModel();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Get all events - for admin users
// For IT users, show their events and all approved events
$events = [];
if ($user && $user['role'] === 'admin') {
    $events = $eventModel->getEvents(); // All events
} elseif ($user && $user['role'] === 'it') {
    // Get all events created by this user + all approved events
    $allEvents = $eventModel->getEvents();
    foreach ($allEvents as $event) {
        if ($event['status'] === 'approved' || $event['created_by'] === $user['username']) {
            $events[] = $event;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Clothing - Manage Events</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .manage-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .section-header {
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .event-card {
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--gray);
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--yellow);
        }
        
        .event-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-date {
            background-color: var(--yellow);
            color: var(--black);
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 4px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 0.75rem;
        }
        
        .event-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: var(--dark-gray);
        }
        
        .event-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #FFC107;
            color: #000;
        }
        
        .status-approved {
            background-color: var(--success);
            color: var(--white);
        }
        
        .status-rejected {
            background-color: var(--danger);
            color: var(--white);
        }
        
        .event-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--gray);
            padding-bottom: 0;
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--dark-gray);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--black);
            border-bottom: 3px solid var(--yellow);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .no-image {
            height: 180px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                border-bottom: none;
                border-left: 3px solid transparent;
                padding: 0.75rem 1rem;
            }
            
            .tab.active {
                border-bottom: none;
                border-left: 3px solid var(--yellow);
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Manage Events</h1>
            <p>Create, update, and manage upcoming events for your boutique.</p>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="manage-section">
                <div class="tabs">
                    <div class="tab active" data-tab="create-event">Create Event</div>
                    <div class="tab" data-tab="manage-events">Manage Events</div>
                </div>
                
                <div id="create-event-tab" class="tab-content active">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="section-header">Create New Event</h2>
                            <form action="index.php?controller=event&action=addEvent" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title" class="form-label">Event Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="date" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="time" class="form-label">Time</label>
                                        <input type="time" class="form-control" id="time" name="time" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="event_image" class="form-label">Upload Event Image</label>
                                    <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                                    <small class="text-muted">Select an image file to upload</small>
                                    <div class="image-preview-container" style="display: none;">
                                        <p>Image Preview:</p>
                                        <img src="" alt="Image Preview" class="preview-image" style="max-width: 300px; max-height: 200px; border-radius: 8px; margin-top: 10px; border: 1px solid var(--gray);">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="image_url" class="form-label">or Image URL</label>
                                    <input type="text" class="form-control" id="image_url" name="image_url" 
                                        placeholder="Enter image URL (optional)">
                                    <small class="text-muted">You can either upload a file above or provide an image URL here.</small>
                                </div>
                                
                                <button type="submit" class="btn">Create Event</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div id="manage-events-tab" class="tab-content">
                    <h2 class="section-header">Your Events</h2>
                    
                    <?php if (empty($events)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times mb-3" style="font-size: 3rem; color: var(--gray);"></i>
                            <h3>No Events Found</h3>
                            <p class="mb-3">You haven't created any events yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="events-grid">
                            <?php foreach ($events as $event): ?>
                                <div class="event-card">
                                    <?php if (!empty($event['image']) || !empty($event['image_url'])): ?>
                                        <img src="<?= !empty($event['image']) ? htmlspecialchars($event['image']) : htmlspecialchars($event['image_url']) ?>" 
                                            alt="<?= htmlspecialchars($event['title']) ?>">
                                    <?php else: ?>
                                        <div class="no-image">No Image Available</div>
                                    <?php endif; ?>
                                    
                                    <div class="event-content">
                                        <div class="event-meta">
                                            <span class="event-date">
                                                <i class="far fa-calendar-alt"></i> 
                                                <?= date('M d, Y', strtotime($event['date'])) ?>
                                            </span>
                                            
                                            <span class="event-status status-<?= strtolower($event['status']) ?>">
                                                <?= ucfirst($event['status']) ?>
                                            </span>
                                        </div>
                                        
                                        <h4><?= htmlspecialchars($event['title']) ?></h4>
                                        <p class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?>
                                        </p>
                                        
                                        <div class="event-actions">
                                            <a href="index.php?controller=event&action=editEvent&id=<?= $event['id'] ?>" class="btn btn-sm">Edit</a>
                                            
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <?php if ($event['status'] === 'pending'): ?>
                                                    <a href="index.php?controller=event&action=approveEvent&id=<?= $event['id'] ?>" class="btn btn-sm">Approve</a>
                                                    <a href="index.php?controller=event&action=rejectEvent&id=<?= $event['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?controller=event&action=deleteEvent&id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include 'views/partials/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab navigation
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
            
            // Check for URL hash and activate corresponding tab
            const hash = window.location.hash;
            if (hash) {
                const tabId = hash.substring(1);
                const tabButton = document.querySelector(`.tab[data-tab="${tabId}"]`);
                if (tabButton) {
                    tabButton.click();
                }
            }
            
            // Preview uploaded image
            document.getElementById('event_image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const previewContainer = document.querySelector('.image-preview-container');
                    const previewImage = previewContainer.querySelector('img');
                    
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                        previewContainer.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>