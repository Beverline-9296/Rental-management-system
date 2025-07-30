<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimplePropertyUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function landlord_can_update_property_basic_information()
    {
        // Create a landlord user
        $landlord = User::create([
            'name' => 'Test Landlord',
            'email' => 'landlord@test.com',
            'password' => bcrypt('password'),
            'role' => 'landlord'
        ]);

        // Create a property
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Test Property',
            'property_type' => 'apartment',
            'location' => 'Test Location',
            'address' => 'Test Address',
            'description' => 'Test Description'
        ]);

        // Create a unit for the property
        $unit = Unit::create([
            'property_id' => $property->id,
            'unit_number' => 'A1',
            'unit_type' => 'apartment',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'rent_amount' => 50000,
            'status' => 'available'
        ]);

        $this->actingAs($landlord);

        $updateData = [
            'name' => 'Updated Property Name',
            'property_type' => 'house',
            'location' => 'Updated Location',
            'address' => 'Updated Address',
            'description' => 'Updated Description',
            'amenities' => ['parking', 'security'],
            'notes' => 'Updated notes',
            'units' => [
                [
                    'id' => $unit->id,
                    'unit_number' => 'A1',
                    'type' => 'apartment',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'rent_amount' => 55000, // Updated rent
                    'deposit_amount' => 55000,
                    'features' => 'balcony, parking',
                    'notes' => 'Corner unit'
                ]
            ]
        ];

        $response = $this->put(route('landlord.properties.update', $property), $updateData);

        // Check if the response is successful
        $this->assertTrue(
            $response->isRedirect() || $response->isSuccessful(),
            'Property update should be successful. Response status: ' . $response->getStatusCode()
        );

        // Verify property was updated
        $property->refresh();
        $this->assertEquals('Updated Property Name', $property->name);
        $this->assertEquals('house', $property->property_type);
        $this->assertEquals('Updated Location', $property->location);
        $this->assertEquals('Updated Address', $property->address);
        $this->assertEquals('Updated Description', $property->description);

        // Verify unit was updated
        $unit->refresh();
        $this->assertEquals(55000, $unit->rent_amount);
    }

    /** @test */
    public function property_update_validates_required_fields()
    {
        // Create a landlord user
        $landlord = User::create([
            'name' => 'Test Landlord',
            'email' => 'landlord@test.com',
            'password' => bcrypt('password'),
            'role' => 'landlord'
        ]);

        // Create a property
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Test Property',
            'property_type' => 'apartment',
            'location' => 'Test Location',
            'address' => 'Test Address'
        ]);

        $this->actingAs($landlord);

        // Try to update with missing required fields
        $updateData = [
            'name' => '', // Required field empty
            'property_type' => '',
            'location' => '',
            'address' => '',
            'units' => [] // No units
        ];

        $response = $this->put(route('landlord.properties.update', $property), $updateData);

        // Should have validation errors
        $response->assertSessionHasErrors(['name', 'property_type', 'location', 'address', 'units']);
    }

    /** @test */
    public function property_update_prevents_duplicate_unit_numbers()
    {
        // Create a landlord user
        $landlord = User::create([
            'name' => 'Test Landlord',
            'email' => 'landlord@test.com',
            'password' => bcrypt('password'),
            'role' => 'landlord'
        ]);

        // Create a property
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Test Property',
            'property_type' => 'apartment',
            'location' => 'Test Location',
            'address' => 'Test Address'
        ]);

        $this->actingAs($landlord);

        $updateData = [
            'name' => $property->name,
            'property_type' => $property->property_type,
            'location' => $property->location,
            'address' => $property->address,
            'units' => [
                [
                    'unit_number' => 'A1', // Duplicate unit number
                    'type' => 'apartment',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'rent_amount' => 50000,
                    'deposit_amount' => 50000,
                    'features' => '',
                    'notes' => ''
                ],
                [
                    'unit_number' => 'A1', // Duplicate unit number
                    'type' => 'apartment',
                    'bedrooms' => 1,
                    'bathrooms' => 1,
                    'rent_amount' => 40000,
                    'deposit_amount' => 40000,
                    'features' => '',
                    'notes' => ''
                ]
            ]
        ];

        $response = $this->put(route('landlord.properties.update', $property), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Unit numbers must be unique within the property.');
    }
}
