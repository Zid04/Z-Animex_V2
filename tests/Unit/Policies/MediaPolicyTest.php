<?php

namespace Tests\Unit\Policies;

use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;
use App\Policies\MediaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MediaPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new MediaPolicy();
    }

    // ── view ─────────────────────────────────────────────────────────────────

    public function test_view_allows_approved_public_media_for_any_user(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['approved' => true, 'is_public' => true]);

        $this->assertTrue($this->policy->view($user, $media));
    }

    public function test_view_denies_unapproved_public_media_for_other_user(): void
    {
        $user  = User::factory()->create();
        $owner = User::factory()->create();
        $media = Media::factory()->create([
            'user_id'   => $owner->id,
            'approved'  => false,
            'is_public' => true,
        ]);

        $this->assertFalse($this->policy->view($user, $media));
    }

    public function test_view_always_allows_the_owner(): void
    {
        $owner = User::factory()->create();
        $media = Media::factory()->create([
            'user_id'   => $owner->id,
            'approved'  => false,
            'is_public' => false,
        ]);

        $this->assertTrue($this->policy->view($owner, $media));
    }

    public function test_view_allows_user_with_watchlist_entry(): void
    {
        $user  = User::factory()->create();
        $owner = User::factory()->create();
        $media = Media::factory()->create([
            'user_id'   => $owner->id,
            'approved'  => false,
            'is_public' => false,
        ]);

        UserMedia::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        $this->assertTrue($this->policy->view($user, $media));
    }

    public function test_view_denies_unapproved_private_media_for_stranger(): void
    {
        $user  = User::factory()->create();
        $owner = User::factory()->create();
        $media = Media::factory()->create([
            'user_id'   => $owner->id,
            'approved'  => false,
            'is_public' => false,
        ]);

        $this->assertFalse($this->policy->view($user, $media));
    }

    // ── update ───────────────────────────────────────────────────────────────

    public function test_update_allows_owner(): void
    {
        $owner = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($this->policy->update($owner, $media));
    }

    public function test_update_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($this->policy->update($other, $media));
    }

    // ── delete ───────────────────────────────────────────────────────────────

    public function test_delete_allows_owner(): void
    {
        $owner = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($this->policy->delete($owner, $media));
    }

    public function test_delete_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($this->policy->delete($other, $media));
    }

    // ── create ───────────────────────────────────────────────────────────────

    public function test_create_always_allows_any_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($this->policy->create($user));
    }
}
