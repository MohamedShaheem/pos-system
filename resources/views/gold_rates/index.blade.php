@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
<section class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Gold / Silver Rates</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Gold / Silver Rates</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Gold / Silver Rates List</h3>
                    <a href="{{ route('gold_rates.createOrEdit') }}" class="btn btn-success btn-sm">Create New Rate</a>
                </div>
            </div>


            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="goldRateTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="gold-tab" data-toggle="tab" href="#gold" role="tab" aria-controls="gold" aria-selected="true">Gold</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="silver-tab" data-toggle="tab" href="#silver" role="tab" aria-controls="silver" aria-selected="false">Silver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="goldpcs-tab" data-toggle="tab" href="#goldpcs" role="tab" aria-controls="goldpcs" aria-selected="false">Gold pcs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="silverpcs-tab" data-toggle="tab" href="#silverpcs" role="tab" aria-controls="silverpcs" aria-selected="false">Silver pcs</a>
                    </li>
                </ul>

                <!-- Tab Panes -->
                <div class="tab-content mt-3" id="goldRateTabsContent">
                    @foreach (['gold', 'silver', 'goldpcs', 'silverpcs'] as $type)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $type }}" role="tabpanel" aria-labelledby="{{ $type }}-tab">
                            <table class="table table-bordered table-striped gold-rate-table">
                                <thead>
                                    <tr>
                                        <th>Carat</th>
                                        <th>Rate (per pawn)</th>
                                        <th>Rate (per gram)</th>
                                        <th>Percentage / Remarks</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $filtered = $goldRates->where('type', $type);
                                    @endphp

                                    @forelse ($filtered as $goldRate)
                                        <tr>
                                            <td>{{ $goldRate->name }}</td>
                                            <td>{{ $goldRate->rate_per_pawn ? number_format($goldRate->rate_per_pawn, 2) : '-' }}</td>
                                            <td>{{ number_format($goldRate->rate, 2) }}</td>
                                            <td>{{ $goldRate->percentage }}</td>
                                            <td>{{ $goldRate->updated_at->format('d-M-Y') }}</td>
                                            <td>
                                                <a href="{{ route('gold_rates.createOrEdit', $goldRate->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                {{-- <form action="{{ route('gold_rates.destroy', $goldRate->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this rate?')">Delete</button>
                                                </form> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No rates found for {{ ucfirst($type) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Initialize DataTable for each tab
    $('.gold-rate-table').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
    });
});
</script>
@endsection
