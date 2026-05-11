<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestEntry;
use Osoobe\Utilities\Tests\Models\TestUser;
use Osoobe\Utilities\Tests\TestCase;

class UserstampTest extends TestCase
{
    public function test_creating_while_authenticated_fills_creator_and_editor(): void
    {
        $user = TestUser::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $this->actingAs($user, 'web');

        $entry = TestEntry::create(['title' => 'Test Entry']);

        $this->assertSame($user->id, $entry->creator_id);
        $this->assertSame(get_class($user), $entry->creator_type);
        $this->assertSame($user->id, $entry->editor_id);
        $this->assertSame(get_class($user), $entry->editor_type);
    }

    public function test_creating_without_auth_leaves_creator_null(): void
    {
        $entry = TestEntry::create(['title' => 'Anonymous Entry']);

        $this->assertNull($entry->creator_id);
        $this->assertNull($entry->creator_type);
    }

    public function test_updating_while_authenticated_updates_editor(): void
    {
        $user = TestUser::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);

        // Create without auth first
        $entry = TestEntry::create(['title' => 'Original']);
        $this->assertNull($entry->creator_id);

        // Now update while authenticated
        $this->actingAs($user, 'web');
        $entry->title = 'Updated';
        $entry->save();

        $this->assertSame($user->id, $entry->editor_id);
        $this->assertSame(get_class($user), $entry->editor_type);
        // creator should still be null since it was created without auth
        $this->assertNull($entry->creator_id);
    }
}
