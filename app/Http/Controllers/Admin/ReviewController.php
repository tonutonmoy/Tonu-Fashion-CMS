<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewRepositoryInterface $reviews,
        private ReviewService $reviewService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.reviews.index', [
            'reviews' => $this->reviews->paginateAdmin($request->all()),
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $this->reviewService->approve($id);

        return back()->with('success', 'Review approved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->reviewService->reject($id);

        return back()->with('success', 'Review rejected.');
    }
}
