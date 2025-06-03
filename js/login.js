document.getElementById('signin-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Here you would typically handle the sign-in logic
    console.log('Sign in attempt:', {username, password });
    
    // For demo purposes, just redirect to index
    window.location.href = 'index.php';
});

