@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header" id="product_form">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="product_name" id="product_name" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variation_id" id="variation_id" class="form-control">
                        <option value="all">Please Select</option>
                        @foreach ($variations as $variation)
                            <option value="{{ $variation->id }}">{{ $variation->variant }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From"
                            class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-responsive product-table">
                <table class="table table-striped table-bordered display ajax_view" id="products_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Variants</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        // $('#product_form #variation_id #product_form #product_name').change(function() {
        //     alert('Hello');
        //     products_table.ajax.reload();
        // });
        products_table=$('table#products_table').DataTable({
            dom: 'Blfrtip',
            language: {
                processing: "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading Data..."
            },
            processing: true,
            serverSide: true,
            url: '/product',
            aLengthMenu: [
                [25, 50, 100, 1000, -1],
                [25, 50, 100, 1000, "All"]
            ],
            buttons: ['excel', 'pdf', 'print'],
            columns: [{
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'variant',
                    name: 'variant'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    </script>
@endsection
