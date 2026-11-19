<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapStyleController extends Controller
{
    public function __invoke(): Response
    {
        $viewContent = view('sitemap.main-sitemap-xsl')->render();

        return response($viewContent, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400, s-maxage=604800',
        ]);
    }
}
