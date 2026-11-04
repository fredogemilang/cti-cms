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
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance'])->get();

        foreach ($targetCpts as $cpt) {
            // 1. Ensure 'second_banner' metabox exists in CPT settings
            $settings = $cpt->settings ?? [];
            $metaBoxes = $settings['meta_boxes'] ?? [];
            $hasSecondBannerBox = collect($metaBoxes)->contains('id', 'second_banner');

            if (! $hasSecondBannerBox) {
                $bannerIndex = null;
                foreach ($metaBoxes as $idx => $box) {
                    if (($box['id'] ?? '') === 'banner') {
                        $bannerIndex = $idx;
                        break;
                    }
                }

                $newBox = [
                    'id' => 'second_banner',
                    'title' => 'Second Banner',
                    'context' => 'normal',
                ];

                if ($bannerIndex !== null) {
                    array_splice($metaBoxes, $bannerIndex + 1, 0, [$newBox]);
                } else {
                    $metaBoxes[] = $newBox;
                }

                $settings['meta_boxes'] = $metaBoxes;
                $cpt->update(['settings' => $settings]);
            }

            // 2. Register the 6 meta fields for the second banner
            $fields = [
                [
                    'name' => 'second_banner_badge',
                    'label' => 'Second Banner Badge',
                    'type' => 'text',
                    'description' => 'Optional badge text for the second banner (e.g. Limited Time Offer)',
                    'order' => 1,
                ],
                [
                    'name' => 'second_banner_headline',
                    'label' => 'Second Banner Headline',
                    'type' => 'text',
                    'description' => 'Headline for the second banner (supports HTML styling). Leave empty to hide this banner.',
                    'order' => 2,
                ],
                [
                    'name' => 'second_banner_description',
                    'label' => 'Second Banner Description',
                    'type' => 'textarea',
                    'description' => 'Optional description paragraph for the second banner.',
                    'order' => 3,
                ],
                [
                    'name' => 'second_banner_cta',
                    'label' => 'Second Banner CTA',
                    'type' => 'text',
                    'description' => 'Button text for the second banner (e.g. Start Now, Claim Now).',
                    'order' => 4,
                ],
                [
                    'name' => 'second_banner_link',
                    'label' => 'Second Banner Link',
                    'type' => 'text',
                    'description' => 'Optional button link (e.g. /contact, https://example.com, #explore). Defaults to #explore if empty.',
                    'order' => 5,
                ],
                [
                    'name' => 'second_banner_logo',
                    'label' => 'Second Banner Logo',
                    'type' => 'media',
                    'description' => 'Optional logo image for the second banner. Falls back to featured image if empty.',
                    'order' => 6,
                ],
            ];

            foreach ($fields as $fieldData) {
                MetaField::updateOrCreate(
                    [
                        'fieldable_type' => CustomPostType::class,
                        'fieldable_id' => $cpt->id,
                        'name' => $fieldData['name'],
                    ],
                    [
                        'label' => $fieldData['label'],
                        'type' => $fieldData['type'],
                        'description' => $fieldData['description'],
                        'default_value' => null,
                        'order' => $fieldData['order'],
                        'is_required' => false,
                        'is_active' => true,
                        'field_group' => 'second_banner',
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $targetCpts = CustomPostType::whereIn('slug', ['technology-alliance', 'technology_alliance'])->get();

        $fieldNames = [
            'second_banner_badge',
            'second_banner_headline',
            'second_banner_description',
            'second_banner_cta',
            'second_banner_link',
            'second_banner_logo',
        ];

        foreach ($targetCpts as $cpt) {
            MetaField::where('fieldable_type', CustomPostType::class)
                ->where('fieldable_id', $cpt->id)
                ->whereIn('name', $fieldNames)
                ->delete();

            $settings = $cpt->settings ?? [];
            if (! empty($settings['meta_boxes'])) {
                $settings['meta_boxes'] = array_values(
                    array_filter($settings['meta_boxes'], fn ($b) => ($b['id'] ?? '') !== 'second_banner')
                );
                $cpt->update(['settings' => $settings]);
            }

            $entries = CptEntry::where('post_type_id', $cpt->id)->get();
            foreach ($entries as $entry) {
                $meta = $entry->meta ?? [];
                $modified = false;
                foreach ($fieldNames as $fn) {
                    if (isset($meta[$fn])) {
                        unset($meta[$fn]);
                        $modified = true;
                    }
                }
                if ($modified) {
                    $entry->update(['meta' => $meta]);
                }
            }
        }
    }
};
