<?php
/**
 * Home Controller
 * Handles home page and general site pages
 */
class HomeController extends Controller {
    /**
     * Home page
     */
    public function index() {
        // Get featured products
        $productModel = $this->model('ProductModel');
        $featuredProducts = $productModel->getFeaturedProducts();
        
        // Get upcoming events
        $eventModel = $this->model('EventModel');
        $upcomingEvents = $eventModel->getUpcomingEvents();
        
        // Render view with data
        $this->view('home', [
            'title' => 'Accueil',
            'featuredProducts' => $featuredProducts,
            'upcomingEvents' => $upcomingEvents
        ]);
    }
    
    /**
     * About page
     */
    public function about() {
        $this->view('about', [
            'title' => 'À Propos'
        ]);
    }
    
    /**
     * Contact page
     */
    public function contact() {
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $message = trim($_POST['message'] ?? '');
            
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Le nom est requis';
            }
            
            if (empty($email)) {
                $errors['email'] = 'L\'email est requis';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'L\'email est invalide';
            }
            
            if (empty($message)) {
                $errors['message'] = 'Le message est requis';
            }
            
            // If no errors, process form
            if (empty($errors)) {
                // In a real application, send email or save to database
                
                // Set flash message
                $this->setFlash('success', 'Votre message a été envoyé avec succès!');
                
                // Redirect to prevent form resubmission
                $this->redirect('home/contact');
            }
            
            // If errors, render view with errors and form data
            $this->view('contact', [
                'title' => 'Contact',
                'errors' => $errors,
                'formData' => [
                    'name' => $name,
                    'email' => $email,
                    'message' => $message
                ]
            ]);
        } else {
            // Display contact form
            $this->view('contact', [
                'title' => 'Contact'
            ]);
        }
    }
} 