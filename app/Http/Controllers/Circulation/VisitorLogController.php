<?php

namespace App\Http\Controllers\Circulation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitorLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $todayVisitorsCount = VisitorLog::whereDate('check_in_at', today())->count();
        $visitors = $query->latest()->paginate(15)->withQueryString();
        $members  = User::role('siswa')->get(['id', 'name', 'member_id']);

        return view('circulation.visitors.index', compact('visitors', 'todayVisitorsCount', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_member'    => ['required', 'boolean'],
            'user_id'      => ['nullable', 'required_if:is_member,1', 'exists:users,id'],
            'visitor_name' => ['nullable', 'required_if:is_member,0', 'string', 'max:255'],
            'institution'  => ['nullable', 'string', 'max:255'],
            'purpose'      => ['required', 'in:reading,borrowing,returning,studying,other'],
        ]);

        $visitorName = null;
        if ($validated['is_member']) {
            $user = User::find($validated['user_id']);
            $visitorName = $user?->name;
        } else {
            $visitorName = $validated['visitor_name'];
        }

        VisitorLog::create([
            'user_id'      => $validated['is_member'] ? $validated['user_id'] : null,
            'visitor_name' => $visitorName,
            'institution'  => $validated['institution'],
            'purpose'      => $validated['purpose'],
            'check_in_at'  => now(),
            'processed_by' => auth()->id(),
        ]);

        return redirect()->route('circulation.visitors.index')
            ->with('success', "Kunjungan atas nama \"{$visitorName}\" berhasil dicatat.");
    }
}
