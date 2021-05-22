<?php

namespace Tests\Feature;

use App\Repositories\Contracts\Categories as CategoriesContract;
use App\Repositories\Eloquent\Categories as CategoriesEloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PersonalAssistant;
use Tests\TestCase;

class CategoriesDatabaseTest extends TestCase
{
    use RefreshDatabase,
    PersonalAssistant;

    protected function setUp(): void
    {
        parent::setUp();
        app()->bind(CategoriesContract::class, CategoriesEloquent::class);
    }

    public function test_remove_linked_category()
    {
        $this->seedEntireDatabase();

        $categories_model = app()->make(CategoriesContract::class);
        $categories_model->removeCategory(5);
        $category = $categories_model->fetchSingleEntry(5);
        $this->assertNotEquals($category, null, 'This category should exist');

        // add an unlinked category...
        $category = $categories_model->createEntry([
            'label' => 'Can Delete This'
        ]);
        $all_categories = $categories_model->fetchAllEntries();
        $this->assertNotEquals($category, null, 'This category should exist');
        $this->assertEquals(count($all_categories), 6, 'There should be 6 categories here');

        // now remove it...
        $categories_model->removeCategory($category->id);
        $category = $categories_model->fetchSingleEntry($category->id);
        $this->assertEquals($category, null, 'This category should not exist');

        $all_categories = $categories_model->fetchAllEntries();
        $this->assertEquals(count($all_categories), 5, 'There should be 5 categories here');
    }
}
