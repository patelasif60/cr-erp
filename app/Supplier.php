<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
	protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'website',
        'description',
        'status',
        'main_point_of_contact',
        'warehouse',
        'e_trailer_account_number',
        'order_schedule',
        'order_deadlines',
        'cuttoff_time',
        'minimums',
        'order_restriction_details',
        'delivery_schedule',
        'lead_time_overview_notes',
        'e_team_purchase_manager',
        'next_order_date',
        'order_method',
        'order_portal_url',
        'order_portal_username',
        'order_portal_password',
        'owner',
        'time_zone_id',
        'account_manager',
        'sales_manager',
        'delivery_day',
        'supplier_product_package_type',
    ];

	public function SupplierList(){
		$SupplierList = Supplier::get();
			foreach ($SupplierList as $suppliers){
				$supplier_id[] = $suppliers->id;
				$supplier_name[] = $suppliers->name;
				$supplier = array_combine( $supplier_id, $supplier_name );
			}
		return $supplier;
	}

    /**
     * The csvheader that belong to the supplier.
     */
    public function CsvHeader()
    {
        return $this->hasMany(\App\CsvHeader::class, 'supplier_id');
    }
    /**
     * The packageing material that belong to the club.
     */
    public function packagingMaterials()
    {
        return $this->hasMany(\App\PackagingMaterials::class, 'supplier_id');
    }
}
