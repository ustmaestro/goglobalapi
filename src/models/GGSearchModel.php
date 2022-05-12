<?php

namespace ustmaestro\goglobalapi\models;

use InvalidArgumentException;
use ustmaestro\goglobalapi\lib\GGHelper;
use ustmaestro\goglobalapi\lib\GGStaticTypes;

/**
 * GoGlobal Search hotel model
 */
class GGSearchModel
{
    /**
     * @var string
     */
    public string $cityCode;

    /**
     * @var string
     */
    public string $hotelName;

    /**
     * @var string
     */
    public string $checkInDate;

    /**
     * @var int
     */
    public int $nights = 1;

    /**
     * @var int
     */
    public int $roomsNumber = 0;

    /**
     * @var array
     */
    public array $roomsPersons = [];

    /**
     * @var int
     */
    public int $sortOrder = GGStaticTypes::SORT_ORDER_ASC_PRICE;

    /**
     * @var int
     */
    public int $minPrice = 5;

    /**
     * @var int
     */
    public int $maxPrice = 50000;

    /**
     * @var int
     */
    public int $maxWaitTime = 30;

    /**
     * @var int
     */
    public int $maxResponses = 2000;

    /**
     * @var array
     */
    public array $stars = [];

    /**
     * @var array
     */
    public array $roomBases = [];

    /**
     * @var bool
     */
    public bool $withHotelFacilities = true;

    /**
     * @var bool
     */
    public bool $withRoomFacilities = true;

    /**
     * @var bool
     */
    public bool $apartmentsOnly = false;


    /**
     * Convert search model to xml
     * @return string
     * @throws InvalidArgumentException
     */
    public function toXml(): string
    {
        if ($this->roomsNumber < 1) {
            throw new InvalidArgumentException("Invalid rooms number");
        }

        $xml = GGHelper::wrapTag("SortOrder", $this->sortOrder);
        $xml .= GGHelper::wrapTag("FilterPriceMin", $this->minPrice);
        $xml .= GGHelper::wrapTag("FilterPriceMax", $this->maxPrice);
        $xml .= GGHelper::wrapTag("MaximumWaitTime", $this->maxWaitTime);
        $xml .= GGHelper::wrapTag("MaxResponses", $this->maxResponses);

        if ($this->withHotelFacilities) {
            $xml .= GGHelper::WrapTag("HotelFacilities","true");
        }

        if ($this->withRoomFacilities) {
            $xml .= GGHelper::WrapTag("HotelFacilities","true");
        }

        if (count($this->roomBases)) {
            $roomBasesXml = "";
            foreach ($this->roomBases as $roomBasis) {
                $roomBasesXml .= GGHelper::wrapTag("FilterRoomBasis", $roomBasis);
            }
            $xml .= GGHelper::wrapTag("FilterRoomBasises", $roomBasesXml);
        }

        if (!empty($this->hotelName)) {
            $xml .= GGHelper::wrapTag("HotelName", $this->hotelName);
        }

        $xml .= GGHelper::wrapTag("CityCode", $this->cityCode);
        $xml .= GGHelper::wrapTag("ArrivalDate", date("Y-m-d", strtotime($this->checkInDate)));
        $xml .= GGHelper::wrapTag("Nights", $this->nights);

        if (count($this->stars)) {
            $xml .= GGHelper::wrapTag("Stars", implode(",", $this->stars));
        }

        if ($this->apartmentsOnly) {
            $xml .= GGHelper::wrapTag("Apartments", "true");
        }

        $roomsXml = "";
        for ($i = 1; $i <= $this->roomsNumber; $i++) {
            $roomXml = "";
            if (!empty($this->roomsPersons[$i]["child"])) {
                for ($k = 1; $k <= $this->roomsPersons[$i]["child"]; $k++) {
                    $roomXml .= GGHelper::wrapTag("Room", 5 + $k);
                }
            }
            $roomsXml .= GGHelper::wrapTag("Room", $roomXml, [
                "Adults" => $this->roomsPersons[$i]["adult"],
                "RoomCount" => 1
            ]);
        }
        $xml .= GGHelper::wrapTag("Rooms", $roomsXml);

        return $xml;
    }
}
