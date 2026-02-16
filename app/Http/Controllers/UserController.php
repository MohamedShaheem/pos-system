<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorPNG;

class UserController extends Controller
{
    // Optional: keep middleware here if not in routes
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    // List all users with pagination and eager load roles
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('users.index', compact('users'));
    }

    // Show create user form with roles
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Store new user after validation
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Show edit form with user and all roles
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    // Update user details with validation
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }


public function generateBarcode($userBarcode)
{
    try {
        $generator = new BarcodeGeneratorPNG();
       
        // Staff ID card optimized settings
        $widthFactor = 2;  // Thicker bars for better printing
        $height = 40;      // Slightly shorter than product barcodes
        $foregroundColor = [0, 0, 0]; // Pure black
       
        $barcode = $generator->getBarcode(
            $userBarcode,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height,
            $foregroundColor
        );
       
        return response($barcode)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=3600');
    } catch (\Exception $e) {
        // Fallback for staff barcode
        $image = imagecreate(180, 40);
        $background = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 3, 10, 10, $userBarcode, $textColor);
       
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
       
        return response($imageData)
            ->header('Content-Type', 'image/png');
    }
}
}
