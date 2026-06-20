<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateVersion extends Model
{
    public $timestamps = false;

    protected $fillable = ['template_id', 'subject', 'body_html', 'edited_by', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
