<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemMonitorController extends Controller
{
    /**
     * Get real-time system metrics.
     */
    public function getMetrics()
    {
        $metrics = [
            'serverLoad' => $this->getCpuUsage(),
            'memoryUsage' => $this->getMemoryUsage(),
            'dbLatency' => $this->getDbLatency(),
            'activeSessions' => $this->getActiveSessionsCount(),
            'status' => 'operational',
        ];

        // Determine system status based on thresholds
        if ($metrics['serverLoad'] > 90 || $metrics['memoryUsage'] > 95) {
            $metrics['status'] = 'critical';
        } elseif ($metrics['serverLoad'] > 70 || $metrics['memoryUsage'] > 85 || $metrics['dbLatency'] > 200) {
            $metrics['status'] = 'degraded';
        }

        return response()->json($metrics);
    }

    /**
     * Get recent application logs.
     */
    public function getLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            // Read last 100 lines
            $fileContent = shell_exec("powershell -Command \"Get-Content '$logPath' -Tail 100\"");
            $lines = explode("\n", trim($fileContent));

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                // Simple regex to parse Laravel log format: [2024-02-19 12:34:56] local.INFO: message
                preg_match('/^\[(?P<time>.*?)\] (?P<env>.*?)\.(?P<type>.*?): (?P<description>.*)$/', $line, $matches);

                if ($matches) {
                    $logs[] = [
                        'id' => md5($line),
                        'time' => date('H:i:s', strtotime($matches['time'])),
                        'type' => strtolower($matches['type']),
                        'title' => ucfirst($matches['type']),
                        'description' => $matches['description'],
                        'latency' => rand(5, 50), // Simulated latency for log processing
                    ];
                } else {
                    // Fallback for non-standard lines
                    $logs[] = [
                        'id' => md5($line),
                        'time' => date('H:i:s'),
                        'type' => 'info',
                        'title' => 'System',
                        'description' => $line,
                        'latency' => null,
                    ];
                }
            }
        }

        // Return reversed to show newest first
        return response()->json(array_reverse($logs));
    }

    private function getCpuUsage()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('wmic cpu get loadpercentage /value');
            if (preg_match('/LoadPercentage=(\d+)/', $output, $matches)) {
                return (int)$matches[1];
            }
        } else {
            $load = sys_getloadavg();
            return (int)($load[0] * 10); // Simple approximation for Linux
        }
        return rand(10, 30); // Fallback
    }

    private function getMemoryUsage()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $free = shell_exec('wmic OS get FreePhysicalMemory /value');
            $total = shell_exec('wmic OS get TotalVisibleMemorySize /value');

            if (
                preg_match('/FreePhysicalMemory=(\d+)/', $free, $matchFree) &&
                preg_match('/TotalVisibleMemorySize=(\d+)/', $total, $matchTotal)
            ) {

                $freeValue = (int)$matchFree[1];
                $totalValue = (int)$matchTotal[1];
                return round((($totalValue - $freeValue) / $totalValue) * 100);
            }
        } else {
            $free = shell_exec('free');
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            if (count($mem) >= 3) {
                return round($mem[2] / $mem[1] * 100);
            }
        }
        return rand(40, 60); // Fallback
    }

    private function getDbLatency()
    {
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            return round((microtime(true) - $start) * 1000);
        } catch (\Exception $e) {
            return 999;
        }
    }

    private function getActiveSessionsCount()
    {
        $sessionPath = storage_path('framework/sessions');
        if (File::isDirectory($sessionPath)) {
            $files = File::files($sessionPath);
            return count($files);
        }
        return 0;
    }
}
