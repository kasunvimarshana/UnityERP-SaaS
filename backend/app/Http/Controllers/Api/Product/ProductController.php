<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\BaseController;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * ProductController constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $products = $this->productService->getAll([], $perPage);

            return $this->paginatedResponse($products, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'type' => 'required|in:inventory,service,combo,bundle,digital',
                'category_id' => 'nullable|exists:product_categories,id',
                'description' => 'nullable|string',
                'buying_price' => 'nullable|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                'buying_unit_id' => 'nullable|exists:units_of_measure,id',
                'selling_unit_id' => 'nullable|exists:units_of_measure,id',
                'stock_unit_id' => 'nullable|exists:units_of_measure,id',
                'tax_rate_id' => 'nullable|exists:tax_rates,id',
                'track_inventory' => 'nullable|boolean',
                'track_serial' => 'nullable|boolean',
                'track_batch' => 'nullable|boolean',
                'has_expiry' => 'nullable|boolean',
                'min_stock_level' => 'nullable|numeric|min:0',
                'max_stock_level' => 'nullable|numeric|min:0',
                'reorder_level' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $product = $this->productService->create($validated);

            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getById($id);

            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve product: ' . $e->getMessage(), [], 404);
        }
    }

    /**
     * Update the specified product.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'sku' => 'sometimes|string|unique:products,sku,' . $id,
                'type' => 'sometimes|in:inventory,service,combo,bundle,digital',
                'category_id' => 'nullable|exists:product_categories,id',
                'description' => 'nullable|string',
                'buying_price' => 'nullable|numeric|min:0',
                'selling_price' => 'sometimes|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                'tax_rate_id' => 'nullable|exists:tax_rates,id',
                'track_inventory' => 'nullable|boolean',
                'min_stock_level' => 'nullable|numeric|min:0',
                'max_stock_level' => 'nullable|numeric|min:0',
                'reorder_level' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $product = $this->productService->update($id, $validated);

            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);

            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Search products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->input('query', '');
            $filters = $request->only(['category_id', 'type', 'is_active', 'min_price', 'max_price']);

            $products = $this->productService->search($query, $filters);

            return $this->successResponse($products, 'Products searched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to search products: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock products.
     *
     * @return JsonResponse
     */
    public function lowStock(): JsonResponse
    {
        try {
            $products = $this->productService->getLowStockProducts();

            return $this->successResponse($products, 'Low stock products retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve low stock products: ' . $e->getMessage());
        }
    }

    /**
     * Get out of stock products.
     *
     * @return JsonResponse
     */
    public function outOfStock(): JsonResponse
    {
        try {
            $products = $this->productService->getOutOfStockProducts();

            return $this->successResponse($products, 'Out of stock products retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve out of stock products: ' . $e->getMessage());
        }
    }

    /**
     * Calculate final price for a product.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function calculatePrice(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|numeric|min:1',
                'context' => 'nullable|array',
            ]);

            $priceDetails = $this->productService->calculateFinalPrice(
                $id,
                $validated['quantity'],
                $validated['context'] ?? []
            );

            return $this->successResponse($priceDetails, 'Price calculated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate price: ' . $e->getMessage());
        }
    }
}
