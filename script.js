const checker = new PasswordStrengthChecker();
const passwordInput = document.getElementById('password');
const togglePasswordBtn = document.getElementById('togglePassword');
const generateBtn = document.getElementById('generateBtn');
const strengthFill = document.getElementById('strengthFill');
const strengthText = document.getElementById('strengthText');
const scoreElement = document.getElementById('score');
const suggestionsDiv = document.getElementById('suggestions');
const suggestionsList = document.getElementById('suggestionsList');

// Criteria elements
const criteriaElements = {
    hasLowercase: document.getElementById('lowercase'),
    hasUppercase: document.getElementById('uppercase'),
    hasNumber: document.getElementById('number'),
    hasSpecialChar: document.getElementById('special'),
    isAtLeast8Chars: document.getElementById('length8'),
    isMoreThan8Chars: document.getElementById('lengthMore'),
    hasNoCommonPatterns: document.getElementById('noPatterns'),
    hasNoRepeatingChars: document.getElementById('noRepeating')
};

function updateStrengthDisplay(result) {
    // Update strength bar
    const percentage = result.percentage;
    strengthFill.style.width = percentage + '%';

    // Update strength bar color
    if (percentage <= 25) {
        strengthFill.style.backgroundColor = '#dc3545';
    } else if (percentage <= 50) {
        strengthFill.style.backgroundColor = '#fd7e14';
    } else if (percentage <= 75) {
        strengthFill.style.backgroundColor = '#ffc107';
    } else {
        strengthFill.style.backgroundColor = '#28a745';
    }

    // Update strength text
    strengthText.textContent = result.strength;
    strengthText.style.color = strengthFill.style.backgroundColor;

    // Update score
    scoreElement.textContent = `Score: ${result.score}/8`;

    // Update criteria icons
    Object.keys(result.details).forEach(key => {
        const element = criteriaElements[key];
        if (element) {
            element.textContent = result.details[key] ? '✅' : '❌';
            element.style.color = result.details[key] ? '#28a745' : '#dc3545';
        }
    });

    // Update suggestions
    const suggestions = checker.getSuggestions(result);
    if (suggestions.length > 0) {
        suggestionsList.innerHTML = '';
        suggestions.forEach(suggestion => {
            const li = document.createElement('li');
            li.textContent = suggestion;
            suggestionsList.appendChild(li);
        });
        suggestionsDiv.classList.remove('hidden');
    } else {
        suggestionsDiv.classList.add('hidden');
    }
}

// Generate password function
async function generatePassword() {
    try {
        generateBtn.disabled = true;
        generateBtn.textContent = 'Generating...';
        generateBtn.style.background = '#999';

        const response = await fetch('generate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'generate' })
        });

        if (!response.ok) {
            throw new Error('Failed to generate password');
        }

        const data = await response.json();
        console.log('Response data:', data);

        // Set the generated password in the input field
        if (data.password) {
            passwordInput.value = data.password;
            console.log('Password set:', data.password);
        } else {
            throw new Error('No password received from server');
        }

        // Trigger the input event to update strength display
        const event = new Event('input', { bubbles: true });
        passwordInput.dispatchEvent(event);

        // Show success message
        showMessage('Password generated successfully!', 'success');

    } catch (error) {
        console.error('Error generating password:', error);
        showMessage('Failed to generate password. Please try again.', 'error');
    } finally {
        generateBtn.disabled = false;
        generateBtn.textContent = 'Generate Password';
        generateBtn.style.background = '#667eea';
    }
}

// Show message function
function showMessage(message, type) {
    // Remove existing message
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;

    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(messageDiv);

    // Remove message after 3 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
}

// Toggle password visibility function
function togglePasswordVisibility() {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;

    // Update button icon
    togglePasswordBtn.textContent = type === 'password' ? '👁️' : '🙈';
    togglePasswordBtn.title = type === 'password' ? 'Show password' : 'Hide password';
}

// Add event listeners
togglePasswordBtn.addEventListener('click', togglePasswordVisibility);
generateBtn.addEventListener('click', generatePassword);

passwordInput.addEventListener('input', function () {
    const password = this.value;
    const result = checker.checkPasswordStrength(password);
    updateStrengthDisplay(result);
});

// Initialize with empty state
updateStrengthDisplay(checker.checkPasswordStrength(''));
