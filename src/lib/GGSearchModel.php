<?php

namespace ustmaestro\goglobalapi\lib;

use ustmaestro\goglobalapi\lib\GGHelper;

class GGSearchModel{
    public $city_code;
    public $hotel_name;
    public $check_in;
    public $nights;

    public $rooms_number = 0;
    public $rooms_persons = [];
    public $sort_order = 1;
    public $min_price = 5;
    public $max_price = 50000;
    public $max_wait_time = 30;
    public $max_responses = 2000;
    public $stars = [];
    public $room_basises = [];
    public $apartments_only = false;

    public function __construct() {
    }

    public function toXml(){
        if($this->rooms_number < 1) throw new InvalidArgumentException("Invalid rooms number");

        $xml = "";
        $xml .= GGHelper::WrapTag('SortOrder', $this->sort_order);
        $xml .= GGHelper::WrapTag('FilterPriceMin', $this->min_price);
        $xml .= GGHelper::WrapTag('FilterPriceMax', $this->max_price);
        $xml .= GGHelper::WrapTag('MaximumWaitTime', $this->max_wait_time);
        $xml .= GGHelper::WrapTag('MaxResponses', $this->max_responses);

        if(count($this->room_basises)){
            $rb_xml = "";
            foreach($this->room_basises as $room_basis)
                $rb_xml .= GGHelper::WrapTag('FilterRoomBasis', $room_basis);
            $xml .= GGHelper::WrapTag('FilterRoomBasises', $rb_xml);
        }

        if($this->hotel_name && !empty($this->hotel_name))
            $xml .= GGHelper::WrapTag('HotelName', $this->hotel_name);

        $xml .= GGHelper::WrapTag('CityCode', $this->city_code);
        $xml .= GGHelper::WrapTag('ArrivalDate', date('Y-m-d',strtotime($this->check_in)));
        $xml .= GGHelper::WrapTag('Nights', $this->nights);

        if(count($this->stars))
            $xml .= GGHelper::WrapTag('Stars', implode(',', $this->stars));
        if($this->apartments_only)
            $xml .= GGHelper::WrapTag('Apartments','true');

        $rooms_xml = "";
        for($i=1; $i<=$this->rooms_number; $i++){
            $child_xml = "";
            if($this->rooms_persons[$i]['child'] > 0){
                for($k=1; $k<=$this->rooms_persons[$i]['child']; $k++)
                    $child_xml .= GGHelper::WrapTag('Room', 5+$k);
            }
            $rooms_xml .= GGHelper::WrapTag('Room', $child_xml,['Adults'=> $this->rooms_persons[$i]['adult'], 'RoomCount'=>1]);
        }
        $xml .= GGHelper::WrapTag('Rooms',$rooms_xml);

        return $xml;
    }

}
