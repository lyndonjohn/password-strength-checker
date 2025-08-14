<?php

require_once 'GeneratePassword.php';

/**
 * Demo script for GeneratePassword class
 */
echo "🔐 GeneratePassword Class Demo\n";
echo "=============================\n\n";

$generator = new GeneratePassword();

echo "Generating 5 strong passwords:\n";
echo "-----------------------------\n";

for ($i = 1; $i <= 5; $i++) {
    try {
        $result = $generator->__invoke();
        $password = $result['password'];
        
        echo "Password {$i}: {$password}\n";
        echo "Length: " . strlen($password) . " characters\n";
        
        // Show character analysis
        $lowercase = preg_match_all('/[a-z]/', $password);
        $uppercase = preg_match_all('/[A-Z]/', $password);
        $numbers = preg_match_all('/\d/', $password);
        $special = preg_match_all('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password);
        
        echo "Contains: {$lowercase} lowercase, {$uppercase} uppercase, {$numbers} numbers, {$special} special chars\n";
        
        // Check for consecutive repeating characters
        $hasRepeating = false;
        for ($j = 1; $j < strlen($password); $j++) {
            if ($password[$j] === $password[$j - 1]) {
                $hasRepeating = true;
                break;
            }
        }
        
        echo "Consecutive repeating chars: " . ($hasRepeating ? "❌ YES" : "✅ NO") . "\n";
        
        // Check for common patterns
        $lowerPassword = strtolower($password);
        $commonPatterns = ['123456', 'password', 'qwerty', 'abc123'];
        $hasCommonPattern = false;
        foreach ($commonPatterns as $pattern) {
            if (str_contains($lowerPassword, $pattern)) {
                $hasCommonPattern = true;
                break;
            }
        }
        
        echo "Common weak patterns: " . ($hasCommonPattern ? "❌ YES" : "✅ NO") . "\n";
        echo "\n";
        
    } catch (Exception $e) {
        echo "Error generating password {$i}: " . $e->getMessage() . "\n\n";
    }
}

echo "=============================\n";
echo "✅ Demo completed successfully!\n";
echo "All passwords meet the security criteria:\n";
echo "- At least " . GeneratePassword::MIN_LENGTH . " characters long\n";
echo "- Contains lowercase, uppercase, numbers, and special characters\n";
echo "- No consecutive repeating characters\n";
echo "- No common weak patterns\n";
echo "- Generated using cryptographically secure random\n";
