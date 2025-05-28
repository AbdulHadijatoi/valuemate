<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Models\Chat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index($user_id = null) { 
        if (!$user_id) {
            $user_id = Auth::id();
        } 

        $data = User::where('id', $user_id)
                    ->get();
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }
    
    public function dataQuery($request, $export = false) {
        
        $data = User::when($request->search_keyword, function($query) use ($request){
            $query->where('first_name', 'like', '%' . $request->search_keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->from_date && $request->to_date, function ($query) use ($request) {
            return $query->whereDate('created_at','>=', $request->from_date)
                        ->whereDate('created_at','<=', $request->to_date);
        });

        if ($export) {
            $data = $data->get();
            $total = $data->count();
        }else{
            $data = $data->paginate($request->per_page);
            $total = $data->total();
        }

        $data = $data->map(function ($item) use($export) {
            $data = [];
            if(!$export){
                $data['id'] = $item->id;
            }
            $data['first_name'] = $item->first_name ?? null;
            $data['last_name'] = $item->last_name ?? null;
            $data['email'] = $item->email ?? null;
            $data['phone'] = $item->phone??null;
            $data['created_at_date'] = $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d') : null;
            $data['created_at_time'] = $item->created_at ? Carbon::parse($item->created_at)->format('H:i:s') : null;
            return $data;
        });

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function getData(Request $request, $export = false) { 
        $request->validate([
            "from_date" => "nullable",
            "to_date" => "nullable",
            "per_page" => "nullable",
            "search_keyword" => "nullable",
        ]);

        $data = $this->dataQuery($request, $export);
        $total = $data['total'];
        
        return response()->json([
            'status' => true,
            'data' => $data['data'],
            "total" => $total,
        ], 200);
    }

    public function export(Request $request){
        $data = $this->dataQuery($request, true)['data'];
        
        $headings = [
            "First Name",
            "Last Name",
            "Email",
            "Phone",
            "Created at Date",
            "Created at Time",
        ];

        return Excel::download(new ExportData(collect($data),$headings), "data_export_" . time() . ".xlsx" );
    }

    public function store(Request $r) {
        $r->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // use password_confirmation field
        ]);

        $user = User::create([
            'first_name'     => $r->first_name,
            'last_name'     => $r->last_name,
            'phone'     => $r->phone,
            'email'    => $r->email,
            'password' => Hash::make($r->password),
        ]);

        $userRole = Role::firstOrCreate(['name' => 'client']);
        $user->assignRole($userRole);
        
        return response()->json([
            'status' => true,
            'message' => 'Successfully created user',
        ]);
    }

    public function update(Request $r, $user_id) {
        $r->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,'.$user_id,
            'phone'    => 'nullable|string|max:255|unique:users,phone,'.$user_id,
        ]);

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        $updateData = [
            "first_name" => $r->first_name,
            "last_name" => $r->last_name,
            "email" => $r->email,
            "phone" => $r->phone,
        ];

        if($r->password){
            $updateData['password'] = Hash::make($r->password);
        }
        
        $user->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated user',
        ]);
    }

    public function delete($user_id) {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted user',
        ]);
    }

    
    public function updatePassword(Request $r, $user_id) {
        $r->validate([
            'password' => 'required|string|min:6|confirmed', // use password_confirmation field
        ]);

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $user->update(['password' => Hash::make($r->password)]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated password',
        ]);
    }

}
