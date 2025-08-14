<?php

use JetBrains\PhpStorm\Pure;

/**
 * Class GeneratePassword
 *
 * This section describes the class below.
 */
class GeneratePassword
{
    // Password strength criteria constants
    public const MIN_LENGTH = 12; // More than 8 characters for better security
    public const MAX_LENGTH = 20; // Reasonable maximum length

    // Character sets for password generation
    public const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwxyz';
    public const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const NUMBER_CHARS = '0123456789';
    public const SPECIAL_CHARS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    // Common weak patterns to avoid
    private $commonPatterns = [
        '123456',
        'password',
        'Pa$$w0rd',
        'p@ssword',
        'qwerty',
        'abc123',
        'password123',
        'admin',
        'letmein',
        'welcome',
        'monkey',
        'dragon',
        'master',
        'hello',
        'freedom',
        'whatever',
        'qazwsx',
        'trustno1',
        'jordan',
        'harley',
        'ranger',
        'buster',
        'thomas',
        'tigger',
        'robert',
        'soccer',
        'batman',
        'test',
        'pass',
        'guest',
        'info',
        'adm',
        'mysql',
        'user',
        'administrator',
        'oracle',
        'ftp',
        'pi',
        'puppet',
        'ansible',
        'ec2-user',
        'vagrant',
        'azureuser',
        'secret',
    ];

    /**
     * Generates a strong password that meets all 8 criteria:
     * 1. Contains at least 1 lowercase letter
     * 2. Contains at least 1 uppercase letter
     * 3. Contains at least 1 number
     * 4. Contains at least 1 special character
     * 5. At least 12 characters long (MIN_LENGTH)
     * 6. No common patterns
     * 7. No repeating consecutive characters
     * 8. Validates the generated password
     */
    public function __invoke()
    {
        $maxAttempts = 20; // Increased attempts for better success rate
        $attempts = 0;

        do {
            $password = $this->generateStrongPassword();
            $attempts++;
            
            // Validate the generated password
            if ($this->validatePassword($password)) {
                return ['password' => $password];
            }
        } while ($attempts < $maxAttempts);

        // If we can't generate a valid password after max attempts, throw an exception
        throw new Exception("Unable to generate a valid password after {$maxAttempts} attempts");
    }

    /**
     * Generates a strong password with all required character types
     * @throws Exception
     */
    private function generateStrongPassword(): string
    {
        $password = '';
        $length = random_int(self::MIN_LENGTH, self::MAX_LENGTH);

        // Ensure we have at least one of each required character type
        $password .= $this->getRandomChar(self::LOWERCASE_CHARS);   // 1 lowercase
        $password .= $this->getRandomChar(self::UPPERCASE_CHARS);   // 1 uppercase
        $password .= $this->getRandomChar(self::NUMBER_CHARS);      // 1 number
        $password .= $this->getRandomChar(self::SPECIAL_CHARS);     // 1 special char

        // Fill the rest with random characters from all sets
        $allChars = self::LOWERCASE_CHARS . self::UPPERCASE_CHARS . self::NUMBER_CHARS . self::SPECIAL_CHARS;
        $remainingLength = $length - 4;

        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $this->getRandomChar($allChars);
        }

        // Shuffle the password to avoid predictable patterns
        $password = $this->shuffleString($password);

        // Ensure no consecutive repeating characters
        $password = $this->fixRepeatingChars($password);

        return $password;
    }

    /**
     * Gets a random character from a given character set using cryptographically secure random
     * @param string $charSet
     * @return string
     * @throws Exception
     */
    private function getRandomChar(string $charSet): string
    {
        if (empty($charSet)) {
            throw new Exception("Character set cannot be empty");
        }
        
        $length = strlen($charSet);
        return $charSet[random_int(0, $length - 1)];
    }

    /**
     * Shuffles a string to randomize character positions
     * @param string $str
     * @return string
     */
    private function shuffleString(string $str): string
    {
        $chars = str_split($str);
        shuffle($chars);
        return implode('', $chars);
    }

    /**
     * Fixes consecutive repeating characters by replacing them
     * @param string $password
     * @return string
     * @throws Exception
     */
    private function fixRepeatingChars(string $password): string
    {
        $chars = str_split($password);
        $allChars = self::LOWERCASE_CHARS . self::UPPERCASE_CHARS . self::NUMBER_CHARS . self::SPECIAL_CHARS;
        $maxRetries = 10; // Prevent infinite loops

        for ($i = 1; $i < count($chars); $i++) {
            if ($chars[$i] === $chars[$i - 1]) {
                // Replace with a different random character
                $retries = 0;
                do {
                    $newChar = $this->getRandomChar($allChars);
                    $retries++;
                    
                    // If we can't find a different character after max retries, 
                    // use a character from a different set
                    if ($retries >= $maxRetries) {
                        $newChar = $this->getAlternativeChar($chars[$i]);
                        break;
                    }
                } while ($newChar === $chars[$i]);
                
                $chars[$i] = $newChar;
            }
        }

        return implode('', $chars);
    }

    /**
     * Gets an alternative character from a different character set
     * @param string $currentChar
     * @return string
     * @throws Exception
     */
    private function getAlternativeChar(string $currentChar): string
    {
        // Determine which set the current character belongs to
        if (str_contains(self::LOWERCASE_CHARS, $currentChar)) {
            $alternativeSets = [self::UPPERCASE_CHARS, self::NUMBER_CHARS, self::SPECIAL_CHARS];
        } elseif (str_contains(self::UPPERCASE_CHARS, $currentChar)) {
            $alternativeSets = [self::LOWERCASE_CHARS, self::NUMBER_CHARS, self::SPECIAL_CHARS];
        } elseif (str_contains(self::NUMBER_CHARS, $currentChar)) {
            $alternativeSets = [self::LOWERCASE_CHARS, self::UPPERCASE_CHARS, self::SPECIAL_CHARS];
        } else {
            $alternativeSets = [self::LOWERCASE_CHARS, self::UPPERCASE_CHARS, self::NUMBER_CHARS];
        }
        
        // Pick a random set and get a character from it
        $randomSet = $alternativeSets[random_int(0, count($alternativeSets) - 1)];
        return $this->getRandomChar($randomSet);
    }

    /**
     * Checks if password contains common weak patterns
     * @param string $password
     * @return bool
     */
    #[Pure] private function hasCommonPatterns(string $password): bool
    {
        $lowerPassword = strtolower($password);

        foreach ($this->commonPatterns as $pattern) {
            if (str_contains($lowerPassword, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates that the generated password meets all criteria
     * @param string $password
     * @return bool
     */
    private function validatePassword(string $password): bool
    {
        // Check length (at least MIN_LENGTH)
        if (strlen($password) < self::MIN_LENGTH) {
            return false;
        }

        // Check for lowercase letters
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Check for uppercase letters
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Check for numbers
        if (!preg_match('/\d/', $password)) {
            return false;
        }

        // Check for special characters (including all characters from SPECIAL_CHARS)
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
            return false;
        }

        // Check for consecutive repeating characters
        for ($i = 1; $i < strlen($password); $i++) {
            if ($password[$i] === $password[$i - 1]) {
                return false;
            }
        }

        // Check for common patterns
        if ($this->hasCommonPatterns($password)) {
            return false;
        }

        return true;
    }
}
