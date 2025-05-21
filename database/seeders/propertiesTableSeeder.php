<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class propertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // comanda para ejecutar solo este seeder: 
        // php artisan db:seed --class=propertiesTableSeeder
        DB::table('rental_properties')->insert([
            'title' => 'Luxury Villa',
            'description' => 'A luxurious villa with a private pool.',
            'price_per_night' => 300,
            'location' => 'Luxury Area',
            'max_guests' => 8,
            'image_url' => 'https://www.martinhal.com/wp-content/uploads/2022/01/martinhal-sagres-luxury-villa-night.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Countryside Cottage',
            'description' => 'A charming cottage in the countryside.',
            'price_per_night' => 80,
            'location' => 'Countryside',
            'max_guests' => 4,
            'image_url' => 'https://www.rightmove.co.uk/news/content/uploads/2018/05/Gotherington-1-1024x682.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Beachfront Apartment',
            'description' => 'An apartment with stunning sea views.',
            'price_per_night' => 150,
            'location' => 'Beachfront',
            'max_guests' => 5,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9M1svF4Bxyv5rMYwepv_qgXhe1oHBAsy1wg&s',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Mountain Cabin',
            'description' => 'Cozy cabin in the mountains, perfect for hiking.',
            'price_per_night' => 120,
            'location' => 'Mountains',
            'max_guests' => 6,
            'image_url' => 'https://pictures.escapia.com/HEACAB/244444/7002830926.JPEG',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'City Loft',
            'description' => 'Modern loft in the city center.',
            'price_per_night' => 200,
            'location' => 'City Center',
            'max_guests' => 3,
            'image_url' => 'https://printersalleylofts.com/wp-content/uploads/2016/10/CITY-LIVING-SPACE.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Lake House',
            'description' => 'Peaceful house by the lake.',
            'price_per_night' => 180,
            'location' => 'Lakeside',
            'max_guests' => 7,
            'image_url' => 'https://idologyasheville.com/img/Things-You-Never-Knew-About-Your-Lakehouse-Home-Floors_IDology-Asheville.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Downtown Studio',
            'description' => 'Compact studio in the heart of downtown.',
            'price_per_night' => 90,
            'location' => 'Downtown',
            'max_guests' => 2,
            'image_url' => 'https://cf.bstatic.com/xdata/images/hotel/max1024x768/556973767.jpg?k=ca30f3b19c07569acb3cdba17fae48eebf4f69a0b68f0458c5825db74cc29f7b&o=&hp=1',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Historic Mansion',
            'description' => 'Stay in a beautifully restored historic mansion.',
            'price_per_night' => 400,
            'location' => 'Historic District',
            'max_guests' => 10,
            'image_url' => 'https://hips.hearstapps.com/hmg-prod/images/the-breakers-built-1895-as-a-summer-estate-by-the-news-photo-1714493438.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Ski Chalet',
            'description' => 'Chalet close to ski slopes.',
            'price_per_night' => 250,
            'location' => 'Ski Resort',
            'max_guests' => 8,
            'image_url' => 'https://www.firefly-collection.com/wp-content/uploads/2022/12/101-3589_chalet-vilaiet_25-01-23_visciani-ph.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Urban Penthouse',
            'description' => 'Luxury penthouse with city views.',
            'price_per_night' => 350,
            'location' => 'City Center',
            'max_guests' => 6,
            'image_url' => 'https://st.hzcdn.com/simgs/pictures/living-rooms/urban-penthouse-living-room-ls3p-living-img~ca6115420fd222b0_4-1603-1-6ef6159.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Tiny House',
            'description' => 'Experience minimalist living in a tiny house.',
            'price_per_night' => 70,
            'location' => 'Suburbs',
            'max_guests' => 2,
            'image_url' => 'https://h6a8m2f3.delivery.rocketcdn.me/wp-content/uploads/2021/05/millertinyhouse.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Desert Retreat',
            'description' => 'A peaceful retreat in the desert.',
            'price_per_night' => 110,
            'location' => 'Desert',
            'max_guests' => 4,
            'image_url' => 'https://www.aman.com/sites/default/files/2023-04/amangiri_usa_-_camp_sarika_pavilion_outdoor_terrace.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Island Bungalow',
            'description' => 'Bungalow on a private island.',
            'price_per_night' => 500,
            'location' => 'Private Island',
            'max_guests' => 4,
            'image_url' => 'https://www.dreamexoticrentals.com/images/Caribbean/Belize/PremierOverwaterBungalow/2.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Forest Lodge',
            'description' => 'Lodge surrounded by forest.',
            'price_per_night' => 130,
            'location' => 'Forest',
            'max_guests' => 5,
            'image_url' => 'https://www.yosemiteforestlodge.com/images/BPFromNEMorningHDR768w.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Suburban Home',
            'description' => 'Family home in a quiet suburb.',
            'price_per_night' => 100,
            'location' => 'Suburbs',
            'max_guests' => 6,
            'image_url' => 'https://www.taclosinglaw.com/wp-content/uploads/2018/10/Why-Americans-Prefer-to-Buy-Suburban-Homes-For-Now.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Eco-Friendly Apartment',
            'description' => 'Sustainable apartment with green features.',
            'price_per_night' => 140,
            'location' => 'Eco District',
            'max_guests' => 3,
            'image_url' => 'https://cf.bstatic.com/xdata/images/hotel/max1024x768/89041558.jpg?k=04c43e8706aaae11ee520b83e2c87d4b86a208618b05db2cc8f6a8ef8e75efd0&o=&hp=1',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Seaside Cottage',
            'description' => 'Cottage with direct access to the beach.',
            'price_per_night' => 160,
            'location' => 'Seaside',
            'max_guests' => 5,
            'image_url' => 'https://onekindesign.com/wp-content/uploads/2017/05/Elegant-Seaside-Cottage-Whipple-Callender-Architects-01-1-Kindesign.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Country Farmhouse',
            'description' => 'Farmhouse surrounded by fields and animals.',
            'price_per_night' => 95,
            'location' => 'Countryside',
            'max_guests' => 7,
            'image_url' => 'https://st.hzcdn.com/simgs/pictures/exteriors/rustic-farm-house-laura-culpepper-aia-img~f9d18eee054afdce_4-9654-1-b9ab94d.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Modern Duplex',
            'description' => 'Spacious duplex with modern amenities.',
            'price_per_night' => 180,
            'location' => 'Urban Area',
            'max_guests' => 6,
            'image_url' => 'https://st.hzcdn.com/simgs/pictures/exteriors/modern-duplex-builder-cutsom-home-design-by-drummond-house-plans-drummond-house-plans-img~2811a9aa059562de_4-3074-1-2ab83e8.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Riverfront House',
            'description' => 'House with a view of the river.',
            'price_per_night' => 170,
            'location' => 'Riverfront',
            'max_guests' => 5,
            'image_url' => 'https://images.mansionglobal.com/im-58909198',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Penthouse Suite',
            'description' => 'Exclusive penthouse with rooftop terrace.',
            'price_per_night' => 450,
            'location' => 'City Center',
            'max_guests' => 4,
            'image_url' => 'https://aluplex.com/wp-content/uploads/2020/11/Penthouse-Suite-Skylights-and-Why-Your-Hotel-Needs-Them.jpg',
            'is_available' => true,
        ]);
        DB::table('rental_properties')->insert([
            'title' => 'Artistic Loft',
            'description' => 'Loft with artistic decor and open space.',
            'price_per_night' => 210,
            'location' => 'Art District',
            'max_guests' => 3,
            'image_url' => 'https://www.thenordroom.com/wp-content/uploads/2020/02/artist-lofts-home-ateliers-nordroom27.jpg',
            'is_available' => true,
        ]);
    }
}
