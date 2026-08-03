<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CptEntryRelationship;
use App\Models\CustomPostType;
use App\Models\MetaField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CptRelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_can_create_relationship_between_cpt_entries()
    {
        // 1. Create Product CPT
        $productCpt = CustomPostType::create([
            'name' => 'product',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'slug' => 'products',
            'icon' => 'shopping_cart',
            'is_active' => true,
        ]);

        // 2. Create SubProduct CPT
        $subProductCpt = CustomPostType::create([
            'name' => 'sub_product',
            'singular_label' => 'Sub Product',
            'plural_label' => 'Sub Products',
            'slug' => 'sub-products',
            'icon' => 'category',
            'is_active' => true,
        ]);

        // 3. Create Relationship MetaField on Product CPT
        $metaField = MetaField::create([
            'fieldable_type' => CustomPostType::class,
            'fieldable_id' => $productCpt->id,
            'name' => 'sub_products',
            'label' => 'Related Sub Products',
            'type' => 'relationship',
            'options' => [
                'target_post_type_id' => $subProductCpt->id,
                'cardinality' => 'many_to_many',
            ],
            'is_active' => true,
        ]);

        // 4. Create Parent Product Entry
        $productEntry = CptEntry::create([
            'post_type_id' => $productCpt->id,
            'title' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'content' => 'Main Product Description',
            'author_id' => $this->user->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 5. Create Child SubProduct Entry
        $subProductEntry = CptEntry::create([
            'post_type_id' => $subProductCpt->id,
            'title' => 'iPhone 15 Pro 256GB Natural Titanium',
            'slug' => 'iphone-15-pro-256gb-natural-titanium',
            'content' => 'Variant specs and details',
            'author_id' => $this->user->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 6. Relate entries
        CptEntryRelationship::create([
            'parent_entry_id' => $productEntry->id,
            'child_entry_id' => $subProductEntry->id,
            'meta_field_id' => $metaField->id,
            'order' => 0,
        ]);

        // 7. Verify Eloquent Relationship
        $related = $productEntry->relatedEntries('sub_products')->get();

        $this->assertCount(1, $related);
        $this->assertEquals($subProductEntry->id, $related->first()->id);
        $this->assertEquals('iPhone 15 Pro 256GB Natural Titanium', $related->first()->title);

        // 8. Verify Reverse Parent Relationship
        $parent = $subProductEntry->parentRelatedEntries('sub_products')->get();
        $this->assertCount(1, $parent);
        $this->assertEquals($productEntry->id, $parent->first()->id);

        // 9. Verify URL when rewrite_url is false (default) -> Standalone URL
        $this->assertEquals(url('/sub-products/iphone-15-pro-256gb-natural-titanium'), $subProductEntry->getUrl());

        // 10. Enable rewrite_url on MetaField -> Nested URL
        $options = $metaField->options;
        $options['rewrite_url'] = true;
        $metaField->update(['options' => $options]);

        $this->assertStringContainsString('/products/iphone-15-pro/iphone-15-pro-256gb-natural-titanium', $subProductEntry->getUrl());

        // 11. Verify Schema JSON-LD output
        $schema = $productEntry->getSchemaJsonLd();
        $this->assertEquals('iPhone 15 Pro', $schema['name']);
        $this->assertArrayHasKey('isRelatedTo', $schema);
        $this->assertEquals('iPhone 15 Pro 256GB Natural Titanium', $schema['isRelatedTo'][0]['name']);
    }

    public function test_it_detaches_relationship_without_deleting_child_entry()
    {
        $productCpt = CustomPostType::create([
            'name' => 'product',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'slug' => 'products',
            'icon' => 'shopping_cart',
            'is_active' => true,
        ]);

        $metaField = MetaField::create([
            'fieldable_type' => CustomPostType::class,
            'fieldable_id' => $productCpt->id,
            'name' => 'sub_products',
            'label' => 'Related Sub Products',
            'type' => 'relationship',
            'is_active' => true,
        ]);

        $parent = CptEntry::create([
            'post_type_id' => $productCpt->id,
            'title' => 'Parent Product',
            'slug' => 'parent-product',
            'author_id' => $this->user->id,
            'status' => 'published',
        ]);

        $child = CptEntry::create([
            'post_type_id' => $productCpt->id,
            'title' => 'Sub Product',
            'slug' => 'sub-product',
            'author_id' => $this->user->id,
            'status' => 'published',
        ]);

        CptEntryRelationship::create([
            'parent_entry_id' => $parent->id,
            'child_entry_id' => $child->id,
            'meta_field_id' => $metaField->id,
        ]);

        // Delete parent (or forceDelete)
        $parent->forceDelete();

        // Check child entry still exists in database (detach requirement)
        $this->assertDatabaseHas('cpt_entries', ['id' => $child->id]);
        $this->assertDatabaseMissing('cpt_entry_relationships', ['parent_entry_id' => $parent->id]);
    }

    public function test_same_cpt_relationship_keeps_standalone_url()
    {
        $productCpt = CustomPostType::create([
            'name' => 'product',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'slug' => 'products',
            'is_active' => true,
        ]);

        $metaField = MetaField::create([
            'fieldable_type' => CustomPostType::class,
            'fieldable_id' => $productCpt->id,
            'name' => 'related_products',
            'label' => 'Related Products',
            'type' => 'relationship',
            'is_active' => true,
        ]);

        $product1 = CptEntry::create([
            'post_type_id' => $productCpt->id,
            'title' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'author_id' => $this->user->id,
            'status' => 'published',
        ]);

        $product2 = CptEntry::create([
            'post_type_id' => $productCpt->id,
            'title' => 'AirPods Pro',
            'slug' => 'airpods-pro',
            'author_id' => $this->user->id,
            'status' => 'published',
        ]);

        CptEntryRelationship::create([
            'parent_entry_id' => $product1->id,
            'child_entry_id' => $product2->id,
            'meta_field_id' => $metaField->id,
        ]);

        // Product2 (AirPods Pro) should KEEP its standalone URL /products/airpods-pro
        $this->assertEquals(url('/products/airpods-pro'), $product2->getUrl());
    }
}
