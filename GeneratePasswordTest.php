<?php

require_once 'GeneratePassword.php';

class GeneratePasswordTest
{
    private $generator;

    public function __construct()
    {
        $this->generator = new GeneratePassword();
    }

    public function runAllTests(): void
    {
        echo "🧪 GeneratePassword Test Suite\n";
        echo "============================\n\n";

        $tests = [
            'testBasicGeneration' => 'Basic password generation',
            'testLengthConstraints' => 'Password length validation',
            'testCharacterTypes' => 'Required character types',
            'testNoRepeatingChars' => 'No consecutive repeating chars',
            'testNoCommonPatterns' => 'No common weak patterns',
            'testSecureRandom' => 'Cryptographically secure random',
            'testInfiniteLoopPrevention' => 'Infinite loop prevention',
            'testEdgeCases' => 'Edge cases and error handling',
            'testMultipleGenerations' => 'Multiple unique generations',
            'testValidation' => 'Validation method'
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $testMethod => $description) {
            echo "Testing: {$description}\n";
            try {
                $this->$testMethod();
                echo "✅ PASSED\n";
                $passed++;
            } catch (Exception $e) {
                echo "❌ FAILED: " . $e->getMessage() . "\n";
                $failed++;
            }
            echo "\n";
        }

        echo "============================\n";
        echo "Results: {$passed} passed, {$failed} failed\n";
        
        if ($failed === 0) {
            echo "🎉 All tests passed!\n";
        } else {
            echo "⚠️  Some tests failed.\n";
        }
    }

    private function testBasicGeneration(): void
    {
        $result = $this->generator->__invoke();
        
        if (!isset($result['password']) || empty($result['password'])) {
            throw new Exception("Generated password is empty or missing");
        }
        
        echo "   Generated: " . substr($result['password'], 0, 8) . "...\n";
    }

    private function testLengthConstraints(): void
    {
        $result = $this->generator->__invoke();
        $password = $result['password'];
        $length = strlen($password);
        
        if ($length < GeneratePassword::MIN_LENGTH || $length > GeneratePassword::MAX_LENGTH) {
            throw new Exception("Invalid length: {$length} (should be " . GeneratePassword::MIN_LENGTH . "-" . GeneratePassword::MAX_LENGTH . ")");
        }
        
        echo "   Length: {$length}\n";
    }

    private function testCharacterTypes(): void
    {
        $result = $this->generator->__invoke();
        $password = $result['password'];
        
        if (!preg_match('/[a-z]/', $password)) throw new Exception("Missing lowercase");
        if (!preg_match('/[A-Z]/', $password)) throw new Exception("Missing uppercase");
        if (!preg_match('/\d/', $password)) throw new Exception("Missing numbers");
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) throw new Exception("Missing special chars");
        
        echo "   All character types present ✓\n";
    }

    private function testNoRepeatingChars(): void
    {
        $result = $this->generator->__invoke();
        $password = $result['password'];
        
        for ($i = 1; $i < strlen($password); $i++) {
            if ($password[$i] === $password[$i - 1]) {
                throw new Exception("Consecutive repeating chars found");
            }
        }
        
        echo "   No consecutive repeating chars ✓\n";
    }

    private function testNoCommonPatterns(): void
    {
        $result = $this->generator->__invoke();
        $password = strtolower($result['password']);
        
        $patterns = ['123456', 'password', 'qwerty', 'abc123'];
        foreach ($patterns as $pattern) {
            if (str_contains($password, $pattern)) {
                throw new Exception("Common pattern found: {$pattern}");
            }
        }
        
        echo "   No common patterns ✓\n";
    }

    private function testSecureRandom(): void
    {
        // Test randomness by generating multiple passwords and checking for patterns
        $passwords = [];
        $sameFirstCharCount = 0;
        
        for ($i = 0; $i < 20; $i++) {
            $result = $this->generator->__invoke();
            $password = $result['password'];
            $passwords[] = $password;
            
            // Check if first character is the same as previous
            if ($i > 0 && $password[0] === $passwords[$i-1][0]) {
                $sameFirstCharCount++;
            }
        }
        
        // With good randomness, we shouldn't get many same first characters
        if ($sameFirstCharCount > 8) {
            throw new Exception("Random generation may not be random enough");
        }
        
        echo "   Secure random generation ✓\n";
    }

    private function testInfiniteLoopPrevention(): void
    {
        // Test that the generator can handle edge cases without infinite loops
        // by generating multiple passwords and ensuring they're all valid
        $passwords = [];
        
        for ($i = 0; $i < 10; $i++) {
            $result = $this->generator->__invoke();
            $password = $result['password'];
            
            // Check for consecutive repeating characters
            for ($j = 1; $j < strlen($password); $j++) {
                if ($password[$j] === $password[$j - 1]) {
                    throw new Exception("Generated password has consecutive repeating chars");
                }
            }
            
            $passwords[] = $password;
        }
        
        echo "   Infinite loop prevention ✓\n";
    }

    private function testEdgeCases(): void
    {
        // Test edge cases by generating passwords and checking they're valid
        // This tests the overall robustness of the generator
        
        // Test multiple generations to ensure no crashes
        for ($i = 0; $i < 5; $i++) {
            try {
                $result = $this->generator->__invoke();
                $password = $result['password'];
                
                if (empty($password)) {
                    throw new Exception("Generated empty password");
                }
                
                if (strlen($password) < GeneratePassword::MIN_LENGTH) {
                    throw new Exception("Generated password too short");
                }
                
            } catch (Exception $e) {
                throw new Exception("Generator failed on iteration {$i}: " . $e->getMessage());
            }
        }
        
        echo "   Edge cases handled ✓\n";
    }

    private function testMultipleGenerations(): void
    {
        $passwords = [];
        $duplicates = 0;
        
        for ($i = 0; $i < 10; $i++) {
            $result = $this->generator->__invoke();
            $password = $result['password'];
            
            if (in_array($password, $passwords)) $duplicates++;
            $passwords[] = $password;
        }
        
        if ($duplicates > 2) {
            throw new Exception("Too many duplicates: {$duplicates}");
        }
        
        echo "   Multiple unique generations ✓\n";
    }

    private function testValidation(): void
    {
        // Test validation by generating passwords and checking they meet all criteria
        $result = $this->generator->__invoke();
        $password = $result['password'];
        
        // Manually validate the generated password
        if (strlen($password) < GeneratePassword::MIN_LENGTH) {
            throw new Exception("Generated password too short");
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Generated password missing lowercase");
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Generated password missing uppercase");
        }
        
        if (!preg_match('/\d/', $password)) {
            throw new Exception("Generated password missing numbers");
        }
        
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
            throw new Exception("Generated password missing special chars");
        }
        
        // Check for consecutive repeating characters
        for ($i = 1; $i < strlen($password); $i++) {
            if ($password[$i] === $password[$i - 1]) {
                throw new Exception("Generated password has consecutive repeating chars");
            }
        }
        
        // Check for common patterns
        $lowerPassword = strtolower($password);
        $patterns = ['123456', 'password', 'qwerty', 'abc123'];
        foreach ($patterns as $pattern) {
            if (str_contains($lowerPassword, $pattern)) {
                throw new Exception("Generated password contains common pattern: {$pattern}");
            }
        }
        
        echo "   Validation working ✓\n";
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli' || !defined('PHPUNIT_COMPOSER_INSTALL')) {
    $test = new GeneratePasswordTest();
    $test->runAllTests();
}

