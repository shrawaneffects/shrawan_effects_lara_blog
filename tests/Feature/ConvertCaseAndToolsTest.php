<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvertCaseAndToolsTest extends TestCase
{
    /**
     * Test that Convert Case page is publicly accessible and contains all UI components.
     */
    public function test_convert_case_page_loads_with_expected_ui_elements(): void
    {
        $response = $this->get('/services/convertcase');

        $response->assertStatus(200);
        $response->assertSee('Convert');
        $response->assertSee('Case');
        $response->assertSee('Type or paste your content here');
        $response->assertSee('Character Count:');
        $response->assertSee('Word Count:');
        $response->assertSee('Line Count:');
        $response->assertSee('Sentence case');
        $response->assertSee('lower case');
        $response->assertSee('UPPER CASE');
        $response->assertSee('Capitalized Case');
        $response->assertSee('aLtErNaTiNg cAsE');
        $response->assertSee('Title Case');
        $response->assertSee('InVeRsE CaSe');
        $response->assertSee('Buy me a');
    }

    /**
     * Test that Admin Tools route requires authentication.
     */
    public function test_admin_tools_requires_authentication(): void
    {
        $response = $this->get('/admin/tools');
        $response->assertRedirect('/login');
    }

    /**
     * Test that Admin can view Tools management.
     */
    public function test_admin_can_view_tools_index(): void
    {
        $admin = new User([
            'id' => 999,
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/tools');

        $response->assertStatus(200);
        $response->assertSee('Web Tools', false);
        $response->assertSee('Convert Case', false);
        $response->assertSee('Bulk Website IP Checker', false);
    }
}
