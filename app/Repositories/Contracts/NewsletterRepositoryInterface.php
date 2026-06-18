<?php

namespace App\Repositories\Contracts;

use App\Models\NewsletterSubscriber;

interface NewsletterRepositoryInterface
{
    public function subscribe(string $email): NewsletterSubscriber;

    public function exists(string $email): bool;
}
