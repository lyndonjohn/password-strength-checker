/**
 * Password Strength Checker
 * A reusable JavaScript module for checking password strength
 * 
 * Criteria:
 * 1. Contains at least 1 lowercase letter (1 point)
 * 2. Contains at least 1 uppercase letter (1 point)
 * 3. Contains at least 1 number (1 point)
 * 4. Contains at least 1 special character (1 point)
 * 5. At least 8 characters long (1 point)
 * 6. More than 8 characters long (1 point)
 * 7. No common patterns (1 point)
 * 8. No repeating numbers and letters (1 point)
 * 
 * Perfect score: 8
 */

class PasswordStrengthChecker {
    constructor() {
        this.commonPatterns = [
            '123456', 'password', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', 'dragon',
            'master', 'hello', 'freedom', 'whatever', 'qazwsx',
            'trustno1', 'jordan', 'harley', 'ranger', 'buster',
            'thomas', 'tigger', 'robert', 'soccer', 'batman',
            'test', 'pass', 'guest', 'info', 'adm', 'mysql',
            'user', 'administrator', 'oracle', 'ftp', 'pi',
            'puppet', 'ansible', 'ec2-user', 'vagrant', 'azureuser'
        ];
    }

    /**
     * Check if password contains at least 1 lowercase letter
     * @param {string} password - The password to check
     * @returns {boolean} - True if contains lowercase letter
     */
    hasLowercase(password) {
        return /[a-z]/.test(password);
    }

    /**
     * Check if password contains at least 1 uppercase letter
     * @param {string} password - The password to check
     * @returns {boolean} - True if contains uppercase letter
     */
    hasUppercase(password) {
        return /[A-Z]/.test(password);
    }

    /**
     * Check if password contains at least 1 number
     * @param {string} password - The password to check
     * @returns {boolean} - True if contains number
     */
    hasNumber(password) {
        return /\d/.test(password);
    }

    /**
     * Check if password contains at least 1 special character
     * @param {string} password - The password to check
     * @returns {boolean} - True if contains special character
     */
    hasSpecialChar(password) {
        return /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    }

    /**
     * Check if password is at least 8 characters long
     * @param {string} password - The password to check
     * @returns {boolean} - True if at least 8 characters
     */
    isAtLeast8Chars(password) {
        return password.length >= 8;
    }

    /**
     * Check if password is more than 8 characters long
     * @param {string} password - The password to check
     * @returns {boolean} - True if more than 8 characters
     */
    isMoreThan8Chars(password) {
        return password.length > 8;
    }

    /**
     * Check if password contains common patterns
     * @param {string} password - The password to check
     * @returns {boolean} - True if no common patterns found
     */
    hasNoCommonPatterns(password) {
        const lowerPassword = password.toLowerCase();
        return !this.commonPatterns.some(pattern => 
            lowerPassword.includes(pattern.toLowerCase())
        );
    }

    /**
     * Check if password has no repeating consecutive characters
     * @param {string} password - The password to check
     * @returns {boolean} - True if no repeating consecutive characters
     */
    hasNoRepeatingChars(password) {
        for (let i = 0; i < password.length - 1; i++) {
            if (password[i] === password[i + 1]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Calculate the overall password strength score
     * @param {string} password - The password to check
     * @returns {object} - Object containing score and detailed results
     */
    checkPasswordStrength(password) {
        if (!password) {
            return {
                score: 0,
                maxScore: 8,
                percentage: 0,
                strength: '',
                details: {
                    hasLowercase: false,
                    hasUppercase: false,
                    hasNumber: false,
                    hasSpecialChar: false,
                    isAtLeast8Chars: false,
                    isMoreThan8Chars: false,
                    hasNoCommonPatterns: false,
                    hasNoRepeatingChars: false
                }
            };
        }

        const details = {
            hasLowercase: this.hasLowercase(password),
            hasUppercase: this.hasUppercase(password),
            hasNumber: this.hasNumber(password),
            hasSpecialChar: this.hasSpecialChar(password),
            isAtLeast8Chars: this.isAtLeast8Chars(password),
            isMoreThan8Chars: this.isMoreThan8Chars(password),
            hasNoCommonPatterns: this.hasNoCommonPatterns(password),
            hasNoRepeatingChars: this.hasNoRepeatingChars(password)
        };

        const score = Object.values(details).filter(Boolean).length;
        const percentage = (score / 8) * 100;

        let strength;
        if (score <= 2) strength = 'Weak... 🤦🏻‍♂️';
        else if (score <= 4) strength = 'Still weak 🤦🏻‍♂️';
        else if (score <= 6) strength = 'Come on, you can do better 😐';
        else if (score <= 7) strength = 'Good job 🦾';
        else strength = 'Special child! 🤯';

        return {
            score,
            maxScore: 8,
            percentage,
            strength,
            details
        };
    }

    /**
     * Get suggestions for improving password strength
     * @param {object} result - The result from checkPasswordStrength
     * @returns {array} - Array of improvement suggestions
     */
    getSuggestions(result) {
        const suggestions = [];
        
        if (!result.details.hasLowercase) {
            suggestions.push('Add at least one lowercase letter (a-z)');
        }
        if (!result.details.hasUppercase) {
            suggestions.push('Add at least one uppercase letter (A-Z)');
        }
        if (!result.details.hasNumber) {
            suggestions.push('Add at least one number (0-9)');
        }
        if (!result.details.hasSpecialChar) {
            suggestions.push('Add at least one special character (!@#$%^&*)');
        }
        if (!result.details.isAtLeast8Chars) {
            suggestions.push('Make your password at least 8 characters long');
        }
        if (!result.details.isMoreThan8Chars) {
            suggestions.push('Make your password longer than 8 characters for better security');
        }
        if (!result.details.hasNoCommonPatterns) {
            suggestions.push('Avoid common words and patterns');
        }
        if (!result.details.hasNoRepeatingChars) {
            suggestions.push('Avoid repeating consecutive characters');
        }

        return suggestions;
    }
}

// Export for use in different environments
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PasswordStrengthChecker;
} else if (typeof window !== 'undefined') {
    window.PasswordStrengthChecker = PasswordStrengthChecker;
}
