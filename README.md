# Password Strength Checker

A reusable JavaScript module for checking password strength with 8 different criteria.

## Features

- **8-Point Scoring System**: Evaluates passwords based on 8 different criteria
- **Real-time Feedback**: Provides instant strength assessment
- **Detailed Analysis**: Shows which criteria are met and which need improvement
- **Smart Suggestions**: Offers specific advice to improve password strength
- **Common Pattern Detection**: Identifies and flags common weak passwords
- **Repeating Character Detection**: Checks for consecutive repeating characters
- **Password Generator**: Generates a strong password based on the criteria

## Password Criteria

1. **Contains at least 1 lowercase letter** (1 point)
2. **Contains at least 1 uppercase letter** (1 point)
3. **Contains at least 1 number** (1 point)
4. **Contains at least 1 special character** (1 point)
5. **At least 8 characters long** (1 point)
6. **More than 8 characters long** (1 point)
7. **No common patterns** (1 point)
8. **No repeating consecutive characters** (1 point)

**Perfect Score: 8/8**

## Usage

### Basic Usage

```javascript
// Create an instance
const checker = new PasswordStrengthChecker();

// Check password strength
const result = checker.checkPasswordStrength('MyPassword123!');

console.log(result);
// Output:
// {
//   score: 7,
//   maxScore: 8,
//   percentage: 87.5,
//   strength: 'Strong',
//   details: {
//     hasLowercase: true,
//     hasUppercase: true,
//     hasNumber: true,
//     hasSpecialChar: true,
//     isAtLeast8Chars: true,
//     isMoreThan8Chars: true,
//     hasNoCommonPatterns: true,
//     hasNoRepeatingChars: false
//   }
// }
```

### Get Improvement Suggestions

```javascript
const result = checker.checkPasswordStrength('weak');
const suggestions = checker.getSuggestions(result);

console.log(suggestions);
// Output:
// [
//   'Add at least one uppercase letter (A-Z)',
//   'Add at least one number (0-9)',
//   'Add at least one special character (!@#$%^&*)',
//   'Make your password at least 8 characters long',
//   'Make your password longer than 8 characters for better security'
// ]
```

### Individual Criteria Checks

```javascript
const checker = new PasswordStrengthChecker();

console.log(checker.hasLowercase('Password')); // true
console.log(checker.hasUppercase('password')); // false
console.log(checker.hasNumber('Password123')); // true
console.log(checker.hasSpecialChar('Password!')); // true
console.log(checker.isAtLeast8Chars('Password')); // true
console.log(checker.isMoreThan8Chars('Password')); // false
console.log(checker.hasNoCommonPatterns('password123')); // false
console.log(checker.hasNoRepeatingChars('Password')); // true
```

## Installation

### Browser Usage

1. Include the script in your HTML:
```html
<script src="password-strength-checker.js"></script>
```

2. Use the global `PasswordStrengthChecker` class:
```javascript
const checker = new PasswordStrengthChecker();
```

### Node.js Usage

1. Import the module:
```javascript
const PasswordStrengthChecker = require('./password-strength-checker.js');
```

2. Create an instance and use:
```javascript
const checker = new PasswordStrengthChecker();
```

## Demo

Open `index.html` in your browser to see a live demo of the password strength checker with a beautiful UI.

## Strength Levels

- **0-2 points**: Very Weak (Red)
- **3-4 points**: Weak (Orange)
- **5-6 points**: Moderate (Yellow)
- **7 points**: Strong (Light Green)
- **8 points**: Very Strong (Green)

## Common Patterns Detected

The checker identifies common weak passwords and patterns including:
- `123456`, `password`, `qwerty`, `abc123`
- `admin`, `letmein`, `welcome`, `monkey`
- `master`, `hello`, `freedom`, `whatever`
- And many more...

## License

This project is open source and available under the MIT License.
