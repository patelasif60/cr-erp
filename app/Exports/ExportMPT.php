<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportMPT implements FromCollection, WithHeadings
{
    private $data;
    // WithHeadings
    public function __construct($data)
    {
        $this->data = $data;     //Inject data
    }
	
        public function headings(): array
    {
        return [
			"productdata.sku",
			"parentage",
			"parent_sku",
			"variation_types",
			"variation_options",
			"title",
			"description",
			"brand_name",
			"price",
			"category_codes",
			"image_url_1",
			"image_url_2",
			"image_url_3",
			"image_url_4",
			"image_url_5",
			"image_url_6",
			"image_url_7",
			"image_url_8",
			"image_url_9",
			"image_url_10",
			"product_id",
			"product_id_type",
			"quantity",
			"item_package_quantity",
			"amount_of_content",
			"unit_of_content",
			"width",
			"length",
			"height",
			"weight",
			"warehouse_id",
			"meta_title",
			"meta_description",
			"meta_keywords",
			"update_delete",
			"enable_product_update",
			"search_terms",
			"bullet_point_1",
			"bullet_point_2",
			"bullet_point_3",
			"bullet_point_4",
			"bullet_point_5",
			"search_term_1",
			"search_term_2",
			"search_term_3",
			"search_term_4",
			"search_term_5",
			"attr_name_1",
			"attr_value_1",
			"attr_name_2",
			"attr_value_2",
			"attr_name_3",
			"attr_value_3",
			"attr_name_4",
			"attr_value_4",
			"attr_name_5",
			"attr_value_5",
			"attr_name_6",
			"attr_value_6",
			"attr_name_7",
			"attr_value_7",
			"attr_name_8",
			"attr_value_8",
			"attr_name_9",
			"attr_value_9"
        ];
		
		/*return [
            'ID',
            'Name',
            'Email',
        ];*/
    }
	
    public function collection()
    {
        return collect($this->data);
    }
}
