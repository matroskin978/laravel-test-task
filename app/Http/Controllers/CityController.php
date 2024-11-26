<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CityController extends Controller
{

    public function getCountriesCapital()
    {
        $data = Http::get('https://countriesnow.space/api/v0.1/countries/capital');
        dump($data->ok());
        dump($data->json()['error']);
    }

    public function getCities()
    {
        $cities_data = Http::retry(3, 100, throw: false)->post('https://countriesnow.space/api/v0.1/countries/cities', [
            'country' => 'spain'
        ])->json();

        if (empty($cities_data['data'])) {
            return $cities_data['msg'];
        }

        City::query()->truncate();
        $cities_chunks = array_chunk($cities_data['data'], 500);
        $ignored = 0;

        foreach ($cities_chunks as $cities) {
            $values = [];
            foreach ($cities as $city) {
                $values[] = [
                    'title' => $city,
                    'slug' => Str::slug($city),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $inserts = DB::table('cities')->insertOrIgnore($values);
            $ignored += (count($values) - $inserts);
        }

        $all_cities = count($cities_data['data']);
        $inserted_cities = $all_cities - $ignored;
        return "Retrieved cities: <b>$all_cities</b> | Inserted cities: <b>$inserted_cities</b> | Ignored: <b>$ignored</b>";
    }

}
