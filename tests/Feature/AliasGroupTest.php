<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\AliasGroup;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class AliasGroupTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();
    }

    #[Test]
    public function user_can_create_a_group()
    {
        $response = $this->postJson('/api/v1/alias-groups', [
            'name' => 'Shopping',
            'description' => 'Online stores',
            'color' => 'cyan',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Shopping')
            ->assertJsonPath('data.color', 'cyan')
            ->assertJsonPath('data.aliases_count', 0);

        $this->assertEquals(1, AliasGroup::count());
    }

    #[Test]
    public function name_is_required_and_must_be_unique_per_user()
    {
        $this->postJson('/api/v1/alias-groups', ['name' => 'Work'])->assertStatus(201);

        $this->postJson('/api/v1/alias-groups', ['name' => 'Work'])
            ->assertStatus(422);

        // Different user can have same name
        $other = $this->createUser('other');
        AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $other->id,
            'name' => 'Work',
        ]);
        $this->assertEquals(2, AliasGroup::count());
    }

    #[Test]
    public function color_must_be_from_palette()
    {
        $this->postJson('/api/v1/alias-groups', [
            'name' => 'Test',
            'color' => 'chartreuse',
        ])->assertStatus(422);
    }

    #[Test]
    public function user_cannot_see_or_modify_other_users_groups()
    {
        $other = $this->createUser('other');
        $foreign = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $other->id,
            'name' => 'Foreign',
        ]);

        $this->patchJson("/api/v1/alias-groups/{$foreign->id}", ['name' => 'Hacked'])
            ->assertNotFound();
        $this->deleteJson("/api/v1/alias-groups/{$foreign->id}")
            ->assertNotFound();
        $this->assertEquals('Foreign', $foreign->fresh()->name);
    }

    #[Test]
    public function empty_group_can_be_deleted()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'To Delete',
        ]);

        $this->deleteJson("/api/v1/alias-groups/{$group->id}")
            ->assertNoContent();

        $this->assertNull(AliasGroup::find($group->id));
    }

    #[Test]
    public function non_empty_group_cannot_be_deleted()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'Has Aliases',
        ]);
        Alias::factory()->create([
            'user_id' => $this->user->id,
            'alias_group_id' => $group->id,
        ]);

        $this->deleteJson("/api/v1/alias-groups/{$group->id}")
            ->assertStatus(422);

        $this->assertNotNull(AliasGroup::find($group->id));
    }

    #[Test]
    public function bulk_move_assigns_group_to_many_aliases()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'Bulk',
        ]);
        $aliases = Alias::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->postJson('/api/v1/aliases/group/bulk', [
            'ids' => $aliases->pluck('id')->toArray(),
            'alias_group_id' => $group->id,
        ]);

        $response->assertSuccessful();

        $this->assertEquals(3, $this->user->aliases()->where('alias_group_id', $group->id)->count());
    }

    #[Test]
    public function bulk_move_with_null_removes_group()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'Bulk',
        ]);
        $aliases = Alias::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'alias_group_id' => $group->id,
        ]);

        $this->postJson('/api/v1/aliases/group/bulk', [
            'ids' => $aliases->pluck('id')->toArray(),
            'alias_group_id' => null,
        ])->assertSuccessful();

        $this->assertEquals(0, $this->user->aliases()->where('alias_group_id', $group->id)->count());
    }

    #[Test]
    public function bulk_move_rejects_foreign_group()
    {
        $other = $this->createUser('other');
        $foreignGroup = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $other->id,
            'name' => 'Foreign',
        ]);
        $aliases = Alias::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->postJson('/api/v1/aliases/group/bulk', [
            'ids' => $aliases->pluck('id')->toArray(),
            'alias_group_id' => $foreignGroup->id,
        ])->assertNotFound();

        $this->assertEquals(0, $this->user->aliases()->whereNotNull('alias_group_id')->count());
    }

    #[Test]
    public function alias_create_accepts_group_id()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'My Group',
        ]);

        $response = $this->postJson('/api/v1/aliases', [
            'domain' => 'mailflusher.com',
            'description' => 'in a group',
            'alias_group_id' => $group->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.alias_group_id', $group->id);
    }

    #[Test]
    public function alias_create_rejects_foreign_group_id()
    {
        $other = $this->createUser('other');
        $foreignGroup = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $other->id,
            'name' => 'Foreign',
        ]);

        $this->postJson('/api/v1/aliases', [
            'domain' => 'mailflusher.com',
            'description' => 'attempt',
            'alias_group_id' => $foreignGroup->id,
        ])->assertStatus(422);
    }

    #[Test]
    public function alias_update_can_change_group()
    {
        $group = AliasGroup::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $this->user->id,
            'name' => 'Target',
        ]);
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $this->patchJson("/api/v1/aliases/{$alias->id}", [
            'alias_group_id' => $group->id,
        ])->assertSuccessful();

        $this->assertEquals($group->id, $alias->fresh()->alias_group_id);
    }
}
