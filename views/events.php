<?php
require_once 'models/EventModel.php';
$eventModel = new EventModel();
// Only display approved events to users
$events = $eventModel->getEvents('approved');
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Boutique - Events</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .event-date {
            background-color: var(--yellow);
            color: var(--black);
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 30px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .event-description {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }
        
        .event-location, .event-time {
            display: flex;
            align-items: center;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }
        
        .event-location i, .event-time i {
            margin-right: 0.5rem;
            color: var(--black);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
            color: var(--dark-gray);
        }
        
        .card {
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .card:hover {
            transform: translateY(-10px);
        }
        
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .card-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .card-body h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--black);
        }
        
        .card-body .btn {
            margin-top: auto;
            align-self: flex-start;
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
        
        .no-image {
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Fashion Events</h1>
            <p>Discover exclusive fashion shows, workshops, and events in your area.</p>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <section class="container">
            <div class="events-section">
                <h2 class="text-center mb-3">Upcoming Events</h2>
                
                <?php if (empty($events)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Events Currently Scheduled</h3>
                        <p>Check back soon for upcoming fashion events and workshops.</p>
                        <?php if ($user && in_array($user['role'], ['it', 'admin'])): ?>
                            <a href="index.php?controller=event&action=manage" class="btn">Create an Event</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="event-grid">
                        <?php foreach ($events as $event): ?>
                            <div class="card">
                                <?php if (!empty($event['image']) || !empty($event['image_url'])): ?>
                                    <img 
                                        src="<?= !empty($event['image']) ? htmlspecialchars($event['image']) : htmlspecialchars($event['image_url']) ?>" 
                                        class="card-img-top" 
                                        alt="<?= htmlspecialchars($event['title']) ?>"
                                    >
                                <?php else: ?>
                                    <div class="no-image">No Image Available</div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <span class="event-date">
                                        <?= isset($event['date']) ? htmlspecialchars(date('F j, Y', strtotime($event['date']))) : (isset($event['event_date']) ? htmlspecialchars(date('F j, Y', strtotime($event['event_date']))) : 'Date TBD') ?>
                                    </span>
                                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                                    <p class="event-description">
                                        <?= isset($event['description']) ? htmlspecialchars($event['description']) : 'No description available.' ?>
                                    </p>
                                    <div class="event-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= isset($event['location']) ? htmlspecialchars($event['location']) : 'Location TBA' ?>
                                    </div>
                                    <div class="event-time">
                                        <i class="far fa-clock"></i>
                                        <?= isset($event['time']) ? htmlspecialchars(date('g:i A', strtotime($event['time']))) : 'Time TBA' ?>
                                    </div>
                                    <a href="index.php?controller=event&action=registerEvent&id=<?= $event['id'] ?>" class="btn mt-3">Register Now</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'views/partials/footer.php'; ?>
</body>
</html> 