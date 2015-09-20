<?php
namespace Home\Widget;
use Think\Controller;
class MarketWidget extends Controller{
	public function index(){
		$markets = M("market");
		$data = $markets -> find();
		echo "{$data['market_name']}";
	}
	public function dex(){
		$markets = M("market");
		$data = $markets -> find();
		echo "{$data['market_phone']}";
	}
}