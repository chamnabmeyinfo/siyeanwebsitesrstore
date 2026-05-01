<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_index_displays_empty_state(): void
    {
        $response = $this->get(route('pages.index'));

        $response->assertOk();
        $response->assertSee(__('No pages yet.'));
    }

    public function test_can_create_page_and_redirects_to_edit(): void
    {
        $response = $this->post(route('pages.store'), [
            'title' => 'About Us',
            'slug' => 'about-us',
            'body' => '<p>Hello world.</p>',
            'is_published' => '1',
            'sort_order' => 10,
        ]);

        $response->assertRedirect();

        $page = Page::query()->where('slug', 'about-us')->first();
        $this->assertNotNull($page);
        $this->assertSame('About Us', $page->title);
        $this->assertTrue($page->is_published);
        $this->assertSame(10, $page->sort_order);

        $response->assertRedirect(route('pages.edit', $page));
    }

    public function test_slug_auto_generated_from_title_when_missing(): void
    {
        $response = $this->post(route('pages.store'), [
            'title' => 'Contact Page',
            'is_published' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'slug' => 'contact-page',
        ]);
    }

    public function test_can_update_page(): void
    {
        $page = Page::factory()->create([
            'title' => 'Original',
            'slug' => 'original',
        ]);

        $response = $this->put(route('pages.update', $page), [
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('pages.edit', $page));

        $page->refresh();
        $this->assertSame('Updated Title', $page->title);
        $this->assertSame('updated-title', $page->slug);
    }

    public function test_update_without_publish_checkbox_unpublishes_page(): void
    {
        $page = Page::factory()->create([
            'title' => 'Story',
            'slug' => 'story',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->put(route('pages.update', $page), [
            'title' => 'Story',
            'slug' => 'story',
            'excerpt' => null,
            'body' => '<p>Hi</p>',
            'sort_order' => 0,
            'published_at' => null,
            'meta_title' => null,
            'meta_description' => null,
        ]);

        $page->refresh();
        $this->assertFalse($page->is_published);
    }

    public function test_can_delete_page(): void
    {
        $page = Page::factory()->create();

        $response = $this->delete(route('pages.destroy', $page));

        $response->assertRedirect(route('pages.index'));
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_public_page_returns_404_when_unpublished(): void
    {
        $page = Page::factory()->draft()->create([
            'slug' => 'secret',
        ]);

        $this->get(route('pages.display', $page))->assertNotFound();
    }

    public function test_public_page_is_visible_when_published(): void
    {
        $page = Page::factory()->create([
            'slug' => 'hello',
            'title' => 'Hello',
            'body' => '<p>Content here.</p>',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('pages.display', $page));

        $response->assertOk();
        $response->assertSee('Hello', false);
        $response->assertSee('Content here.', false);
    }

    public function test_public_page_hidden_when_publish_date_in_future(): void
    {
        $page = Page::factory()->create([
            'slug' => 'future',
            'is_published' => true,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('pages.display', $page))->assertNotFound();
    }

    public function test_admin_show_preview_loads_unpublished_page(): void
    {
        $page = Page::factory()->draft()->create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
        ]);

        $response = $this->get(route('pages.show', $page));

        $response->assertOk();
        $response->assertSee('Draft Page', false);
    }
}
