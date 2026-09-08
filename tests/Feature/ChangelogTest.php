<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChangelogTest extends TestCase
{
    public function test_can_fetch_git_changelog_commits(): void
    {
        $response = $this->getJson('/api/v1/changelog');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'hash',
                        'short_hash',
                        'author',
                        'email',
                        'date_formatted',
                        'date_iso',
                        'relative_time',
                        'type',
                        'scope',
                        'subject',
                        'message',
                    ]
                ],
                'meta' => [
                    'total',
                    'generated_at',
                ]
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }
}
