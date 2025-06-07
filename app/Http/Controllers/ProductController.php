<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('products/Index');
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('product_name', 'like', "%{$search}%")->orWhere('product_code', 'like', "%{$search}%");
            })
            ->paginate(10);

        $data = $products->getCollection()->map(function ($product) {
            return [
                'product_id' => encrypt($product->product_id),
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'product_description' => $product->product_description,
                'product_price' => $product->product_price,
                'product_quantity' => $product->product_quantity,
                'product_image' => $product->product_image ? asset($product->product_image):null,
            ];
        });

        return response()->json([
            "result" => true,
            "data" => $data,
            "message" => 'Products retrieved successfully.',
            "pagination" => [
                "total" => $products->total(),
                "current_page" => $products->currentPage(),
                "last_page" => $products->lastPage(),
                "per_page" => $products->perPage(),
            ],
        ]);
    }


    public function store(Request $request)
    {
        if ($request->has('product_id')) {
            $product_id = decrypt($request->input('product_id'));
            $product = Product::findOrFail($product_id);
            $product->product_name = $request->input('product_name');
            $product->product_code = $request->input('product_code');
            $product->product_description = $request->input('product_description');
            $product->product_price = $request->input('product_price');
            $product->product_quantity = $request->input('product_quantity');

            if ($request->hasFile('product_image')) {
                // Delete old image if it exists
                if ($product->product_image && file_exists(public_path($product->product_image))) {
                    @unlink(public_path($product->product_image));
                }
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('images', $imageName, 'public');
                $product->product_image = 'storage/images/' . $imageName;
            }
            $product->save();

            return response()->json([
                'result' => true,
                'message' => 'Product updated successfully.',
            ]);
        } else {
            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'product_code' => 'required|string|max:255',
                'product_description' => 'nullable|string|max:255',
                'product_price' => 'required|numeric',
                'product_quantity' => 'required|integer',
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $product = new Product();
            $product->product_name = $validated['product_name'];
            $product->product_code = $validated['product_code'];
            $product->product_description = $validated['product_description'];
            $product->product_price = $validated['product_price'];
            $product->product_quantity = $validated['product_quantity'];

            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('images', $imageName, 'public');
                $product->product_image = 'storage/images/' . $imageName;
            }

            $product->save();

            return response()->json([
                'result' => true,
                'message' => 'Product created successfully.',
            ]);
        }
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $product = Product::where('product_id', $id)->firstOrFail();

        return response()->json([
            'result' => true,
            'message' => 'Product retrieved successfully.',
            'data' => [
                'product_id' => encrypt($product->product_id),
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'product_description' => $product->product_description,
                'product_price' => $product->product_price,
                'product_quantity' => $product->product_quantity,
                'product_image' => $product->product_image ? asset($product->product_image) : null,
            ],
        ]);
    }

    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        $product = Product::findOrFail($id);

        // Delete the product image if it exists
        if ($product->product_image && file_exists(public_path($product->product_image))) {
            @unlink(public_path($product->product_image));
        }

        $product->delete();

        return response()->json([
            'result' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
