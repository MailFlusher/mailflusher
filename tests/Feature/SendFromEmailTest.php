<?php

namespace Tests\Feature;

use App\Mail\SendFromEmail;
use App\Models\Alias;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendFromEmailTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser('mrunknown', 'kaktus@mailflusher.com');
    }

    #[Test]
    public function it_can_send_email_from_alias_from_file()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Alias::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        $extension = 'contact=ebay.com';

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_send_from_alias.eml'),
                '--sender' => $this->user->email,
                '--recipient' => ['ebay+'.$extension.'@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [$extension],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(SendFromEmail::class, function ($mail) {
            return $mail->hasTo('contact@ebay.com');
        });
    }

    #[Test]
    public function it_can_send_from_alias_to_multiple_emails_from_file()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Alias::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        $extension1 = 'contact=ebay.com';
        $extension2 = 'support=ebay.com';

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_multiple_send_from.eml'),
                '--sender' => $this->user->email,
                '--recipient' => [
                    'ebay+'.$extension1.'@mrunknown.mailflusher.com',
                    'ebay+'.$extension2.'@mrunknown.mailflusher.com',
                ],
                '--local_part' => ['ebay', 'ebay'],
                '--extension' => [$extension1, $extension2],
                '--domain' => ['mrunknown.mailflusher.com', 'mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(SendFromEmail::class, function ($mail) {
            return $mail->hasTo('contact@ebay.com');
        });

        Mail::assertQueued(SendFromEmail::class, function ($mail) {
            return $mail->hasTo('support@ebay.com');
        });
    }

    #[Test]
    public function it_can_send_email_from_catch_all_alias_that_does_not_yet_exist()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $extension = 'contact=ebay.com';

        $this->assertDatabaseMissing('aliases', [
            'email' => 'ebay@mrunknown.mailflusher.com',
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_send_from_alias.eml'),
                '--sender' => $this->user->email,
                '--recipient' => ['ebay+'.$extension.'@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [$extension],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.mailflusher.com',
            'local_part' => 'ebay',
            'domain' => 'mrunknown.mailflusher.com',
            'emails_forwarded' => 0,
            'emails_blocked' => 0,
            'emails_replied' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        $this->assertEquals('Created automatically by catch-all', $this->user->aliases()->first()->description);

        Mail::assertQueued(SendFromEmail::class, function ($mail) {
            return $mail->hasTo('contact@ebay.com');
        });
    }
}
