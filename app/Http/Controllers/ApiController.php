<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SalesRecord;
use App\Models\SalesRecordDetails;
use App\Models\StockRecord;
use App\Models\StockRecordDetails;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    use ResponseTrait;

    public function productCreatePreRequisit(): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'categories' => Category::query()->get(['id', 'name']),
                'brands' => Brand::query()->get(['id', 'name']),
            ];
            return response()->json([
                'status' => true,
                'message' => 'Data fetched successfully',
                'data' => $data
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => $e->getTrace()
            ], 500);
        }
    }

    public function productCreate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:products',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'quantity' => 'required|numeric',
                'price' => 'required|numeric',
                'usp' => 'nullable',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors()
                ], 500);
            }
            DB::beginTransaction();
            $attributes = $request->only(['name', 'category_id', 'brand_id', 'quantity', 'price', 'usp']);
            $data = Product::query()->create($attributes);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $data
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => $e->getTrace()
            ], 500);
        }
    }

    public function createStockRecord(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric',
                'products.*.sku' => 'required',
                'challan_no' => 'required|numeric',
                'department' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors()
                ], 500);
            }
            DB::beginTransaction();
            $attributes = $request->only(['challan_no', 'department']);
            $data = StockRecord::query()->create($attributes);
            foreach ($request->products as $product){
                StockRecordDetails::query()->create([
                    'stock_record_id' => $data->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'sku' => $product['sku']
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Stock record created successfully',
                'data' => $data
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => $e->getTrace()
            ], 500);
        }
    }

    public function createSalesRecord(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric',
                'products.*.sku' => 'required',
                'products.*.unit_price' => 'required|numeric',
                'remark' => 'nullable|string',
                'customer_info' => 'nullable|array',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors()
                ], 500);
            }
            DB::beginTransaction();
            $data = SalesRecord::query()->create([
                'invoice_no' => sprintf('INV-%s', time()),
                'customer_info' => $request->customer_info,
                'remark' => $request->remark,
            ]);
            $totalPrice = 0;
            foreach ($request->products as $product){
                SalesRecordDetails::query()->create([
                    'sales_record_id' => $data->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'sku' => $product['sku'],
                    'unit_price' => $product['unit_price'],
                    'total_price' => $product['unit_price'] * $product['quantity'],
                ]);
                $totalPrice += $product['unit_price'] * $product['quantity'];
            }
            $data->update([
                'total_amount' => $totalPrice
            ]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Sales record created successfully',
                'data' => []
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => $e->getTrace()
            ], 500);
        }
    }




}
