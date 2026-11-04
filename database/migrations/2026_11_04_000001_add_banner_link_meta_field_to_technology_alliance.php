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
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance', 'products', 'tech-products'])->get();

        foreach ($targetCpts as $cpt) {
            $ctaField = MetaField::where('fieldable_type', CustomPostType::class)
                ->where('fieldable_id', $cpt->id)
                ->where('name', 'banner_cta')
                ->first();

            $order = $ctaField ? ($ctaField->order + 1) : 15;

            // Shift fields after banner_cta to make room for banner_link
            MetaField::where('fieldable_type', CustomPostType::class)
                ->where('fieldable_id', $cpt->id)
                ->where('field_group', 'banner')
                ->where('order', '>=', $order)
                ->where('name', '!=', 'banner_link')
                ->increment('order');

            MetaField::updateOrCreate(
                [
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $cpt->id,
                    'name' => 'banner_link',
                ],
                [
                    'label' => 'Banner Link',
                    'type' => 'text',
                    'description' => 'Optional link for the banner button (e.g. /contact, https://example.com, #explore). Defaults to #explore if empty.',
                    'default_value' => null,
                    'order' => $order,
                    'is_required' => false,
                    'is_active' => true,
                    'field_group' => 'banner',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance', 'products', 'tech-products'])->get();

        foreach ($targetCpts as $cpt) {
            MetaField::where('fieldable_type', CustomPostType::class)
                ->where('fieldable_id', $cpt->id)
                ->where('name', 'banner_link')
                ->delete();

            $entries = CptEntry::where('post_type_id', $cpt->id)->get();
            foreach ($entries as $entry) {
                $meta = $entry->meta ?? [];
                if (isset($meta['banner_link'])) {
                    unset($meta['banner_link']);
                    $entry->update(['meta' => $meta]);
                }
            }
        }
    }
};
