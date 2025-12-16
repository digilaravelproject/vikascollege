<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Enquiry;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class LeadAdminController extends Controller
{
    use AuthorizesRequests;

    public function admissions(Request $r)
    {
        $this->authorize('view admissions');
        $q = Admission::query();

        // 1. Get unique programmes (referred to as 'discipline' in the view filter)
        $disciplines = Admission::select('programme')
            ->distinct()
            ->whereNotNull('programme')
            ->orderBy('programme')
            ->pluck('programme');

        if ($r->search) {
            $searchTerm = '%' . $r->search . '%';
            // Use a closure (local scope) for grouping OR conditions related to search
            $q->where(function ($query) use ($searchTerm) {
                $query->where('email', 'like', $searchTerm)
                    ->orWhere('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('mobile_no', 'like', $searchTerm);
            });
        }

        // 2. Apply discipline (programme) filter
        if ($r->discipline) {
            $q->where('programme', $r->discipline);
        }

        // 3. Apply date filters
        if ($r->from) {
            $q->whereDate('created_at', '>=', $r->from);
        }
        if ($r->to) {
            $q->whereDate('created_at', '<=', $r->to);
        }

        // 4. Rename result variable to $leads to match the view
        $leads = $q->orderBy('created_at', 'desc')->paginate(20);

        // 5. Pass $leads and $disciplines to the view
        return view('admin.leads.admissions', compact('leads', 'disciplines'));
    }

    public function enquiries(Request $r)
    {
        $this->authorize('view enquiries');
        $q = Enquiry::query();

        if ($r->search) {
            $searchTerm = '%' . $r->search . '%';
            // Use a closure (local scope) for grouping OR conditions related to search
            $q->where(function ($query) use ($searchTerm) {
                $query->where('email', 'like', $searchTerm)
                    ->orWhere('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm) // Added last_name
                    ->orWhere('mobile_no', 'like', $searchTerm);  // Added mobile_no
            });
        }

        $enquiries = $q->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.leads.enquiries', compact('enquiries'));
    }
}
