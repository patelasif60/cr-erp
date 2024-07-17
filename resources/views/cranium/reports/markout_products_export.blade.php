<table>
    <thead>
        <tr>
            <th>ETIN</th>
            <th>Product Listing Name</th>
            <th>Qty</th>
            <th>Reason</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        @if($result)
            @foreach($result as $row)
                <tr>
                    <td>{{ $row->ETIN }}</td>
                    <td>@if(isset($row->product->product_listing_name)){{ $row->product->product_listing_name }}@endif</td>
                    <td>{{ $row->qty }}</td>
                    <td>{{ $row->reason }}</td>
                    <td>{{ $row->address }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
