<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Apartment;
use App\Models\Apt_image;
use App\Models\Booking;
use App\Models\Favorites;
use App\Models\Rating;
use Illuminate\Database\Seeder;
use Faker\Factory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        // Create admin user
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => null,
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        echo "Created admin user with phone: {$admin->phone} (role: {$admin->role})\n";

        // Create regular users (who will also be apartment owners)
        $users = User::factory()
            ->count(10) // Create 10 regular users
            ->approved()
            ->create();

        echo "Created " . $users->count() . " regular users\n";

        // Some users will own apartments (no separate owner role)
        $apartments = collect();
        
        // First 5 users will own apartments
        $apartmentOwners = $users->take(5);
        
        foreach ($apartmentOwners as $owner) {
            $apartmentCount = rand(2, 4);
            for ($i = 0; $i < $apartmentCount; $i++) {
                $apartment = Apartment::factory()->create([
                    'owner_id' => $owner->id,
                ]);
                $apartments->push($apartment);
            }
        }

        echo "Created " . $apartments->count() . " apartments\n";

        // Add images to apartments
        foreach ($apartments as $apartment) {
            $imageCount = rand(3, 5);
            $hasPrimary = false;
            
            for ($j = 0; $j < $imageCount; $j++) {
                $isPrimary = (!$hasPrimary && $j === 0);
                Apt_image::factory()->create([
                    'apartment_id' => $apartment->id,
                    'is_primary' => $isPrimary,
                ]);
                if ($isPrimary) $hasPrimary = true;
            }
        }

        echo "Added images to apartments\n";

        // Create bookings (users can book apartments they don't own)
        foreach ($users as $user) {
            $bookingCount = rand(1, 3);
            $userApartments = $apartments
                ->where('owner_id', '!=', $user->id) // Can't book own apartment
                ->values();
            
            for ($k = 0; $k < min($bookingCount, count($userApartments)); $k++) {
                Booking::factory()->create([
                    'user_id' => $user->id,
                    'apartments_id' => $userApartments[$k]->id,
                ]);
            }
        }

        echo "Created bookings\n";

        // Create favorites
        foreach ($users as $user) {
            $favoriteCount = rand(2, 5);
            $userApartments = $apartments
                ->where('owner_id', '!=', $user->id)
                ->values();
            
            $shuffledApartments = $userApartments->shuffle();
            
            for ($l = 0; $l < min($favoriteCount, count($shuffledApartments)); $l++) {
                Favorites::firstOrCreate([
                    'user_id' => $user->id,
                    'apartment_id' => $shuffledApartments[$l]->id,
                ]);
            }
        }

        echo "Created favorites\n";

        // Create ratings
        foreach ($users as $user) {
            $userBookings = Booking::where('user_id', $user->id)->get();
            $ratingCount = rand(1, min(3, $userBookings->count()));
            
            foreach ($userBookings->take($ratingCount) as $booking) {
                Rating::firstOrCreate([
                    'user_id' => $user->id,
                    'apartment_id' => $booking->apartments_id,
                ], [
                    'rating' => rand(1, 5),
                    'comment' => $faker->optional(0.8)->sentence(),
                ]);
            }
        }

        echo "Created ratings\n";
        echo "Database seeding completed successfully!\n";
    }
}