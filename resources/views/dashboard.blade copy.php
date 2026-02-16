@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)
@section('content')
<style>
  :root {
    --primary-color: #1d344d;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-bg: #f8f9fa;
    --border-color: #dee2e6;
    --text-color: #495057;
  }


  .making-charges-input-editable {
    background-color: #fff3cd !important;
    border: 2px solid var(--warning-color) !important;
    cursor: text !important;
}

.making-charges-input-editable:focus {
    background-color: #ffffff !important;
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25) !important;
}

/* subtotal css */


  .sub-total-container {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: flex-end;
}

.edit-subtotal-btn {
    cursor: pointer;
    color: #6c757d;
    font-size: 14px;
    transition: all 0.2s ease;
    padding: 4px;
    border-radius: 3px;
}

.edit-subtotal-btn:hover {
    color: var(--primary-color);
    background-color: rgba(29, 52, 77, 0.1);
    transform: scale(1.1);
}

.edit-subtotal-input {
    width: 160px;
    font-size: 16px;
    font-weight: 800;
    color: var(--success-color);
    text-align: right;
    padding: 8px 12px;
    background-color: #fff3cd;
    border: 2px solid var(--warning-color);
    border-radius: 4px;
}

.edit-subtotal-input:focus {
    background-color: #ffffff;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25);
    outline: none;
}

.manual-edit-badge {
    font-size: 10px;
    background-color: #17a2b8;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 4px;
}

.edit-actions .btn {
    padding: 5px 5px;
    font-size: 10px;
    line-height: 1;
    border-radius: 3px;
    margin-left: 2px;
}

.edit-actions .btn i {
    font-size: 9px;
}

.edit-actions .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.edit-actions .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.edit-actions .btn:hover {
    opacity: 0.85;
}


  /* Gold Rate Warning Modal Styles */
  #goldRateWarningModal .modal-header {
      background: linear-gradient(45deg, #ffc107, #ff9800);
      color: white;
      border-radius: 8px 8px 0 0;
      border-bottom: none;
  }

  #goldRateWarningModal .modal-content {
      border-radius: 8px;
      border: none;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  #goldRateWarningModal .fa-coins {
      animation: bounce 2s infinite;
  }

  @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
  }

  #goldRateWarningModal .alert-warning {
      border-radius: 10px;
      border-left: 4px solid #ff9800;
  }

  .container{
    max-width: 1300px !important;
    width: 100%;
    
  }
  .pos-container {
    max-width: 1300px;
    width: 100%;
    margin: 0 auto;
    padding: 20px;
  }

  .pos-card {
    width: 100%;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .btn {
    border-radius: 4px;
    padding: 12px 20px;
    font-weight: 600;
    font-size: 16px;
  }

  .btn-xs {
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 600;
  }

  .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
  }

  .btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
  }

  .btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #212529;
  }

  .btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
  }

  .btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
  }

  .product-table {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .table thead th {
    background-color: var(--light-bg);
    border-bottom: 2px solid var(--border-color);
    font-weight: 700;
    padding: 12px 12px;
    font-size: 14px;
    color: var(--primary-color);
    text-align: center;
  }

  .table tbody td {
    padding: 16px 12px;
    vertical-align: middle;
    font-size: 16px;
    border-bottom: 1px solid #f1f3f5;
  }

  .table tfoot th {
    background-color: var(--light-bg);
    font-weight: 700;
    padding: 18px 12px;
    font-size: 14px;
  }

  /* Enhanced styling for important fields */
  .product-name {
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    color: var(--primary-color);
    line-height: 1.4;
  }

.weight-input {
  font-size: 18px;
  font-weight: 700;
  background-color: #fff3cd;
  border: 2px solid var(--warning-color);
  text-align: center;
  padding: 12px 16px;
  width: 120px;
}

.weight-input:focus {
  background-color: #ffffff;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25);
}

.weight-input[readonly] {
  text-align: end;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  color: #495057;
  cursor: not-allowed;
  width: 160px;
}

.weight-input[readonly]:focus {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  box-shadow: none;
}

  .gold-rate-display {
    font-size: 16px;
    font-weight: 700;
    color: #856404;
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 8px 8px;
    border-radius: 4px;
    text-align: center;
  }

  .sub-total {
    width: 160px;
    font-size: 16px;
    font-weight: 800;
    color: var(--success-color);
    text-align: right;
    padding: 8px 12px;
    background-color: #f8fff9;
    border-radius: 4px;
    border: 1px solid #d4edda;
  }

  .weight-remaining {
    width: 50px;
    font-size: 10px;
    color: #6c757d;
    font-weight: 600;
    margin-top: 4px;
  }

  .weight-remaining span{
    font-size: 13px !important;
    color: #0084f8;
  }

.customer-info {
  background: var(--light-bg);
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 5px;
  font-size: 14px;
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.customer-info h6 {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 15px;
  border-bottom: 2px solid var(--primary-color);
  padding-bottom: 8px;
}

/* Customer Advance Balance Display */
#customerAdvanceBalance {
  font-size: 16px;
  font-weight: 800;
  color: var(--success-color);
  background-color: #f7f7f7;
  padding: 4px 12px;
  border-radius: 4px;
  border: 1px solid #d4edda;
  display: inline-block;
  min-width: 120px;
  text-align: center;
}

#customerGoldAdvanceBalance {
  font-size: 16px;
  font-weight: 800;
  color: var(--success-color);
  background-color: #f7f7f7;
  padding: 4px 12px;
  border-radius: 4px;
  border: 1px solid #d4edda;
  display: inline-block;
  min-width: 50px;
  text-align: center;
}


/* Advance Buttons */
/* .customer-info .btn-group .btn {
  font-size: 14px;
  font-weight: 600;
  padding: 8px 10px;
  border-radius: 4px;
  margin-right: 8px;
} */
/* 
.customer-info .btn-info {
  background-color: #17a2b8;
  border-color: #17a2b8;
  color: white;
}

.customer-info .btn-success {
  background-color: var(--success-color);
  border-color: var(--success-color);
  color: white;
} */

.used-advance-section {
  background-color: #f8fff9;
  padding: 12px;
  border-radius: 6px;
  border: 1px solid #d4edda;
  margin-top: 15px;
}

.used-advance-section strong {
  color: var(--success-color);
  font-size: 16px;
}

#used_advance_display {
  background-color: var(--success-color);
  color: white;
  font-size: 18px;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 4px;
}

  .total-section {
    display: flex;
    justify-content: flex-end;
    max-width: 100%;
    width: 100%;
    background: var(--light-bg);
    padding: 10px;
    border-radius: 8px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .total-section .row {
    margin-bottom: 8px;
    font-size: 15px;
    font-weight: 600;
  }

  .total-section .row:last-child {
    margin-bottom: 0;
  }

  .total-section strong {
    font-weight: 700;
    font-size: 16px;
  }

  .action-buttons {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 18px;
  }

  .action-buttons .btn {
    width: 200px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 700;
  }

  .modal-header {
  background: var(--primary-color);
  color: white;
  border-radius: 8px 8px 0 0;
}

.modal-content {
  border-radius: 8px;
  border: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}


.modal-footer .btn {
  font-size: 16px;
  font-weight: 600;
  padding: 12px 20px;
  border-radius: 4px;
}

  .form-label {
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-color);
    font-size: 16px;
  }

  .badge {
    font-size: 16px;
    font-weight: 700;
    padding: 10px 16px;
    border-radius: 4px;
  }

  .badge-secondary {
    background-color: #6c757d;
    color: white;
  }

  .badge-primary {
    background-color: var(--primary-color);
    color: white;
  }

  .badge-success {
    background-color: var(--success-color);
    color: white;
  }

  .badge-warning {
    background-color: var(--warning-color);
    color: #212529;
  }

  /* Enhanced input styling for numeric values */
  /* .form-control[type="number"] {
    font-size: 18px;
    font-weight: 600;
    text-align: right;
    padding: 12px 16px;
  } */

  .wastage-input, .stone-input {
     width: 100px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
  }

  .making-charges-input {
    width: 100px;
    font-size: 16px;
    font-weight: 600;
    text-align: right;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
  }

  .discount-input {
    width: 120px;
    font-size: 16px;
    font-weight: 600;
    text-align: right;
    background-color: #fff;
    border: 2px solid #17a2b8;
  }

  .discount-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25);
  }

  .qty-input {
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    background-color: #e7f3ff;
    border: 2px solid #007bff;
  }

  /* Row numbering enhancement */
  .table tbody td:nth-child(2) strong {
    font-size: 18px;
    font-weight: 800;
    color: var(--primary-color);
    background-color: #f8f9fa;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
  }

  /* Cash payment input styling */
#advance {
  font-size: 18px;
  font-weight: 700;
  text-align: right;
  background-color: #f8fff9;
  border: 2px solid var(--success-color);
  padding: 12px 16px;
  border-radius: 4px;
}

#advance:focus {
  background-color: #ffffff;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25);
  outline: none;
}

  @media (max-width: 768px) {
    .action-buttons {
      flex-direction: column;
    }

    .pos-container {
      padding: 15px;
    }

    .table thead th {
      font-size: 14px;
      padding: 12px 8px;
    }

    .table tbody td {
      font-size: 14px;
      padding: 12px 8px;
    }

    .product-name {
      font-size: 16px;
    }

    .weight-input {
      font-size: 16px;
    }

    .sub-total {
      font-size: 16px;
    }

    .badge {
      font-size: 14px;
      padding: 8px 12px;
    }

    .btn {
      font-size: 14px;
      padding: 10px 16px;
    }
  }

  @media (max-width: 576px) {
    .table thead th {
      font-size: 12px;
      padding: 10px 6px;
    }

    .table tbody td {
      font-size: 12px;
      padding: 10px 6px;
    }

    .product-name {
      font-size: 14px;
    }

    .weight-input {
      font-size: 14px;
    }

    .sub-total {
      font-size: 14px;
    }

    .form-control {
      font-size: 14px;
    }

    .form-control-sm {
      font-size: 13px;
    }
  }

  .reservation-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 10px;
    font-size: 15px;
    font-weight: 500;
  }

  .reservation-item:last-child {
    margin-bottom: 0;
  }

  .reservation-status {
    font-weight: 700;
    text-transform: capitalize;
    font-size: 16px;
  }

  .reservation-status.pending {
    color: #ffc107;
  }

  .reservation-status.partial {
    color: #17a2b8;
  }

  .reservation-status.paid {
    color: #28a745;
  }

  .reservation-status.cancelled {
    color: #dc3545;
  }

  .reservation-list::-webkit-scrollbar {
    width: 6px;
  }

  .reservation-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  .reservation-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
  }

  .reservation-list::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  /* Alert styling for weight-based products */
  .weight-alert {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 12px 16px;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
  }

  /* Enhanced button styling for remove advance */
 #removeAdvanceBtn {
  background: none;
  border: none;
  color: var(--danger-color);
  font-size: 18px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
  cursor: pointer;
}

  #removeAdvanceBtn:hover {
  background-color: rgba(220, 53, 69, 0.1);
  transform: scale(1.1);
}
  /* Table hover effects */
  .table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
  }

  /* Delete row button enhancement */
  .delete_row {
    font-size: 16px;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .delete_row:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
  }

  @media (max-width: 768px) {
  .customer-info {
    padding: 15px;
    font-size: 14px;
  }
  
  .customer-info h6 {
    font-size: 16px;
  }
  
  #customerAdvanceBalance {
    font-size: 16px;
    padding: 6px 10px;
  }
  
  .customer-info .btn-group .btn {
    font-size: 13px;
    padding: 8px 12px;
  }
  
  #advance {
    font-size: 16px;
    padding: 10px 14px;
  }
  
  #used_advance_display {
    font-size: 16px;
    padding: 6px 12px;
  }
}

@media (max-width: 576px) {
  .customer-info {
    padding: 12px;
    font-size: 13px;
  }
  
  .customer-info h6 {
    font-size: 15px;
  }
  
  #customerAdvanceBalance {
    font-size: 14px;
    padding: 5px 8px;
  }
  
  .customer-info .btn-group .btn {
    font-size: 12px;
    padding: 6px 10px;
  }
  
  #advance {
    font-size: 14px;
    padding: 8px 12px;
  }
}

/* Add this to your existing CSS */
.negative-balance {
    color: white;
    background-color: #dc3545 !important; 
    border-color: #c82333 !important;
}

.positive-balance {
    color: #212529;
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
}

.change-balance {
    color: white;
    background-color: #dc3545 !important;
    border-color: #c82333 !important;
}
/* Add to your CSS section */
/* Style specifically for the Gold Exchange section */
#goldExchangeSection {
    background-color: #f8fff9;
    border: 1px solid #d4edda;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

#exchangeGoldValue {
    font-weight: bold;
    background-color: #fff8e3 !important; /* Bootstrap warning bg */
}

#goldExchangeCheckbox {
    transform: scale(1.2);
    margin-right: 6px;
}


.gold-exchange-row {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
}

.gold-exchange-row:first-child {
    background-color: #fff;
    border-color: #007bff;
}


.add-row-btn{
    height: 40px;  
    width: 80px;  
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-exchange-row, .remove-exchange-row {
    height: 28px;       /* Smaller height */
    width: 28px;        /* Smaller width */
    padding: 5px;
    font-size: 12px;    /* Smaller icon size */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px; /* Slight rounding */
}

.add-exchange-row i, .remove-exchange-row i {
    font-size: 12px;
}

#totalExchangeValue {
    font-size: 16px;
    color: #000000;
}

/* Add this CSS to your stylesheet */
#barcodeInputModal .modal-content {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border: none;
}

#barcodeInputModal .modal-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border-radius: 15px 15px 0 0;
    border-bottom: none;
}

#barcodeInputModal .modal-header .close {
    color: white;
    opacity: 0.8;
}

#barcodeInputModal .modal-header .close:hover {
    opacity: 1;
}

#barcodeInputModal .modal-body {
    padding: 2rem;
    background-color: #f8f9fa;
}

#barcodeInputModal .form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    font-size: 16px;
    transition: all 0.3s ease;
}

#barcodeInputModal .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-2px);
}

#barcodeInputModal .alert-info {
    border-radius: 10px;
    border-left: 4px solid #17a2b8;
    background-color: #d1ecf1;
    animation: slideIn 0.3s ease;
}

#barcodeInputModal .modal-footer {
    border-top: none;
    padding: 1rem 2rem 2rem;
    background-color: #f8f9fa;
    border-radius: 0 0 15px 15px;
}

#barcodeInputModal .btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#barcodeInputModal .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scanning animation effect */
.scanning-effect {
    position: relative;
    overflow: hidden;
}

.scanning-effect::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 123, 255, 0.3), transparent);
    animation: scan 2s infinite;
}

@keyframes scan {
    0% { left: -100%; }
    100% { left: 100%; }
}

.payment-detail-row {
    display: none;
    margin-top: 10px;
}

.payment-detail-row.active {
    display: flex;
}

.payment-detail-input {
      font-size: 18px;
  font-weight: 700;
  text-align: right;
  background-color: #f8fff9;
  border: 2px solid var(--success-color);
  padding: 12px 16px;
  border-radius: 4px;
}

.payment-detail-input:focus {
  background-color: #ffffff;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(29, 52, 77, 0.25);
  outline: none;
}

.payment-detail-label {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 5px;
}
/* Editable Gold Amount Input */
#usedGoldAmount {
    transition: all 0.3s ease;
}

#usedGoldAmount:focus {
    background-color: #ffffff;
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    transform: scale(1.02);
}

#usedGoldAmount:hover:not(:focus) {
    border-color: #ff9800;
}
  /* Bootstrap 4 Select2 fix */
.select2-container--default .select2-selection--single {
    height: 38px; /* Bootstrap 4 input height */
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    display: flex;
    align-items: center;
    background-color: #fff;
}

/* Text inside select */
.select2-container--default
.select2-selection--single
.select2-selection__rendered {
    line-height: 24px;
    padding-left: 0;
    color: #495057;
}

/* Arrow alignment */
.select2-container--default
.select2-selection--single
.select2-selection__arrow {
    height: 100%;
    top: 0;
    right: 8px;
}


/* Focus state (Bootstrap 4 style) */
.select2-container--default.select2-container--focus
.select2-selection--single {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
}

</style>
<section class="content">
<div class="container">
<div class="pos-container">
  <div class="pos-card">
    <form action="{{ route('pos_orders.store') }}" method="POST" id="posOrderForm" >
      @csrf

      <!-- Exchange gold Checkbox -->
      <div class="row mb-3">
          <div class="col-md-12 text-right">
              <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="goldExchangeCheckbox">
                  <label class="form-check-label" for="goldExchangeCheckbox">Gold Exchange (Old Gold To New Gold)</label>
              </div>
          </div>
      </div>

      <!-- Customer & Product Selection -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="product_no" class="form-label">Product Code / Scan</label>
          {{-- <input type="text" class="form-control" id="product_no" name="product_no" placeholder="Enter Product No / Scan Barcode"
          /> --}}

          <input type="text" 
                class="form-control" 
                id="product_no" 
                name="product_no" 
                placeholder="Scan Barcode"
                autocomplete="off"
                autocapitalize="off" 
                autocorrect="off"
                spellcheck="false"
          />
        </div>
        <div class="col-md-6">
          <label for="customer" class="form-label">Customer</label>
          <div class="customer_select_div">
              <select name="customer_id" class="form-control" id="customer" required >
                  @foreach ($customers as $customer)
                      <option value="{{ $customer->id }}" {{ $customer->id == 1 ? 'selected' : '' }}>
                          {{ $customer->name }} - {{$customer->tel}}
                      </option>
                  @endforeach
              </select>
          </div>

          <input
            type="text"
            class="form-control mt-2"
            id="manual_customer"
            name="manual_customer"
            placeholder="Enter New Customer Name"
            style="display: none"
          />
        </div>
      </div>


      <!-- Customer Advance & Reserve Section -->
      <div class="row mb-3" id="customerInfosection" style="display: none;">
        <div class="col-md-6">
          <div class="customer-info">
            <h6 class="mb-2">Customer Advance</h6>
            <div class="row pl-3">
              <div class="mb-2">
                <strong
                  >A/D Balance:
                  <span id="customerAdvanceBalance" style="margin-left: 5px;">Rs 0.00</span>
                <span id="customerAdvanceBalanceSpinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                </strong>
              </div>
              <div class="mb-2 ml-4">
                <strong
                  >A/D Gold:
                  <span id="customerGoldAdvanceBalance" style="margin-left: 5px;">0 g</span>
                <span id="customerGoldAdvanceBalanceSpinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                </strong>
              </div>
            </div>
           
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-info" id="useAdvanceBtn">
                Use Advance
              </button>
              <button
                type="button"
                class="btn btn-success ml-1"
                data-toggle="modal"
                data-target="#addAdvanceModal"
              >
                A/D Cash
              </button>

               <button
                type="button"
                class="btn btn-warning ml-1"
                data-toggle="modal"
                data-target="#addGoldAdvanceModal"
              >
                A/D Gold
              </button>

              <button
                type="button"
                class="btn btn-warning ml-1"
                data-toggle="modal"
                data-target="#addCashGoldAdvanceModal"
              >
                A/D (Cash and Gold)
              </button>
            </div>
          </div>
        </div>
       <!-- Updated Product Reservation Section -->
        <div class="col-md-6">
        <div class="customer-info">
            <h6 class="mb-2">Product Reservation</h6>
            <p class="mb-2 text-muted small">
            Reserve selected products for customer
            </p>
            
            <!-- Pending Reservations Info -->
            <div class="reservation-info mb-3" id="reservationInfo" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-bold text-primary">Pending Reservations:</span>
                <span class="badge badge-info" id="pendingReservationsCount">0</span>
            </div>
            
            <!-- Reservations List -->
            <div id="reservationsList" class="reservation-list" style="max-height: 200px; overflow-y: auto;">
                <!-- Reservations will be populated here -->
            </div>
            
            <!-- View All Link -->
            <div class="text-center mt-2">
                <a href="{{route('reservation.index')}}" class="btn btn-sm btn-outline-primary" id="viewAllReservations">
                View All Reservations
                </a>
            </div>
            </div>

            <!-- Reserve Button -->
            <button
            type="button"
            class="btn btn-warning btn-sm"
            data-toggle="modal"
            data-target="#reserveProductModal"
            >
            Reservation
            </button>
        </div>
        </div>
      </div>

      <!-- Hidden Tax Rate Section -->
      <div class="form-group row col-3" style="display: none">
        <label for="tax_rate" class="col-12" style="font-size: 0.8rem"
          >Tax Rate</label
        >
        <input
          type="text"
          readonly
          class="form-control form-control-sm col-10"
          id="tax_rate_x"
          name="tax_rate_x"
          value="{{ $taxRate->rate }}%"
          placeholder="8.14%"
        />
        <input
          type="hidden"
          readonly
          class="form-control form-control-sm col-8"
          id="tax_rate"
          name="tax_rate"
          value=""
          placeholder="8.14%"
        />
        <input
          type="hidden"
          readonly
          class="form-control form-control-sm col-8"
          id="tax_rate_v"
          name="tax_rate_v"
          value="{{ $taxRate->rate }}"
          placeholder="8.14%"
        />
      </div>

      <!-- Products Table -->
      <div class="product-table">
        <table id="products" class="table table-sm mb-0">
          <thead>
            <tr>
              <th width="80">Action</th>
              <th>No</th>
              <th>Product Name</th>
              <th>Net Weight</th>
              <th>Wastage Weight</th>
              <th>Stone Weight</th>
              <th>Gold Rate</th>
              <th style="display: none">Qty</th>
              <th>Making Charges</th>
              <th>Discount</th>
              <th>Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <!-- Product rows will be added here dynamically -->
          </tbody>
        </table>
      </div>

     <!-- Total Section aligned to right -->
<div class="total-section d-flex justify-content-end">
  <div class="w-100" style="max-width: 400px">
    {{-- <div class="row">
      <div class="col-6">
        <strong>Sub Total:</strong>
      </div>
      <div class="col-6 text-right">
        <span class="badge badge-secondary" id="sub_total_amount">Rs 0.00</span>
        <input type="hidden" id="main_sub_total" name="main_sub_total" value="0.00">
      </div>
    </div> --}}
    
    <!-- Discount Section -->
    <input type="hidden" name="total_discount" id="total_discount" value="0.00">

    <div class="row mt-2">
      <div class="col-6">
        <strong>Payment Method:</strong>
      </div>
      <div class="col-6 text-right">
        <select class="form-control" name="payment_method" id="payment_method" style="font-size: 13px;">
          <option value="cash">Cash</option>
          <option value="chq">Chq</option>
          <option value="bank_transfer">Bank Transfer</option>
          <option value="card">Card</option>
        </select>
      </div>
    </div>
    
    <div class="row mt-2">
      <div class="col-6">
        <strong>Total Amount:</strong>
      </div>
      <div class="col-6 text-right">
        <span class="badge badge-primary" id="total_amount" style="font-size: 15px;">Rs 0.00</span>
      </div>
    </div>
    
    <div class="row mt-2" style="display: none">
      <div class="col-6">Inclusive Tax:</div>
      <div class="col-6 text-right" id="inclusive_tax">0.00</div>
    </div>


    <!-- Gold Exchange Section (hidden by default) -->
    <div id="goldExchangeSection" class="advance-row mb-2" style="display: none;">
        <div class="row mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong style="color: var(--success-color); font-size: 16px;">Gold Exchange</strong>
            </div>
            
            <!-- Multiple Gold Exchange Container -->
            <div id="goldExchangeList" class="w-100">
                <div class="gold-exchange-row mb-2">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Ct.</label>
                            <select name="exchange_gold_rates[]" class="form-control exchange-gold-rate">
                                @foreach ($goldRates->whereIn('id', [2, 4, 13, 5, 62]) as $rate)
                                    <option value="{{ $rate->id }}">{{ $rate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Weight</label>
                            <input type="number" class="form-control exchange-gold-weight" 
                                  name="exchange_gold_weights[]" step="0.001" min="0" value="0">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Amount (Rs)</label>
                            <input type="text" class="form-control exchange-gold-value" 
                                  name="exchange_gold_amounts[]">
                        </div>
                        <div class="col-md-1 add-row-btn">
                            <button type="button" class="btn btn-sm btn-success add-exchange-row" title="Add Another Gold">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Exchange Value Display -->
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="alert alert-info p-2">
                        <strong>Total Exchange Value: Rs <span id="totalExchangeValue"> 0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Used Advance Section -->
    <div class="row mt-2 used-advance-section" id="usedAdvanceSection" style="display: none;">
      <div class="col-12">
        <div class="used-advance-container" style="background-color: #f8fff9; border: 2px solid #d4edda; border-radius: 6px; padding: 15px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong style="color: var(--success-color); font-size: 16px;">Used Advances:</strong>
            <button type="button" class="btn btn-xs btn-outline-danger" id="clearAllAdvancesBtn" title="Clear All Advances" style="font-size: 12px; padding: 4px 8px;">
              Clear All
            </button>
          </div>
          <!-- Cash Advance Display -->
          <div id="usedCashAdvanceRow" style="display: none;" class="advance-row mb-2">
            <div class="d-flex justify-content-between align-items-center">
              <span class="advance-label">Cash Advance:</span>
              <div class="d-flex align-items-center">
                <span class="badge badge-success mr-2" id="used_cash_advance_display" style="font-size: 14px;">Rs 0.00</span>
                <button type="button" class="btn btn-xs btn-outline-danger remove-advance-btn" data-type="cash" title="Remove Cash Advance">
                  <i class="fas fa-times" style="font-size: 10px;"></i>
                </button>
              </div>
            </div>
            <div class="advance-details mt-1" style="font-size: 12px; color: #6c757d;">
              <span id="cashAdvanceDetails">General Cash Advance</span>
            </div>
          </div>
          
          <!-- Gold Advance Display -->
          <div id="usedGoldAdvanceRow" style="display: none;" class="advance-row mb-2">
              <div class="d-flex justify-content-between align-items-center">
                  <span class="advance-label">Gold Advance:</span>
                  <div class="d-flex align-items-center">
                      <span class="badge badge-warning mr-2" id="used_gold_advance_display" style="font-size: 14px;">0.000g</span>
                      <button type="button" class="btn btn-xs btn-outline-danger remove-advance-btn ml-1" data-type="gold" title="Remove Gold Advance">
                          <i class="fas fa-times" style="font-size: 10px;"></i>
                      </button>
                  </div>
              </div>
              <div class="advance-details mt-1" style="font-size: 12px; color: #6c757d;">
                  <span id="goldAdvanceDetails">General Gold Advance</span>
              </div>
              
              <!-- Editable Gold Amount Price -->
              <div class="mt-2">
                  <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Gold Amount (Rs):</label>
                  <input type="number" 
                        name="used_gold_amount" 
                        id="usedGoldAmount" 
                        value="0.00"
                        step="0.01"
                        min="0"
                        class="form-control form-control-sm"
                        placeholder="0.00"
                        style="font-size: 14px; background-color: #fff3cd; border: 2px solid #ffc107; font-weight: 600;">
                  <small class="text-muted">
                      <i class="fas fa-info-circle"></i> Edit to adjust gold advance amount
                  </small>
              </div>
          </div>
              <input type="hidden" id="total_used_advance_display">
        </div>
      </div>
    </div>


   <!-- Cheque Payment Amount Row -->
    <div class="row mt-2 payment-detail-row" id="chqPaymentRow">
      <div class="col-6">
        <strong>Chq Payment:</strong>
      </div>
      <div class="col-6">
        <input type="number" 
              id="chq_payment" 
              name="chq_payment" 
              value="0.00" 
              class="form-control form-control-sm text-right payment-amount-input" 
              placeholder="0.00" 
              step="0.01"
              min="0"
              style="font-size: 15px; border:2px solid rgb(0, 153, 255);"/>
      </div>
    </div>

    <!-- Bank Transfer Payment Amount Row -->
    <div class="row mt-2 payment-detail-row" id="bankTransferPaymentRow">
      <div class="col-6">
        <strong>Bank Transfer Payment:</strong>
      </div>
      <div class="col-6">
        <input type="number" 
              id="bank_transfer_payment" 
              name="bank_transfer_payment" 
              value="0.00" 
              class="form-control form-control-sm text-right payment-amount-input" 
              placeholder="0.00" 
              step="0.01"
              min="0"
              style="font-size: 15px; border:2px solid rgb(4, 0, 255);"/>
      </div>
    </div>

    <!-- Card Payment Amount Row -->
    <div class="row mt-2 payment-detail-row" id="cardPaymentRow">
      <div class="col-6">
        <strong>Card Payment:</strong>
      </div>
      <div class="col-6">
        <input type="number" 
              id="card_payment" 
              name="card_payment" 
              value="0.00" 
              class="form-control form-control-sm text-right payment-amount-input" 
              placeholder="0.00" 
              step="0.01"
              min="0"
              style="font-size: 15px; border:2px solid rgb(140, 0, 255);" />
      </div>
    </div>
    
    <div class="row mt-2">
      <div class="col-6">
        <strong>Cash Payment:</strong>
      </div>
      <div class="col-6" >
        <input type="hidden" id="total" name="total" value="0.00" />
        <input type="number" id="advance" name="advance" value="0.00" class="form-control form-control-sm text-right" placeholder="0.00" step="0.01" style="font-size: 15px;"/>
        <input type="hidden" id="balance" name="balance" value="0.00" />
      </div>
    </div>
    
    <div class="row mt-2">
      <div class="col-6">
        <strong>Balance Due:</strong>
      </div>
      <div class="col-6 text-right">
        <span class="badge badge-warning" id="balance_amount" style="font-size: 15px;">Rs 0.00</span>
      </div>
    </div>
  </div>
</div>

      <input type="hidden" name="advance_used" id="advanceUsed" />
      <input type="hidden" name="used_cash_advance" id="usedCashAdvance" value="0.00">
      <input type="hidden" name="used_gold_grams" id="usedGoldGrams" value="0.000">
      {{-- <input type="hidden" name="used_gold_amount" id="usedGoldAmount" value="0.00"> --}}
      <input type="hidden" name="used_gold_rate" id="usedGoldRate" value="0.00">
      <input type="hidden" name="used_gold_rate_id" id="usedGoldRateId" value="">
      <input type="hidden" name="cash_advance_order_no" id="cashAdvanceOrderNo" value="">
      <input type="hidden" name="gold_advance_order_no" id="goldAdvanceOrderNo" value="">
      <input type="hidden" name="advance_usage_type" id="advanceUsageType" value="">
      <input type="hidden" name="is_exchange" id="isExchange" value="">

      <input type="hidden" name="processed_by" id="processedBy" value="">

      <input type="hidden" name="redirect_to_invoice" value="1">

      <!-- Add these hidden inputs after the existing gold advance inputs -->
      <input type="hidden" name="used_gold_auto_amount" id="usedGoldAutoAmount" value="0.00">
      <input type="hidden" name="used_gold_manual_amount" id="usedGoldManualAmount" value="0.00">
      <input type="hidden" name="used_gold_product_grams" id="usedGoldProductGrams" value="0.000">
      <input type="hidden" name="used_gold_excess_grams" id="usedGoldExcessGrams" value="0.000">


      <!-- Action Buttons -->
      <div class="action-buttons">
        <button type="button" id="cancelButton" class="btn btn-danger">
          Clear
        </button>
        {{-- <button type="submit" name="save" class="btn btn-success">
          Save Order
        </button> --}}
        <button type="submit" name="print" id="printButton" class="btn btn-success">
          Save & Print Order
        </button>
        {{-- <button type="button" id="addManualRow" class="btn btn-info">
          Add Manual Row
        </button> --}}
      </div>
    </form>
  </div>
</div>
    </div>
</section>

<!-- Add Advance Modal -->
<div class="modal fade" id="addAdvanceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Customer Advance</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addAdvanceForm">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Amount</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rs </span>
              </div>
              <input
                type="number"
                class="form-control"
                name="amount"
                step="0.01"
                min="0"
                placeholder="0.00"
                required
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Order_No</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">No : </span>
              </div>
              <input
                type="text"
                class="form-control"
                name="order_no"
                placeholder=""
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea
              class="form-control"
              name="notes"
              rows="3"
              placeholder="Optional notes about this advance payment..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary">Save Advance</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Gold Advance Modal -->
<div class="modal fade" id="addGoldAdvanceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Customer Gold Advance</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addGoldAdvanceForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="" class="form-label">Gold Rate</label>
           <select name="gold_rate" id="gold_rate" class="form-control select2">
              @foreach ($goldRates as $rate)
                  <option value="{{ $rate->id }}">{{ $rate->name }} ({{ $rate->percentage }})</option>
              @endforeach
          </select>
          </div>
          <div class="form-group">
            <label class="form-label">Gold(gram)</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">g </span>
              </div>
              <input
                type="number"
                class="form-control"
                name="gold_amount"
                step="0.001"
                min="0"
                placeholder="0.00"
                required
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Order_No</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">No : </span>
              </div>
              <input
                type="text"
                class="form-control"
                name="gold_order_no"
                placeholder=""
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea
              class="form-control"
              name="note"
              rows="3"
              placeholder="Optional notes about this gold advance..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary">Save Advance</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Add Gold + Cash Advance Modal -->
<div class="modal fade" id="addCashGoldAdvanceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cash + Gold Advance</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addCashGoldAdvanceForm">
        <div class="modal-body">

          <div class="form-group">
            <label class="form-label">Cash Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rs.</span>
              </div>
              <input
                type="number"
                class="form-control"
                name="cash_amount"
                step="0.01"
                min="0.01"
                placeholder="0.00"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Gold (Carat-wise) <span class="text-danger">*</span></label>
            <div id="goldCaratGroupList">
              <div class="gold-carat-row input-group mb-2">
                <select name="gold_rates[]" class="form-control mr-2 gold-carat-select" required>
                  <option value="">Select Carat</option>
                  @foreach ($goldRates->take(4) as $rate)
                    <option value="{{ $rate->id }}">{{ $rate->name }}</option>
                  @endforeach
                </select>
                <input 
                  type="number" 
                  name="gold_grams[]" 
                  class="form-control mr-2 gold-gram-input" 
                  step="0.001" 
                  min="0" 
                  placeholder="Gram" 
                  required 
                />
                {{-- <button type="button" class="btn btn-sm btn-success add-carat-row">+</button> --}}
              </div>
            </div>
            <small class="text-muted">Add multiple gold entries with different carats if needed</small>
          </div>

          <div class="form-group">
            <label class="form-label">Order No <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">No:</span>
              </div>
              <input
                type="text"
                class="form-control"
                name="cash_gold_order_no"
                placeholder="Enter order number"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea
              class="form-control"
              name="cash_gold_note"
              rows="3"
              placeholder="Optional notes about this advance..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary">Save Advance</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Reserve Product Modal -->
<div class="modal fade" id="reserveProductModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reserve Product</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reserveProductForm">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Total Amount</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rs </span>
              </div>
              <input
                type="number"
                class="form-control"
                id="reserveTotalAmount"
                readonly
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Initial Payment</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rs </span>
              </div>
              <input
                type="number"
                class="form-control"
                name="initial_payment"
                step="0.01"
                min="0"
                placeholder="0.00"
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Delivery Date</label>
            <input type="date" class="form-control" name="delivery_date" />
          </div>
        </div>
        <input type="hidden" />
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary">
            Create Reservation
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Enhanced Barcode Input Modal -->
<div class="modal fade" id="barcodeInputModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-barcode mr-2"></i>
          Staff Verification Required
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="barcodeForm">
        <div class="modal-body text-center">
          <div class="mb-4">
            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
            <p class="lead mb-0">Please scan your staff barcode to complete this order</p>
          </div>
          
          <div class="form-group">
            <label class="form-label font-weight-bold">Staff Barcode</label>
            <input type="text" class="form-control scanning-effect text-center" 
                   id="staffBarcode" name="staff_barcode" 
                   placeholder="Scan or enter staff barcode" required autofocus
                   style="font-size: 18px; letter-spacing: 2px;">
            <small class="text-muted mt-2 d-block">
              <i class="fas fa-info-circle mr-1"></i>
              Scan your barcode or type manually
            </small>
          </div>
          
          <div id="staffInfo" class="alert alert-info" style="display: none;">
            <i class="fas fa-user-check mr-2"></i>
            <strong>Staff Verified:</strong> <span id="staffName"></span>
          </div>
          
          <div id="orderSummary" class="alert alert-light">
            <div class="row text-left">
              <div class="col-6"><strong>Customer:</strong></div>
              <div class="col-6" id="modalCustomerName">-</div>
            </div>
            <div class="row text-left">
              <div class="col-6"><strong>Total Amount:</strong></div>
              <div class="col-6" id="modalTotalAmount">Rs 0.00</div>
            </div>
            <div class="row text-left">
              <div class="col-6"><strong>Items:</strong></div>
              <div class="col-6" id="modalItemCount">0</div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success" id="confirmProcessBtn">
            <i class="fas fa-check mr-1"></i> Process Order
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Gold Rate Warning Modal -->
<div class="modal fade" id="goldRateWarningModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          Gold Rate Update Required
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <i class="fas fa-coins fa-3x text-warning mb-3"></i>
        <p class="lead">The gold rate needs to be updated!</p>
        <p class="text-muted">Please update the gold rate before processing orders.</p>
      </div>
      <div class="modal-footer justify-content-center">
          @if(Auth::check() && Auth::user()->role->name === 'superadmin')
              <a href="{{ route('gold_rates.index') }}" class="btn btn-success">Update</a>
          @endif
          <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
  rel="stylesheet"
/>

<!-- Include Select2 CSS -->
<link
  href="{{ asset('plugins/select2/css/select2.min.css') }}"
  rel="stylesheet"
/>
<!-- Include Select2 Bootstrap 4 Theme CSS -->
<link
  href="{{
    asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')
  }}"
  rel="stylesheet"
/>

<!-- Include Select2 JS -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#addGoldAdvanceModal').on('shown.bs.modal', function () {
        $('#gold_rate').select2({
            placeholder: 'Select gold rate',
            allowClear: false,
            width: '100%',
            dropdownParent: $('#addGoldAdvanceModal')
        });
    });
});
</script>
<script>
$(document).ready(function() {
    
    // Cache frequently used DOM elements
    const $productNoInput = $('#product_no');
    const $customerSelect = $('#customer');
    const $productsTable = $('#products tbody');
    const $advanceInput = $('#advance');
    const $totalElement = $('#total');
    const $balanceAmount = $('#balance_amount');
    const $advanceUsed = $('#advanceUsed');
    const $customerAdvanceBalance = $('#customerAdvanceBalance');
    const $usedAdvanceSection = $('#usedAdvanceSection');
    
    // Gold rates data for JavaScript
    const goldRatesData = @json($goldRates);
    
    // Performance variables
    let rowNo = 0;
    let calculationDebounce = null;
    let advanceDebounce = null;
    
    // Currency formatter - create once, reuse
    const currencyFormatter = new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    const formatCurrency = (value) => 'Rs ' + currencyFormatter.format(value);
    
    // ============== INITIALIZATION ==============
    
    // Auto-focus on product search field when page loads
    $productNoInput.focus();

     $('#payment_method').trigger('change');
    
    // AJAX setup - do once
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize Select2
    $customerSelect.select2({
        theme: 'bootstrap4',
        allowClear: true
    });
    
    // ==============  EVENT HANDLERS ==============
    
    // Product search with debounce
    $productNoInput.on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            $(this).trigger('change');
        }
    });
    
    //  product search
    $productNoInput.on('change', function(e) {
        e.preventDefault();
        const productNo = $(this).val().trim();
        if (!productNo) return;
        
        fetchProductDetails(productNo);
    });


    // --------------- Payment method handler ----------------//


  // Payment method change handler - Updated
$('#payment_method').on('change', function() {
    const selectedMethod = $(this).val();

        // 🔹 Clear ALL non-cash payment inputs
    $('#chq_payment, #bank_transfer_payment, #card_payment')
        // .val('')  // null
         .val(0)
        .trigger('input');
    
    // Hide all payment detail rows
    $('.payment-detail-row').removeClass('active');
    
    // Cash payment is ALWAYS visible
    $('#advance').closest('.row').show();
    
    // Show relevant payment detail row based on selection
    switch(selectedMethod) {
        case 'chq':
            $('#chqPaymentRow').addClass('active');
            break;
        case 'bank_transfer':
            $('#bankTransferPaymentRow').addClass('active');
            break;
        case 'card':
            $('#cardPaymentRow').addClass('active');
            break;
        case 'cash':
            // Only cash payment visible, no additional fields
            // Don't reset cash when switching to cash-only mode
            break;
    }
    
    // Recalculate balance with current values
    calculateBalanceForPaymentMethod();
});

// Handle ALL payment amount input changes (unified handler)
    $('#advance, #chq_payment, #bank_transfer_payment, #card_payment').on('input', function() {
        calculateBalanceForPaymentMethod();
    });

// =============== BALANCE CALCULATION FUNCTION ===============

// Main balance calculation based on payment method - FIXED VERSION
function calculateBalanceForPaymentMethod() {
    const total = parseFloat($('#total').val()) || 0;
    const totalAdvanceUsed = currentAdvanceUsage.total_amount || 0;
    const exchangeGold = parseFloat($('#totalExchangeValue').text()) || 0;
    const paymentMethod = $('#payment_method').val();
    
    // ALWAYS get cash payment value
    let cashPayment = parseFloat($('#advance').val()) || 0;
    let additionalPayment = 0;
    
    // Get additional payment amount based on selected method
    // Only include if that payment row is actually active
    switch(paymentMethod) {
        case 'chq':
            if ($('#chqPaymentRow').hasClass('active')) {
                additionalPayment = parseFloat($('#chq_payment').val()) || 0;
            }
            break;
        case 'bank_transfer':
            if ($('#bankTransferPaymentRow').hasClass('active')) {
                additionalPayment = parseFloat($('#bank_transfer_payment').val()) || 0;
            }
            break;
        case 'card':
            if ($('#cardPaymentRow').hasClass('active')) {
                additionalPayment = parseFloat($('#card_payment').val()) || 0;
            }
            break;
        case 'cash':
            // Cash only - no additional payment
            additionalPayment = 0;
            break;
    }
    
    // Total payment calculation
    const totalPayment = cashPayment + additionalPayment;
    const balance = total - totalAdvanceUsed - totalPayment - exchangeGold;
    
    // Update the balance display
    updateBalanceDisplay(total, balance);
}

// ----------------------- Barcode keyboard typing prevent ---------------------------------
@if(Auth::check() && Auth::user()->role->name === 'staff')

let scanTimeout = null;
let lastInputTime = 0;

$productNoInput.on('input', function () {
    const now = Date.now();
    const timeDiff = now - lastInputTime;
    lastInputTime = now;

    // Clear previous timer
    if (scanTimeout) {
        clearTimeout(scanTimeout);
    }

    scanTimeout = setTimeout(() => {
        const barcode = $(this).val().trim();

        if (barcode.length >= 3 && timeDiff < 80) {
            fetchProductDetails(barcode);
        }

        // Always clear input after processing
        $(this).val('');
    }, 500); // scanner finishes within this window
});

// Optional safety: scanners that send ENTER at the end
$productNoInput.on('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();

        const barcode = $(this).val().trim();
        if (barcode.length >= 3) {
            fetchProductDetails(barcode);
        }

        $(this).val('');
    }
});


@endif
//------------------------------------------------------------

    // Debounced calculation handlers
    $(document).on('input change', '.qty-input, .wastage-input, .stone-input, .gold-rate-input, .making-charges-input, .discount-input', function() {
        const row = $(this).closest('tr');
        debouncedUpdateRowCalculations(row);
    });
    
    //  weight input handler
    $(document).on('change', '.weight-input', function() {
        const row = $(this).closest('tr');
        updateRowCalculations(row);
    });
    
    //  advance input handler
    $advanceInput.on('input', function() {
        debouncedAdvanceCalculation();
    });


    // ============== Staff Barcode input ============

$('#printButton').off('click').on('click', function(e) {
    e.preventDefault();
    
    // Check user role - don't show barcode popup for superadmin
    @if(Auth::check() && Auth::user()->role->name === 'staff')
    // Staff users need to scan barcode
    
    // Validate form first
    const totalAmount = parseFloat($('#total').val()) || 0;
    const itemCount = $('#products tbody tr').length;
    
    if (totalAmount <= 0 || itemCount === 0) {
        toastr.error('Please add products to the order first');
        return;
    }
    
    // Populate order summary in modal
    updateModalOrderSummary();
    
    // Show barcode input modal with animation
    $('#barcodeInputModal').modal('show');
    @else
    // For non-staff users (superadmin), ask for confirmation first
    Swal.fire({
        title: 'Confirm Order Submission',
        text: 'Are you sure you want to process this order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, process it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#posOrderForm')[0].submit();
        }
    });
    @endif

});

// Function to update modal order summary
function updateModalOrderSummary() {
    const totalAmount = parseFloat($('#total').val()) || 0;
    const itemCount = $('#products tbody tr').length;
    const customerName = $('#customer option:selected').text().split(' - ')[0] || 'Walk-in Customer';
    
    $('#modalCustomerName').text(customerName);
    $('#modalTotalAmount').text(formatCurrency(totalAmount));
    $('#modalItemCount').text(itemCount + ' item(s)');
}

// Auto-focus and add scanning effect when modal opens
$('#barcodeInputModal').on('shown.bs.modal', function() {
    $('#staffBarcode').focus().addClass('scanning-effect');
    
    // Add subtle pulse animation to the barcode icon
    $('.fa-barcode').addClass('pulse-animation');
});

// Clear effects when modal closes
$('#barcodeInputModal').on('hidden.bs.modal', function() {
    $('#staffBarcode').val('').removeClass('scanning-effect');
    $('#staffInfo').hide().removeClass('alert-success').addClass('alert-info');
    $('#confirmProcessBtn').prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Process Order');
    $('.fa-barcode').removeClass('pulse-animation');
    
    // Clear any error states
    $('#staffBarcode').removeClass('is-invalid');
    $('.invalid-feedback').remove();
});

// Handle barcode input - submit on Enter key
$('#staffBarcode').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $('#barcodeForm').submit();
    }
});

// Add real-time validation feedback
$('#staffBarcode').on('input', function() {
    const $this = $(this);
    $this.removeClass('is-invalid is-valid');
    $('.invalid-feedback').remove();
    
    if ($this.val().length >= 3) {
        $this.addClass('is-valid');
    }
});

// Handle barcode form submission with enhanced UX
$('#barcodeForm').on('submit', function(e) {
    e.preventDefault();
    
    const barcode = $('#staffBarcode').val().trim();
    if (!barcode) {
        showBarcodeError('Please enter a staff barcode');
        return;
    }
    
    const $submitBtn = $('#confirmProcessBtn');
    const $barcodeInput = $('#staffBarcode');
    
    // Update UI for processing state
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Verifying...');
    $barcodeInput.prop('disabled', true);
    
    // Verify barcode and get user ID
    $.ajax({
        url: '/verify-staff-barcode',
        method: 'POST',
        data: {
            barcode: barcode,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Set the user ID in the hidden input
                $('#processedBy').val(response.user_id);
                
                // Show success state
                showStaffVerificationSuccess(response.user_name);
                
                // Auto-submit after short delay for better UX
                setTimeout(() => {
                    $('#barcodeInputModal').modal('hide');
                    
                    // Submit the main POS form
                    $('#posOrderForm')[0].submit();
                }, 1000);
                
            } else {
                showBarcodeError(response.message || 'Invalid staff barcode');
                resetBarcodeForm();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showBarcodeError(response?.message || 'Error verifying barcode. Please try again.');
            resetBarcodeForm();
        }
    });
});

// Show staff verification success
function showStaffVerificationSuccess(userName) {
    $('#staffName').text(userName);
    $('#staffInfo')
        .removeClass('alert-info')
        .addClass('alert-success')
        .show()
        .html(`
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Verified:</strong> ${userName}
        `);
    
    $('#confirmProcessBtn')
        .removeClass('btn-success')
        .addClass('btn-primary')
        .html('<i class="fas fa-check-double mr-1"></i> Processing Order...');
        
    // Add success effect to barcode input
    $('#staffBarcode').removeClass('scanning-effect').addClass('is-valid');
}

// Show barcode error
function showBarcodeError(message) {
    const $barcodeInput = $('#staffBarcode');
    
    // Add error styling
    $barcodeInput.addClass('is-invalid');
    
    // Remove existing feedback
    $('.invalid-feedback').remove();
    
    // Add error message
    $barcodeInput.after(`<div class="invalid-feedback d-block">${message}</div>`);
    
    // Show error toast
    toastr.error(message);
    
    // Shake animation for visual feedback
    $barcodeInput.addClass('shake-animation');
    setTimeout(() => {
        $barcodeInput.removeClass('shake-animation');
    }, 600);
}

// Reset barcode form to initial state
function resetBarcodeForm() {
    const $submitBtn = $('#confirmProcessBtn');
    const $barcodeInput = $('#staffBarcode');
    
    $submitBtn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Process Order');
    $barcodeInput.prop('disabled', false).focus();
    $('#staffInfo').hide();
}

// Add pulse animation
if (!$('#pulse-animation-css').length) {
    $('<style id="pulse-animation-css">')
        .text(`
            .pulse-animation {
                animation: pulse 1.5s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            
            .shake-animation {
                animation: shake 0.6s ease-in-out;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `)
        .appendTo('head');
}

    // ============== Barcode input end =======
    
    // ==============  FUNCTIONS ==============
    
    // Debounced functions for better performance
    const debouncedUpdateRowCalculations = debounce((row) => {
        updateRowCalculations(row);
    }, 150);
    
    const debouncedAdvanceCalculation = debounce(() => {
        calculateAdvanceBalance();
    }, 100);
    
    const debouncedCalculateTotal = debounce(() => {
        calculateTotal();
    }, 100);
    
    // Utility debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    //  AJAX product fetch
function fetchProductDetails(productNo) {
    $.ajax({
        url: `{{ route('product.details', '') }}/${productNo}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Show warning if gold rate is outdated
                if (response.gold_rate_outdated) {
                    $('#goldRateWarningModal').modal('show');
                    $productNoInput.val('');
                } else {
                    // Add product if rate is up to date
                    addProductRow(response.data);
                    $productNoInput.val('');
                }
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Error fetching product details.');
        }
    });
}
    

//  product row addition
function addProductRow(productDetails) {
    rowNo = $productsTable.find('tr').length + 1;
    
    
    // Validation checks - fail fast
    if (productDetails.qty == 0) {
        toastr.error('Selected item was sold out');
        return;
    }
    
    // check duplicate product added or not 
    const existingRow = $productsTable.find(`input[value="${productDetails.id}"]`).closest('tr');
      if (existingRow.length > 0) {
          toastr.warning('This product is already added.');
          return;
      }

    
    // Pre-calculate values
    const goldRate = productDetails.gold_rate ? parseFloat(productDetails.gold_rate.rate) : 0;
    const goldRateId = productDetails.gold_rate_id || '';
    const goldRateName = productDetails.gold_rate ? productDetails.gold_rate.name : 'No Rate';
    const productType = productDetails.product_type ? productDetails.product_type.toLowerCase() : 'gold';
    const type = productDetails.type;
    
    // Ensure all values are proper numbers - use ||= for defaults
    const qty = 1;
    const makingCharges = parseFloat(productDetails.making_charges) || 0;
    const wastageWeight = parseFloat(productDetails.wastage_weight) || 0;
    const stoneWeight = parseFloat(productDetails.stone_weight) || 0;
    const netWeight = parseFloat(productDetails.weight) || 0;
    
    // Build row HTML efficiently
    const rowHTML = buildProductRowHTML({
        rowNo, productDetails, type, netWeight, qty, makingCharges,
        wastageWeight, stoneWeight, goldRate, goldRateId, goldRateName, productType
    });
    
    // Add row and calculate if needed
    $productsTable.append(rowHTML);

    if (type != 1) {
        debouncedCalculateTotal();
    }
}

//  row HTML builder
function buildProductRowHTML(params) {
    const {
        rowNo, productDetails, type, netWeight, qty, makingCharges,
        wastageWeight, stoneWeight, goldRate, goldRateId, goldRateName, productType
    } = params;
    
    let subTotal = 0;
    let weightInputHtml = '';
    let makingChargesHtml = '';
    
    if (type == 1) {
        // Weight-based product - editable making charges
        weightInputHtml = `
            <div class="d-flex align-items-center gap-1">
                <p class="weight-remaining">Available: <span>${netWeight.toFixed(3)}g</span></p>
                <input type="number" 
                      name="products[${rowNo}][weight]" 
                      class="form-control weight-input" 
                      value="" 
                      step="0.001" 
                      min="0" 
                      max="${netWeight}" 
                      placeholder="Enter weight" 
                      required
                      onfocus="this.select()"
                      onkeydown="return event.keyCode !== 13">
            </div>`;
        
        // Editable making charges for weight-based products
        makingChargesHtml = `
            <div class="d-flex align-items-center">
                <input type="number" 
                      class="form-control form-control-sm making-charges-input making-charges-input-editable" 
                      value="${makingCharges.toFixed(2)}" 
                      step="0.01" 
                      min="0"
                      placeholder="Enter charges"
                      title="Editable making charges">
            </div>`;
    } else {
        // Fixed weight product - readonly making charges
        const totalWeight = netWeight + wastageWeight - stoneWeight;
        subTotal = Math.max(0, (qty * goldRate * totalWeight) + makingCharges);
        
        weightInputHtml = `
            <input type="number" 
                  name="products[${rowNo}][weight]" 
                  class="form-control weight-input" 
                  value="${netWeight.toFixed(3)}" 
                  step="0.001" 
                  min="0" 
                  readonly>`;
        
        // Readonly making charges for fixed weight products
        makingChargesHtml = `
            <input type="number" 
                  class="form-control form-control-sm making-charges-input" 
                  value="${makingCharges.toFixed(2)}" 
                  step="0.01" 
                  min="0" 
                  readonly
                  title="Fixed making charges">`;
    }

    // Updated subtotal section with edit button
    const subTotalHtml = `
        <div class="sub-total-container">
            <div class="sub-total">${type == 1 ? 'Rs 0.00' : formatCurrency(subTotal)}</div>
            <a href="#" class="edit-subtotal-btn" title="Edit Subtotal">
                <i class="fas fa-pen"></i>
            </a>
        </div>
        <input type="hidden" name="products[${rowNo}][sub_total]" value="${subTotal.toFixed(2)}">`;
    
    const hiddenInputs = [
        ['product_id', productDetails.id],
        ['name', productDetails.name],
        ['max_qty', productDetails.qty],
        ['original_weight', netWeight.toFixed(3)],
        ['qty', qty],
        ['making_charges', makingCharges.toFixed(2)],
        ['wastage_weight', wastageWeight.toFixed(3)],
        ['stone_weight', stoneWeight.toFixed(3)],
        ['gold_rate', goldRate.toFixed(2)],
        ['gold_rate_id', goldRateId],
        ['product_type', productType],
        ['type', type],
        ['discount', '0']
    ].map(([name, value]) => 
        `<input type="hidden" name="products[${rowNo}][${name}]" value="${value}">`
    ).join('');
    
    return `
         <tr data-product-type="${type}">
            <td class="p-1 pl-2">
                <a class="btn btn-xs btn-danger delete_row"><i class="fas fa-times"></i></a>
                ${hiddenInputs}
            </td>
            <td class="p-1 pl-2"><strong>${rowNo}</strong></td>
            <td class="p-1 pl-2">
                <div class="product-name">${productDetails.name}</div>
            </td>
            <td class="p-1 pl-2">${weightInputHtml}</td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm wastage-input" value="${wastageWeight.toFixed(3)}" step="0.001" min="0" readonly>
            </td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm stone-input" value="${stoneWeight.toFixed(3)}" step="0.001" min="0" readonly>
            </td>
            <td class="p-1 pl-2">
                <div class="gold-rate-display">${goldRateName}</div>
            </td>
            <td class="p-1 pl-2" style="display: none;">
                <input type="number" class="form-control form-control-sm qty-input" value="${qty}" min="1" max="${productDetails.qty}">
            </td>
            <td class="p-1 pl-2">
                ${makingChargesHtml}
            </td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm discount-input" value="0" step="0.01" min="0">
            </td>
            <td class="p-1 pl-2">
                ${subTotalHtml}
            </td>
        </tr>`;
}

// Manual row addition - optimized
$('#addManualRow').on('click', function() {
    rowNo = $productsTable.find('tr').length + 1;
    const defaultRate = goldRatesData.find(rate => rate.is_default) || goldRatesData[0];

    const subTotalCellHtml = `<div class="sub-total">0.00</div><input type="hidden" name="products[${rowNo}][sub_total]" value="0.00">`;

    const newRow = `
        <tr>
            <td class="p-1 pl-2">
                <a class="btn btn-xs btn-danger delete_row"><i class="fas fa-times"></i></a>
                <input type="hidden" name="products[${rowNo}][product_id]" value="0">
                <input type="hidden" name="products[${rowNo}][wastage_weight]" value="0">
                <input type="hidden" name="products[${rowNo}][stone_weight]" value="0">
                <input type="hidden" name="products[${rowNo}][gold_rate]" value="${defaultRate ? defaultRate.rate : 0}">
                <input type="hidden" name="products[${rowNo}][gold_rate_id]" value="${defaultRate ? defaultRate.id : ''}">
                <input type="hidden" name="products[${rowNo}][qty]" value="1">
                <input type="hidden" name="products[${rowNo}][making_charges]" value="0">
                <input type="hidden" name="products[${rowNo}][weight]" value="0">
            </td>
            <td class="p-1 pl-2">${rowNo}</td>
            <td class="p-1 pl-2">
                <input type="text" name="products[${rowNo}][name]" class="form-control form-control-sm" placeholder="Product Name" required>
            </td>
            <td class="p-1 pl-2">
                <input type="number" name="products[${rowNo}][weight]" class="form-control form-control-sm weight-input" placeholder="Net Weight" step="0.001" min="0" required>
            </td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm wastage-input" value="0" step="0.001" min="0">
            </td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm stone-input" value="0" step="0.001" min="0">
            </td>
            <td class="p-1 pl-2">
                <span class="form-control form-control-sm bg-light">${defaultRate ? defaultRate.name : 'No Rate Available'}</span>
            </td>
            <td class="p-1 pl-2" style="display: none;">
                <input type="number" name="products[${rowNo}][qty]" class="form-control form-control-sm qty-input" value="1" min="1" required>
            </td>
            <td class="p-1 pl-2">
                <input type="number" name="products[${rowNo}][making_charges]" class="form-control form-control-sm making-charges-input" value="0" step="0.01" min="0">
            </td>
            <td class="p-1 pl-2">
                <input type="number" class="form-control form-control-sm discount-input" value="0" step="0.01" min="0">
            </td>
            <td class="p-1 pl-2">
                ${subTotalCellHtml}
            </td>
        </tr>`;
        
    $productsTable.append(newRow);

    debouncedCalculateTotal();
});





//  row calculations
function updateRowCalculations(row) {
    const $row = $(row);
    const rowIndex = $row.index();
    
    // Check if this row has manual subtotal
    if (manuallyEditedRows.has(rowIndex)) {
        const manualData = manuallyEditedRows.get(rowIndex);
        debouncedCalculateTotal();
        return;
    }
    
    // Get values
    const weight = parseFloat($row.find('.weight-input').val()) || 0;
    const wastageWeight = parseFloat($row.find('.wastage-input').val()) || 0;
    const stoneWeight = parseFloat($row.find('.stone-input').val()) || 0;
    const qty = parseFloat($row.find('.qty-input').val()) || 1;
    
    // Get making charges from input (now editable for weight-based products)
    const makingChargesInput = $row.find('.making-charges-input');
    const makingCharges = parseFloat(makingChargesInput.val()) || 0;
    
    const discount = parseFloat($row.find('.discount-input').val()) || 0;
    const goldRate = parseFloat($row.find('input[name*="[gold_rate]"]').val()) || 0;
    const productType = parseInt($row.find('input[name*="[type]"]').val()) || 0;
    const originalWeight = parseFloat($row.find('input[name*="[original_weight]"]').val()) || 0;
    
    // Validate weight for weight-based products
    if (productType == 1 && weight > originalWeight) {
        toastr.error(`Weight cannot exceed available weight (${originalWeight.toFixed(3)}g)`);
        $row.find('.weight-input').val(originalWeight.toFixed(3));
        return;
    }
    
    // Calculate efficiently
    const totalWeight = weight + wastageWeight - stoneWeight;
    let subTotal = 0;
    
    if (weight > 0) {
        const baseAmount = (qty * goldRate * totalWeight) + makingCharges;
        subTotal = Math.max(0, baseAmount - discount);
    }
    
    // Batch DOM updates
    const updates = [
        ['[sub_total]', subTotal.toFixed(2)],
        ['[wastage_weight]', wastageWeight.toFixed(3)],
        ['[stone_weight]', stoneWeight.toFixed(3)],
        ['[qty]', qty],
        ['[making_charges]', makingCharges.toFixed(2)], // Update hidden input with manual value
        // ['[weight]', weight.toFixed(3)],
        ['[discount]', discount.toFixed(2)]
    ];
    
    updates.forEach(([selector, value]) => {
        $row.find(`input[name*="${selector}"]`).val(value);
    });
    
    // Update display
    $row.find('.sub-total').text(formatCurrency(subTotal));
    
    // Update weight remaining for weight-based products
    if (productType == 1) {
        const remaining = originalWeight - weight;
        $row.find('.weight-remaining span').text(`${remaining.toFixed(3)}g`);
    }
    
    debouncedCalculateTotal();
}






// ============== MANUAL SUBTOTAL EDIT FUNCTIONALITY ============== 

// Track manually edited subtotals
let manuallyEditedRows = new Map();

// Toggle edit mode for subtotal
$(document).on('click', '.edit-subtotal-btn', function(e) {
    e.preventDefault();
    const $btn = $(this);
    const $row = $btn.closest('tr');
    const $container = $btn.closest('.sub-total-container');
    const $display = $container.find('.sub-total');
    const currentValue = parseFloat($row.find('input[name*="[sub_total]"]').val()) || 0;
    const rowIndex = $row.index();
    
    // Check if already in edit mode
    if ($container.find('.edit-subtotal-input').length > 0) {
        return;
    }
    
    // Create edit input
    const $input = $('<input>', {
        type: 'number',
        class: 'form-control edit-subtotal-input',
        value: currentValue.toFixed(2),
        step: '0.01',
        min: '0',
        'data-original-value': currentValue.toFixed(2)
    });
    
    // Hide display, show input
    $display.hide();
    $btn.hide();
    $container.prepend($input);
    
    // Add save/cancel buttons
    const $actionBtns = $(`
        <div class="edit-actions ml-2">
            <button type="button" class="btn btn-xs btn-success save-subtotal-btn" title="Save">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="btn btn-xs btn-danger cancel-subtotal-edit" title="Cancel">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
    
    $container.append($actionBtns);
    $input.focus().select();
    
    // Handle Enter key
    $input.on('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $container.find('.save-subtotal-btn').click();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            $container.find('.cancel-subtotal-edit').click();
        }
    });
});

// Save manual subtotal edit
$(document).on('click', '.save-subtotal-btn', function(e) {
    e.preventDefault();
    const $btn = $(this);
    const $container = $btn.closest('.sub-total-container');
    const $input = $container.find('.edit-subtotal-input');
    const $row = $container.closest('tr');
    const $display = $container.find('.sub-total');
    const $editBtn = $container.find('.edit-subtotal-btn');
    const rowIndex = $row.index();
    
    const newValue = parseFloat($input.val()) || 0;
    const originalValue = parseFloat($input.data('original-value')) || 0;
    
    // Validate input
    if (newValue < 0) {
        toastr.error('Subtotal cannot be negative');
        $input.focus();
        return;
    }
    
    // Update subtotal
    $row.find('input[name*="[sub_total]"]').val(newValue.toFixed(2));
    $display.text(formatCurrency(newValue));
    
    // Mark row as manually edited
    manuallyEditedRows.set(rowIndex, {
        isManual: true,
        manualValue: newValue,
        originalValue: originalValue
    });
    
    // Add manual edit indicator if changed
    if (newValue !== originalValue) {
        $row.attr('data-manual-subtotal', 'true');
        if (!$display.find('.manual-edit-badge').length) {
            $display.append('<span class="manual-edit-badge">M</span>');
        }
    } else {
        $row.removeAttr('data-manual-subtotal');
        $display.find('.manual-edit-badge').remove();
        manuallyEditedRows.delete(rowIndex);
    }
    
    // Remove input and show display
    $container.find('.edit-actions').remove();
    $input.remove();
    $display.show();
    $editBtn.show();
    
    // Recalculate totals
    debouncedCalculateTotal();
    
    toastr.success('Subtotal updated successfully');
});

// Cancel manual subtotal edit
$(document).on('click', '.cancel-subtotal-edit', function(e) {
    e.preventDefault();
    const $btn = $(this);
    const $container = $btn.closest('.sub-total-container');
    const $input = $container.find('.edit-subtotal-input');
    const $display = $container.find('.sub-total');
    const $editBtn = $container.find('.edit-subtotal-btn');
    
    // Remove input and show display
    $container.find('.edit-actions').remove();
    $input.remove();
    $display.show();
    $editBtn.show();
});



$(document).on('input change', '.making-charges-input-editable', function() {
    const row = $(this).closest('tr');
    const newValue = parseFloat($(this).val()) || 0;
    
    // Update hidden input
    row.find('input[name*="[making_charges]"]').val(newValue.toFixed(2));
    
    // Recalculate row
    debouncedUpdateRowCalculations(row);
});



// Highlight making charges field when focused (visual feedback)
$(document).on('focus', '.making-charges-input-editable', function() {
    $(this).select();
});


// Update the calculateTotal function to use the new balance calculation
function calculateTotal() {
    let subTotal = 0;
    let totalDiscount = 0;

    // Calculate product totals
    $productsTable.find('tr').each(function() {
        const $row = $(this);
        const subTotalText = $row.find('.sub-total').text().replace('Rs ', '').replace(/,/g, '');
        const productSubTotal = parseFloat(subTotalText) || 0;
        const productDiscount = parseFloat($row.find('.discount-input').val()) || 0;
        
        subTotal += productSubTotal;
        totalDiscount += productDiscount;
    });

    const finalTotal = Math.max(0, subTotal);

    // Update displays
    $('#sub_total_amount').text(formatCurrency(subTotal));
    $('#total_amount').text(formatCurrency(finalTotal));
    $('#total').val(finalTotal.toFixed(2));
    $('#total_discount').val(totalDiscount.toFixed(2));
    
    // Calculate tax
    const taxRate = parseFloat($('#tax_rate_v').val()) || 0;
    const inclusiveTax = (finalTotal * taxRate) / (100 + taxRate);
    $('#inclusive_tax').text(inclusiveTax.toFixed(2));
    $('#tax_rate').val(inclusiveTax.toFixed(2));
    
    // Use the new flexible balance calculation
    calculateBalanceForPaymentMethod();
    
    // Clear advance if total is 0
    if (finalTotal === 0) {
        clearAllAdvances();
        $('#advance').val('0.00');
        $('.payment-amount-input').val('0.00');
    }
    
    // Update reserve modal if open
    if ($('#reserveProductModal').is(':visible')) {
        $('#reserveTotalAmount').val(finalTotal.toFixed(2));
    }
}

// Updated advance calculation for cash payment changes
function calculateAdvanceBalance() {
    const total = parseFloat($totalElement.val()) || 0;
    const totalAdvanceUsed = currentAdvanceUsage.total_amount;
    const cashPayment = parseFloat($advanceInput.val()) || 0;
    
    updateBalanceWithAdvance(total);
    calculateBalanceForPaymentMethod();
}

// Updated balance calculation with advance consideration
function updateBalanceWithAdvance(total) {
    const totalAdvanceUsed = currentAdvanceUsage.total_amount || 0;
    const cashPayment = parseFloat($advanceInput.val()) || 0;
    const exchangeGold = parseFloat($('#totalExchangeValue').val()) || 0;

    const balance = total - totalAdvanceUsed - cashPayment - exchangeGold;

    updateBalanceDisplay(total, balance);
    calculateBalanceForPaymentMethod();
}

//  balance display update
function updateBalanceDisplay(total, balance = null) {
    // If balance not provided, calculate it
    if (balance === null) {
        const totalAdvanceUsed = currentAdvanceUsage.total_amount || 0;
        const exchangeGold = parseFloat($('#totalExchangeValue').text()) || 0;
        const paymentMethod = $('#payment_method').val();
        
        let cashPayment = parseFloat($('#advance').val()) || 0;
        let additionalPayment = 0;
        
        // Get additional payment based on active method
        switch(paymentMethod) {
            case 'chq':
                if ($('#chqPaymentRow').hasClass('active')) {
                    additionalPayment = parseFloat($('#chq_payment').val()) || 0;
                }
                break;
            case 'bank_transfer':
                if ($('#bankTransferPaymentRow').hasClass('active')) {
                    additionalPayment = parseFloat($('#bank_transfer_payment').val()) || 0;
                }
                break;
            case 'card':
                if ($('#cardPaymentRow').hasClass('active')) {
                    additionalPayment = parseFloat($('#card_payment').val()) || 0;
                }
                break;
        }
        
        const totalPayment = cashPayment + additionalPayment;
        balance = total - totalAdvanceUsed - totalPayment - exchangeGold;
    }

    const $balanceBadge = $('#balance_amount');
    $balanceBadge.removeClass('negative-balance positive-balance change-balance');

    let balanceText;
    if (balance < 0) {
        balanceText = formatCurrency(Math.abs(balance)) + ' (Change)';
        $balanceBadge.addClass('change-balance');
    } else if (balance > 0) {
        balanceText = formatCurrency(balance);
        $balanceBadge.addClass('positive-balance');
    } else {
        balanceText = formatCurrency(balance);
    }

    $balanceBadge.text(balanceText);
    $('#balance').val(balance.toFixed(2));
}
//  delete row handler
$(document).on('click', '.delete_row', function() {
    const $row = $(this).closest('tr');
    const customerId = $customerSelect.val();
    const currentAdvanceUsed = parseFloat($advanceUsed.val()) || 0;

    const rowIndex = $row.index();
    
    // Remove from manual edits map
    if (manuallyEditedRows.has(rowIndex)) {
        manuallyEditedRows.delete(rowIndex);
    }
    
    $row.remove();
    debouncedCalculateTotal();
    
    const remainingProducts = $productsTable.find('tr').length;
    
    if (remainingProducts === 0) {
        clearUsedAdvance();
        $advanceInput.val('0.00').trigger('input');
    } else if (currentAdvanceUsed > 0 && customerId && !$('#add_manual_customer').is(':checked')) {
        recalculateAdvanceBalance(customerId);
    }
});

    
// ============== GOLD EXCHAGE FUNCTIONS ==============
    // Toggle gold exchange section
    $('#goldExchangeCheckbox').on('change', function() {
        $('#goldExchangeSection').toggle(this.checked);
        
        if (this.checked) {
            calculateTotalExchangeValue();
        } else {
            // Reset all exchange values when unchecked
            resetAllExchangeValues();
        }
        
        calculateTotal();
    });



$(document).on('input change', '.exchange-gold-value', function() {
    calculateTotalExchangeValue();
    calculateTotal();
});

// $(document).on('input', '#totalExchangeValue', function () {
//     calculateTotal();
// });

// ----------------------------------------------------------------------------------------------------------
// Calculate exchange gold value when weight or rate changes
// $(document).on('input change', '#exchangeGoldRate, #exchangeGoldWeight', function() {
//     calculateExchangeGoldValue();
//     calculateTotal();
// });


    // Add new gold exchange row
    $(document).on('click', '.add-exchange-row', function() {
        const $currentRow = $(this).closest('.gold-exchange-row');
        const $newRow = $currentRow.clone();
        
        // Clear values in new row
        $newRow.find('input').val('0');
        $newRow.find('select').prop('selectedIndex', 0);
        
        // Change + button to - button for removal
        $newRow.find('.add-exchange-row')
            .removeClass('btn-success add-exchange-row')
            .addClass('btn-danger remove-exchange-row')
            .attr('title', 'Remove This Gold')
            .html('<i class="fas fa-minus"></i>');
        
        $('#goldExchangeList').append($newRow);
        calculateTotalExchangeValue();
    });


     // Remove gold exchange row
    $(document).on('click', '.remove-exchange-row', function() {
        $(this).closest('.gold-exchange-row').remove();
        calculateTotalExchangeValue();
        calculateTotal();
    });

        // Calculate individual exchange value when rate or weight changes
    $(document).on('input change', '.exchange-gold-rate, .exchange-gold-weight', function() {
        const $row = $(this).closest('.gold-exchange-row');
        calculateRowExchangeValue($row);
        calculateTotalExchangeValue();
        calculateTotal();
    });


        // Calculate exchange value for a specific row
    function calculateRowExchangeValue($row) {
        const goldRateId = $row.find('.exchange-gold-rate').val();
        const goldRate = goldRatesData.find(rate => rate.id == goldRateId)?.rate || 0;
        const weight = parseFloat($row.find('.exchange-gold-weight').val()) || 0;

        // If you want to optionally use this rate x weight logic, you can show a suggestion
        const suggestedValue = goldRate * weight;

        // DO NOT overwrite the manually typed value
        // Optionally: show this value as a tooltip/hint somewhere

        return parseFloat($row.find('.exchange-gold-value').val()) || 0;
    }



    // Calculate total exchange value from all rows
    function calculateTotalExchangeValue() {
        let totalValue = 0;
        
        $('#goldExchangeList .gold-exchange-row').each(function() {
            const $row = $(this);
            const rowValue = calculateRowExchangeValue($row);
            totalValue += rowValue;
        });
        
        $('#totalExchangeValue').text(totalValue.toFixed(2));
        
        // Update the main exchange value (for backward compatibility)
        $('#totalExchangeValue').val(totalValue.toFixed(2));
        
        // Set exchange flag
        $('#isExchange').val(totalValue > 0 ? 1 : 0);
        
        return totalValue;
    }

    // Reset all exchange values
    function resetAllExchangeValues() {
        // Remove all rows except the first one
        $('#goldExchangeList .gold-exchange-row:not(:first)').remove();
        
        // Reset the first row
        const $firstRow = $('#goldExchangeList .gold-exchange-row:first');
        $firstRow.find('input').val('0');
        $firstRow.find('select').prop('selectedIndex', 0);
        
        // Make sure first row has + button
        $firstRow.find('.remove-exchange-row')
            .removeClass('btn-danger remove-exchange-row')
            .addClass('btn-success add-exchange-row')
            .attr('title', 'Add Another Gold')
            .html('<i class="fas fa-plus"></i>');
        
        $('#totalExchangeValue').text('0.00');
        $('#totalExchangeValue').val('0');
        $('#isExchange').val('');
    }


// function calculateExchangeGoldValue() {
//     if (!$('#goldExchangeCheckbox').is(':checked')) return 0;

//     const goldRateId = $('#exchangeGoldRate').val();
//     const goldRate = goldRatesData.find(rate => rate.id == goldRateId)?.rate || 0;
//     const weight = parseFloat($('#exchangeGoldWeight').val()) || 0;
//     const value = goldRate * weight;

//     // Only update if not manually typing in the value field
//     if (!$('#totalExchangeValue').is(':focus')) {
//         $('#totalExchangeValue').val(value.toFixed(2));
//     }
    
//     $('#isExchange').val(weight > 0 ? 1 : 0);

//     return value;
// }


// Updated calculateExchangeGoldValue function to work with multiple exchanges
    function calculateExchangeGoldValue() {
        if (!$('#goldExchangeCheckbox').is(':checked')) return 0;
        
        return calculateTotalExchangeValue();
    }


        // Get all exchange data for form submission
    function getAllExchangeData() {
        const exchangeData = [];
        
        $('#goldExchangeList .gold-exchange-row').each(function() {
            const $row = $(this);
            const goldRateId = $row.find('.exchange-gold-rate').val();
            const weight = parseFloat($row.find('.exchange-gold-weight').val()) || 0;
            const amount = parseFloat($row.find('.exchange-gold-value').val()) || 0;
            
            if (weight > 0) {
                exchangeData.push({
                    gold_rate_id: goldRateId,
                    weight: weight,
                    amount: amount
                });
            }
        });
        
        return exchangeData;
    }



    // ============== CUSTOMER & ADVANCE FUNCTIONS =============
// Update manual gold amount input handler
$(document).on('input change', '#usedGoldAmount', function() {
    const newManualAmount = parseFloat($(this).val()) || 0;
    
    // Validate
    if (newManualAmount < 0) {
        $(this).val('0.00');
        toastr.error('Amount cannot be negative');
        return;
    }
    
    // Check if manual input is allowed
    if ($(this).prop('readonly')) {
        toastr.warning('This field is auto-calculated based on gold rate');
        return;
    }

    // Update the hidden manual amount field IMMEDIATELY
    $('#usedGoldManualAmount').val(newManualAmount.toFixed(2));
    
    // Recalculate gold advance if it's being used
    if (currentAdvanceUsage.gold.used) {
        // Get total product weight + wastage weight
        let totalProductGrams = 0;
        $productsTable.find('tr').each(function() {
            const weight = parseFloat($(this).find('.weight-input').val()) || 0;
            const wastageWeight = parseFloat($(this).find('.wastage-input').val()) || 0;
            const stoneWeight = parseFloat($(this).find('.stone-input').val()) || 0;
            totalProductGrams += (weight + wastageWeight - stoneWeight);
        });
        
        const adGoldGrams = currentAdvanceUsage.gold.grams;
        const goldRate = currentAdvanceUsage.gold.rate;
        
        // Calculate new total amount
        // Calculate new total amount
        let newGoldAmount = 0;
        if (adGoldGrams > totalProductGrams) {
            // AD gold is MORE than product grams
            // Use user-entered rate if available, otherwise use stored rate
            const userEnteredRate = parseFloat($('#GoldRatePerGram').val()) || 0;
            const effectiveRate = userEnteredRate > 0 ? userEnteredRate : goldRate;
            
            // Calculate for product grams using effective gold rate
            const autoAmount = totalProductGrams * effectiveRate;
            newGoldAmount = autoAmount + newManualAmount;
            
            // Update hidden auto amount field as well
            $('#usedGoldAutoAmount').val(autoAmount.toFixed(2));
        } else {
            // Use custom rate if available
            const userEnteredRate = parseFloat($('#GoldRatePerGram').val()) || 0;
            const effectiveRate = userEnteredRate > 0 ? userEnteredRate : goldRate;
            
            newGoldAmount = adGoldGrams * effectiveRate;
            $('#usedGoldAutoAmount').val(newGoldAmount.toFixed(2));
        }
        
        const oldGoldAmount = currentAdvanceUsage.gold.amount;
        
        currentAdvanceUsage.gold.amount = newGoldAmount;
        currentAdvanceUsage.total_amount = currentAdvanceUsage.cash.amount + newGoldAmount;
        $advanceUsed.val(currentAdvanceUsage.total_amount.toFixed(2));
        
        // Recalculate all totals and balance
        calculateBalanceForPaymentMethod();
        
        // Update the info message with new values
        if (adGoldGrams > totalProductGrams) {
            const autoAmount = totalProductGrams * goldRate;
            const excessGrams = adGoldGrams - totalProductGrams;
            
            showGoldCalculationInfo(`
                <div class="card border-info shadow-sm">
                    <div class="card-body p-3 text-dark">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total AD Gold</span>
                            <span class="font-weight-bold text-primary">${adGoldGrams.toFixed(3)} g</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Product Weight (incl. wastage)
                                <small class="text-muted">(${totalProductGrams.toFixed(3)} g × Rs ${goldRate.toFixed(2)})</small>
                            </span>
                            <span class="font-weight-bold text-success">Rs ${autoAmount.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Excess Gold <small class="text-warning d-block">Enter price manually below</small></span>
                            <span class="font-weight-bold text-warning">${excessGrams.toFixed(3)} g</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">Total Amount</span>
                            <span class="font-weight-bold text-success">Rs ${newGoldAmount.toFixed(2)}</span>
                        </div>
                        <small class="text-muted d-block mt-2">(Auto: Rs ${autoAmount.toFixed(2)} + Manual: Rs ${newManualAmount.toFixed(2)})</small>
                    </div>
                </div>
            `);
        }
        
        // Show feedback if amount changed significantly
        if (Math.abs(oldGoldAmount - newGoldAmount) > 0.01) {
            showGoldAmountUpdateFeedback(oldGoldAmount, newGoldAmount);
        }
    }
});

// Add listener for product weight AND wastage changes to recalculate gold advance
$(document).on('input change', '.weight-input, .wastage-input, .stone-input', function() {
    const row = $(this).closest('tr');
    updateRowCalculations(row);
    
    // Recalculate gold advance if it's being used
    if (currentAdvanceUsage.gold.used) {
        const newGoldAmount = calculateSmartGoldAdvanceAmount();
        currentAdvanceUsage.gold.amount = newGoldAmount;
        currentAdvanceUsage.total_amount = currentAdvanceUsage.cash.amount + newGoldAmount;
        $advanceUsed.val(currentAdvanceUsage.total_amount.toFixed(2));
        calculateBalanceForPaymentMethod();
    }
});

// Provide visual feedback when gold amount is updated
function showGoldAmountUpdateFeedback(oldAmount, newAmount) {
    const $input = $('#usedGoldAmount');
    
    // Add highlight effect
    $input.addClass('gold-edit-highlight');
    setTimeout(() => {
        $input.removeClass('gold-edit-highlight');
    }, 500);
    
    // Optional: Show subtle notification
    const difference = newAmount - oldAmount;
    const changeText = difference > 0 
        ? `+Rs ${Math.abs(difference).toFixed(2)}` 
        : `-Rs ${Math.abs(difference).toFixed(2)}`;
    
    // Update badge color temporarily to show change
    const $badge = $('#used_gold_advance_display');
    const originalClass = $badge.attr('class');
    
    if (difference > 0) {
        $badge.removeClass('badge-warning').addClass('badge-success');
    } else if (difference < 0) {
        $badge.removeClass('badge-warning').addClass('badge-danger');
    }
    
    setTimeout(() => {
        $badge.attr('class', originalClass);
    }, 1000);
}

// Add highlight animation CSS class
if (!$('#gold-highlight-css').length) {
    $('<style id="gold-highlight-css">')
        .text(`
            .gold-edit-highlight {
                animation: goldPulse 0.5s ease-in-out;
            }
            
            @keyframes goldPulse {
                0%, 100% { 
                    background-color: #fff3cd; 
                    transform: scale(1);
                }
                50% { 
                    background-color: #28a745; 
                    color: white;
                    transform: scale(1.02);
                }
            }
        `)
        .appendTo('head');
}

// Focus and select all on click for easy editing
$(document).on('click', '#usedGoldAmount', function() {
    $(this).select();
});

// Format on blur
$(document).on('blur', '#usedGoldAmount', function() {
    const value = parseFloat($(this).val()) || 0;
    $(this).val(value.toFixed(2));
});

///////////////////////////////////////////////////////////////////////////

    
    //  manual customer toggle
    $('#add_manual_customer').on('change', function() {
        const isManual = $(this).is(':checked');
        
        if (isManual) {
            $('.customer_select_div').hide();
            $('#manual_customer').show().prop('required', true);
            $customerSelect.prop('required', false);
            $customerAdvanceBalance.text('Rs 0.00');
            $('#useAdvanceBtn').prop('disabled', true);
        } else {
            $('.customer_select_div').show();
            $('#manual_customer').hide().prop('required', false);
            $customerSelect.prop('required', true);
            $('#useAdvanceBtn').prop('disabled', false);
            
            const customerId = $customerSelect.val();
            if (customerId) {
                loadCustomerAdvanceBalance(customerId);
            }
        }
    });

    function toggleCustomerInfoSection(customerId) {
        const $customerInfoSection = $('#customerInfosection');
        
        if (customerId !== '1' && customerId !== 1) {
            $customerInfoSection.show();
        } else {
            $customerInfoSection.hide();
        }

    }


    //  customer change handler
    $customerSelect.on('change', function() {
      const customerId = $(this).val();
      
      // Toggle customer info section visibility based on customer ID
      toggleCustomerInfoSection(customerId);
      
      if (customerId && !$('#add_manual_customer').is(':checked')) {
          // Only load advance data if customer is not ID 1
          if (customerId !== '1' && customerId !== 1) {
              loadCustomerBalances(customerId);
              loadCustomerReservations(customerId);
          }
      } else {
          $customerAdvanceBalance.text('Rs 0.00');
          $('#customerGoldAdvanceBalance').text('0 g');
          $('#reservationInfo').hide();
      }
  });
    
    // Cached AJAX functions to prevent duplicate requests
    const ajaxCache = new Map();
    const CACHE_DURATION = 30000; // 30 seconds
    
    function cachedAjax(url, options = {}) {
        const cacheKey = url + JSON.stringify(options);
        const cached = ajaxCache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < CACHE_DURATION) {
            return Promise.resolve(cached.data);
        }
        
        return $.ajax(url, options).then(response => {
            ajaxCache.set(cacheKey, {
                data: response,
                timestamp: Date.now()
            });
            return response;
        });
    }
    

function loadCustomerBalances(customerId) {
    // Show loading indicators
    $('#customerAdvanceBalance').text('Loading...');
    $('#customerGoldAdvanceBalance').text('Loading...');

    cachedAjax(`/customer/cash-gold/${customerId}/advance-balance`, {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).then(response => {
        if (response.success) {
            $('#customerAdvanceBalance').text(`Rs ${parseFloat(response.cash_balance).toFixed(2)}`);
            $('#customerGoldAdvanceBalance').text(`${parseFloat(response.gold_balance).toFixed(3)} g`);
        } else {
            $('#customerAdvanceBalance').text('Rs 0.00');
            $('#customerGoldAdvanceBalance').text('0.000 g');
        }
    }).catch(() => {
        $('#customerAdvanceBalance').text('Rs 0.00');
        $('#customerGoldAdvanceBalance').text('0.000 g');
    });
}


    function loadCustomerReservations(customerId) {
        if (!customerId) {
            $('#reservationInfo').hide();
            return;
        }
        
        cachedAjax(`/customer/${customerId}/reservations`, {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        }).then(response => {
            if (response.success) {
                displayReservations(response.reservations);
            } else {
                $('#reservationInfo').hide();
            }
        }).catch(() => {
            $('#reservationInfo').hide();
        });
    }
    
    //  reservation display
    function displayReservations(reservations) {
        const pendingReservations = reservations.filter(r => r.status === 'pending' || r.status === 'partial');
        const pendingCount = pendingReservations.length;
        
        $('#pendingReservationsCount').text(pendingCount);
        
        if (pendingCount > 0) {
            $('#reservationInfo').show();
            
            const $reservationsList = $('#reservationsList');
            $reservationsList.empty();
            
            // Build HTML efficiently
            const reservationsHTML = pendingReservations.map(reservation => {
                const deliveryDate = new Date(reservation.delivery_date).toLocaleDateString();
                const remainingAmount = reservation.total_amount - reservation.paid_amount;
                
                return `
                    <div class="reservation-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="font-weight-bold">ID: #${reservation.id}</div>
                                <div class="text-muted small">
                                    ${reservation.products_count} product(s) • 
                                    Delivery: ${deliveryDate}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">Rs ${parseFloat(reservation.total_amount).toFixed(2)}</div>
                                <div class="reservation-status ${reservation.status}">${reservation.status}</div>
                            </div>
                        </div>
                        ${remainingAmount > 0 ? `
                            <div class="mt-1 text-danger small">
                                Remaining: Rs ${remainingAmount.toFixed(2)}
                            </div>
                        ` : ''}
                    </div>`;
            }).join('');
            
            $reservationsList.html(reservationsHTML);
        } else {
            $('#reservationInfo').hide();
        }
    }
    
    // ============== ADVANCE FUNCTIONS ==============
    
    function clearUsedAdvance() {
        $usedAdvanceSection.hide();
        $('#used_advance_display').text('Rs 0.00');
        $advanceUsed.val('0.00');
    }
    
    function recalculateAdvanceBalance(customerId) {
        const currentOrderTotal = parseFloat($totalElement.val()) || 0;
        const currentAdvanceInOrder = parseFloat($advanceInput.val()) || 0;
        const advanceUsedFromCustomer = parseFloat($advanceUsed.val()) || 0;
        
        if (currentOrderTotal < currentAdvanceInOrder) {
            const excessAdvance = currentAdvanceInOrder - currentOrderTotal;
            const newAdvanceInOrder = Math.max(0, currentOrderTotal);
            const newAdvanceUsed = Math.max(0, advanceUsedFromCustomer - excessAdvance);
            
            $advanceInput.val(newAdvanceInOrder.toFixed(2)).trigger('input');
            $advanceUsed.val(newAdvanceUsed.toFixed(2));
            
            loadCustomerAdvanceBalance(customerId);
            
            if (excessAdvance > 0) {
                toastr.info(`Rs ${excessAdvance.toFixed(2)} advance has been returned to customer balance`);
            }
        }
    }
    
    // ============== FORM HANDLERS ==============
    

    // Cash + Gold Advance Function ===========
    $(document).on('click', '.add-carat-row', function() {
        const $row = $(this).closest('.gold-carat-row');
        const $newRow = $row.clone();

        $newRow.find('input').val('');
        $newRow.find('select').prop('selectedIndex', 0); // Reset select to first option
        $newRow.find('.add-carat-row')
            .removeClass('btn-success add-carat-row')
            .addClass('btn-danger remove-carat-row')
            .text('–');

        $('#goldCaratGroupList').append($newRow);
    });

    $(document).on('click', '.remove-carat-row', function() {
        $(this).closest('.gold-carat-row').remove();
    });

   $('#addCashGoldAdvanceForm').on('submit', function(e) {
    e.preventDefault();

    const customerId = $customerSelect.val();
    if (!customerId || $('#add_manual_customer').is(':checked')) {
        toastr.error('Please select a customer from the dropdown first');
        return;
    }

    // Collect carat-wise gold data with proper validation
    const goldEntries = [];
    let hasValidGoldEntries = false;
    let hasInvalidGoldGram = false;  // <-- Make sure this is declared!

    $('#goldCaratGroupList .gold-carat-row').each(function() {
        const caratId = $(this).find('.gold-carat-select').val();
        const gram = $(this).find('.gold-gram-input').val();
        const gramValue = parseFloat(gram);

        if (gramValue > 1000) {
            hasInvalidGoldGram = true;
        }

        if (caratId && gram && gramValue > 0) {
            goldEntries.push({
                carat_id: parseInt(caratId),
                gram: gramValue
            });
            hasValidGoldEntries = true;
        }
    });

    if (hasInvalidGoldGram) {
        toastr.error('Please enter a value below 1000g');
        return;
    }

    if (!hasValidGoldEntries) {
        toastr.error('Please add at least one gold entry with valid carat and gram values');
        return;
    }

    const cash_amount = parseFloat($('input[name="cash_amount"]').val());
    if (!cash_amount || cash_amount <= 0) {
        toastr.error('Please enter a valid cash amount');
        return;
    }

    // All validations passed, now disable button and show processing text
    const $submitBtn = $(this).find('button[type="submit"]');
    const originalText = $submitBtn.text();
    $submitBtn.prop('disabled', true).text('Processing...');

    const formData = {
        cash_amount: cash_amount,
        cash_gold_order_no: $('input[name="cash_gold_order_no"]').val(),
        cash_gold_note: $('textarea[name="cash_gold_note"]').val(),
        gold_entries: goldEntries,
        routeName: 'dashboard',
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: `/customer/cash-gold/${customerId}/advance`,
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': formData._token },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Advance added successfully');
                $('#addCashGoldAdvanceModal').modal('hide');
                if (response.new_cash_balance !== undefined) {
                    $('#customerAdvanceBalance').text(`Rs ${parseFloat(response.new_cash_balance).toFixed(2)}`);
                }
                if (response.new_gold_balance !== undefined) {
                    $('#customerGoldAdvanceBalance').text(`${parseFloat(response.new_gold_balance).toFixed(3)} g`);
                }
                $('#addCashGoldAdvanceForm')[0].reset();
                $('#goldCaratGroupList .gold-carat-row:not(:first)').remove();
                $('#goldCaratGroupList .gold-carat-row:first input').val('');
                $('#goldCaratGroupList .gold-carat-row:first select').prop('selectedIndex', 0);

                if (response.print_url) {
                    window.location.href = response.print_url;
                }
            } else {
                toastr.error(response.message || 'Failed to add advance');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response?.errors) {
                Object.values(response.errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(response?.message || 'Failed to add advance');
            }
        },
        complete: function() {
            $submitBtn.prop('disabled', false).text(originalText);
        }
    });
});



    //  advance form submission
    $('#addAdvanceForm').on('submit', function(e) {
        e.preventDefault();
        
        const customerId = $customerSelect.val();
        if (!customerId || $('#add_manual_customer').is(':checked')) {
            toastr.error('Please select a customer from the dropdown first');
            return;
        }
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        const routeName = 'dashboard';
        $submitBtn.prop('disabled', true).text('Processing...');
        
        const formData = {
            amount: parseFloat($('input[name="amount"]').val()),
            order_no: $('input[name="order_no"]').val(),
            notes: $('textarea[name="notes"]').val(),
            routeName: routeName,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/customer/${customerId}/advance`,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Advance added successfully');
                    
                    if (response.new_balance !== undefined) {
                        $customerAdvanceBalance.text(`Rs ${parseFloat(response.new_balance).toFixed(2)}`);
                    }
                    
                    $('#addAdvanceModal').modal('hide');
                    $('#addAdvanceForm')[0].reset();
                    
                    if (response.print_url) {
                        window.location.href = response.print_url;
                    }
                } else {
                    toastr.error(response.message || 'Failed to add advance');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    Object.keys(response.errors).forEach(key => {
                        toastr.error(response.errors[key][0]);
                    });
                } else {
                    toastr.error(response?.message || 'Failed to add advance');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    //  gold advance form submission
   $('#addGoldAdvanceForm').on('submit', function(e) {
    e.preventDefault();
    
    const customerId = $customerSelect.val();
    if (!customerId || $('#add_manual_customer').is(':checked')) {
        toastr.error('Please select a customer from the dropdown first');
        return;
    }

    const gramValue = parseFloat($('input[name="gold_amount"]').val());

    if (isNaN(gramValue) || gramValue <= 0) {
        toastr.error('Please enter a valid gold amount');
        return;
    }

    if (gramValue > 1000) {
        toastr.error('Please enter a gold amount below 1000g');
        return; // <-- stop here
    }

    const $submitBtn = $(this).find('button[type="submit"]');
    const originalText = $submitBtn.text();
    const routeName = 'dashboard';

    $submitBtn.prop('disabled', true).text('Processing...');

    const formData = {
        gold_amount: gramValue,
        gold_rate: $('#gold_rate').val(),
        order_no: $('input[name="gold_order_no"]').val(),
        note: $('textarea[name="note"]').val(),
        routeName: routeName,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    $.ajax({
        url: `/customer/gold/${customerId}/advance`,
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Gold Advance added successfully');

                if (response.new_gold_balance !== undefined) {
                    $('#customerGoldAdvanceBalance').text(`${parseFloat(response.new_gold_balance).toFixed(3)} g`);
                }

                $('#addGoldAdvanceModal').modal('hide');
                $('#addGoldAdvanceForm')[0].reset();

                if (response.print_url) {
                    window.location.href = response.print_url;
                }
            } else {
                toastr.error(response.message || 'Failed to add Gold advance');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                Object.keys(response.errors).forEach(key => {
                    toastr.error(response.errors[key][0]);
                });
            } else {
                toastr.error(response?.message || 'Failed to add gold advance');
            }
        },
        complete: function() {
            $submitBtn.prop('disabled', false).text(originalText);
        }
    });
});

    
    // ============== USE ADVANCE FUNCTIONALITY ==============
    
   let customerAdvanceData = null;

$('#useAdvanceBtn').on('click', function() {
    const customerId = $customerSelect.val();
    const orderTotal = parseFloat($totalElement.val()) || 0;
    const currentAdvance = parseFloat($advanceInput.val()) || 0;
    
    if (!customerId || $('#add_manual_customer').is(':checked')) {
        toastr.error('Please select a customer from the dropdown first');
        return;
    }
    
    if (orderTotal <= 0) {
        toastr.error('Please add products to the order first');
        return;
    }
    
    // Fetch advance data from controller
    fetchCustomerAdvanceData(customerId);
});

// Updated fetchCustomerAdvanceData function to include gold rate
function fetchCustomerAdvanceData(customerId) {
    $.ajax({
        url: `/pos/advance/${customerId}`,
        method: 'GET',
        success: function(response) {
            console.log('Advance data response:', response); // Debug log
            if (response.success) {
                customerAdvanceData = response.data;
                
                // Store general gold rate data for calculations
                if (response.data.general_gold_rate_data) {
                    customerAdvanceData.general_gold_rate = response.data.general_gold_rate_data.rate;
                    customerAdvanceData.general_gold_rate_id = response.data.general_gold_rate_data.id;
                }
                
                showUseAdvanceModal();
            } else {
                toastr.error(response.message || 'Error fetching advance data');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            toastr.error('Error connecting to server');
        }
    });
}

// Fetch order-specific advance details
function fetchOrderAdvanceDetails(customerId, orderNo) {
    $.ajax({
        url: `/pos/advance/${customerId}/order/${orderNo}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateOrderAdvanceDisplay(response.data);
            } else {
                toastr.error(response.message || 'Error fetching order details');
            }
        },
        error: function() {
            toastr.error('Error connecting to server');
        }
    });
}

// Updated showUseAdvanceModal function to display gold rates properly
function showUseAdvanceModal() {
    const orderTotal = parseFloat($totalElement.val()) || 0;
    const currentAdvance = parseFloat($advanceInput.val()) || 0;
    const remainingBalance = orderTotal - currentAdvance;
    
    // Ensure numeric values and handle null/undefined
    const generalCashAdvance = parseFloat(customerAdvanceData.general_cash_advance) || 0;
    const generalGoldAdvance = parseFloat(customerAdvanceData.general_gold_advance) || 0;
    const generalGoldRate = parseFloat(customerAdvanceData.general_gold_rate) || 0;
    const generalGoldRateName = customerAdvanceData.general_gold_rate_name || '-';
    
    // Generate order number options
    let orderOptions = '<option value="">Select Order (Optional)</option>';
    if (customerAdvanceData.order_numbers && customerAdvanceData.order_numbers.length > 0) {
        customerAdvanceData.order_numbers.forEach(orderNo => {
            orderOptions += `<option value="${orderNo}">${orderNo}</option>`;
        });
    }
    
    const modalHtml = `
        <div class="modal fade" id="useAdvanceModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Use Customer Advance - ${customerAdvanceData.customer_name}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Order Selection -->
                        <div class="form-group">
                            <label for="orderSelect">Select Order (Optional):</label>
                            <select class="form-control" id="orderSelect">
                                ${orderOptions}
                            </select>
                        </div>
                        
                        <!-- General Advances Display -->
                        <div id="generalAdvanceSection">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>General Cash Advance</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Available: Rs <span id="generalCashAmount">${generalCashAdvance.toFixed(2)}</span></p>
                                            <div class="form-group">
                                                <label for="useCashAmount">Use Cash Amount:</label>
                                                <input type="number" class="form-control" id="useCashAmount"
                                                      max="${Math.min(generalCashAdvance, remainingBalance)}" 
                                                      min="0" step="0.01" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>General Gold Advance</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Available: <span id="generalGoldAmount">${generalGoldAdvance.toFixed(3)}</span> grams</p>
                                            <p class="text-muted small">Rate: ${generalGoldRateName}</p>
                                            <div class="form-group">
                                                <label for="useGoldAmount">Use Gold Amount (grams):</label>
                                                <input type="number" class="form-control" id="useGoldAmount"
                                                      max="${generalGoldAdvance}" 
                                                      min="0" step="0.001" value="0">

                                                <label for="GoldRatePerGram">Enter Gold Rate Per Gram (Optional)</label>
                                                <input type="number" class="form-control" id="GoldRatePerGram" 
                                                    step="0.01" min="0" placeholder="Leave empty to use default rate">
                                                <small class="text-muted">If specified, this rate will be used instead of the stored rate</small>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order-Specific Advances Display (Hidden by default) -->
                        <div id="orderAdvanceSection" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Order Cash Advance</h6>
                                        </div>
                                        <div class="card-body" id="orderCashContent">
                                            <!-- Will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Order Gold Advance</h6>
                                        </div>
                                        <div class="card-body" id="orderGoldContent">
                                            <!-- Will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Summary -->
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <strong>Summary:</strong><br>
                                Order Total: Rs ${orderTotal.toFixed(2)}<br>
                                Current Payment: Rs ${currentAdvance.toFixed(2)}<br>
                                Remaining Balance: Rs ${remainingBalance.toFixed(2)}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmUseAdvance">Apply Advance</button>
                    </div>
                </div>
            </div>
        </div>`;
    
    $('#useAdvanceModal').remove();
    $('body').append(modalHtml);
    $('#useAdvanceModal').modal('show');
    
    // Handle order selection change
    $('#orderSelect').on('change', function() {
        const selectedOrder = $(this).val();
        if (selectedOrder) {
            $('#generalAdvanceSection').hide();
            $('#orderAdvanceSection').show();
            fetchOrderAdvanceDetails($customerSelect.val(), selectedOrder);
            // Reset general advance inputs
            $('#useCashAmount, #useGoldAmount').val(0);
        } else {
            $('#generalAdvanceSection').show();
            $('#orderAdvanceSection').hide();
            // Reset order advance inputs
            $('#useOrderCashAmount, #useOrderGoldAmount').val(0);
        }
    });
    
    // Handle confirmation
    $('#confirmUseAdvance').on('click', function() {
        const selectedOrder = $('#orderSelect').val();
        let cashAmount = 0;
        let goldAmount = 0;
        let isValid = true;
        
        if (selectedOrder) {
            // Using order-specific advance
            cashAmount = parseFloat($('#useOrderCashAmount').val()) || 0;
            goldAmount = parseFloat($('#useOrderGoldAmount').val()) || 0;
            
            // Validate order advance amounts
            const maxOrderCash = parseFloat($('#orderCashAmount').text()) || 0;
            const maxOrderGold = parseFloat($('#orderGoldAmount').text()) || 0;
            
            if (cashAmount > maxOrderCash) {
                toastr.error(`Cannot use more than Rs ${maxOrderCash.toFixed(2)} from order cash advance`);
                isValid = false;
            }
            
            if (goldAmount > maxOrderGold) {
                toastr.error(`Cannot use more than ${maxOrderGold.toFixed(4)} grams from order gold advance`);
                isValid = false;
            }
        } else {
            // Using general advance
            cashAmount = parseFloat($('#useCashAmount').val()) || 0;
            goldAmount = parseFloat($('#useGoldAmount').val()) || 0;
            
            // Validate general advance amounts
            if (cashAmount > generalCashAdvance) {
                toastr.error(`Cannot use more than Rs ${generalCashAdvance.toFixed(2)} from general cash advance`);
                isValid = false;
            }
            
            if (goldAmount > generalGoldAdvance) {
                toastr.error(`Cannot use more than ${generalGoldAdvance.toFixed(4)} grams from general gold advance`);
                isValid = false;
            }
        }
        
        if (cashAmount <= 0 && goldAmount <= 0) {
            toastr.error('Please enter at least one advance amount to use');
            isValid = false;
        }
        
        if (!isValid) return;
        
        // Apply advance
        applyAdvanceToOrder(cashAmount, goldAmount, selectedOrder);
        $('#useAdvanceModal').modal('hide');
    });
}

// Clear custom gold rate when modal closes
$('#useAdvanceModal').on('hidden.bs.modal', function() {
    $('#GoldRatePerGram').val('');
});

function calculateSmartGoldAdvanceAmount() {
    if (!currentAdvanceUsage.gold.used) return 0;
    
    // Get total product weight + wastage weight in grams
    let totalProductGrams = 0;
    $productsTable.find('tr').each(function() {
        const weight = parseFloat($(this).find('.weight-input').val()) || 0;
        const wastageWeight = parseFloat($(this).find('.wastage-input').val()) || 0;
        const stoneWeight = parseFloat($(this).find('.stone-input').val()) || 0;
        totalProductGrams += (weight + wastageWeight - stoneWeight);
    });
    
    const adGoldGrams = currentAdvanceUsage.gold.grams;
    const goldRate = currentAdvanceUsage.gold.rate;
    
    let calculatedAmount = 0;
    
    if (adGoldGrams > totalProductGrams) {
        // AD gold is MORE than product grams
        // Use user-entered rate if available, otherwise use stored rate
        const userEnteredRate = parseFloat($('#GoldRatePerGram').val()) || 0;
        const effectiveRate = userEnteredRate > 0 ? userEnteredRate : goldRate;
        
        // Calculate for product grams using effective gold rate
        const autoCalculatedAmount = totalProductGrams * effectiveRate;
        
        // Get manual input for the EXCESS gold amount
        const manualGoldAmount = parseFloat($('#usedGoldAmount').val()) || 0;
        
        // Total = Auto-calculated (for product grams) + Manual entry (for excess grams)
        calculatedAmount = autoCalculatedAmount + manualGoldAmount;
        
        // Calculate excess grams
        const excessGrams = adGoldGrams - totalProductGrams;
        
        // Enable manual input and show info
        $('#usedGoldAmount')
            .prop('readonly', false)
            .css('background-color', '#fff3cd')
            .attr('placeholder', '0.00');
        
        // Show detailed info message with custom rate if used
        showGoldCalculationInfo(`
            <div class="card border-info shadow-sm">
                <div class="card-body p-3 text-dark">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total AD Gold</span>
                        <span class="font-weight-bold text-primary">
                            ${adGoldGrams.toFixed(3)} g
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            Product Weight (incl. wastage)
                            <small class="text-muted">
                                (${totalProductGrams.toFixed(3)} g × Rs ${effectiveRate.toFixed(2)})
                            </small>
                        </span>
                        <span class="font-weight-bold text-success">
                            Rs ${autoCalculatedAmount.toFixed(2)}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            Excess Gold
                            <small class="text-warning d-block">
                                Enter price manually below
                            </small>
                        </span>
                        <span class="font-weight-bold text-warning">
                            ${excessGrams.toFixed(3)} g
                        </span>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">
                            Total Amount
                        </span>
                        <span class="font-weight-bold text-success">
                            Rs ${calculatedAmount.toFixed(2)}
                        </span>
                    </div>

                    <small class="text-muted d-block mt-2">
                        (Auto: Rs ${autoCalculatedAmount.toFixed(2)} + Manual: Rs ${manualGoldAmount.toFixed(2)})
                    </small>
                </div>
            </div>
        `);
        
        // Update the auto amount hidden field
        $('#usedGoldAutoAmount').val(autoCalculatedAmount.toFixed(2));

    } else {
        // AD gold is EQUAL or LESS than product grams
        // Use user-entered rate if available, otherwise use stored rate
        const userEnteredRate = parseFloat($('#GoldRatePerGram').val()) || 0;
        const effectiveRate = userEnteredRate > 0 ? userEnteredRate : goldRate;
        
        // Calculate normally using effective gold rate
        calculatedAmount = adGoldGrams * effectiveRate;
        
        // Update manual input but keep it readonly
        $('#usedGoldAmount')
            .val(calculatedAmount.toFixed(2))
            .prop('readonly', true)
            .css('background-color', '#e9ecef')
            .attr('placeholder', 'Auto-calculated');
        
        // Update the auto amount hidden field
        $('#usedGoldAutoAmount').val(calculatedAmount.toFixed(2));
        
        // Show info if custom rate is used
        if (userEnteredRate > 0) {
            showGoldCalculationInfo(`
                <div class="alert alert-info p-2 mb-2">
                    <span>${adGoldGrams.toFixed(3)} g × Rs ${effectiveRate.toFixed(2)} = Rs ${calculatedAmount.toFixed(2)}</span>
                </div>
            `);
        } else {
            hideGoldCalculationInfo();
        }
    }
    
    return calculatedAmount;
}

// Listen for changes to custom gold rate input
$(document).on('input change', '#GoldRatePerGram, #OrderGoldRatePerGram', function() {
    if (currentAdvanceUsage.gold.used) {
        const newGoldAmount = calculateSmartGoldAdvanceAmount();
        currentAdvanceUsage.gold.amount = newGoldAmount;
        
        const userEnteredRate = parseFloat($(this).val()) || 0;
        if (userEnteredRate > 0) {
            currentAdvanceUsage.gold.rate = userEnteredRate;
            $('#usedGoldRate').val(userEnteredRate.toFixed(2));
        }
        
        currentAdvanceUsage.total_amount = currentAdvanceUsage.cash.amount + newGoldAmount;
        $advanceUsed.val(currentAdvanceUsage.total_amount.toFixed(2));

        calculateBalanceForPaymentMethod();
        
        $('#used_gold_advance_display').text(`-${currentAdvanceUsage.gold.grams.toFixed(3)}g`);
    }
});

// Show calculation info message
function showGoldCalculationInfo(message) {
    // Remove existing info
    $('#goldCalculationInfo').remove();
    
    // Add new info message
    const infoHtml = `
        <div id="goldCalculationInfo" class="alert alert-info mt-2 mb-0" style="font-size: 11px; padding: 8px;">
            <i class="fas fa-info-circle mr-1"></i>
            ${message}
        </div>`;
    
    $('#usedGoldAmount').after(infoHtml);
}

// Hide calculation info message
function hideGoldCalculationInfo() {
    $('#goldCalculationInfo').remove();
}
    
 // Update the order advance display to include gold rate information
function updateOrderAdvanceDisplay(orderData) {
    const orderTotal = parseFloat($totalElement.val()) || 0;
    const currentAdvance = parseFloat($advanceInput.val()) || 0;
    const remainingBalance = orderTotal - currentAdvance;
    
    // Store order gold rate for later use
    window.currentOrderGoldRate = parseFloat(orderData.gold_rate) || 0;
    window.currentOrderGoldRateId = orderData.gold_rate_id || '';
    
    // Ensure numeric values
    const cashAdvance = parseFloat(orderData.cash_advance) || 0;
    const goldAdvance = parseFloat(orderData.gold_advance) || 0;
    const goldRate = parseFloat(orderData.gold_rate) || 0;
    const goldRateName = orderData.gold_rate_name || '-';
    window.currentOrderGoldRateName = goldRateName; 
    
    // Update cash advance section
    const maxUsableCash = Math.min(cashAdvance, remainingBalance);
    $('#orderCashContent').html(`
        <p>Available: Rs <span id="orderCashAmount">${cashAdvance.toFixed(2)}</span></p>
        <div class="form-group">
            <label for="useOrderCashAmount">Use Cash Amount:</label>
            <input type="number" class="form-control" id="useOrderCashAmount"
                  max="${maxUsableCash}" min="0" step="0.01" value="0">
        </div>
    `);
    
    // Update gold advance section with rate information
    $('#orderGoldContent').html(`
        <p>Available: <span id="orderGoldAmount">${goldAdvance.toFixed(4)}</span> grams</p>
        <p class="text-muted small">Rate: ${goldRateName}</p>
        <div class="form-group">
            <label for="useOrderGoldAmount">Use Gold Amount (grams):</label>
            <input type="number" class="form-control" id="useOrderGoldAmount"
                max="${goldAdvance}" min="0" step="0.001" value="0">

            <label for="OrderGoldRatePerGram">Enter Gold Rate Per Gram (Optional)</label>
            <input type="number" class="form-control" id="OrderGoldRatePerGram" step="0.01" min="0" placeholder="Leave empty to use default rate">
            <small class="text-muted">If specified, this rate will be used instead of the stored rate</small>
        </div>
    `);
}

let currentAdvanceUsage = {
    cash: {
        amount: 0,
        order_no: '',
        type: '', // 'general' or 'order_specific'
        used: false
    },
    gold: {
        grams: 0,
        amount: 0,
        rate: 0,
        rate_id: '',
        order_no: '',
        type: '', // 'general' or 'order_specific'
        used: false
    },
    total_amount: 0
};

// Updated apply advance to order function
function applyAdvanceToOrder(cashAmount, goldAmount, orderNo = null) {
    const orderTotal = parseFloat($totalElement.val()) || 0;
    let totalAdvanceValue = 0;
    
    // Reset current usage
    currentAdvanceUsage = {
        cash: { amount: 0, order_no: '', type: '', used: false },
        gold: { grams: 0, amount: 0, rate: 0, rate_id: '', order_no: '', type: '', used: false },
        total_amount: 0
    };
    
    // Apply cash advance (unchanged)
    if (cashAmount > 0) {
        currentAdvanceUsage.cash = {
            amount: cashAmount,
            order_no: orderNo || '',
            type: orderNo ? 'order_specific' : 'general',
            used: true
        };
        totalAdvanceValue += cashAmount;
        
        $('#used_cash_advance_display').text(`- Rs ${cashAmount.toFixed(2)}`);
        $('#cashAdvanceDetails').text(orderNo ? `Order: ${orderNo}` : 'General Cash Advance');
        $('#usedCashAdvanceRow').show();
        
        $('#usedCashAdvance').val(cashAmount.toFixed(2));
        $('#cashAdvanceOrderNo').val(orderNo || '');
    }
    
    // Apply gold advance - UPDATED SECTION (inside applyAdvanceToOrder function)
    if (goldAmount > 0) {
        let goldRate = 0;
        let goldRateId = '';
        
        // Get user-entered gold rate (highest priority)
        const userEnteredRate = orderNo 
            ? (parseFloat($('#OrderGoldRatePerGram').val()) || 0)  // Order-specific input
            : (parseFloat($('#GoldRatePerGram').val()) || 0);       // General input
        
        if (userEnteredRate > 0) {
            // Use the rate entered by user
            goldRate = userEnteredRate;
            // Keep the rate_id from database for reference
            if (orderNo) {
                goldRateId = window.currentOrderGoldRateId || '';
            } else {
                goldRateId = customerAdvanceData?.general_gold_rate_id || '';
            }
        } else if (orderNo) {
            goldRate = window.currentOrderGoldRate || 0;
            goldRateId = window.currentOrderGoldRateId || '';
        } else {
            if (customerAdvanceData && customerAdvanceData.general_gold_rate) {
                goldRate = parseFloat(customerAdvanceData.general_gold_rate) || 0;
                goldRateId = customerAdvanceData.general_gold_rate_id || '';
            } else {
                const defaultGoldRate = goldRatesData.find(rate => rate.is_default) || goldRatesData[0];
                goldRate = defaultGoldRate ? parseFloat(defaultGoldRate.rate) : 0;
                goldRateId = defaultGoldRate ? defaultGoldRate.id : '';
            }
        }
        
        // Store gold advance data FIRST
        currentAdvanceUsage.gold = {
            grams: goldAmount,
            amount: 0, // Will be calculated by calculateSmartGoldAdvanceAmount
            rate: goldRate,
            rate_id: goldRateId,
            order_no: orderNo || '',
            type: orderNo ? 'order_specific' : 'general',
            used: true
        };
        
        // Calculate smart gold amount (includes both auto + manual)
        const goldAmountValue = calculateSmartGoldAdvanceAmount();
        currentAdvanceUsage.gold.amount = goldAmountValue;
        totalAdvanceValue += goldAmountValue;
        
        // Calculate breakdown for display
        let totalProductGrams = 0;
        $productsTable.find('tr').each(function() {
            const weight = parseFloat($(this).find('.weight-input').val()) || 0;
            const wastageWeight = parseFloat($(this).find('.wastage-input').val()) || 0;
            const stoneWeight = parseFloat($(this).find('.stone-input').val()) || 0;
            totalProductGrams += (weight + wastageWeight - stoneWeight);
        });
        
        // Store breakdown for backend
        if (goldAmount > totalProductGrams) {
            const autoAmount = totalProductGrams * goldRate;
            const manualAmount = parseFloat($('#usedGoldAmount').val()) || 0;
            
            // Store these for backend processing
            $('#usedGoldAutoAmount').val(autoAmount.toFixed(2));
            $('#usedGoldManualAmount').val(manualAmount.toFixed(2));
            $('#usedGoldProductGrams').val(totalProductGrams.toFixed(3));
            $('#usedGoldExcessGrams').val((goldAmount - totalProductGrams).toFixed(3));
        } else {
            $('#usedGoldAutoAmount').val(goldAmountValue.toFixed(2));
            $('#usedGoldManualAmount').val('0.00');
            $('#usedGoldProductGrams').val(goldAmount.toFixed(3));
            $('#usedGoldExcessGrams').val('0.000');
        }
        
        // Update balance displays
        const currentCashBalanceText = $('#customerAdvanceBalance').text().replace(/[^\d.-]/g, '');
        const currentGoldBalanceText = $('#customerGoldAdvanceBalance').text().replace(/[^\d.-]/g, '');
        const currentCashBalance = parseFloat(currentCashBalanceText) || 0;
        const currentGoldBalance = parseFloat(currentGoldBalanceText) || 0;
        
        const updatedCashBalance = Math.max(currentCashBalance - cashAmount, 0);
        const updatedGoldBalance = Math.max(currentGoldBalance - goldAmount, 0);
        
        $('#customerAdvanceBalance').text(`Rs ${updatedCashBalance.toFixed(2)}`);
        $('#customerGoldAdvanceBalance').text(`${updatedGoldBalance.toFixed(3)} g`);
        
        // Update displays
        $('#used_gold_advance_display').text(`-${goldAmount.toFixed(3)}g`);
        $('#goldAdvanceDetails').text(orderNo ? `Order: ${orderNo} - ${window.currentOrderGoldRateName || '-'}` : `General Gold Advance - ${customerAdvanceData.general_gold_rate_name || '-'}`);
        $('#usedGoldAdvanceRow').show();
        
        // Update hidden inputs
        $('#usedGoldGrams').val(goldAmount.toFixed(3));
        $('#usedGoldRate').val(goldRate.toFixed(2));
        $('#usedGoldRateId').val(goldRateId);
        $('#goldAdvanceOrderNo').val(orderNo || '');
    }
    
    // Update total advance usage
    currentAdvanceUsage.total_amount = totalAdvanceValue;
    
    updateAdvanceDisplays();
    updateAdvanceUsageType();
    
    if (totalAdvanceValue > 0) {
        $usedAdvanceSection.show();
        $advanceUsed.val(totalAdvanceValue.toFixed(2));
    }
    
    debouncedCalculateTotal();
    $('#useAdvanceModal').modal('hide');
    
    // Success message
    const successParts = [];
    if (cashAmount > 0) successParts.push(`Rs ${cashAmount.toFixed(2)} cash`);
    if (goldAmount > 0) successParts.push(`${goldAmount.toFixed(3)}g gold (Rs ${goldAmountValue.toFixed(2)} value)`);
    
    const sourceText = orderNo ? `from Order ${orderNo}` : 'from general advance';
    toastr.success(`Applied ${successParts.join(' + ')} ${sourceText}`);
}

// Update advance displays
function updateAdvanceDisplays() {
    const totalUsed = currentAdvanceUsage.total_amount;
    
    // Update total used advance display
    $('#total_used_advance_display').text(`Rs ${totalUsed.toFixed(2)}`);
    
    // Show/hide individual advance rows
    $('#usedCashAdvanceRow').toggle(currentAdvanceUsage.cash.used);
    $('#usedGoldAdvanceRow').toggle(currentAdvanceUsage.gold.used);
}

// Update advance usage type for backend
function updateAdvanceUsageType() {
    let usageType = '';
    
    if (currentAdvanceUsage.cash.used && currentAdvanceUsage.gold.used) {
        usageType = 'both';
    } else if (currentAdvanceUsage.cash.used) {
        usageType = 'cash_only';
    } else if (currentAdvanceUsage.gold.used) {
        usageType = 'gold_only';
    }
    
    $('#advanceUsageType').val(usageType);
}


// Individual advance removal functions
$(document).on('click', '.remove-advance-btn', function() {
    const advanceType = $(this).data('type');
    removeSpecificAdvance(advanceType);
});

// Clear all advances
$(document).on('click', '#clearAllAdvancesBtn', function() {
    if (confirm('Are you sure you want to clear all used advances?')) {
        clearAllAdvances();
    }
});


// Updated removeSpecificAdvance function
function removeSpecificAdvance(type) {
    const customerId = $customerSelect.val();
    let removedAmount = 0;
    let removedText = '';
    
    if (type === 'cash' && currentAdvanceUsage.cash.used) {
        removedAmount = currentAdvanceUsage.cash.amount;
        removedText = `Rs ${removedAmount.toFixed(2)} cash advance`;
        
        // Reset cash advance
        currentAdvanceUsage.cash = {
            amount: 0, order_no: '', type: '', used: false
        };
        
        // Update hidden inputs
        $('#usedCashAdvance').val('0.00');
        $('#cashAdvanceOrderNo').val('');
        
        // Hide cash row
        $('#usedCashAdvanceRow').hide();
        
      } else if (type === 'gold' && currentAdvanceUsage.gold.used) {
      removedAmount = currentAdvanceUsage.gold.amount;
      const removedGrams = currentAdvanceUsage.gold.grams;
      removedText = `${removedGrams.toFixed(3)}g gold advance (Rs ${removedAmount.toFixed(2)} value)`;
      
      // Reset gold advance
      currentAdvanceUsage.gold = {
          grams: 0, amount: 0, rate: 0, rate_id: '', order_no: '', type: '', used: false
      };
      
      // Update hidden inputs AND visible input
      $('#usedGoldGrams').val('0.000');
      $('#usedGoldAmount').val('0.00'); // Reset visible input
      $('#usedGoldRate').val('0.00');
      $('#usedGoldRateId').val('');
      $('#goldAdvanceOrderNo').val('');
      
      // Hide gold row
      $('#usedGoldAdvanceRow').hide();
  }
      
    // Recalculate total advance amount
    currentAdvanceUsage.total_amount = currentAdvanceUsage.cash.amount + currentAdvanceUsage.gold.amount;
    
    // Update displays
    updateAdvanceDisplays();
    updateAdvanceUsageType();
    
    // Update main advance used value
    $advanceUsed.val(currentAdvanceUsage.total_amount.toFixed(2));
    
    // Hide section if no advances are used
    if (currentAdvanceUsage.total_amount === 0) {
        $usedAdvanceSection.hide();
        $advanceUsed.val('0.00');
    }
    
    // Recalculate totals
    debouncedCalculateTotal();
    
    // Reload customer balance if applicable
    if (customerId && !$('#add_manual_customer').is(':checked')) {
        loadCustomerBalances(customerId);
    }
    
    toastr.success(`Removed ${removedText}`);
}


// Clear all advances function
function clearAllAdvances() {
    const customerId = $customerSelect.val();
    const totalRemoved = currentAdvanceUsage.total_amount;
    
    // Reset all advance usage
    currentAdvanceUsage = {
        cash: { amount: 0, order_no: '', type: '', used: false },
        gold: { grams: 0, amount: 0, rate: 0, rate_id: '', order_no: '', type: '', used: false },
        total_amount: 0
    };
    
    // Clear all hidden inputs
    $('#usedCashAdvance').val('0.00');
    $('#usedGoldGrams').val('0.000');
    $('#usedGoldAmount').val('0.00'); // Reset visible input
    $('#usedGoldRate').val('0.00');
    $('#usedGoldRateId').val('');
    $('#cashAdvanceOrderNo').val('');
    $('#goldAdvanceOrderNo').val('');
    $('#advanceUsageType').val('');
        
    // Hide advance section
    $usedAdvanceSection.hide();
    $advanceUsed.val('0.00');
    
    // Hide individual rows
    $('#usedCashAdvanceRow, #usedGoldAdvanceRow').hide();
    
    // Recalculate totals
    debouncedCalculateTotal();
    
    // Reload customer balances
    if (customerId && !$('#add_manual_customer').is(':checked')) {
        loadCustomerBalances(customerId);
    }
    
    if (totalRemoved > 0) {
        toastr.success(`Cleared all advances (Rs ${totalRemoved.toFixed(2)})`);
    }
}

// Remove advance functionality (updated)
$(document).on('click', '#removeAdvanceBtn', function() {
    const customerId = $customerSelect.val();
    const advanceUsed = parseFloat($advanceUsed.val()) || 0;
    const currentCashPayment = parseFloat($advanceInput.val()) || 0;
    const totalAmount = parseFloat($totalElement.val()) || 0;
    
    const advanceCoverage = Math.min(advanceUsed, totalAmount);
    
    $advanceUsed.val('0.00');
    
    if (currentCashPayment > 0) {
        $advanceInput.val((currentCashPayment + advanceCoverage).toFixed(2));
    }
    
    $advanceInput.trigger('input');
    $usedAdvanceSection.hide();
    
    // Clear advance usage data
    $('#advance_usage_data').val('');
    
    if (customerId && !$('#add_manual_customer').is(':checked')) {
        loadCustomerAdvanceBalance(customerId);
    }
    
    toastr.success('Advance removed from order');
});
    
    // ============== AUTOCOMPLETE FUNCTIONALITY ==============
    
    //  autocomplete for advance order number
    function setupAutocomplete(inputSelector, urlTemplate, modalSelector) {
        const $input = $(inputSelector);
        let currentQuery = '';
        let isDeleting = false;
        let hasSelection = false;
        let debounceTimer = null;
        
        $input.on('keydown', function(e) {
            const input = this;
            const currentValue = $(this).val();
            const selectionStart = input.selectionStart;
            const selectionEnd = input.selectionEnd;
            
            hasSelection = selectionStart !== selectionEnd;
            isDeleting = e.key === 'Backspace' || e.key === 'Delete' || hasSelection;
            
            if ((e.key === 'ArrowRight' || e.key === 'Tab' || e.key === 'Enter') && hasSelection) {
                setTimeout(() => {
                    input.setSelectionRange(currentValue.length, currentValue.length);
                }, 0);
                return;
            }
            
            if (hasSelection && e.key.length === 1) {
                isDeleting = false;
            }
        });
        
        $input.on('input', function(e) {
            const value = $(this).val();
            const input = this;
            
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            
            if (isDeleting || value.length < 1) {
                isDeleting = false;
                return;
            }
            
            currentQuery = value;
            
            debounceTimer = setTimeout(() => {
                const customerId = $customerSelect.val();
                if (!customerId || $(input).val() !== currentQuery) return;
                
                const url = urlTemplate.replace('{customerId}', customerId);
                
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: { query: currentQuery },
                    success: function(data) {
                        const currentInputValue = $(input).val();
                        if (currentInputValue !== currentQuery) return;
                        
                        const suggestion = data.suggestion;
                        if (suggestion && 
                            suggestion.toLowerCase().startsWith(currentQuery.toLowerCase()) &&
                            suggestion.toLowerCase() !== currentQuery.toLowerCase()) {
                            
                            if (currentInputValue === currentQuery) {
                                $input.val(suggestion);
                                input.setSelectionRange(currentQuery.length, suggestion.length);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Autocomplete Error:', error);
                    }
                });
            }, 300);
        });
        
        // Reset on modal hide
        $(modalSelector).on('hidden.bs.modal', function() {
            $input.val('');
            isDeleting = false;
            hasSelection = false;
            currentQuery = '';
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
        });
    }
    
    // Setup autocomplete for both forms
    setupAutocomplete(
        'input[name="order_no"]',
        '/customer/advances/{customerId}/order-no/suggestions',
        '#addAdvanceModal'
    );
    
    setupAutocomplete(
        'input[name="gold_order_no"]',
        '/customer/gold/advances/{customerId}/order-no/suggestions',
        '#addGoldAdvanceModal'
    );
    
    // ============== RESERVATION FUNCTIONALITY ==============
    
    //  reservation modal setup
    $(document).on('show.bs.modal', '#reserveProductModal', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('#noProductsWarning').remove();
        
        const totalAmount = parseFloat($totalElement.val()) || 0;
        $('#reserveTotalAmount').val(totalAmount.toFixed(2));
        
        const today = new Date();
        const defaultDeliveryDate = new Date(today.getTime() + (7 * 24 * 60 * 60 * 1000));
        const formattedDate = defaultDeliveryDate.toISOString().split('T')[0];
        $('input[name="delivery_date"]').val(formattedDate);
        
        const reservableProducts = getReservableProducts();
        
        if (reservableProducts.length === 0) {
            $('#reserveProductModal .modal-body').prepend(
                '<div class="alert alert-warning" id="noProductsWarning">' +
                'No products available for reservation. Manual products cannot be reserved.' +
                '</div>'
            );
            $('#reserveProductModal button[type="submit"]').prop('disabled', true);
        } else {
            $('#reserveProductModal button[type="submit"]').prop('disabled', false);
        }
    });
    
    // Get reservable products helper
    function getReservableProducts() {
        const products = [];
        $productsTable.find('tr').each(function() {
            const $row = $(this);
            const productId = $row.find('input[name*="[product_id]"]').val();
            if (productId && productId !== '0') {
                const quantity = parseInt($row.find('input[name*="[qty]"]').val()) || 1;
                const subTotal = parseFloat($row.find('input[name*="[sub_total]"]').val()) || 0;
                const unitPrice = subTotal / quantity;
                
                products.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    line_total: subTotal
                });
            }
        });
        return products;
    }
    
    //  reservation form submission
    $('#reserveProductForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const customerId = $customerSelect.val();
        if (!customerId || $('#add_manual_customer').is(':checked')) {
            toastr.error('Please select a customer from the dropdown first');
            return;
        }
        
        const products = getReservableProducts();
        if (products.length === 0) {
            toastr.error('Please add products to reserve (manual products cannot be reserved)');
            return;
        }
        
        const formData = {
            products: products,
            total_amount: parseFloat($('#reserveTotalAmount').val()) || 0,
            initial_payment: parseFloat($('input[name="initial_payment"]').val()) || 0,
            delivery_date: $('input[name="delivery_date"]').val() || null
        };
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Creating Reservation...');
        
        $.ajax({
            url: `/customer/${customerId}/reservations`,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Products reserved successfully');
                    $('#reserveProductModal').modal('hide');
                    $('#reserveProductForm')[0].reset();
                    
                    // const confirmClear = confirm('Products have been reserved successfully! Would you like to clear the current order form?');
                    // if (confirmClear) {
                    //     clearForm();
                    // }

                    if (response.print_url) {
                        window.location.href = response.print_url;
                    }
                    
                } else {
                    toastr.error(response.message || 'Failed to reserve products');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to reserve products';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                toastr.error(errorMessage);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Real-time validation for initial payment
    $(document).on('input', 'input[name="initial_payment"]', function() {
        const totalAmount = parseFloat($('#reserveTotalAmount').val()) || 0;
        const initialPayment = parseFloat($(this).val()) || 0;
        
        if (initialPayment > totalAmount) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Initial payment cannot exceed total amount</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // ============== FORM CLEARING ==============
    
function clearForm() {
    // Clear products
    $productsTable.empty();
    $productNoInput.val('');
    
    // Clear manual edits
    manuallyEditedRows.clear();
    
    // Clear all advances
    clearAllAdvances();
    
    // Reset payment fields
    $('#advance').val('0.00');
    $('#chq_payment').val('0.00');
    $('#bank_transfer_payment').val('0.00');
    $('#card_payment').val('0.00');
    
    // Reset payment method to cash
    $('#payment_method').val('cash').trigger('change');
    
    // Recalculate
    calculateBalanceForPaymentMethod();
}


    //  cancel button
    $('#cancelButton').on('click', function() {
        // Reset customer selection
        $customerSelect.val('1').trigger('change');
        
        // Clear manual customer
        $('#add_manual_customer').prop('checked', false);
        $('#manual_customer').hide().prop('required', false);
        $('.customer_select_div').show();
        $customerSelect.prop('required', true);
        $('#customerInfosection').hide();
         $('#advance').val('0.00');
    $('#chq_payment').val('0.00');
    $('#bank_transfer_payment').val('0.00');
    $('#card_payment').val('0.00');
        
  $('#payment_method').val('cash').trigger('change');
         // Reset gold exchange toggle + hide section + reset values
        $('#goldExchangeCheckbox').prop('checked', false);
        $('#goldExchangeSection').hide();
        resetAllExchangeValues();

        // Clear form fields efficiently
        const fieldsToReset = [
            [$productNoInput, ''],
            [$advanceInput, '0.00'],
            [$advanceUsed, '0.00'],
            [$totalElement, '0.00'],
            ['#total_discount', '0.00'],
            ['#balance', '0.00'],
            ['#exchangeGoldWeight', '0']
        ];
        
        fieldsToReset.forEach(([field, value]) => {
            const $field = typeof field === 'string' ? $(field) : field;
            $field.val(value);
        });
        
        // Clear displays
        const displaysToReset = [
            ['#total_amount', 'Rs 0.00'],
            ['#sub_total_amount', 'Rs 0.00'],
            ['#inclusive_tax', '0.00'],
            ['#tax_rate', '0.00'],
            [$balanceAmount, 'Rs 0.00'],
            [$customerAdvanceBalance, 'Rs 0.00'],
            ['#used_advance_display', 'Rs 0.00']
        ];
        
        displaysToReset.forEach(([element, text]) => {
            const $element = typeof element === 'string' ? $(element) : element;
            $element.text(text);
        });
        
        // Clear products and hide sections
        $productsTable.empty();
        $usedAdvanceSection.hide();
        $('#reservationInfo').hide();
        
        // Re-enable advance button and reset counter
        $('#useAdvanceBtn').prop('disabled', false);
        rowNo = 0;
        
        toastr.success('Form cleared successfully');
    });
    
    // ============== UTILITY FUNCTIONS ==============
    
    // Clean up modals
    $(document).on('hidden.bs.modal', '#useAdvanceModal', function() {
        $(this).remove();
    });
    
    // Total discount handler
    $('#total_discount').on('input', function() {
        debouncedCalculateTotal();
    });
    
    // Select2 event handlers
    $customerSelect.on('select2:select', function(e) {
        const customerId = e.params.data.id;
        if (customerId && !$('#add_manual_customer').is(':checked')) {
            loadCustomerAdvanceBalance(customerId);
            loadCustomerReservations(customerId);
        }
    });
    
    $customerSelect.on('select2:clear', function() {
        $customerAdvanceBalance.text('Rs 0.00');
        $('#reservationInfo').hide();
    });
    
    // Initialize on page load
    const initialCustomerId = $customerSelect.val();

    // Toggle customer info section on initial load
    toggleCustomerInfoSection(initialCustomerId);

    if (initialCustomerId && !$('#add_manual_customer').is(':checked')) {
        // Only load data if customer is not ID 1
        if (initialCustomerId !== '1' && initialCustomerId !== 1) {
            loadCustomerBalances(initialCustomerId);
            loadCustomerReservations(initialCustomerId);
        }
    }
    // Reservation utility functions
    window.ReservationFunctions = {
        getReservableProducts: getReservableProducts,
        
        validateReservationPrerequisites: function() {
            const customerId = $customerSelect.val();
            const isManualCustomer = $('#add_manual_customer').is(':checked');
            const totalAmount = parseFloat($totalElement.val()) || 0;
            const reservableProducts = getReservableProducts();
            
            const errors = [];
            
            if (!customerId || isManualCustomer) {
                errors.push('Please select a customer from the dropdown');
            }
            
            if (totalAmount <= 0) {
                errors.push('Please add products to the order first');
            }
            
            if (reservableProducts.length === 0) {
                errors.push('No products available for reservation');
            }
            
            return {
                isValid: errors.length === 0,
                errors: errors
            };
        },
        
        clearReservationForm: function() {
            $('#reserveTotalAmount').val('');
            $('input[name="initial_payment"]').val('');
            $('input[name="delivery_date"]').val('');
            $('#reserveProductForm .alert').remove();
        },
        
        getReservationTotal: function() {
            const products = getReservableProducts();
            return products.reduce((total, product) => total + product.line_total, 0);
        }
    };
});
</script>
@endsection
