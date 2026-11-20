<?php

namespace Plugins\PartnerRewardCatalog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Plugins\PartnerRewardCatalog\Support\CatalogStore;

class CatalogController extends Controller
{
    public function show(Request $request)
    {
        $locale = app()->getLocale();
        $isId = $locale === 'id';

        $config = CatalogStore::getConfig();
        $pdfUrl = CatalogStore::pdfUrl($config);

        $pageTitle = $isId ? $config['title_id'] : $config['title_en'];
        $pageSubtitle = $isId ? $config['subtitle_id'] : $config['subtitle_en'];

        $layout = view()->exists('csi::layouts.app') ? 'csi::layouts.app' : 'layouts.app';

        return view('partner-reward-catalog::public.show', [
            'layout' => $layout,
            'config' => $config,
            'pdfUrl' => $pdfUrl,
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'seoTitle' => $pageTitle.' | '.setting('site_name', 'Converion Solution Informatica'),
            'seoDescription' => $pageSubtitle,
        ]);
    }
}
