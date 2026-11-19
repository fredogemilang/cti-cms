<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StringTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationApiController extends Controller
{
    /**
     * Get dictionary of string translations for a specified locale.
     *
     * GET /api/v1/translations/{locale}
     */
    public function index(Request $request, string $locale): JsonResponse
    {
        $availableLocales = available_locales();

        // Standardize BCP 47 locale validation
        if (! in_array($locale, $availableLocales, true)) {
            return response()->json([
                'status' => 'error',
                'message' => "Unsupported locale '{$locale}'.",
                'available_locales' => $availableLocales,
            ], 404);
        }

        $dictionary = StringTranslation::getDictionary($locale);

        return response()->json([
            'status' => 'success',
            'locale' => $locale,
            'count' => count($dictionary),
            'translations' => $dictionary,
        ]);
    }
}
