<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index() { 
        return Invoice::all(); 
    }
    
    public function store(Request $r) {
        return Invoice::create($r->validate([
            'valuation_request_id'=>'required',
            'client_email'=>'required',
            'company_email'=>'required',
            'file_path'=>'required'
        ]));
    }

    public function show(Invoice $invoice) { 
        return $invoice; 
    }
    
    public function update(Request $r, Invoice $invoice) { 
        $invoice->update($r->all()); return $invoice; 
    }
    
    public function destroy(Invoice $invoice) { 
        $invoice->delete(); return response()->noContent(); 
    }
    
}
