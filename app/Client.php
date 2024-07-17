<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'product_locations',
    ];
	protected $table = 'clients';

	public function ClientList(){
		$result = Client::get();
		foreach ($result as $clients){
			$client[$clients->id] = $clients->company_name;
		}
		return $client;
	}
}
