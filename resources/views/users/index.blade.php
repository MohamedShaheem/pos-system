@extends('layouts.admin')

@section('content')

<style>
    .staff-card-container {
        display: none;
    }

    .id-card {
        width: 85.60mm;
        height: 53.98mm;
        background: #ffffff;
        border: 3px solid #dc2626;
        border-radius: 12px;
        font-family: 'Arial', sans-serif;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .card-header {
        background: #ffffff;
        padding: 8px;
        border-bottom: 2px solid #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 20mm;
    }

    .company-logo {
        width: 150px;
        height: 150px;
        object-fit: contain;
    }

    .company-name {
        font-size: 16px;
        font-weight: bold;
        color: #dc2626;
        margin: 0;
        letter-spacing: 1px;
    }

    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        text-align: center;
    }

    .staff-name {
        font-size: 16px;
        font-weight: bold;
        color: #1f2937;
        margin: 0;
    }

    .staff-id {
        font-size: 16px;
        font-weight: bold;
        color: #dc2626;
        font-family: 'Courier New', monospace;
        margin: 0;
    }

    .barcode-image {
        width: 90px;
        height: 25px;
        object-fit: contain;
        margin-top: 5px;
    }

    /* Print Styles */
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .staff-card-container {
            display: block !important;
            width: 85.60mm !important;
            height: 53.98mm !important;
            margin: 0 !important;
            padding: 0 !important;
            page-break-after: always;
        }
        
        .id-card {
            border: 3px solid #dc2626;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }
</style>

<div class="container pb-3">
    <h1 class="mb-4">User Management</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Create New User</a>
    
    <table class="table table-bordered table-striped" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Barcode</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name ?? 'N/A' }}</td>
                <td>{{ $user->barcode }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" style="margin-right: 5px;">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    
                    <button class="btn btn-info btn-sm" style="margin-right: 5px;" onclick="printStaffCard('{{ $user->barcode }}')">
                        <i class="bi bi-card-heading"></i> Print ID Card
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    
    {{ $users->links() }}
</div>

<!-- Hidden staff ID cards for each user -->
@foreach ($users as $user)
    <div id="staff-card-{{ $user->barcode }}" class="staff-card-container">
        <div class="id-card">
            <!-- Header with Company Name -->
            <div class="card-header">
                <img src="{{ asset('media/logo.png') }}" alt="Logo" class="company-logo">
                <h1 class="company-name">JEWEL PLAZA</h1>
            </div>
            
            <!-- Main Content Area - Centered -->
            <div class="card-body">
                <div class="staff-name">Staff Name: {{ $user->name }}</div>
                <div class="staff-id">Employee ID: {{ $user->barcode }}</div>
                <img src="{{ route('users.barcode', $user->barcode) }}" 
                     alt="Staff Barcode {{ $user->barcode }}" 
                     class="barcode-image" />
            </div>
        </div>
    </div>
@endforeach

<script>
    function printStaffCard(userBarcode) {
        var cardElement = document.getElementById('staff-card-' + userBarcode);
        if (!cardElement) {
            alert('Staff card not found for barcode: ' + userBarcode);
            return;
        }
        
        var cardContent = cardElement.innerHTML;
        var printWindow = window.open('', '', 'height=400,width=600');
        
        printWindow.document.write('<html><head><title>Print Staff ID Card - ' + userBarcode + '</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { margin: 0; padding: 10mm; background: white; }');
        printWindow.document.write('@page { size: A4; margin: 10mm; }');
        printWindow.document.write('.id-card { width: 85.60mm; height: 53.98mm; background: #ffffff; border: 3px solid #dc2626; border-radius: 12px; font-family: Arial, sans-serif; display: flex; flex-direction: column; margin: 0 auto; overflow: hidden; }');
        printWindow.document.write('.card-header { background: #ffffff; padding: 8px; border-bottom: 2px solid #dc2626; display: flex; align-items: center; justify-content: center; gap: 10px; height: 20mm; }');
        printWindow.document.write('.company-logo { width: 70px; height: 70px; object-fit: contain; }');
        printWindow.document.write('.company-name { font-size: 16px; font-weight: bold; color: #dc2626; margin: 0; letter-spacing: 1px; }');
        printWindow.document.write('.card-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 10px; text-align: center; }');
        printWindow.document.write('.staff-name { font-size: 16px; font-weight: bold; color: #dc2626; font-family: "Courier New", monospace; margin: 0; }');
        printWindow.document.write('.staff-id { font-size: 16px; font-weight: bold; color: #dc2626; font-family: "Courier New", monospace; margin: 0; }');
        printWindow.document.write('.barcode-image { width: 90px; height: 25px; object-fit: contain; margin-top: 5px; }');
        printWindow.document.write('* { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body onload="window.print(); window.close();">');
        printWindow.document.write(cardContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
    }

    $(document).ready(function() {
        $('#users-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'desc']],
        });
    });
</script>
@endsection