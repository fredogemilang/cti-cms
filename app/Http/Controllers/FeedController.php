<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use App\Models\Page;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        $siteName = setting('site_name', config('app.name', 'CDT'));
        $siteTagline = setting('site_tagline', 'Central Data Technology');
        $siteUrl = url('/');

        // Fetch recent published posts / CPT entries
        $posts = CptEntry::with('postType')
            ->published()
            ->latest('published_at')
            ->take(30)
            ->get();

        $pages = Page::published()
            ->latest('published_at')
            ->take(10)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $xml .= '  <channel>'."\n";
        $xml .= '    <title>'.e($siteName).'</title>'."\n";
        $xml .= '    <link>'.e($siteUrl).'</link>'."\n";
        $xml .= '    <description>'.e($siteTagline).'</description>'."\n";
        $xml .= '    <language>'.app()->getLocale().'</language>'."\n";
        $xml .= '    <pubDate>'.date(DATE_RSS).'</pubDate>'."\n";
        $xml .= '    <atom:link href="'.e(url('/feed')).'" rel="self" type="application/rss+xml" />'."\n";

        foreach ($posts as $post) {
            $title = $post->title;
            $link = $post->getUrl();
            $description = $post->excerpt ?: substr(strip_tags($post->content ?? ''), 0, 300);
            $pubDate = $post->published_at ? $post->published_at->format(DATE_RSS) : date(DATE_RSS);

            $xml .= '    <item>'."\n";
            $xml .= '      <title>'.e($title).'</title>'."\n";
            $xml .= '      <link>'.e($link).'</link>'."\n";
            $xml .= '      <guid isPermaLink="true">'.e($link).'</guid>'."\n";
            $xml .= '      <pubDate>'.e($pubDate).'</pubDate>'."\n";
            $xml .= '      <description><![CDATA['.$description.']]></description>'."\n";
            $xml .= '    </item>'."\n";
        }

        foreach ($pages as $page) {
            if ($page->slug === 'home') {
                continue;
            }
            $title = $page->title;
            $link = $page->getUrl();
            $pubDate = $page->published_at ? $page->published_at->format(DATE_RSS) : date(DATE_RSS);

            $xml .= '    <item>'."\n";
            $xml .= '      <title>'.e($title).'</title>'."\n";
            $xml .= '      <link>'.e($link).'</link>'."\n";
            $xml .= '      <guid isPermaLink="true">'.e($link).'</guid>'."\n";
            $xml .= '      <pubDate>'.e($pubDate).'</pubDate>'."\n";
            $xml .= '      <description><![CDATA['.e($title).']]></description>'."\n";
            $xml .= '    </item>'."\n";
        }

        $xml .= '  </channel>'."\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
