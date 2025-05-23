<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MY Boutique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            border-top: 5px solid var(--primary-yellow);
        }
        
        .register-title {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-black);
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
            outline: none;
        }
        
        .register-button {
            background-color: var(--primary-yellow);
            color: var(--primary-black);
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .register-button:hover {
            background-color: var(--accent-yellow);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .register-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-links a {
            display: inline-block;
            margin-top: 10px;
            color: var(--accent-dark);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .register-links a:hover {
            color: var(--primary-yellow);
        }
        
        .home-link {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        
        .home-link a {
            display: inline-flex;
            align-items: center;
            color: var(--accent-dark);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .home-link a:hover {
            color: var(--primary-yellow);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 0;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h2 class="register-title">Create Account</h2>
            
            <form action="index.php?controller=auth&action=register" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your first name" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter your surname" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required>
                </div>
                
                <div class="form-group">
                    <label for="card" class="form-label">Card Number (optional)</label>
                    <input type="text" class="form-control" id="card" name="card" placeholder="Enter your card number (optional)">
                </div>
                
                <button type="submit" class="register-button">Create Account</button>
            </form>
            
            <div class="register-links">
                <a href="index.php?controller=auth&action=login">Already have an account? Login</a>
            </div>
            
            <div class="home-link">
                <a href="index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>