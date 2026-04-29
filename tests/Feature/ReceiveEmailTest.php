<?php

namespace Tests\Feature;

use App\Mail\ForwardEmail;
use App\Models\Alias;
use App\Models\AliasRecipient;
use App\Models\Domain;
use App\Models\Recipient;
use App\Models\Username;
use App\Notifications\Account\NearBandwidthLimit;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReceiveEmailTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser('mrunknown');
    }

    #[Test]
    public function it_can_forward_email_from_file()
    {
        Mail::fake();
        Notification::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        $this->assertEquals('Created automatically by catch-all', $this->user->aliases()->first()->description);

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_can_forward_email_from_file_with_capitals()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_caps.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['EBAY@MRUNKNOWN.MAILFLUSHER.COM'],
                '--local_part' => ['EBAY'],
                '--extension' => [''],
                '--domain' => ['MRUNKNOWN.MAILFLUSHER.COM'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_from_file_with_attachment()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_attachment.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['attachment@mrunknown.mailflusher.com'],
                '--local_part' => ['attachment'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'attachment@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'attachment',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_from_file_to_multiple_recipients()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_multiple_recipients.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com', 'amazon@mrunknown.mailflusher.com', 'paypal@mrunknown.flushed.com'],
                '--local_part' => ['ebay', 'amazon', 'paypal'],
                '--extension' => ['', '', ''],
                '--domain' => ['mrunknown.mailflusher.com', 'mrunknown.mailflusher.com', 'mrunknown.mailflusher.com'],
                '--size' => '1217',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertDatabaseHas('aliases', [
            'email' => 'amazon@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'amazon',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertDatabaseHas('aliases', [
            'email' => 'paypal@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'paypal',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(3, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_from_file_with_extension()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_extension.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay+a@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => ['2.3'],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '789',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_with_existing_alias()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Alias::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        $defaultRecipient = $this->user->defaultRecipient;

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '559',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        $this->assertCount(1, $this->user->aliases);

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($defaultRecipient) {
            return $mail->hasTo($defaultRecipient->email);
        });
    }

    #[Test]
    public function it_can_forward_email_with_uuid_generated_alias()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $uuid = '86064c92-da41-443e-a2bf-5a7b0247842f';

        config([
            'mailflusher.admin_username' => 'random',
        ]);

        Alias::factory()->create([
            'id' => $uuid,
            'user_id' => $this->user->id,
            'email' => $uuid.'@flushed.com',
            'local_part' => $uuid,
            'domain' => 'flushed.com',
        ]);

        $defaultRecipient = $this->user->defaultRecipient;

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_uuid.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => [$uuid.'@flushed.com'],
                '--local_part' => [$uuid],
                '--extension' => [''],
                '--domain' => ['flushed.com'],
                '--size' => '892',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'local_part' => $uuid,
            'domain' => 'flushed.com',
            'email' => $uuid.'@flushed.com',
            'emails_blocked' => 0,
        ]);

        $this->assertCount(1, $this->user->aliases);

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($defaultRecipient) {
            return $mail->hasTo($defaultRecipient->email);
        });
    }

    #[Test]
    public function it_can_forward_email_with_random_word_generated_alias()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $localPart = 'circus.waltz449';

        config([
            'mailflusher.admin_username' => 'random',
        ]);

        Alias::factory()->create([
            'user_id' => $this->user->id,
            'email' => $localPart.'@flushed.com',
            'local_part' => $localPart,
            'domain' => 'flushed.com',
        ]);

        $defaultRecipient = $this->user->defaultRecipient;

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_random_words.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => [$localPart.'@flushed.com'],
                '--local_part' => [$localPart],
                '--extension' => [''],
                '--domain' => ['flushed.com'],
                '--size' => '892',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'local_part' => $localPart,
            'domain' => 'flushed.com',
            'email' => $localPart.'@flushed.com',
            'emails_blocked' => 0,
        ]);

        $this->assertCount(1, $this->user->aliases);

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($defaultRecipient) {
            return $mail->hasTo($defaultRecipient->email);
        });
    }

    #[Test]
    public function it_can_forward_email_with_existing_alias_and_receipients()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        $recipient = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'one@example.com',
        ]);

        $recipient2 = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'two@example.com',
        ]);

        AliasRecipient::create([
            'alias' => $alias,
            'recipient' => $recipient,
        ]);

        AliasRecipient::create([
            'alias' => $alias,
            'recipient' => $recipient2,
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '444',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo('one@example.com');
        });

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo('two@example.com');
        });
    }

    #[Test]
    public function it_can_attach_recipients_to_new_alias_with_extension()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $recipient = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'one@example.com',
        ]);

        $recipient2 = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'two@example.com',
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_extension.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => ['2.3'],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '444',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'extension' => '2.3',
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($recipient) {
            return $mail->hasTo($recipient->email);
        });

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($recipient2) {
            return $mail->hasTo($recipient2->email);
        });
    }

    #[Test]
    public function it_can_not_attach_unverified_recipient_to_new_alias_with_extension()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'one@example.com',
            'email_verified_at' => null,
        ]);

        $verifiedRecipient = Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'two@example.com',
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_with_extension.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => ['2.3'],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '444',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        $alias = $this->user->aliases()->where('email', 'ebay@mrunknown.'.config('mailflusher.domain'))->first();

        $this->assertCount(1, $alias->recipients);

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($verifiedRecipient) {
            return $mail->hasTo($verifiedRecipient->email);
        });
    }

    #[Test]
    public function it_does_not_send_email_if_default_recipient_has_not_yet_been_verified()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->user->defaultRecipient->update(['email_verified_at' => null]);

        $this->assertNull($this->user->defaultRecipient->email_verified_at);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseMissing('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);

        Mail::assertNotQueued(ForwardEmail::class);
    }

    #[Test]
    public function it_can_unsubscribe_alias_by_emailing_list_unsubscribe()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Alias::factory()->create([
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        Recipient::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'kaktus@mailflusher.com',
        ]);

        $this->assertDatabaseHas('aliases', [
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'active' => true,
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_unsubscribe.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['8f36380f-df4e-4875-bb12-9c4448573712@unsubscribe.mailflusher.com'],
                '--local_part' => ['8f36380f-df4e-4875-bb12-9c4448573712'],
                '--extension' => [''],
                '--domain' => ['unsubscribe.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'active' => false,
        ]);

        Mail::assertNotQueued(ForwardEmail::class);
    }

    #[Test]
    public function it_cannot_unsubscribe_alias_if_not_a_verified_user_recipient()
    {
        Mail::fake();

        Mail::assertNothingSent();

        Alias::factory()->create([
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
        ]);

        $this->assertDatabaseHas('aliases', [
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'active' => true,
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_unsubscribe.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['8f36380f-df4e-4875-bb12-9c4448573712@unsubscribe.mailflusher.com'],
                '--local_part' => ['8f36380f-df4e-4875-bb12-9c4448573712'],
                '--extension' => [''],
                '--domain' => ['unsubscribe.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'id' => '8f36380f-df4e-4875-bb12-9c4448573712',
            'user_id' => $this->user->id,
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'active' => true,
        ]);

        Mail::assertNotQueued(ForwardEmail::class);
    }

    #[Test]
    public function it_can_forward_email_to_admin_username_for_root_domain()
    {
        Mail::fake();

        Mail::assertNothingSent();

        config(['mailflusher.admin_username' => 'mrunknown']);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_admin.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@flushed.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['flushed.com'],
                '--size' => '1346',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@flushed.com',
            'local_part' => 'ebay',
            'domain' => 'flushed.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_for_custom_domain()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $domain = Domain::factory()->create([
            'user_id' => $this->user->id,
            'domain' => 'example.com',
            'domain_verified_at' => now(),
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_custom_domain.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@example.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['example.com'],
                '--size' => '871',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'aliasable_id' => $domain->id,
            'aliasable_type' => 'App\Models\Domain',
            'email' => 'ebay@example.com',
            'local_part' => 'ebay',
            'domain' => 'example.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_for_custom_domain_with_verified_sending()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $domain = Domain::factory()->create([
            'user_id' => $this->user->id,
            'domain' => 'example.com',
            'domain_verified_at' => now(),
            'domain_sending_verified_at' => now(),
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_custom_domain.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@example.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['example.com'],
                '--size' => '871',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'aliasable_id' => $domain->id,
            'aliasable_type' => 'App\Models\Domain',
            'email' => 'ebay@example.com',
            'local_part' => 'ebay',
            'domain' => 'example.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_for_username()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $username = Username::factory()->create([
            'user_id' => $this->user->id,
            'username' => 'janedoe',
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_username.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@janedoe.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['janedoe.mailflusher.com'],
                '--size' => '638',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'aliasable_id' => $username->id,
            'aliasable_type' => 'App\Models\Username',
            'email' => 'ebay@janedoe.mailflusher.com',
            'local_part' => 'ebay',
            'domain' => 'janedoe.mailflusher.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_send_near_bandwidth_limit_notification()
    {
        Notification::fake();

        Notification::assertNothingSent();

        $this->user->update(['bandwidth' => 100943820]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        Notification::assertSentTo(
            $this->user,
            NearBandwidthLimit::class
        );
    }

    #[Test]
    public function it_does_not_send_near_bandwidth_limit_notification_more_than_once_per_day()
    {
        Notification::fake();

        Notification::assertNothingSent();

        Cache::put("user:{$this->user->id}:near-bandwidth", now()->toDateTimeString(), now()->addDay());

        $this->user->update(['bandwidth' => 9485760]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.'.config('mailflusher.domain'),
            'local_part' => 'ebay',
            'domain' => 'mrunknown.'.config('mailflusher.domain'),
            'emails_forwarded' => 1,
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'bandwidth' => '9486760',
        ]);

        Notification::assertNotSentTo(
            $this->user,
            NearBandwidthLimit::class
        );
    }

    #[Test]
    public function it_can_forward_email_from_file_for_all_domains()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_other_domain.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.flushed.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.flushed.com'],
                '--size' => '1000',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'email' => 'ebay@mrunknown.flushed.com',
            'local_part' => 'ebay',
            'domain' => 'mrunknown.flushed.com',
            'emails_blocked' => 0,
        ]);
        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    #[Test]
    public function it_can_forward_email_for_custom_domain_with_default_recipient()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $recipient = Recipient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $domain = Domain::factory()->create([
            'user_id' => $this->user->id,
            'default_recipient_id' => $recipient->id,
            'domain' => 'example.com',
            'domain_verified_at' => now(),
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_custom_domain.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@example.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['example.com'],
                '--size' => '871',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'aliasable_id' => $domain->id,
            'aliasable_type' => 'App\Models\Domain',
            'email' => 'ebay@example.com',
            'local_part' => 'ebay',
            'domain' => 'example.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($recipient) {
            return $mail->hasTo($recipient->email);
        });
    }

    #[Test]
    public function it_can_forward_email_for_username_with_default_recipient()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $recipient = Recipient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $username = Username::factory()->create([
            'user_id' => $this->user->id,
            'default_recipient_id' => $recipient->id,
            'username' => 'janedoe',
        ]);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email_username.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@janedoe.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['janedoe.mailflusher.com'],
                '--size' => '559',
            ]
        )->assertExitCode(0);

        $this->assertDatabaseHas('aliases', [
            'aliasable_id' => $username->id,
            'aliasable_type' => 'App\Models\Username',
            'email' => 'ebay@janedoe.mailflusher.com',
            'local_part' => 'ebay',
            'domain' => 'janedoe.mailflusher.com',
            'emails_blocked' => 0,
        ]);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) use ($recipient) {
            return $mail->hasTo($recipient->email);
        });
    }

    #[Test]
    public function it_can_forward_email_using_old_reply_to_and_from_headers()
    {
        Mail::fake();

        Mail::assertNothingSent();

        $this->user->update(['use_reply_to' => true]);

        $this->assertTrue($this->user->use_reply_to);

        $this->artisan(
            'mailflusher:receive-email',
            [
                'file' => base_path('tests/emails/email.eml'),
                '--sender' => 'kaktus@mailflusher.com',
                '--recipient' => ['ebay@mrunknown.mailflusher.com'],
                '--local_part' => ['ebay'],
                '--extension' => [''],
                '--domain' => ['mrunknown.mailflusher.com'],
                '--size' => '1346',
            ]
        )->assertExitCode(0);

        $this->assertEquals(1, $this->user->aliases()->count());

        Mail::assertQueued(ForwardEmail::class, function ($mail) {
            $mail->build();

            return $mail->hasTo($this->user->email) && $mail->hasFrom('ebay@mrunknown.mailflusher.com') && $mail->hasReplyTo('ebay+kaktus=mailflusher.com@mrunknown.mailflusher.com');
        });
    }
}
