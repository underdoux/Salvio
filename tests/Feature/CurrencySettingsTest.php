<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\DatabaseMigrations;
use Spatie\Permission\Models\Role;

class CurrencySettingsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Run custom migrations
        $this->runCustomMigrations();

        // Create admin role
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        // Set default currency settings
        Setting::create(['key' => 'currency_code', 'value' => 'IDR']);
        Setting::create(['key' => 'currency_symbol', 'value' => 'Rp']);
        Setting::create(['key' => 'currency_position', 'value' => 'before']);
        Setting::create(['key' => 'currency_symbols', 'value' => json_encode([
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'MYR' => 'RM',
            'JPY' => '¥',
            'CNY' => '¥',
            'KRW' => '₩'
        ])]);
    }

    public function test_currency_settings_page_requires_authentication()
    {
        $response = $this->get(route('settings.currency'));
        $response->assertRedirect(route('login'));
    }

    public function test_currency_settings_page_requires_admin_role()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.currency'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_currency_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('settings.currency'));
        $response->assertSuccessful();
        $response->assertSee('Currency Settings');
    }

    public function test_admin_can_update_currency_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('settings.update'), [
            'settings' => [
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_position' => 'before'
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('USD', Setting::where('key', 'currency_code')->first()->value);
        $this->assertEquals('$', Setting::where('key', 'currency_symbol')->first()->value);
    }

    public function test_currency_format_money_method_works_correctly()
    {
        // Test before position
        Setting::where('key', 'currency_position')->update(['value' => 'before']);
        $this->assertEquals('Rp 1.000.000', Setting::formatMoney(1000000));

        // Test after position
        Setting::where('key', 'currency_position')->update(['value' => 'after']);
        $this->assertEquals('1.000.000 Rp', Setting::formatMoney(1000000));
    }

    public function test_invalid_currency_settings_are_validated()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('settings.update'), [
            'settings' => [
                'currency_code' => 'INVALID',
                'currency_symbol' => '',
                'currency_position' => 'invalid'
            ]
        ]);

        $response->assertSessionHasErrors(['settings.currency_code', 'settings.currency_position']);
    }

    public function test_currency_symbols_are_mapped_correctly()
    {
        $symbols = json_decode(Setting::where('key', 'currency_symbols')->first()->value, true);

        $this->assertArrayHasKey('USD', $symbols);
        $this->assertEquals('$', $symbols['USD']);
        $this->assertArrayHasKey('EUR', $symbols);
        $this->assertEquals('€', $symbols['EUR']);
    }

    public function test_validate_currency_settings_command_works()
    {
        // Test with valid settings
        $this->artisan('settings:validate-currency')
            ->expectsOutput('✓ All currency settings are valid.')
            ->assertExitCode(0);

        // Test with invalid settings
        Setting::where('key', 'currency_position')->update(['value' => 'invalid']);

        $this->artisan('settings:validate-currency')
            ->expectsOutput('Found currency setting issues:')
            ->assertExitCode(1);

        // Test fix option
        $this->artisan('settings:validate-currency --fix')
            ->expectsOutput('Settings have been fixed successfully.')
            ->assertExitCode(0);

        // Verify settings were fixed
        $this->assertEquals('before', Setting::where('key', 'currency_position')->first()->value);
    }
}
