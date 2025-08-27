<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
// Uncomment this after creating the mail class:
// use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    // Show contact form (GET)
    public function index()
    {
        return view('contact');
    }

    // Handle form submission (POST)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        // Store in database
        Contact::create($validated);

        // Optional: Send email notification
        // Mail::to('admin@example.com')->send(new ContactFormMail($validated));

        return redirect()->back()->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }

    // Optional: Add method to view submissions (for admin)
    public function list()
    {
        $contacts = Contact::latest()->get();
        return view('admin.contacts.index', compact('contacts'));
    }
}