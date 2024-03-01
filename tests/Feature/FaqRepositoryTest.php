<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Repositories\FaqRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * FaqRepositoryTest
 */
class FaqRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method createfaq
     *
     * @return void
     */
    public function testCreateFaq(): void
    {
        $faqRepository = new FaqRepository(new Faq);
        // create faq
        $faqRepository->createFaq(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        // assert faq
        $this->assertDatabaseHas(
            'faqs',
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
    }

    /**
     * Method testUpdateFaq
     *
     * @return void
     */
    public function testUpdateFaq(): void
    {
        $faqRepository = new FaqRepository(new Faq);
        // create faq
        $faq = $faqRepository->createFaq(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $faqRepository->updateFaq(
            [
                'id' => $faq->id,
                'question' => 'question two',
                'answer' => 'answer two',
                'status' => 1
            ]
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
     * Method testGetFaqList
     *
     * @return void
     */
    public function testGetFaqList(): void
    {
        $this->insertFaq();
        $faqRepository = new FaqRepository(new Faq);
        $faqs = $faqRepository->getFaq([]);
        $this->assertEquals(count($faqs), 4);
    }

    /**
     * Method testSearchFaq
     * test faq search
     * 
     * @return void
     */
    public function testSearchFaq(): void
    {
        $this->insertFaq();
        $faqRepository = new FaqRepository(new Faq);
        $faqs = $faqRepository->getFaq(['search' => 'four']);
        $this->assertEquals(count($faqs), 1);
    }

    /**
     * Method testGetFaqDetail
     * get faq detail with corrent id
     * 
     * @return void
     */
    public function testGetFaqDetail(): void
    {
        $faqRepository = new FaqRepository(new Faq);
        // create faq
        $faq = $faqRepository->createFaq(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $faqRepository = new FaqRepository(new Faq);
        $faqs = $faqRepository->getFaqDetail($faq->id);
        $this->assertEquals($faqs->question, 'question one');
    }

    /**
     * Method testGetFaqDetailWithWrongId
     * get faq detail with wrong id
     * 
     * @return void
     */
    public function testGetFaqDetailWithWrongId(): void
    {
        $faqRepository = new FaqRepository(new Faq);
        // create faq
        $faq = $faqRepository->createFaq(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $faqRepository = new FaqRepository(new Faq);
        $id = $faq->id + rand(2, 50);
        $faqs = $faqRepository->getFaqDetail($id);
        $this->assertNull($faqs);
    }


    /**
     * Method testDeleteFaq
     * delete faq with corrent id
     * 
     * @return void
     */
    public function testDeleteFaq(): void
    {
        $faqRepository = new FaqRepository(new Faq);
        // create faq
        $faq = $faqRepository->createFaq(
            [
                'question' => 'question one',
                'answer' => 'answer one',
                'status' => 0
            ]
        );
        $status = $faqRepository->deleteFaq($faq->id);
        $this->assertTrue($status);
    }
}
