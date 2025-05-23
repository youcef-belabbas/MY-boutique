<?php
require_once 'models/EventModel.php';
$eventModel = new EventModel();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

if (!isset($event) || !$event) {
    header('Location: index.php?controller=event&action=manage&error=Event not found');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Clothing - Edit Event</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .manage-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .section-header {
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid var(--gray);
        }
        
        .image-preview-container {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Edit Event</h1>
            <p>Update event details</p>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="manage-section">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-header">Edit Event: <?= htmlspecialchars($event['title']) ?></h2>
                        <form action="index.php?controller=event&action=editEvent" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
                            <input type="hidden" name="created_by" value="<?= htmlspecialchars($event['created_by']) ?>">
                            <input type="hidden" name="created_at" value="<?= htmlspecialchars($event['created_at']) ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($event['status']) ?>">
                            <input type="hidden" name="current_image_url" value="<?= htmlspecialchars($event['image_url'] ?? '') ?>">
                            
                            <div class="form-group">
                                <label for="title" class="form-label">Event Title</label>
                                <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($event['description']) ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required value="<?= htmlspecialchars($event['date']) ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="time" class="form-label">Time</label>
                                    <input type="time" class="form-control" id="time" name="time" required value="<?= htmlspecialchars($event['time']) ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required value="<?= htmlspecialchars($event['location']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="event_image" class="form-label">Event Image</label>
                                <?php if (!empty($event['image_url'])): ?>
                                <div class="image-preview-container">
                                    <p>Current Image:</p>
                                    <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="Current Event Image" class="preview-image">
                                </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                                <small class="text-muted">Upload a new image or leave empty to keep the current one.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="image_url" class="form-label">or Image URL</label>
                                <input type="text" class="form-control" id="image_url" name="image_url" 
                                    placeholder="Enter image URL (optional)" value="<?= htmlspecialchars($event['image_url'] ?? '') ?>">
                                <small class="text-muted">You can either upload a file above or provide an image URL here.</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn">Update Event</button>
                                <a href="index.php?controller=event&action=manage" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'views/partials/footer.php'; ?>
    
    <script>
        // Preview uploaded image
        document.getElementById('event_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Create or update image preview
                    let previewContainer = document.querySelector('.image-preview-container');
                    if (!previewContainer) {
                        previewContainer = document.createElement('div');
                        previewContainer.className = 'image-preview-container';
                        const p = document.createElement('p');
                        p.textContent = 'New Image Preview:';
                        previewContainer.appendChild(p);
                        e.target.parentNode.appendChild(previewContainer);
                    } else {
                        previewContainer.innerHTML = '<p>New Image Preview:</p>';
                    }
                    
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = 'Image Preview';
                    img.className = 'preview-image';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 