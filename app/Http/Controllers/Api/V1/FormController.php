<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submit(Request $request, string $slug)
    {
        $form = Form::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $payload = $request->all();
        unset($payload['_token']);

        $entry = FormEntry::create([
            'form_id' => $form->id,
            'data' => $payload,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['ok' => true, 'entry_id' => $entry->id], 201);
    }
}
