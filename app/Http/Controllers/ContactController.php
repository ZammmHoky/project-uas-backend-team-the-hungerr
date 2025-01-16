<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact; 

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all(); // mengambil semua data dari tabel contacts
        return response()->json([
            'success' => true,
            'data' => $contacts,
        ], 200);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        $contact = new Contact();
        $contact->name = $validated['name'];
        $contact->email = $validated['email'];
        $contact->message = $validated['message'];
        $contact->save();

        return response()->json([
            'message' => 'Pesan Anda telah dikirim. Terima kasih!',
            'data' => $contact,
        ]);
    }
}
