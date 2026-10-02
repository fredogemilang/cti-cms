<?php

namespace App\Console\Commands;

use App\Models\CustomPostType;
use Illuminate\Console\Command;

class UpdateCustomerSuccessMetaField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:update-cs-meta-field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Customer Success related_products meta field to support both Technology Alliance and Tech Products';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cpt = CustomPostType::where('slug', 'customer-success')->first();
        $alliance = CustomPostType::where('slug', 'technology-alliance')->first();
        $techProd = CustomPostType::where('slug', 'tech-products')->first();

        if (! $cpt || ! $alliance || ! $techProd) {
            $this->error('One or more required CPTs not found!');
            return Command::FAILURE;
        }

        $field = $cpt->metaFields()->where('name', 'related_products')->first();

        if (! $field) {
            $this->error('related_products field not found in Customer Success CPT!');
            return Command::FAILURE;
        }

        $options = $field->options ?? [];
        $options['target_cpt'] = ['technology-alliance', 'tech-products'];
        $options['target_post_type_ids'] = [$alliance->id, $techProd->id];
        unset($options['target_post_type_id']);

        $field->options = $options;
        $field->save();

        $this->info('SUCCESS: Updated related_products field options for Customer Success CPT!');
        return Command::SUCCESS;
    }
}
