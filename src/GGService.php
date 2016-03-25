<?php

namespace GoGlobalApi;

use GoGlobalApi\lib\GGHelper;
use GoGlobalApi\lib\GGStaticTypes;
use GoGlobalApi\lib\GGResponseParser;
use GoGlobalApi\lib\GGSearchModel;

	
/**
*	GoGlobal Service Class
*/
class GGService {

    const GGServiceUrl = "http://xml.qa.goglobal.travel/XMLWebService.asmx";

    protected $_agency;
    protected $_user;
    protected $_password;
    protected $_url;
    protected $_wsdl;
    protected $_maxResult = 10000;
    protected $_timeout = 60;
	
    public $_lastXml = "";
	
	/**
	*	GoGlobal Service constructor
	*/
    public function __construct( $config = array()) {
        foreach(array('agency', 'user', 'password') as $ck) {
			if(!array_key_exists($ck, $config) || strlen($config[$ck]) < 1) {
				throw new \InvalidArgumentException("Missing config value: [".$ck."]");	
			}
		}			
        $this->_agency 		= $config['agency'];
        $this->_user 		= $config['user'];
        $this->_password 	= $config['password'];		
        if(array_key_exists('url', $config)) { 
			$this->_url 	= $config['url'];
        } else {
			$this->_url 	= self::GGServiceUrl;
		}
        $this->_wsdl = new \SoapClient($this->_url."?WSDL");
    }
	
	/**
	*	Setting GoGlobal request max results
	*/
    public function setMaxResult($max) {
        $this->_maxResult = intval($max);
    }
	
	/**
	*	Getting GoGlobal request max results
	*/	
    public function getMaxResult() {
        return $this->_maxResult;
    }
	
	/**
	*	Setting GoGlobal request timeout
	*/
    public function setTimeout($timeout) {
        $this->_timeout = intval($timeout);
    }
	
	/**
	*	Getting GoGlobal request timeout
	*/	
    public function getTimeout() {
        return $this->_timeout;
    }
	
	/**
	*	Make GoGlobal request header
	*/	
    protected function _makeXmlHeader($requestType){
		$xml  = GGHelper::WrapTag('Agency', $this->_agency);
		$xml .= GGHelper::WrapTag('User', $this->_user);
		$xml .= GGHelper::WrapTag('Password', $this->_password);
		$xml .= GGHelper::WrapTag('Operation', GGStaticTypes::$RequestType[$requestType]);
		$xml .= GGHelper::WrapTag('OperationType', 'Request');
		$xml  = GGHelper::WrapTag('Header', $xml);
        return $xml;
    }
	
	/**
	*	Sending GoGlobal request
	*/	
    protected function _sendRequest($requestType, $xmlRequest = "") {
        $xml = GGHelper::WrapTag('Root', $this->_makeXmlHeader($requestType) . GGHelper::WrapTag('Main', $xmlRequest));
        $resp = $this->_wsdl->MakeRequest([
            'requestType' => $requestType,
            'xmlRequest' => $xml,
        ]);
        $this->_lastXml = $xml;
        $requestResult = $resp->MakeRequestResult;
        return $requestResult;
    }
	
	/**
	*	Search hotels without geocodes
	*/	
    public function searchHotels(GGSearchModel $model) {
        $requestType = 1;
		$xml = $model->toXml();
        return GGResponseParser::parseHotelsList($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Search hotels with geocodes
	*/	
    public function searchHotelsGeo(GGSearchModel $model) {
        $requestType = 11;
		$xml = $model->toXml();
        return GGResponseParser::parseHotelsList($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting hotel information without geocodes
	*/
    public function getHotelInfo($hotelCode) {
		$requestType = 6;
        $xml = GGHelper::WrapTag('HotelSearchCode', $hotelCode);
        return GGResponseParser::parseHotelInfo($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting hotel information with geocodes
	*/	
    public function getHotelInfoGeo($hotelCode) {
        $requestType = 61;
        $xml = GGHelper::WrapTag('HotelSearchCode', $hotelCode);
        return GGResponseParser::parseHotelInfo($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Insert new booking
	*/	
    public function insertBooking($agentReference, $hotelCode, $hotelCheckIn, $hotelNights, $hasAlternative, $leaderId, $bookingRooms) {
        $requestType = 2;
		$xml  = "";
        $xml .= GGHelper::WrapTag('AgentReference', $agentReference);
        $xml .= GGHelper::WrapTag('HotelSearchCode', $hotelCode);
        $xml .= GGHelper::WrapTag('ArrivalDate', $hotelCheckIn);
        $xml .= GGHelper::WrapTag('Nights', $hotelNights);
        $xml .= GGHelper::WrapTag('NoAlternativeHotel', $hasAlternative);
        $xml .= GGHelper::WrapTag('Leader', '', ['LeaderPersonID' => $leaderId]);
		$roomTypeXml = "";
		foreach($bookingRooms as $roomType => $rooms){
			$roomXml = "";
			foreach($rooms as $roomId => $room){
				$personXml = "";
				foreach($room as $personId => $person){
					if($person['type'] == "adult")
						$personXml .= GGHelper::WrapTag('PersonName', $person['name'], ['PersonID' => $personId]);
					else if($person['type'] == "child")
						$personXml .= GGHelper::WrapTag('ExtraBed', $person['name'], ['PersonID' => $personId, 'ChildAge' => $person['age']]);
				}
				$roomXml .= GGHelper::WrapTag('Room', $personXml, ['RoomID' => $roomId]);
			}
			$roomTypeXml .= GGHelper::WrapTag('RoomType', $roomXml, ['Type' => $roomType]);
		}
        $xml .= GGHelper::WrapTag('Rooms', $roomTypeXml);
        return GGResponseParser::parseBookingInsert($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Cancel booking
	*/		
    public function cancelBooking($bookingId) {
        $requestType = 3;
		$xml  = GGHelper::WrapTag('GoBookingCode', $bookingId);
        return GGResponseParser::parseBookingCancel($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Search booking
	*/		
    public function searchBooking($bookingId) {
        $requestType = 4;
		$xml  = GGHelper::WrapTag('GoBookingCode', $bookingId);
        return GGResponseParser::parseBookingSearch($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Search booking advanced
	*/	
    public function searchBookingAdv($dateFrom = "", $dateTo = "", $paxName = "", $cityCode = "", $nights = "", $hotelName = "") {
        $requestType = 10;
		$xml  = "";
		$xml .= (!empty($dateFrom)) ? GGHelper::WrapTag('ArrivalDateRangeFrom', $dateFrom) : "";
		$xml .= (!empty($dateTo)) ? GGHelper::WrapTag('ArrivalDateRangeTo', $dateTo) : "";
		$xml .= (!empty($paxName)) ? GGHelper::WrapTag('PaxName', $paxName) : "";
		$xml .= (!empty($cityCode)) ? GGHelper::WrapTag('CityCode', $cityCode) : "";
		$xml .= (!empty($nights)) ? GGHelper::WrapTag('Nights', $nights) : "";
		$xml .= (!empty($hotelName)) ? GGHelper::WrapTag('HotelName', $hotelName) : "";		
        return GGResponseParser::parseBookingSearchAdv($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Check single booking status
	*/	
    public function checkBookingStatus($bookingId) {
        $requestType = 5;
		$xml  = GGHelper::WrapTag('GoBookingCode', $bookingId);
        return GGResponseParser::parseBookingCheckStatus($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Check multiple booking status
	*/		
    public function checkBookingsStatus($bookingsId = []) {
        $requestType = 5;
		$xml = "";
		foreach($bookingsId as $bookingId)
			$xml  .= GGHelper::WrapTag('GoBookingCode', $bookingId);
        return GGResponseParser::parseBookingCheckStatus($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Check booking valuation
	*/		
    public function checkBookingValuation($hotelCode, $arrivalDate) {
        $requestType = 9;
		$xml  = GGHelper::WrapTag('HotelSearchCode', $hotelCode);
		$xml .= GGHelper::WrapTag('ArrivalDate', $arrivalDate);
        return GGResponseParser::parseBookingValuationCheck($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting booking voucher
	*/		
    public function getBookingVoucher($bookingId, $emergencyPhone = true) {
        $requestType = 8;
		$xml  = GGHelper::WrapTag('GoBookingCode', $bookingId);
		$xml .= GGHelper::WrapTag('GetEmergencyPhone', $emergencyPhone);
        return GGResponseParser::parseBookingVoucher($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting booking voucher
	*/		
    public function getPiggyPoints($bookingId, $emergencyPhone = true) {
        $requestType = 12;
        return GGResponseParser::parsePiggyPoints($this->_sendRequest($requestType, ''));
    }

	
	/**
	*	Getting price breakdown
	*/		
    public function getPaymentService($bookingId, $returnUrl) {
        $requestType = 13;
        $xml = GGHelper::WrapTag('GoBookingCode', $bookingId);
        $xml = GGHelper::WrapTag('ReturnUrl', $returnUrl);
        return GGResponseParser::parsePaymentService($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting price breakdown
	*/		
    public function getPriceBreakdown($hotelCode) {
        $requestType = 14;
        $xml = GGHelper::WrapTag('HotelSearchCode', $hotelCode);
        return GGResponseParser::parsePriceBreakdown($this->_sendRequest($requestType, $xml));
    }
	
	/**
	*	Getting price breakdown
	*/		
    public function getBookingAmendementInfo($bookingId) {
        $requestType = 14;
        $xml = GGHelper::WrapTag('GoBookingCode', $bookingId);
        return GGResponseParser::parseBookingAmendementInfo($this->_sendRequest($requestType, $xml));
    }

}

