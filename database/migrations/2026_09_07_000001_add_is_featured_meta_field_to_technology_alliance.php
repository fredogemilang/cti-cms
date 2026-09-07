<?php

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance', 'products'])->get();

        foreach ($targetCpts as $cpt) {
            $metaField = MetaField::updateOrCreate(
                [
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $cpt->id,
                    'name' => 'is_featured',
                ],
                [
                    'label' => 'Featured Product',
                    'type' => 'switcher',
                    'description' => 'Show as featured product chip in Career Explore modal and prominent sections',
                    'default_value' => '0',
                    'order' => 1,
                    'is_required' => false,
                    'is_active' => true,
                    'field_group' => null,
                ]
            );

            // Populate the default 9 featured products for technology alliance
            $defaultFeaturedSlugs = [
                'akamai',
                'amazon-web-services',
                'dynatrace',
                'f5',
                'tidb',
                'hitachi-vantara',
                'zscaler',
                'nebula-cloud-console',
                'netgain-systems',
            ];

            $entries = CptEntry::where('post_type_id', $cpt->id)->get();
            foreach ($entries as $entry) {
                $meta = $entry->meta ?? [];
                $slug = strtolower($entry->slug);

                if (in_array($slug, $defaultFeaturedSlugs, true)) {
                    $meta['is_featured'] = true;
                } else {
                    $meta['is_featured'] = $meta['is_featured'] ?? false;
                }

                $entry->update(['meta' => $meta]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance', 'products'])->get();

        foreach ($targetCpts as $cpt) {
            MetaField::where('fieldable_type', CustomPostType::class)
                ->where('fieldable_id', $cpt->id)
                ->where('name', 'is_featured')
                ->delete();

            $entries = CptEntry::where('post_type_id', $cpt->id)->get();
            foreach ($entries as $entry) {
                $meta = $entry->meta ?? [];
                unset($meta['is_featured']);
                $entry->update(['meta' => $meta]);
            }
        }
    }
};
