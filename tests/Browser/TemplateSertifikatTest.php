<?php

namespace Tests\Browser;

use App\Models\InfoOr;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Group;

class TemplateSertifikatTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $infoOr;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->infoOr = InfoOr::factory()->create();
    }

    /**
     * Test admin can successfully upload a certificate template.
     */
    #[Group('template-sertifikat-dusk')]
    public function testAdminCanUploadTemplateSuccessfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit(route('template.upload'))
                    ->assertSee('Upload Template Sertifikat');

            $browser->type('nama_template', 'Template Kelulusan 2024')
                    ->select('info_or_id', $this->infoOr->id)
                    ->attach('file_template', UploadedFile::fake()->create('my_template.html', 100, 'text/html'))
                    ->press('Upload') // Assuming button text is 'Upload'
                    ->assertPathIs(route('template.upload'))
                    ->assertSee('Template berhasil di-upload.');

            // Check if the new template is listed
            $browser->assertSee('Template Kelulusan 2024');
        });
    }

    /**
     * Test certificate template upload form validation.
     */
    #[Group('template-sertifikat-dusk')]
    public function testTemplateUploadFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit(route('template.upload'))
                    ->press('Upload')
                    ->assertPathIs(route('template.upload'))
                    ->assertSee('The nama template field is required.')
                    ->assertSee('The file template field is required.')
                    ->assertSee('The info or id field is required.');

            // Test wrong file type validation
            $browser->type('nama_template', 'Template Gagal')
                    ->select('info_or_id', $this->infoOr->id)
                    ->attach('file_template', UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'))
                    ->press('Upload')
                    ->assertPathIs(route('template.upload'))
                    ->assertSee('The file template field must be a file of type: html.');
        });
    }
}
