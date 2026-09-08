<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Process;
use Carbon\Carbon;

class ChangelogController extends Controller
{
    public function index(): JsonResponse
    {
        $baseDir = base_path();
        $process = Process::path($baseDir)->run(['git', '-c', 'safe.directory=*', 'log', '--pretty=format:%H|%h|%an|%ae|%ct|%s', '-n', '100']);
        
        $output = trim($process->output());
        if (empty($output)) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]]);
        }

        $lines = explode("\n", $output);
        $commits = [];

        foreach ($lines as $line) {
            $parts = explode('|', $line, 6);
            if (count($parts) < 6) {
                continue;
            }

            [$hash, $shortHash, $author, $email, $timestamp, $subject] = $parts;
            $date = Carbon::createFromTimestamp((int) $timestamp)->setTimezone(config('app.timezone', 'America/Sao_Paulo'));

            $type = 'other';
            $scope = null;
            $message = $subject;

            if (preg_match('/^([a-zA-Z]+)(?:\(([^)]+)\))?!?: (.+)$/', $subject, $matches)) {
                $type = strtolower($matches[1]);
                $scope = !empty($matches[2]) ? $matches[2] : null;
                $message = $matches[3];
            }

            $commits[] = [
                'hash' => $hash,
                'short_hash' => $shortHash,
                'author' => $author,
                'email' => $email,
                'date_formatted' => $date->format('d/m/Y H:i'),
                'date_iso' => $date->toIso8601String(),
                'relative_time' => $date->locale('pt_BR')->diffForHumans(),
                'type' => $type,
                'scope' => $scope,
                'subject' => $subject,
                'message' => $message,
            ];
        }

        return response()->json([
            'data' => $commits,
            'meta' => [
                'total' => count($commits),
                'generated_at' => now()->toIso8601String(),
            ]
        ]);
    }
}
