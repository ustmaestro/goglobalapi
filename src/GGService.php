<?php

namespace ustmaestro\goglobalapi;

use Exception;
use InvalidArgumentException;
use SoapFault;
use ustmaestro\goglobalapi\lib\GGHelper;
use ustmaestro\goglobalapi\lib\GGResponseParser;
use ustmaestro\goglobalapi\lib\GGStaticTypes;
use ustmaestro\goglobalapi\models\GGSearchModel;

/**
 * GoGlobal Service Class
 */
class GGService
{
    const GG_SERVICE_URL = "https://directbooking.xml.goglobal.travel./xmlwebservice.asmx";
    const GG_API_VERSION = "2.3";
    const GG_API_RESPONSE_FORMAT_JSON = "JSON";
    const GG_API_RESPONSE_FORMAT_XML = "XML";

    /**
     * @var string|null
     */
    private ?string $agency;

    /**
     * @var string|null
     */
    private ?string $user;

    /**
     * @var string|null
     */
    private ?string $password;

    /**
     * @var string|null
     */
    private ?string $url;

    /**
     * @var GGSoapClient
     */
    private GGSoapClient $soapClient;

    /**
     * @var int
     */
    private int $maxResults = 10000;

    /**
     * @var int
     */
    private int $timeout = 60;

    /**
     * @var string
     */
    private string $responseFormat = self::GG_API_RESPONSE_FORMAT_XML;

    /**
     * @var bool
     */
    private bool $parseResult = false;

    /**
     * @var string|null
     */
    private ?string $lastXml;

    /**
     * GoGlobal Service constructor
     *
     * @param array $config
     * @throws SoapFault
     */
    public function __construct(array $config = [])
    {
        foreach (['agency', 'user', 'password'] as $checkedSetting) {
            if (!array_key_exists($checkedSetting, $config) || strlen($config[$checkedSetting]) < 1) {
                throw new InvalidArgumentException("Missing config value: [" . $checkedSetting . "]");
            }
        }
        $this->agency = $config['agency'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->url = $config['url'] ?? self::GG_SERVICE_URL;
        $this->soapClient = new GGSoapClient($this->url . "?WSDL");
    }

    /**
     * Getting GoGlobal API Version
     * @return string
     */
    public function getApiVersion(): string
    {
        return self::GG_API_VERSION;
    }

    /**
     * Setting GoGlobal request max results
     * @return $this
     */
    public function setMaxResults(int $maxResults): self
    {
        $this->maxResults = $maxResults;
        return $this;
    }

    /**
     * Getting GoGlobal request max results
     * @return int
     */
    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    /**
     * Setting GoGlobal request timeout
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Getting GoGlobal request timeout
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Getting GoGlobal service response format
     * @return string
     */
    public function getResponseFormat(): string
    {
        return $this->responseFormat;
    }

    /**
     * Setting GoGlobal service response format
     * @param string $responseFormat
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setResponseFormat(string $responseFormat): self
    {
        if (!in_array($responseFormat, [self::GG_API_RESPONSE_FORMAT_XML, self::GG_API_RESPONSE_FORMAT_JSON])) {
            throw new InvalidArgumentException("Invalid config, response format: [" . $responseFormat . "]");
        }
        $this->responseFormat = $responseFormat;
        return $this;
    }

    /**
     * @return bool
     */
    public function getParseResult(): bool
    {
        return $this->parseResult;
    }

    /**
     * @param bool $parseResult
     * @return $this
     */
    public function setParseResult(bool $parseResult): self
    {
        $this->parseResult = $parseResult;
        return $this;
    }

    /**
     * Getting GoGlobal last request xml
     * @return string|null
     */
    public function getLastXml(): ?string
    {
        return $this->lastXml;
    }

    /**
     * Make GoGlobal request header
     * @param int $requestType
     * @return string
     */
    protected function _makeXmlHeader(int $requestType): string
    {
        $xml = GGHelper::wrapTag("Agency", $this->agency);
        $xml .= GGHelper::wrapTag("User", $this->user);
        $xml .= GGHelper::wrapTag("Password", $this->password);
        $xml .= GGHelper::wrapTag("Operation", GGStaticTypes::REQUEST_TYPE[$requestType]);
        $xml .= GGHelper::wrapTag("OperationType", "Request");
        return GGHelper::wrapTag("Header", $xml);
    }

    /**
     * Sending GoGlobal request
     * @param int $requestType
     * @param string $xmlRequest
     * @return string
     */
    protected function _sendRequest(int $requestType, string $xmlRequest = ""): string
    {
        $xml = $this->_makeXmlHeader($requestType);
        $xml .= GGHelper::wrapTag("Main", $xmlRequest, [
            "Version" => $this->getApiVersion(),
            "ResponseFormat" => $this->getResponseFormat()
        ]);
        $xml = GGHelper::wrapTag("Root", $xml);

        $this->lastXml = $xml;
        $resp = $this->soapClient->MakeRequest([
            "requestType" => $requestType,
            "xmlRequest" => $xml,
        ]);        
        return $resp->MakeRequestResult;
    }

    /**
     * Search hotels without geocodes
     * @param GGSearchModel $model
     * @return array|string
     * @throws Exception
     */
    public function searchHotels(GGSearchModel $model): array|string
    {
        $requestType = 1;
        $xml = $model->toXml();
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseHotelsList($this->_sendRequest($requestType, $xml));
    }

    /**
     * Search hotels with geocodes
     * @param GGSearchModel $model
     * @return array|string
     * @throws Exception
     */
    public function searchHotelsGeo(GGSearchModel $model): array|string
    {
        $requestType = 11;
        $xml = $model->toXml();
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseHotelsList($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting hotel information without geocodes
     * @param string $hotelCode
     * @return array|string
     * @throws Exception
     */
    public function getHotelInfo(string $hotelCode): array|string
    {
        $requestType = 6;
        $xml = GGHelper::wrapTag("HotelSearchCode", $hotelCode);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseHotelInfo($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting hotel information with geocodes
     * @param string $hotelCode
     * @return array|string
     * @throws Exception
     */
    public function getHotelInfoGeo(string $hotelCode): array|string
    {
        $requestType = 61;
        $xml = GGHelper::wrapTag("HotelSearchCode", $hotelCode);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseHotelInfo($this->_sendRequest($requestType, $xml));
    }

    /**
     * Insert new booking
     * @param string $agentReference
     * @param string $hotelCode
     * @param string $hotelCheckIn
     * @param string $hotelNights
     * @param string $hasAlternative
     * @param string $leaderId
     * @param array $bookingRooms
     * @return array|string
     * @throws Exception
     */
    public function insertBooking(
        string $agentReference, 
        string $hotelCode, 
        string $hotelCheckIn, 
        string $hotelNights, 
        string $hasAlternative, 
        string $leaderId, 
        array $bookingRooms
    ): array|string {
        $requestType = 2;
        $xml = GGHelper::wrapTag("AgentReference", $agentReference);
        $xml .= GGHelper::wrapTag("HotelSearchCode", $hotelCode);
        $xml .= GGHelper::wrapTag("ArrivalDate", $hotelCheckIn);
        $xml .= GGHelper::wrapTag("Nights", $hotelNights);
        $xml .= GGHelper::wrapTag("NoAlternativeHotel", $hasAlternative);
        $xml .= GGHelper::wrapTag("Leader", "", ["LeaderPersonID" => $leaderId]);
        $roomTypeXml = "";
        foreach ($bookingRooms as $roomType => $rooms) {
            $roomXml = "";
            foreach ($rooms as $roomId => $room) {
                $personXml = "";
                foreach ($room as $personId => $person) {
                    if ($person["type"] == "adult") {
                        $personXml .= GGHelper::wrapTag("PersonName", $person["name"], [
                            "PersonID" => $personId
                        ]);
                    } else if ($person["type"] == "child") {
                        $personXml .= GGHelper::wrapTag("ExtraBed", $person["name"], [
                            "PersonID" => $personId,
                            "ChildAge" => $person["age"]
                        ]);
                    }
                }
                $roomXml .= GGHelper::wrapTag("Room", $personXml, ["RoomID" => $roomId]);
            }
            $roomTypeXml .= GGHelper::wrapTag("RoomType", $roomXml, ["Type" => $roomType]);
        }
        $xml .= GGHelper::wrapTag("Rooms", $roomTypeXml);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingInsert($this->_sendRequest($requestType, $xml));
    }

    /**
     * Cancel booking
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function cancelBooking(string $bookingId): array|string
    {
        $requestType = 3;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingCancel($this->_sendRequest($requestType, $xml));
    }

    /**
     * Search booking
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function searchBooking(string $bookingId): array|string
    {
        $requestType = 4;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingSearch($this->_sendRequest($requestType, $xml));
    }

    /**
     * Search booking advanced
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $paxName
     * @param string $cityCode
     * @param string $nights
     * @param string $hotelName
     * @return array|string
     * @throws Exception
     */
    public function searchBookingAdvanced(
        string $dateFrom = "",
        string $dateTo = "",
        string $paxName = "",
        string $cityCode = "",
        string $nights = "",
        string $hotelName = ""
    ): array|string {
        $requestType = 10;
        $xml = (!empty($dateFrom)) ? GGHelper::wrapTag('ArrivalDateRangeFrom', $dateFrom) : "";
        $xml .= (!empty($dateTo)) ? GGHelper::wrapTag('ArrivalDateRangeTo', $dateTo) : "";
        $xml .= (!empty($paxName)) ? GGHelper::wrapTag('PaxName', $paxName) : "";
        $xml .= (!empty($cityCode)) ? GGHelper::wrapTag('CityCode', $cityCode) : "";
        $xml .= (!empty($nights)) ? GGHelper::wrapTag('Nights', $nights) : "";
        $xml .= (!empty($hotelName)) ? GGHelper::wrapTag('HotelName', $hotelName) : "";
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingSearchAdv($this->_sendRequest($requestType, $xml));
    }

    /**
     * Check single booking status
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function checkBookingStatus(string $bookingId): array|string
    {
        $requestType = 5;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingCheckStatus($this->_sendRequest($requestType, $xml));
    }

    /**
     * Check multiple booking status
     * @param array $bookingsIds
     * @return array|string
     * @throws Exception
     */
    public function checkBookingsStatuses(array $bookingsIds = []): array|string
    {
        $requestType = 5;
        $xml = "";
        foreach ($bookingsIds as $bookingId) {
            $xml .= GGHelper::wrapTag('GoBookingCode', $bookingId);
        }
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingCheckStatus($this->_sendRequest($requestType, $xml));
    }

    /**
     * Check booking valuation
     * @param string $hotelCode
     * @param string $arrivalDate
     * @return array|string
     * @throws Exception
     */
    public function checkBookingValuation(string $hotelCode, string $arrivalDate): array|string
    {
        $requestType = 9;
        $xml = GGHelper::wrapTag('HotelSearchCode', $hotelCode);
        $xml .= GGHelper::wrapTag('ArrivalDate', $arrivalDate);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingValuationCheck($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting booking voucher
     * @param string $bookingId
     * @param bool $emergencyPhone
     * @return array|string
     * @throws Exception
     */
    public function getBookingVoucher(string $bookingId, bool $emergencyPhone = true): array|string
    {
        $requestType = 8;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        $xml .= GGHelper::wrapTag('GetEmergencyPhone', $emergencyPhone);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingVoucher($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting booking voucher
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function getPiggyPoints(string $bookingId): array|string
    {
        $requestType = 12;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parsePiggyPoints($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting price breakdown
     * @param string $bookingId
     * @param string $returnUrl
     * @return array|string
     * @throws Exception
     */
    public function getPaymentService(string $bookingId, string $returnUrl): array|string
    {
        $requestType = 13;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        $xml .= GGHelper::wrapTag('ReturnUrl', $returnUrl);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parsePaymentService($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting price breakdown
     * @param string $hotelCode
     * @return array|string
     * @throws Exception
     */
    public function getPriceBreakdown(string $hotelCode): array|string
    {
        $requestType = 14;
        $xml = GGHelper::wrapTag('HotelSearchCode', $hotelCode);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parsePriceBreakdown($this->_sendRequest($requestType, $xml));
    }

    /**
     * Getting booking amendment info
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function getBookingAmendmentInfo(string $bookingId): array|string
    {
        $requestType = 15;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingAmendmentInfo($this->_sendRequest($requestType, $xml));
    }

    /**
     * Booking amendment request
     * @param string $bookingId
     * @return array|string
     * @throws Exception
     */
    public function bookingAmendmentRequest(string $bookingId): array|string
    {
        $requestType = 16;
        $xml = GGHelper::wrapTag('GoBookingCode', $bookingId);
        return (($this->getResponseFormat() == self::GG_API_RESPONSE_FORMAT_JSON) || !$this->getParseResult())
            ? $this->_sendRequest($requestType, $xml)
            : GGResponseParser::parseBookingAmendmentInfo($this->_sendRequest($requestType, $xml));
    }
}
