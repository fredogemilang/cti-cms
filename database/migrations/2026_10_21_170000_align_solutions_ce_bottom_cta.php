<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Page;
use App\Models\PageBlock;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::where('slug', 'solutions/customer-engagement')->first();
        if ($page) {
            $title = PageBlock::where('page_id', $page->id)->where('name', 'bottom_cta_title')->first();
            if ($title) {
                $title->value = 'Ready to improve every customer conversation?';
                $tr = $title->translations ?? [];
                $tr['id'] = ['value' => 'Siap meningkatkan kualitas percakapan dengan pelanggan?'];
                $title->translations = $tr;
                $title->save();
            }

            $subtitle = PageBlock::where('page_id', $page->id)->where('name', 'bottom_cta_subtitle')->first();
            if ($subtitle) {
                $subtitle->value = "Tell us about your team's needs and we'll show you how Kouventa fits.";
                $tr = $subtitle->translations ?? [];
                $tr['id'] = ['value' => 'Ceritakan kebutuhan tim Anda, dan kami tunjukkan bagaimana Kouventa bisa membantu.'];
                $subtitle->translations = $tr;
                $subtitle->save();
            }
        }

        $key = \App\Models\StringTranslationKey::where('group', 'kouventa')->where('key', 'Answer')->first();
        if ($key) {
            \App\Models\StringTranslation::updateOrCreate(
                ['translation_key_id' => $key->id, 'locale' => 'id'],
                ['value' => 'Tanya Jawab']
            );
        }
    }

    public function down(): void
    {
    }
};
