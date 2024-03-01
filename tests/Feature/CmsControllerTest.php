<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * CmsControllerTest
 */
class CmsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method createAdmin
     *
     * @return object
     */
    public function createAdmin()
    {
        $data = [
            'name' => 'Admin',
            'email' => 'backend@mailinator.com',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
        ];
        return User::create($data);
    }
    /**
     * Method createCms
     *
     * @return void
     */
    public function createCms()
    {
        $pages = [
            [
                'id' => 1,
                'slug' => 'about-us',
                'page_title' => 'About Us',
                'page_content' => "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>",
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
            ],
            [
                'id' => 2,
                'slug' => 'terms-and-condition',
                'page_title' => 'Terms & Conditions',
                'page_content' => "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>",
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
            ],
            [
                'id' => 3,
                'slug' => 'privacy-policy',
                'page_title' => 'Privacy Policy',
                'page_content' => "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>",
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
            ],
        ];
        CmsPage::insert($pages);
    }
    /**
     * Method testEditCms
     *
     * @return void
     */
    public function testEditCms(): void
    {
        // Create cms in database
        $this->createCms();
        $cms = CmsPage::where('slug', 'terms-and-condition')->first();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.cms.edit', $cms->slug);
        $response = $this->actingAs($admin)->get($url);
        $response->assertViewIs('admin.cms.index');
        $response->assertViewHas('cms', $cms);
    }

    /**
     * Method testNotFindCmsWithEdit
     * cms not find with edit function
     * 
     * @return void
     */
    public function testNotFindCmsWithEdit(): void
    {
        // Create cms in database
        $this->createCms();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.cms.edit', 'test-slug');
        $response = $this->actingAs($admin)->get($url);
        $response->assertRedirect(route('admin.dashboard'));
    }


    /**
     * Method testHttpUpdateCms
     *
     * @return void
     */
    public function testHttpUpdateCms(): void
    {
        $this->createCms();
        $cms = CmsPage::where('slug', 'terms-and-condition')->first();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.cms.update', $cms->id);
        $data = [
            'page_content' =>
            "<p>Lorem Ipsum is simply dummy text.</p>",
            'page_title' => 'About'
        ];
        $response = $this->actingAs($admin)->ajaxPost($url, $data);
        $response->assertSee('Content updated successfully');
        $this->assertDatabaseHas(
            'cms_pages',
            [
                'page_title' => 'About',
                'page_content' => '<p>Lorem Ipsum is simply dummy text.</p>',
            ]
        );
    }
}
