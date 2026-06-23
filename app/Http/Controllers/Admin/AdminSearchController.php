<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\License;
use App\Models\Media;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $type = (string) $request->get('type', 'products');
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $items = match ($type) {
            'orders' => Order::query()
                ->where(function ($query) use ($q) {
                    $query->where('order_number', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%");
                })
                ->latest()
                ->limit(8)
                ->get(['id', 'order_number', 'customer_name', 'customer_phone'])
                ->map(fn (Order $order) => [
                    'label' => $order->order_number,
                    'meta' => $order->customer_name.' · '.$order->customer_phone,
                    'value' => $order->order_number,
                ]),
            'categories' => Category::query()
                ->where('name', 'like', "%{$q}%")
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name'])
                ->map(fn (Category $cat) => [
                    'label' => $cat->name,
                    'meta' => 'Category',
                    'value' => $cat->name,
                ]),
            'brands' => Brand::query()
                ->where('name', 'like', "%{$q}%")
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name'])
                ->map(fn (Brand $brand) => [
                    'label' => $brand->name,
                    'meta' => 'Brand',
                    'value' => $brand->name,
                ]),
            'customers' => User::query()
                ->where('role', UserRole::Customer)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                })
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'email', 'phone'])
                ->map(fn (User $user) => [
                    'label' => $user->name,
                    'meta' => $user->phone ?: $user->email,
                    'value' => $user->name,
                ]),
            'users' => User::query()
                ->whereIn('role', UserRole::assignableTeamRoles())
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'email', 'role'])
                ->map(fn (User $user) => [
                    'label' => $user->name,
                    'meta' => $user->email,
                    'value' => $user->name,
                ]),
            'pages' => CmsPage::query()
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                })
                ->orderBy('title')
                ->limit(8)
                ->get(['id', 'title', 'slug'])
                ->map(fn (CmsPage $page) => [
                    'label' => $page->title,
                    'meta' => $page->slug,
                    'value' => $page->title,
                ]),
            'media' => Media::query()
                ->where('filename', 'like', "%{$q}%")
                ->latest()
                ->limit(8)
                ->get(['id', 'filename', 'folder'])
                ->map(fn (Media $item) => [
                    'label' => $item->filename,
                    'meta' => $item->folder,
                    'value' => $item->filename,
                ]),
            'licenses' => License::query()
                ->where(function ($query) use ($q) {
                    $query->where('license_key', 'like', "%{$q}%")
                        ->orWhere('licensed_domain', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%");
                })
                ->latest()
                ->limit(8)
                ->get(['id', 'license_key', 'licensed_domain', 'customer_name'])
                ->map(fn (License $license) => [
                    'label' => $license->license_key,
                    'meta' => $license->licensed_domain ?: $license->customer_name,
                    'value' => $license->license_key,
                ]),
            default => Product::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                })
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'sku'])
                ->map(fn (Product $product) => [
                    'label' => $product->name,
                    'meta' => $product->sku ?: 'Product',
                    'value' => $product->name,
                ]),
        };

        return response()->json($items->values());
    }
}
