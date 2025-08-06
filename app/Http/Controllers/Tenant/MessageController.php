<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of messages
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Message::where('sender_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
        
        // Filter by message type
        if ($request->filled('type')) {
            $query->where('message_type', $request->type);
        }
        
        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('receiver_id', $user->id)->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('receiver_id', $user->id)->where('is_read', true);
            }
        }
        
        $messages = $query->with(['sender', 'receiver', 'property'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->count(),
            'unread' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
            'sent' => Message::where('sender_id', $user->id)->count(),
            'received' => Message::where('receiver_id', $user->id)->count(),
        ];
        
        return view('tenant.messages.index', compact('messages', 'stats'));
    }
    
    /**
     * Show the form for creating a new message
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        
        // Get landlords for this tenant
        $landlords = User::where('role', 'landlord')
            ->whereHas('ownedProperties.units.tenantAssignments', function($query) use ($user) {
                $query->where('tenant_id', $user->id);
            })->get();
        
        // Get properties where tenant is assigned
        $properties = Property::whereHas('units.tenantAssignments', function($query) use ($user) {
            $query->where('tenant_id', $user->id);
        })->get();
        
        // Pre-fill recipient if specified
        $recipient = null;
        if ($request->filled('to')) {
            $recipient = User::find($request->to);
        }
        
        return view('tenant.messages.create', compact('landlords', 'properties', 'recipient'));
    }
    
    /**
     * Store a newly created message
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:normal,high,urgent',
            'message_type' => 'required|in:general,maintenance,lease,payment',
            'property_id' => 'nullable|exists:properties,id',
            'attachments.*' => 'nullable|file|max:10240',
        ]);
        
        $validated['sender_id'] = Auth::id();
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }
        
        Message::create($validated);
        
        return redirect()->route('tenant.messages.index')
                        ->with('success', 'Message sent successfully!');
    }
    
    /**
     * Display the specified message
     */
    public function show(Message $message): View
    {
        $user = Auth::user();
        
        // Check if user is authorized to view this message
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Unauthorized to view this message.');
        }
        
        // Mark as read if user is the receiver
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }
        
        return view('tenant.messages.show', compact('message'));
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead(Message $message): RedirectResponse
    {
        $user = Auth::user();
        
        if ($message->receiver_id === $user->id) {
            $message->markAsRead();
        }
        
        return back()->with('success', 'Message marked as read.');
    }
    
    /**
     * Delete the specified message
     */
    public function destroy(Message $message): RedirectResponse
    {
        $user = Auth::user();
        
        // Only sender can delete the message
        if ($message->sender_id !== $user->id) {
            abort(403, 'Unauthorized to delete this message.');
        }
        
        // Delete attachments from storage
        if ($message->attachments) {
            foreach ($message->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }
        
        $message->delete();
        
        return redirect()->route('tenant.messages.index')
                        ->with('success', 'Message deleted successfully!');
    }
}
