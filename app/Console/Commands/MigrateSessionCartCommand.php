<?php

namespace App\Console\Commands;

use App\Services\CartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;

class MigrateSessionCartCommand extends Command
{
    protected $signature = 'cart:migrate-session';

    protected $description = 'Migrate legacy session cart items into the database shopping cart';

    public function handle(CartService $cartService): int
    {
        $sessionCart = Session::get('cart', []);

        if (empty($sessionCart)) {
            $this->info('No session cart items to migrate.');

            return self::SUCCESS;
        }

        $cartService->migrateSessionCart($sessionCart);
        Session::forget(['cart', 'discount', 'coupon_code']);

        $this->info('Migrated ' . count($sessionCart) . ' session cart item(s).');

        return self::SUCCESS;
    }
}
