<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon as Carbon;

class SubCostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('sub_cost')->truncate();
        DB::table('sub_cost')->insert([
            [
                'master_cost_id' => 1,
                'cost_name'      => 'Total Product Cost',
                'cost_formula'   => '{"total_product_cost":{"master_product":["cost","acquisition_cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 1,
                'cost_name'      => 'Acquisition Cost',
                'cost_formula'   => '{"acquisition_cost":{"master_product":["cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 1,
                'cost_name'      => 'Product Cost',
                'cost_formula'   => '{"product_cost":{"master_product":["cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 1,
                'cost_name'      => 'Contracted Cost',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Total Shipping',
                'cost_formula'   => '{"total_shipping" :{"misc_cost_values": ["shipping_cost_base","additional_handling","remote_area_surcharge","delivery_area_surcharge","extended_delivery_area_surcharge"],"carrier_peak_surchrges":["additional_handling","large_package_gt_50_lbs"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Base Shipping Cost',
                'cost_formula'   => '{"base_shipping_cost":{"misc_cost_values": ["shipping_cost_base"],"master_product": ["weight"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Fuel Surcharge',
                //'cost_formula'   => null,
                'cost_formula'   => '{"fuel_surcharge":{"carrier_dynamic_fees":["effective_date","ground","air","international_air"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Additional Handling',
                'cost_formula'   => '{"additional_handling":{"carrier_standard_fees":["weight_gt_50_lbs_3"],"misc_cost_values":["additional_handling"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Residential Surcharge',
                'cost_formula'   => '{"residential_surcharge":{"carrier_standard_fees":["residential_surcharge_ground"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Remote Area Surcharge',
                'cost_formula'   => '{"remote_area_surcharge":{"carrier_standard_fees":["continental_us_ground"],"misc_cost_values":["remote_area_surcharge"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Delivery Area Surcharge',
                'cost_formula'   => '{"delivery_area_surcharge":{"carrier_standard_fees":["residential_ground"],"misc_cost_values":["delivery_area_surcharge"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Extended Delivery Area Surcharge',
                'cost_formula'   => '{"extended_delivery_area_surcharge":{"carrier_standard_fees":["residential_extended_ground"],"misc_cost_values":["extended_delivery_area_surcharge"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Dry Ice Surcharge',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Large Package Surcharge',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Misc. Shipping Charges',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Peak Surcharge',
                'cost_formula'   => '{"peak_surcharge":{"carrier_peak_surcharges":["ground_residential"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Peak Additional Handling',
                'cost_formula'   => '{"peak_additional_surcharge":{"carrier_peak_surcharges":["additional_handling"],"misc_cost_values":["additional_handling"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Peak:Large Package',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 2,
                'cost_name'      => 'Weight Multiplier',
                'cost_formula'   => '{"weight_multiplier":{"price_group": ["weight_multiplier"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 3,
                'cost_name'      => 'Labor Cost',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 3,
                'cost_name'      => 'Credit Card Fees',
                'cost_formula'   => null,
                //'cost_formula'   => '{"credit_card_fees":{"price_group": ["credit_card_fees"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 3,
                'cost_name'      => 'Marketplace Fees',
                'cost_formula'   => null,
                //'cost_formula'   => '{"marketplace_fees":{"price_group": ["marketplace_fees"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 3,
                'cost_name'      => 'Overhead expenses',
                'cost_formula'   => null,
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
             [
                'master_cost_id' => 3,
                'cost_name'      => 'Misc. Fees & Charges',
                'cost_formula'   => '{"misc_fees_and_Charges":{"misc_cost_values": ["labor_cost","overhead_expenses"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 4,
                'cost_name'      => 'Packaging & Material',
                'cost_formula'   => '{"packaging_and_material":{"misc_cost_values": ["packaging_and_material_cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 4,
                'cost_name'      => 'Coolant Cost',
                'cost_formula'   => '{"coolant_cost":{"misc_cost_values": ["coolant_cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 5,
                'cost_name'      => 'Markup: Price Group',
                'cost_formula'   => '{"markup_price_group":{"price_group": ["markup_price_group"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'master_cost_id' => 5,
                'cost_name'      => 'Markup: Total Cost',
                'cost_formula'   => '{"markup_total_cost":{"price_group": ["markup_total_cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],

             [
                'master_cost_id' => 5,
                'cost_name'      => 'Markup: Product & Materials Cost',
                'cost_formula'   => '{"markup_product_materials_cost":{"price_group": ["markup_product_materials_cost"]}}',
                'created_at'     => Carbon::now()->format('Y-m-d H:i:s'),
            ],

        ]);
    }
}
