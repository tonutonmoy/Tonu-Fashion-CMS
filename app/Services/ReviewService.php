<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function __construct(
        private ReviewRepositoryInterface $reviews
    ) {}

    public function submit(int $productId, int $userId, array $data): Review
    {
        return $this->reviews->create([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $data['order_id'] ?? null,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'is_approved' => false,
        ]);
    }

    public function approve(int $reviewId): void
    {
        DB::transaction(function () use ($reviewId) {
            $review = $this->reviews->update($reviewId, ['is_approved' => true]);
            $this->recalculateProductRating($review->product_id);
        });
    }

    public function reject(int $reviewId): void
    {
        $review = $this->reviews->find($reviewId);
        $productId = $review->product_id;
        $this->reviews->delete($reviewId);
        $this->recalculateProductRating($productId);
    }

    private function recalculateProductRating(int $productId): void
    {
        $reviews = Review::query()
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->get(['rating']);

        Product::query()->whereKey($productId)->update([
            'avg_rating' => round((float) $reviews->avg('rating'), 2),
            'review_count' => $reviews->count(),
        ]);
    }
}
