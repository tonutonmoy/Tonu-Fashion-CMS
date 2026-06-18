<?php

namespace App\Repositories\Eloquent;

use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterRepositoryInterface;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function __construct(private NewsletterSubscriber $model) {}

    public function subscribe(string $email): NewsletterSubscriber
    {
        return $this->model->newQuery()->firstOrCreate(
            ['email' => strtolower($email)],
            ['status' => 'active']
        );
    }

    public function exists(string $email): bool
    {
        return $this->model->newQuery()->where('email', strtolower($email))->exists();
    }
}
