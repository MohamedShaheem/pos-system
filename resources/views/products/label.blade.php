<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label - {{ $product->product_no }}</title>
    <style>
        @page { size: 2.2in 1in; margin: 0; }
        body { margin: 0; padding: 0; background: white; font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    @php
        $wastage = $product->wastage_weight ?? 0;
        $stone = $product->stone_weight ?? 0;
        $making = $product->making_charges ?? 0;
        $product_type = $product->product_type;
        $type = $product->type;
        $netWeight = $product->weight ?? 0;
        $goldRateType = $product->goldRate?->type ?? null;
        
        $goldRate = $product->goldRate?->rate ?? 0;
        $wastageValue = $wastage * $goldRate;
        $adjustedMaking = $making + $wastageValue;
    @endphp               
        <div id="label-{{ $product->product_no }}" class="barcode-container">
            <div style="display: flex; width: 2.2in; height: 1in; background: #fff; overflow: hidden;">
                <!-- Main label area -->
                <div id="label-{{ $product->product_no }}" class="barcode-container">
                <div style="width: 2.2in; height: 1in; background: #fff; overflow: hidden; position: relative; font-family: Arial, sans-serif;">
                    <!-- Barcode -->
                    <div style="position: absolute; top: 8px; left: 50px; width: 150px; display: flex; flex-direction: column; align-items: center;">
                        <img src="{{ route('products.barcode', $product->product_no) }}" 
                            alt="Barcode {{ $product->product_no }}" 
                            style="width: 100%; height: 25px; object-fit: contain;" />
                        <p style="margin: 0px 0 0; font-size: 8px; text-align: center; font-weight: 800;">{{ $product->product_no }} Jewel Plaza</p>
                    </div>

                    <!-- Product info -->
                    @if($product_type == 'gold')
                        <div style="position: absolute; bottom: 10px; left: 85px; width: 100px; font-family: Arial, sans-serif;">
                            <div style="font-size: 5pt; font-weight: 600; text-align: left; line-height: 1.1; margin-bottom: 2px; text-transform: uppercase;">
                                {{ $product->goldRate->name ?? '' }} GOLD {{ $product->supplier->short_code ?? '' }} <br> {{ $product->name }}
                            </div>

                            <div style="font-size: 6pt; text-align: left; display: flex; gap: 7px; font-weight: 600; margin-left: -6px;" >
                               @if( $goldRateType === 'gold') 
                                    @if ($wastage > 0)
                                        <span>{{ number_format($wastage, 3) }}</span>
                                    @endif

                                    @if ($stone > 0)
                                        <span>{{ number_format($stone, 3) }}</span>
                                    @endif
                                    
                                    @if ($making > 0)
                                        <span>{{ number_format($making, 2) }}</span>
                                    @endif
                                @endif
                                    
                                <span>{{ number_format($product->weight, 3) }}</span>

                                @if ( $goldRateType === 'goldpcs' )
                                    <span>{{ number_format($product->goldRate->rate, 2) }}</span>
                                @endif
                               
                            </div>
                        </div>
                    @else
                        <div style="position: absolute; bottom: 16px; left: 85px; width: 100px; font-family: Arial, sans-serif;">
                            <div style="font-size: 6pt; font-weight: 600; text-align: left; line-height: 1.1; margin-bottom: 2px; text-transform: uppercase;">
                                Silver {{ $product->supplier->short_code ?? '' }} <br> {{ $product->name }}
                            </div>

                            <div style="font-size: 6pt; text-align: left; display: flex; gap: 4px; font-weight: 600; margin-left: -6px;" >
                                @if ($making > 0)
                                    <span>{{ number_format($product->making_charges, 2) }}</span>
                                @endif
                                
                                    <span>{{ number_format($product->weight, 3) }}</span>
                                    
                                    <span>{{ number_format($product->goldRate->rate, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
                
                <!-- Adhesive Free Tail -->
                <div style="width: 1.1in; height: 1in; display: flex; align-items: center; justify-content: center;">
                </div>
            </div>
        </div>

    <script>
    window.print();
    window.onafterprint = () => window.close();
    </script>

</body>
</html>