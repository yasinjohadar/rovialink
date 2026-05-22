<?php

namespace App\Http\Controllers\Frontend\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RendersShopCatalog
{
    protected function wantsCatalogJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    protected function catalogJsonResponse(LengthAwarePaginator $products): JsonResponse
    {
        return response()->json([
            'success' => true,
            'total' => $products->total(),
            'html' => [
                'results' => view('frontend.partials.shop-results', compact('products'))->render(),
            ],
        ]);
    }

    protected function catalogRedirectResponse(string $url): JsonResponse
    {
        return response()->json([
            'success' => true,
            'redirect' => $url,
        ]);
    }
}
