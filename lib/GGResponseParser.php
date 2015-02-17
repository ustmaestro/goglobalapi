<?php

/* GoGlobal Response parser */

namespace GoGlobalApi\lib;


class GGResponseParser{
	
	public static function parseHotelsList ($xmlString) {
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$xml = new \SimpleXMLElement($xmlString);
			if ($xml->Header->Statistics->ResultsQty > 0) {
				foreach($xml->Main->Hotel as $hotel) {
					$id = (string) $hotel->HotelCode;
					$u = explode('/', (string) $hotel->HotelSearchCode);
					$u = $u[0];
					if(!array_key_exists($id, $response)){
						$hotelData = [
							'HotelUnique' 		=> $u,
							'HotelCode' 		=> (string) $hotel->HotelCode,
							'HotelName' 		=> (string) $hotel->HotelName,
							'CountryId' 		=> (string) $hotel->CountryId,
							'Stars' 			=> floor((string) $hotel->Category),
							'Location' 			=> (string) $hotel->Location,
							'LocationCode' 		=> (string) $hotel->LocationCode,
							'Thumbnail' 		=> (string) $hotel->Thumbnail,
							'Bundles'			=> [],
							'BoardTypes'		=> [],
						];	
						$hotelData['Bundles'][] = [
							'HotelSearchCode' 	=> (string) $hotel->HotelSearchCode,
							'RoomType' 			=> (string) $hotel->RoomType,
							'RoomBasis' 		=> (string) $hotel->RoomBasis,
							'Availability' 		=> (string) $hotel->Availability,
							'TotalPrice' 		=> (string) $hotel->TotalPrice,
							'Currency' 			=> (string) $hotel->Currency,						
							'CxlDeadline' 		=> DateTime::createFromFormat('j/M/Y', (string) $hotel->CxlDeadline)->format('d-m-Y'),
							'Preferred' 		=> (string) $hotel->Preferred,
							'Remark' 			=> (string) $hotel->Remark,
							'SpecialOffer' 		=> (string) $hotel->SpecialOffer,
						];
						$hotelData['BoardTypes'][] = (string) $hotel->RoomBasis;
						if(isset($hotel->GeoCodes) && ( count($hotel->GeoCodes->children()) == 2)) {
							$hotelData['GeoCodes']['Latitude'] 	= (string) $hotel->GeoCodes->Latitude;
							$hotelData['GeoCodes']['Longitude'] = (string) $hotel->GeoCodes->Longitude;
						}
						if(isset($hotel->TripAdvisor) && ( count($hotel->TripAdvisor->children()) == 4)) {
							$hotelData['TripAdvisor']['Rating'] 		= (string) $hotel->TripAdvisor->Rating;
							$hotelData['TripAdvisor']['RatingImage'] 	= (string) $hotel->TripAdvisor->RatingImage;
							$hotelData['TripAdvisor']['Reviews']		= (string) $hotel->TripAdvisor->Reviews;
							$hotelData['TripAdvisor']['ReviewCount'] 	= (string) $hotel->TripAdvisor->ReviewCount;
						}
						$response[$id] = $hotelData;
					} else {
						$response[$id]['Bundles'][] = [
							'HotelSearchCode' 	=> (string) $hotel->HotelSearchCode,
							'RoomType' 			=> (string) $hotel->RoomType,
							'RoomBasis' 		=> (string) $hotel->RoomBasis,
							'Availability' 		=> (string) $hotel->Availability,
							'TotalPrice' 		=> (string) $hotel->TotalPrice,
							'Currency' 			=> (string) $hotel->Currency,						
							'CxlDeadline' 		=> DateTime::createFromFormat('j/M/Y', (string) $hotel->CxlDeadline)->format('d-m-Y'),
							'Preferred' 		=> (string) $hotel->Preferred,
							'Remark' 			=> (string) $hotel->Remark,
							'SpecialOffer' 		=> (string) $hotel->SpecialOffer,
						];
						$response[$id]['BoardTypes'][] = (string) $hotel->RoomBasis;
					}
				}
			}
		}
		return $response;
	} 
	
	public static function parseHotelInfo ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$hotel = $xml->Main;
			$response = [
				'HotelCode' 		=> (string) $hotel->HotelSearchCode,
				'HotelName' 		=> (string) $hotel->HotelName,
				'Address' 			=> (string) $hotel->Address,
				'CityCode' 			=> (string) $hotel->CityCode,
				'Phone' 			=> (string) $hotel->Phone,
				'Fax' 				=> (string) $hotel->Fax,
				'Stars'				=> floor((string) $hotel->Category),
				'Description' 		=> (string) $hotel->Description,
				'HotelFacilities'	=> (string) $hotel->HotelFacilities,
				'RoomFacilities' 	=> (string) $hotel->RoomFacilities,
				'RoomCount' 		=> (string) $hotel->RoomCount,
				'Pictures' 			=> [],
			];
			if( count($hotel->Pictures->children()) > 0) {
				foreach($hotel->Pictures->Picture as $picture) {
					$response['Pictures'][] = [ 
						'Url' 			=> (string) $picture,
						'Description'	=> (string) $picture->attributes()->Description,
					];
				}
			}
			if(isset($hotel->GeoCodes) && ( count($hotel->GeoCodes->children()) == 2)) {
				$response['GeoCodes']['Latitude'] 	= (string) $hotel->GeoCodes->Latitude;
				$response['GeoCodes']['Longitude'] 	= (string) $hotel->GeoCodes->Longitude;
			}
			if(isset($hotel->TripAdvisor) && ( count($hotel->TripAdvisor->children()) == 4)) {
				$response['TripAdvisor']['Rating'] 			= (string) $hotel->TripAdvisor->Rating;
				$response['TripAdvisor']['RatingImage'] 	= (string) $hotel->TripAdvisor->RatingImage;
				$response['TripAdvisor']['Reviews']			= (string) $hotel->TripAdvisor->Reviews;
				$response['TripAdvisor']['ReviewCount'] 	= (string) $hotel->TripAdvisor->ReviewCount;
			}
		}
        return $response;
	}
	
	public static function parseBookingInsert ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$booking = $xml->Main;
			$response = [
				'GoBookingCode' 		=> (string) $booking->GoBookingCode,
				'GoReference' 			=> (string) $booking->GoReference,
				'ClientBookingCode' 	=> (string) $booking->ClientBookingCode,
				'BookingStatus' 		=> (string) $booking->BookingStatus,
				'TotalPrice' 			=> (string) $booking->TotalPrice,
				'Currency' 				=> (string) $booking->Currency,
				'HotelName'				=> (string) $booking->HotelName,
				'HotelSearchCode' 		=> (string) $booking->HotelSearchCode,
				'CityCode' 				=> (string) $booking->CityCode,
				'RoomType' 				=> (string) $booking->RoomType,
				'RoomBasis' 			=> (string) $booking->RoomBasis,
				'ArrivalDate' 			=> (string) $booking->ArrivalDate,
				'CancellationDeadline' 	=> (string) $booking->CancellationDeadline,
				'Nights' 				=> (string) $booking->Nights,
				'NoAlternativeHotel' 	=> (string) $booking->NoAlternativeHotel,
				'LeaderPersonID' 		=> (string) $booking->Leader->attributes()->LeaderPersonID,
				'Remark' 				=> (string) $booking->Remark,
				'Preferances'			=> [],
				'Rooms'					=> []		
			];
			foreach($booking->Preferances as $preferance) {
				$key 	= (string) $preferance->getName();
				$value 	= (string) $preferance;
				$booking->Preferances[$key] = $value;
			}
			foreach($booking->Rooms as $roomType) {
				$roomTypeData = [
					'Type' 		=> (string) $roomType->attributes()->Type,
					'Adults' 	=> (string) $roomType->attributes()->Adults,
					'Cots' 		=> (string) $roomType->attributes()->Cots,
					'Rooms'		=> []
				];
				foreach($roomType as $room) {
					$roomData = [
						'RoomID' 	=> $room->attributes()->RoomID,
						'Category' 	=> $room->attributes()->Category,
						'Persons'	=> []
					]; 
					foreach($room as $person){
						if($person->getName() == 'PersonName'){
							$personData = [
								'PersonID' 		=> $person->attributes()->PersonID,
								'Type' 			=> 'ADT',
								'PersonName' 	=> (string) $person							
							];
						} else {
							$personData = [
								'PersonID' 		=> $person->attributes()->PersonID,
								'Type' 			=> 'CHD',
								'Age'			=> $person->attributes()->ChildAge,
								'PersonName' 	=> (string) $person							
							];	
						}
						$roomData['Persons'][] = $personData;
					}
					$roomTypeData['Rooms'][] = $roomData;
				}
				$response['Rooms'][] = $roomTypeData;
			}
		}
        return $response;
	}
	
	public static function parseBookingCancel ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {		
			$booking = $xml->Main;
			$response = [
				'GoBookingCode' => $booking->GoBookingCode,
				'BookingStatus' => $booking->BookingStatus,
			];
		}
        return $response;
	}
	
	public static function parseBookingSearch ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {		
			$booking = $xml->Main;
			$response = [
				'GoBookingCode' 		=> (string) $booking->GoBookingCode,
				'GoReference' 			=> (string) $booking->GoReference,
				'ClientBookingCode' 	=> (string) $booking->ClientBookingCode,
				'BookingStatus' 		=> (string) $booking->BookingStatus,
				'TotalPrice' 			=> (string) $booking->TotalPrice,
				'Currency' 				=> (string) $booking->Currency,
				'HotelName'				=> (string) $booking->HotelName,
				'HotelSearchCode' 		=> (string) $booking->HotelSearchCode,
				'CityCode' 				=> (string) $booking->CityCode,
				'RoomType' 				=> (string) $booking->RoomType,
				'RoomBasis' 			=> (string) $booking->RoomBasis,
				'ArrivalDate' 			=> (string) $booking->ArrivalDate,
				'CancellationDeadline' 	=> (string) $booking->CancellationDeadline,
				'Nights' 				=> (string) $booking->Nights,
				'NoAlternativeHotel' 	=> (string) $booking->NoAlternativeHotel,
				'LeaderPersonID' 		=> (string) $booking->Leader->attributes()->LeaderPersonID,
				'Remark' 				=> (string) $booking->Remark,
				'Preferances'			=> [],
				'Rooms'					=> []		
			];
			foreach($booking->Preferances as $preferance) {
				$key 	=  (string) $preferance->getName();
				$value 	=  (string) $preferance;
				$response['Preferances'][$key] = $value;
			}
			foreach($booking->Rooms as $roomType) {
				$roomTypeData = [
					'Type' 		=> (string) $roomType->attributes()->Type,
					'Adults' 	=> (string) $roomType->attributes()->Adults,
					'Cots' 		=> (string) $roomType->attributes()->Cots,
					'Rooms'		=> []
				];
				foreach($roomType as $room) {
					$roomData = [
						'RoomID' 	=> (string) $room->attributes()->RoomID,
						'Category' 	=> (string) $room->attributes()->Category,
						'Persons'	=> []
					]; 
					foreach($room as $person){
						if($person->getName() == 'PersonName'){
							$personData = [
								'PersonID' 		=> (string) $person->attributes()->PersonID,
								'Type' 			=> 'ADT',
								'PersonName' 	=> (string) $person							
							];
						} else {
							$personData = [
								'PersonID' 		=> (string) $person->attributes()->PersonID,
								'Type' 			=> 'CHD',
								'Age'			=> (string) $person->attributes()->ChildAge,
								'PersonName' 	=> (string) $person							
							];	
						}
						$roomData['Persons'][] = $personData;
					}
					$roomTypeData['Rooms'][] = $roomData;
				}
				$response['Rooms'][] = $roomTypeData;
			}
		}
        return $response;
	}
	
	public static function parseBookingSearchAdv ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);		
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {		
			foreach($xml->Main->Bookings->Booking as $booking){
				$bookingData = [
					'GoBookingCode' 		=> (string) $booking->GoBookingCode,
					'GoReference' 			=> (string) $booking->GoReference,
					'ClientBookingCode' 	=> (string) $booking->ClientBookingCode,
					'BookingStatus' 		=> (string) $booking->BookingStatus,
					'TotalPrice' 			=> (string) $booking->TotalPrice,
					'Currency' 				=> (string) $booking->Currency,
					'HotelName'				=> (string) $booking->HotelName,
					'HotelSearchCode' 		=> (string) $booking->HotelSearchCode,
					'CityCode' 				=> (string) $booking->CityCode,
					'RoomType' 				=> (string) $booking->RoomType,
					'RoomBasis' 			=> (string) $booking->RoomBasis,
					'ArrivalDate' 			=> (string) $booking->ArrivalDate,
					'CancellationDeadline' 	=> (string) $booking->CancellationDeadline,
					'Nights' 				=> (string) $booking->Nights,
					'NoAlternativeHotel' 	=> (string) $booking->NoAlternativeHotel,
					'LeaderPersonID' 		=> (string) $booking->Leader->attributes()->LeaderPersonID,
					'Remark' 				=> (string) $booking->Remark,
					'Preferances'			=> [],
					'Rooms'					=> []		
				];
				foreach($booking->Preferances as $preferance) {
					$key 	= (string) $preferance->getName();
					$value 	= (string) $preferance;
					$bookingData['Preferances'][$key] = $value;
				}
				foreach($booking->Rooms as $roomType) {
					$roomTypeData = [
						'Type' 		=> (string) $roomType->attributes()->Type,
						'Adults' 	=> (string) $roomType->attributes()->Adults,
						'Cots' 		=> (string) $roomType->attributes()->Cots,
						'Rooms'		=> []
					];
					foreach($roomType as $room) {
						$roomData = [
							'RoomID' 	=> (string) $room->attributes()->RoomID,
							'Category' 	=> (string) $room->attributes()->Category,
							'Persons'	=> []
						]; 
						foreach($room as $person){
							if($person->getName() == 'PersonName'){
								$personData = [
									'PersonID' 		=> (string) $person->attributes()->PersonID,
									'Type' 			=> 'ADT',
									'PersonName' 	=> (string) $person							
								];
							} else {
								$personData = [
									'PersonID' 		=> (string) $person->attributes()->PersonID,
									'Type' 			=> 'CHD',
									'Age'			=> (string) $person->attributes()->ChildAge,
									'PersonName' 	=> (string) $person							
								];	
							}
							$roomData['Persons'][] = $personData;
						}
						$roomTypeData['Rooms'][] = $roomData;
					}
					$bookingData['Rooms'][] = $roomTypeData;
				}
				$response[] = $bookingData;
			}
		}        
        return $response;
	}
	
	public static function parseBookingCheckStatus ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			foreach($xml->Main->GoBookingCode as $booking){
				$id = (string) $booking;
				$bookingData = [				
					'GoBookingCode' => $id,
					'GoReference' 	=> (string) $booking->attributes()->GoReference,
					'Status' 		=> (string) $booking->attributes()->Status,	
					'TotalPrice' 	=> (string) $booking->attributes()->TotalPrice,	
					'Currency' 		=> (string) $booking->attributes()->TotalPrice
				];
				$response[$id] = $bookingData;
			} 
		}
        return $response;
	}
	
	public static function parseBookingValuationCheck ($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];		
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$booking = $xml->Main;
			$response = [
				'HotelSearchCode' 		=> (string) $booking->HotelSearchCode,
				'ArrivalDate' 			=> (string) $booking->ArrivalDate,
				'CancellationDeadline' 	=> (string) $booking->CancellationDeadline,
				'Remarks' 				=> (string) $booking->Remarks,
				'Rates' 				=> (string) $booking->Rates		
			]; 
		}
        return $response;
	}
	
	public static function parseBookingVoucher($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$booking = $xml->Main;
			$response = [
				'GoBookingCode' 			=> (string) $booking->GoBookingCode,
				'HotelName' 				=> (string) $booking->HotelName,
				'Address' 					=> (string) $booking->Address,
				'Phone' 					=> (string) $booking->Phone,
				'Fax' 						=> (string) $booking->Fax,		
				'CheckInDate' 				=> (string) $booking->CheckInDate,			
				'Nights' 					=> (string) $booking->Nights,	
				'RoomBasis' 				=> (string) $booking->RoomBasis,		
				'Rooms' 					=> (string) $booking->Rooms,		
				'Remarks' 					=> (string) $booking->Remarks,		
				'BookedAndPayableBy' 		=> (string) $booking->BookedAndPayableBy,		
				'SupplierReferenceNumber' 	=> (string) $booking->SupplierReferenceNumber,		
				'EmergencyPhone' 			=> (string) $booking->EmergencyPhone
			];
		}		
        return $response;
	}
	
	public static function parsePiggyPoints($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$status = $xml->Main->Status;
			$response = [
				'code' 			=> (string) $status->attributes()->code,
				'description' 	=> (string) $status->attributes()->description,
				'currencies' 	=> [],
			]; 
			foreach($status->Currency as $currency){
				$currencyData = [
					'code' 		=> (string) $currency->attributes()->code,
					'points' 	=> (string) $currency->attributes()->points
				];
				$response['currencies'][] = $currencyData;
			}
		}      
        return $response;
	}
	
	public static function parsePaymentService($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {		
			$response = [
				'PaymentUrl' 	=> (string) $xml->Main->PaymentUrl
			]; 
		}
        return $response;
	}
	
	public static function parsePriceBreakdown($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$response = [
				'HotelName' 	=> (string) $xml->Main->HotelName,
				'Rooms' 		=> [],
			]; 
			foreach($xml->Main->Room as $room){
				$roomData = [
					'RoomType'			=> (string) $room->RoomType,
					'Children' 			=> (string) $room->Children,
					'Cots' 				=> (string) $room->Cots,
					'PriceBreakdowns'	=> []
				];
				foreach($room->PriceBreakdown as $price_break_down){
					$priceData = [
						'FromDate' 	=> (string) $room->PriceBreakdown->FromDate,
						'ToDate' 	=> (string) $room->PriceBreakdown->ToDate,
						'Price' 	=> (string) $room->PriceBreakdown->Price,
						'Currency' 	=> (string) $room->PriceBreakdown->Currency
					];				
					$roomData['PriceBreakdowns'][] 	= $priceData;
				}
				$response['Rooms'][] = $roomData;
			} 
		}		
        return $response;
	}
	
	public static function parseBookingAmendementInfo($xmlString) {
        $xml = new \SimpleXMLElement($xmlString);
		$response = [];
		if(isset($xml->Main->Error)){ 
			$response['error'] = (string) $xml->Main->Error; 
		} else {
			$response = [
				'ArrivalDate' 	=> (string) $xml->Main->ArrivalDate,
				'Nights' 		=> (string) $xml->Main->Nights,
				'Rooms' 		=> [],
				'Remarks'		=> [],
			];			
			foreach($xml->Main->Rooms as $roomType) {
				$roomTypeData = [
					"Type" 		=> (string) $roomType->attributes()->Type,
					"Adults" 	=> (string) $roomType->attributes()->Adults,
					"Rooms"		=> []
				];
				foreach($roomType as $room) {
					$roomData = [
						'RoomID' 	=> (string) $room->attributes()->RoomID,
						'Category' 	=> (string) $room->attributes()->Category,
						'RoomBasis' => (string) $room->attributes()->RoomBasis,
						'Cots' 		=> (string) $room->attributes()->Cots,
						'Persons'	=> []
					]; 
					foreach($room as $person){
						$personData = [
							'PersonID' 		=> (string) $person->attributes()->PersonID,						
							'FirstName' 	=> (string) $person->FirstName,						
							'LastName' 		=> (string) $person->LastName,						
							'Title' 		=> (string) $person->Title,						
						];
						$roomData['Persons'][] = $personData;
					}
					$roomTypeData['Rooms'][] = $roomData;
				}
				
				$response['Rooms'][] = $roomTypeData;
			} 			
			foreach($xml->Main->Remarks as $remark) {
				$remarkId =  $remark->attributes()->id;
				$response['Remarks'][$remarkId] = (string) $remark;
			}
		}
        return $response;
	}
}
