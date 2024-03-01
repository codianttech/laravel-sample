<?php

namespace Tests\Feature\AdminController;

use App\Models\Faq;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * FaqControllerTest
 */
class FaqControllerTest extends TestCase
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
            'email_verified_at' => Carbon::now(),
            'otp' => 1234
        ];
        return User::create($data);
    }
    /**
     * Method insertFaq
     *
     * @return void
     */
    public function insertFaq()
    {
        // Create 4 user in database
        $data = [
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ],
            [
                'question' => 'question two',
                'answer' => 'answer two',
                'status' => 0
            ],
            [
                'question' => 'question three',
                'answer' => 'answer two',
                'status' => 1,
            ],
            [
                'question' => 'question four',
                'answer' => 'answer two',
                'status' => 0
            ]
        ];
        Faq::insert($data);
    }
    /**
     * Method testFaqListWithAjaxRequest
     *
     * @return void
     */
    public function testFaqListWithAjaxRequest(): void
    {
        // Create faq in database
        $this->insertFaq();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/faq';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 4)
                    ->has('links')
            );
    }

    /**
     * Method testLoadFaqListViewPage
     *
     * @return void
     */
    public function testLoadFaqListViewPage(): void
    {
        // Create faq in database
        $this->insertFaq();
        $faq = '';
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/faq';
        $response = $this->actingAs($admin)->get($url);
        $response->assertViewIs('admin.faq.index');
        $response->assertViewHas('faq', $faq);
    }

    /**
     * Method testShowViewPageToCreate
     *
     * @return void
     */
    public function testShowViewPageToCreate(): void
    {
        // Create faq in database
        $this->insertFaq();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/faq/create';
        $response = $this->actingAs($admin)->ajaxGet($url);
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('success')
                    ->has('data')
                    ->has('message')
            );
    }

    /**
     * Method testCreateFaqValidation
     *
     * @return void
     */
    public function testCreateFaqValidation(): void
    {
        $Faq = [
            'question' => '',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.store');
        $response = $this->actingAs($admin)->post($url, $Faq);
        $response->assertSessionHasErrors(
            [
                'question' => 'The question field is required.'
            ]
        );
        $response->assertStatus(302);
        // max limit validations
        $Faq = [
            'question' => 'Lorem Ipsum is simply dummy text of the 
            printing and typesetting industry. Lorem Ipsum has been the industry
             standard dummy text ever since the 1500s, 
             when an unknown printer took a galley of type
              and scrambled it to make a type specimen book.
               It has survived not only five centuries, but also 
               the leap into electronic typesetting, remaining essentially unchanged.
                It was popularised in the 1960s with the release of Letraset sheets 
                containing Lorem Ipsum passages, and more recently with desktop publishing 
                software like Aldus PageMaker including versions of Lorem Ipsum.',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.store');
        $response = $this->actingAs($admin)->post($url, $Faq);
        $response->assertSessionHasErrors(
            [
                'question' => 'The question must not be greater than 500 characters.'
            ]
        );
        $response->assertStatus(302);
        // test unique validations
        $FaqData = [
            'question' => 'Lorem Ipsum is simply dummy text',
            'answer' => 'answer two',
            'status' => 1
        ];
        $Faq = [
            'question' => 'Lorem Ipsum is simply dummy text',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.store');
        $this->actingAs($admin)->post($url, $Faq);
        $response = $this->actingAs($admin)->post($url, $FaqData);
        $response->assertSessionHasErrors(
            [
                'question' => 'The question has already been taken.'
            ]
        );
        $response->assertStatus(302);
        // test answer field required
        $Faq = [
            'question' => 'Lorem Ipsum is simply dummy text',
            'answer' => '',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.store');
        $response = $this->actingAs($admin)->post($url, $Faq);
        $response->assertSessionHasErrors(
            [
                'answer' => 'The answer field is required.'
            ]
        );
    }

    /**
     * Method testStoreFaq
     *
     * @return void
     */
    public function testStoreFaq(): void
    {
        // create faq

        $Faq = [
            'question' => 'question two',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.store');
        $response = $this->actingAs($admin)->post($url, $Faq);
        $response
            ->assertJson(
                [
                    'success' => true,
                    'data' => null,
                    'message' => 'FAQs added successfully.',
                ],
                200
            );
        // assert faq
        $this->assertDatabaseHas(
            'faqs',
            [
                'question' => 'question two',
                'answer' => 'answer two',
                'status' => 1
            ]
        );
    }

    /**
     * Method testShowViewPageToEdit
     *
     * @return void
     */
    public function testShowViewPageToEdit(): void
    {
        // create faq
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.edit', $faq->id);
        $response = $this->actingAs($admin)->ajaxGet($url);
        $response
            ->assertJson(
                [
                    'success' => true,
                    'message' => '',
                ],
                200
            );
    }

    /**
     * Method testUpdateFaqValidation
     *
     * @return void
     */
    public function testUpdateFaqValidation(): void
    {
        // create faq
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $updateFaq = [
            'id' => $faq->id,
            'question' => '',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $loginUser = $this->createAdmin();
        $url = route('admin.faq.update', $faq->id);
        $response = $this->actingAs($loginUser)->put($url, $updateFaq);
        $response->assertSessionHasErrors(
            [
                'question' => 'The question field is required.'
            ]
        );
        $response->assertStatus(302);
        // max limit validations
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $updateFaq = [
            'id' => $faq->id,
            'question' => 'Lorem Ipsum is simply dummy text of the 
            printing and typesetting industry. Lorem Ipsum has been the industry
             standard dummy text ever since the 1500s, 
             when an unknown printer took a galley of type
              and scrambled it to make a type specimen book.
               It has survived not only five centuries, but also 
               the leap into electronic typesetting, remaining essentially unchanged.
                It was popularised in the 1960s with the release of Letraset sheets 
                containing Lorem Ipsum passages, and more recently with desktop publishing 
                software like Aldus PageMaker including versions of Lorem Ipsum.',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.update', $faq->id);
        $response = $this->actingAs($admin)->put($url, $updateFaq);
        $response->assertSessionHasErrors(
            [
                'question' => 'The question must not be greater than 500 characters.'
            ]
        );
        $response->assertStatus(302);
        // test answer field required
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $FaqData = [
            'question' => 'Lorem Ipsum is simply dummy text',
            'answer' => '',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.update', $faq->id);
        $response = $this->actingAs($admin)->put($url, $FaqData);
        $response->assertSessionHasErrors(
            [
                'answer' => 'The answer field is required.'
            ]
        );
    }

    /**
     * Method testUpdateFaq
     *
     * @return void
     */
    public function testhttpUpdateFaq(): void
    {
        // create faq
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $updateFaq = [
            'id' => $faq->id,
            'question' => 'question two',
            'answer' => 'answer two',
            'status' => 1
        ];
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.update', $faq->id);
        $response = $this->actingAs($admin)->put($url, $updateFaq);
        $response
            ->assertJson(
                [
                    'success' => true,
                    'data' => null,
                    'message' => 'FAQs updated successfully.',
                ],
                200
            );
        // assert updated faq
        $this->assertDatabaseHas(
            'faqs',
            [
                'question' => 'question two',
                'answer' => 'answer two',
                'status' => 1
            ]
        );
    }

    /**
     * Method testHttpDeleteFaq
     *
     * @return void
     */
    public function testHttpDeleteFaq(): void
    {
        // create faq
        $faq = Faq::create(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        // Create admin in database
        $admin = $this->createAdmin();
        $url = route('admin.faq.destroy', $faq->id);
        $response = $this->actingAs($admin)->delete($url);
        $response
            ->assertJson(
                [
                    'success' => true,
                    'data' => null,
                    'message' => 'FAQs deleted successfully.',
                ],
                200
            );
        $this->assertSoftDeleted(
            'faqs',
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
    }
}
