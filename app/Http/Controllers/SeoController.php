<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function __construct(private SeoService $seo) {}

    public function sitemap(): Response
    {
        $urls = $this->seo->sitemapUrls();
        $xml = view('seo.sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response($this->seo->robotsTxt(), 200)->header('Content-Type', 'text/plain');
    }
}
