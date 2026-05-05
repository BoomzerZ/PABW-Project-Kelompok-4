<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $comments = [
            5 => [
                'Mantap banget gearnya, sangat responsif!',
                'Kualitas premium, tidak menyesal beli di sini.',
                'AI recommender-nya beneran kasih saran yang oke!',
                'Barang original dan pengiriman cepat.',
                'Gacor parah buat main Valorant!'
            ],
            4 => [
                'Barang bagus, tapi pengiriman agak lama.',
                'Sesuai deskripsi, worth it untuk harganya.',
                'Build quality solid, cuma kabelnya agak kaku.',
                'Puas pakai keyboard ini, suaranya thocky.',
                'Mouse-nya ringan banget, pas di tangan.'
            ],
            3 => [
                'Lumayan lah buat budget pelajar.',
                'Kualitas standar, sesuai harga.',
                'Ada sedikit lecet di box, tapi fungsinya normal.',
            ]
        ];

        foreach ($products as $product) {
            // Each product gets 2-4 random reviews
            $numReviews = rand(2, 4);
            $reviewingUsers = $users->random(min($numReviews, $users->count()));

            foreach ($reviewingUsers as $user) {
                $rating = rand(3, 5);
                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'rating' => $rating,
                    'comment' => $comments[$rating][array_rand($comments[$rating])],
                ]);
            }
        }
    }
}
