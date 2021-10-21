<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // dd($request);
        if (request()->ajax()) {
            $products = Product::leftjoin('product_variants as pv', 'pv.product_id', '=', 'products.id')
                ->leftjoin('product_variant_prices as pvc', 'pvc.product_id', 'products.id')
                ->select(
                    'products.id as id',
                    'title',
                    'description',
                    'sku',
                    'products.created_at',
                    'pv.variant as variant',
                    'price',
                    'stock',
                    'pv.id as variant_id'
                )
                ->groupBy('products.id');

                if(!empty($request->product_name)){
                    $products->where('products.title', 'LIKE', "%{$request->product_name}%");
                }

                if(!empty($variation_id)){
                    $products->where('pv.id',$variation_id);
                }

                if(!empty($request->price_from) || !empty($request->price_to)){
                    // $products->where('pvc.price', '>=', $request->price_from)->where('pvc.price', '<=', $request->price_to);
                    $products->whereBetween(DB::raw('pvc.price'), [$request->price_from, $request->price_to]);
                }

                if(!empty($request->date)){
                    $products->where('products.created_at',$request->date);
                }

            $datatable = DataTables::of($products)
                ->addIndexColumn()
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<a class="btn waves-effect waves-light btn-success btn-sm btn-circle ml-1 " title="Edit Blog Details" href="' . route('product.edit', $row->id) . '"><i class="fa fa-edit"></i></a>';
                        return $html;
                    }
                )

                ->editColumn('title', function ($row) {
                    $now = Carbon::now();
                    $diffHours = $row->created_at->diffForHumans($now);
                    $html = $row->title . ' ';
                    $html .= 'created_at:' . $diffHours;
                    return $html;
                })
                ->editColumn('description', function ($row) {
                    return $row->description;
                })
                ->editColumn('variant', function ($row) {
                    $html = '';
                    $variants = ProductVariant::where('product_id', $row->id)->orwhere('variant_id', $row->variant_id)->pluck('variant')->toArray();
                    $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">';
                    $html .= '<div class="row">';
                    foreach ($variants as $key => $variant) {
                        $html .= '<p>' . $variant . "/" . '</p>';
                    }
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-6">';
                    $html .= '<div class="row">';
                    $variant_prices = ProductVariantPrice::where('product_id', $row->id)->select('price', 'stock')->get();
                    foreach ($variant_prices as $key => $variant_price) {
                        $html .= '<p>' . '<strong>Price:</strong>' . $variant_price->price . '</p>';
                        $html .= '<p>' . '<strong>InStock:</strong>' . $variant_price->stock . '</p>';
                    }
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    return $html;
                });
            $rawColumns = ['action', 'title', 'description', 'variant'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        $variations=ProductVariant::select('variant','id')->get();

        return view('products.index')->with(compact('variations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
