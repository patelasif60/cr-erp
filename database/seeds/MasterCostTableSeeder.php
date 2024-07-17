<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon as Carbon;

class MasterCostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_cost')->delete();
        DB::table('master_cost')->insert([
            [
                'name'           => 'Product Cost',
                'type'           => 'Cost',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Shipping Cost',
                'type'           => 'Cost',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Business expenses',
                'type'           => 'Cost',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Packaging Matirial Cost',
                'type'           => 'Cost',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Mark Of Price Group',
                'type'           => 'Pricing',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
