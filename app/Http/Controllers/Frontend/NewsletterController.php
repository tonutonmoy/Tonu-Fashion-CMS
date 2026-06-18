<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterSubscribeRequest;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function __construct(private NewsletterRepositoryInterface $newsletter) {}

    public function subscribe(NewsletterSubscribeRequest $request): RedirectResponse
    {
        $this->newsletter->subscribe($request->email);

        return back()->with('success', 'Thank you for subscribing!');
    }
}
