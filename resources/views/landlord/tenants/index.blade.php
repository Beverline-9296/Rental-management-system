@extends('landlord.layouts.app')

@section('title', 'Manage Tenants')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Success Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    @if(session('tenant_password'))
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded">
                            <p class="text-sm font-semibold text-blue-800">🔑 Tenant Login Credentials:</p>
                            <p class="text-sm text-blue-700"><strong>Email:</strong> {{ session('tenant_email') }}</p>
                            <p class="text-sm text-blue-700"><strong>Password:</strong> <code class="bg-blue-100 px-2 py-1 rounded">{{ session('tenant_password') }}</code></p>
                            <p class="text-xs text-blue-600 mt-1">⚠️ Please share these credentials with the tenant securely. They can use these to log in to their dashboard.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tenant Management</h1>
        <a href="{{ route('landlord.tenants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Add New Tenant
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                        @php
                            $assignment = $tenant->tenantAssignments->first();
                            $unit = $assignment ? $assignment->unit : null;
                            $property = $unit ? $unit->property : null;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $tenant->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $tenant->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $property ? $property->name : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $unit ? $unit->unit_number : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'terminated' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-yellow-100 text-yellow-800',
                                    ][$assignment ? $assignment->status : 'expired'] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $assignment ? ucfirst($assignment->status) : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('landlord.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('landlord.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('landlord.tenants.reset-password', $tenant) }}" method="POST" class="inline mr-3" onsubmit="return confirm('Generate a new password for this tenant?');">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Reset Password</button>
                                </form>
                                <form action="{{ route('landlord.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this tenant? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No tenants found. <a href="{{ route('landlord.tenants.create') }}" class="text-blue-600 hover:text-blue-800">Add a new tenant</a> to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tenants->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
