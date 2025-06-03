document.getElementById('signup-form').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    const full_name = document.getElementById('full_name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('c_password').value;

    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        e.preventDefault();
    }
    
    // Here you would typically handle the sign-up logic
    console.log('Sign up attempt:', {username ,full_name, email, password });
    
});