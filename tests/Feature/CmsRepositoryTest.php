<?php

namespace Tests\Feature;

use App\Exceptions\CustomException;
use App\Models\CmsPage;
use App\Repositories\CmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * CmsRepositoryTest
 */
class CmsRepositoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Method testGetCmsDetails
     * get cms detail from given slug
     * 
     * @return void
     */
    public function testGetCmsDetails(): void
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
        $cmsPage =  new CmsRepository(new CmsPage);
        $cmsData = $cmsPage->getCmsDetails('about-us');
        $this->assertEquals('about-us', $cmsData->slug);
    }

    /**
     * Method testGetCmsDetailsNotFind
     * find cms detail with given invalid slug
     * 
     * @return void
     */
    public function testGetCmsDetailsNotFind(): void
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
        $cmsPage =  new CmsRepository(new CmsPage);
        $cmsData = $cmsPage->getCmsDetails('About Us');
        $this->assertNull($cmsData);
    }

    /**
     * Method testUpdateCms
     * test cms updated correctly
     * 
     * @return void
     */
    public function testUpdateCms(): void
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
        ];
        CmsPage::insert($pages);
        $cmsPage =  new CmsRepository(new CmsPage);
        $data = [
            'id' => 1,
            'page_content' =>
            "<p>Lorem Ipsum is simply dummy text.</p>", 'page_title' => 'About'
        ];
        $cmsPage->updateCms($data);
        $this->assertDatabaseHas(
            'cms_pages',
            [
                'page_title' => 'About',
                'page_content' => '<p>Lorem Ipsum is simply dummy text.</p>',
            ]
        );
    }
    /**
     * Method testCmsNotUpdate
     * test cms not correctly updated
     * 
     * @return void
     */
    public function testCmsNotUpdate(): void
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
        ];
        CmsPage::insert($pages);
        $cmsPage =  new CmsRepository(new CmsPage);
        $data = [
            'id' => 1,
            'page_content' =>
            "<p>Lorem Ipsum is simply dummy text.</p>", 'page_title' => 'About'
        ];
        $cmsPage->updateCms($data);
        $this->assertDatabaseMissing(
            'cms_pages',
            [
                'page_title' => 'About Us',
                'page_content' => "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>",
            ]
        );
    }
}
