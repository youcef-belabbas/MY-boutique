<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MY Boutique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            border-top: 5px solid var(--primary-yellow);
        }
        
        .login-title {
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
        
        .login-button {
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
        
        .login-button:hover {
            background-color: var(--accent-yellow);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .login-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-links a {
            display: inline-block;
            margin-top: 10px;
            color: var(--accent-dark);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-links a:hover {
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
        
        .alert {
            text-align: center;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            border-left: 4px solid #4CAF50;
            color: #388E3C;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Login to MY Boutique</h2>
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?controller=auth&action=login" method="post">
                <div class="form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="login-button">Login</button>
            </form>
            
            <div class="login-links">
                <a href="index.php?controller=auth&action=register">Don't have an account? Register</a>
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