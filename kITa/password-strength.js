// password-strength.js

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength-indicator mt-2';
    
    // Insert strength indicator after password input
    passwordInput.parentNode.insertBefore(strengthIndicator, passwordInput.nextSibling);
    
    // Create progress bar container
    const progressBar = document.createElement('div');
    progressBar.className = 'progress';
    progressBar.style.height = '5px';
    
    // Create progress bar indicator
    const progressIndicator = document.createElement('div');
    progressIndicator.className = 'progress-bar';
    progressIndicator.style.width = '0%';
    progressIndicator.style.transition = 'all 0.3s';
    
    // Create message element
    const messageElement = document.createElement('small');
    messageElement.className = 'text-muted';
    
    // Append elements
    progressBar.appendChild(progressIndicator);
    strengthIndicator.appendChild(progressBar);
    strengthIndicator.appendChild(messageElement);
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const length = password.length;
        
        let strength = '';
        let message = '';
        
        if (length === 0) {
            strength = '';
            message = '';
        } else if (length < 8) {
            strength = 'weak';
            message = 'Password is too weak! Must be at least 8 characters.';
        } else if (length === 8) {
            strength = 'good';
            message = 'Password strength is good!';
        } else {
            strength = 'strong';
            message = 'Password strength is strong!';
        }
        
        // Update UI
        progressIndicator.style.width = progressWidth;
        progressIndicator.style.backgroundColor = progressColor;
        messageElement.textContent = message;
        messageElement.style.color = progressColor;
        
        // Add a data attribute for potential server-side validation
        passwordInput.setAttribute('data-strength', strength);
    });
});