<?php

namespace App\Enums;

enum AdminPermission: string
{
    case Store = 'store';
    case Blog = 'blog';
    case Cms = 'cms';
    case Settings = 'settings';

    public function label(): string
    {
        return match ($this) {
            self::Store => 'Store Management',
            self::Blog => 'Blog Posts',
            self::Cms => 'Pages, Menus & Media',
            self::Settings => 'Theme, Marketing & Settings',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Store => 'Products, orders, customers, coupons, reviews',
            self::Blog => 'Create, edit and delete blog articles',
            self::Cms => 'Custom pages, navigation menus, media library',
            self::Settings => 'Website builder, theme, SEO and store settings',
        };
    }

    /** @return array<string, bool> */
    public static function defaultMapForRole(UserRole $role): array
    {
        $all = collect(self::cases())->mapWithKeys(fn (self $p) => [$p->value => false])->all();

        return match ($role) {
            UserRole::Admin => array_merge($all, [
                self::Store->value => true,
                self::Blog->value => true,
                self::Cms->value => true,
                self::Settings->value => true,
            ]),
            UserRole::Staff => array_merge($all, [
                self::Store->value => true,
            ]),
            default => $all,
        };
    }

    /** @param  array<string, mixed>  $input */
    public static function normalizeFromInput(array $input, UserRole $role): array
    {
        $defaults = self::defaultMapForRole($role);
        $normalized = [];

        foreach (self::cases() as $permission) {
            $key = $permission->value;
            if (array_key_exists($key, $input)) {
                $normalized[$key] = filter_var($input[$key], FILTER_VALIDATE_BOOLEAN);
            } else {
                $normalized[$key] = $defaults[$key] ?? false;
            }
        }

        return $normalized;
    }
}
