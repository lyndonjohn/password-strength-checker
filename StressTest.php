<?php

require_once 'GeneratePassword.php';

/**
 * Stress test for GeneratePassword class
 */
class StressTest
{
    private $generator;
    private $startTime;
    private $passwords = [];

    public function __construct()
    {
        $this->generator = new GeneratePassword();
    }

    public function runStressTest(): void
    {
        echo "🔥 GeneratePassword Stress Test\n";
        echo "==============================\n\n";

        $this->startTime = microtime(true);

        // Test 1: Generate many passwords quickly
        echo "Test 1: Rapid password generation (100 passwords)\n";
        $this->testRapidGeneration(100);
        echo "✅ PASSED\n\n";

        // Test 2: Test for uniqueness
        echo "Test 2: Password uniqueness test\n";
        $this->testUniqueness();
        echo "✅ PASSED\n\n";

        // Test 3: Memory usage test
        echo "Test 3: Memory usage test\n";
        $this->testMemoryUsage();
        echo "✅ PASSED\n\n";

        // Test 4: Concurrent-like test
        echo "Test 4: Concurrent-like generation test\n";
        $this->testConcurrentLike();
        echo "✅ PASSED\n\n";

        $endTime = microtime(true);
        $totalTime = round($endTime - $this->startTime, 2);

        echo "==============================\n";
        echo "🎉 All stress tests passed!\n";
        echo "Total time: {$totalTime} seconds\n";
        echo "Total passwords generated: " . count($this->passwords) . "\n";
    }

    private function testRapidGeneration(int $count): void
    {
        $startTime = microtime(true);
        
        for ($i = 0; $i < $count; $i++) {
            try {
                $result = $this->generator->__invoke();
                $password = $result['password'];
                $this->passwords[] = $password;
                
                // Quick validation
                if (empty($password) || strlen($password) < GeneratePassword::MIN_LENGTH) {
                    throw new Exception("Invalid password generated at iteration {$i}");
                }
                
            } catch (Exception $e) {
                throw new Exception("Generation failed at iteration {$i}: " . $e->getMessage());
            }
        }
        
        $endTime = microtime(true);
        $time = round($endTime - $startTime, 3);
        $rate = round($count / $time, 1);
        
        echo "   Generated {$count} passwords in {$time}s ({$rate} passwords/sec)\n";
    }

    private function testUniqueness(): void
    {
        $uniqueCount = count(array_unique($this->passwords));
        $totalCount = count($this->passwords);
        $uniquenessRate = round(($uniqueCount / $totalCount) * 100, 1);
        
        echo "   Unique passwords: {$uniqueCount}/{$totalCount} ({$uniquenessRate}%)\n";
        
        if ($uniquenessRate < 95) {
            throw new Exception("Uniqueness rate too low: {$uniquenessRate}%");
        }
    }

    private function testMemoryUsage(): void
    {
        $memoryBefore = memory_get_usage(true);
        
        // Generate some more passwords
        for ($i = 0; $i < 50; $i++) {
            $result = $this->generator->__invoke();
            $this->passwords[] = $result['password'];
        }
        
        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;
        $memoryMB = round($memoryUsed / 1024 / 1024, 2);
        
        echo "   Memory usage: {$memoryMB} MB for 50 additional passwords\n";
        
        if ($memoryMB > 10) { // More than 10MB would be concerning
            throw new Exception("Memory usage too high: {$memoryMB} MB");
        }
    }

    private function testConcurrentLike(): void
    {
        // Simulate concurrent-like behavior by generating passwords in quick succession
        $results = [];
        $startTime = microtime(true);
        
        for ($i = 0; $i < 20; $i++) {
            $result = $this->generator->__invoke();
            $results[] = $result['password'];
            
            // Small delay to simulate real-world usage
            usleep(1000); // 1ms delay
        }
        
        $endTime = microtime(true);
        $time = round($endTime - $startTime, 3);
        
        // Check all results are valid
        foreach ($results as $index => $password) {
            if (strlen($password) < GeneratePassword::MIN_LENGTH) {
                throw new Exception("Invalid password in concurrent test at index {$index}");
            }
        }
        
        echo "   Concurrent-like test completed in {$time}s\n";
    }
}

// Run stress test if executed directly
if (php_sapi_name() === 'cli' || !defined('PHPUNIT_COMPOSER_INSTALL')) {
    $stressTest = new StressTest();
    $stressTest->runStressTest();
}
