### goglobalapi
This is GoGlobal WebService api library written in PHP.

### Requirements
In order to use this library your php server need to meet fallowing requirements:
```
php version >= 8.0
php-xml module enabled
php-soap module enabled
```

### Install
The preferred way to install this extension is through <a href="http://getcomposer.org/download/">composer</a>.

```php
php composer.phar require --prefer-dist ustmaestro/goglobalapi "dev-master"
```
or add
```php
"ustmaestro/goglobalapi": "dev-master"
```
to the `require` section of your `composer.json` file.

### Usage
An example o using GGService

```php
use ustmaestro\goglobalapi\GGService;
use ustmaestro\goglobalapi\lib\GGStaticTypes;
use ustmaestro\goglobalapi\models\GGSearchModel;

$config['agency'] = 'Your GG Agency ID';
$config['user'] = 'Your GG Username';
$config['password'] = 'Your GG Password';

$service = new GGService($config);
$service
    ->setMaxResults(200)
    ->setTimeout(120)
    ->setResponseFormat(GGService::GG_API_RESPONSE_FORMAT_JSON);

$model = new GGSearchModel();
$model->cityCode = 2016;           // GG internal city code
$model->checkInDate = '20221003';  // check in date (format:Ymd)
$model->nights = 3;                // number of nights
$model->roomsNumber = 2;           // number of rooms
$model->roomsPersons = [           // persons count by rooms !!! attention index starts with -1-
    1 => [                                                // room 1
        GGStaticTypes::PERSON_TYPE_ADT => 1,              // adults count
        GGStaticTypes::PERSON_TYPE_CHD => 0,              // child count
    ],
    2 => [                                                // room 2
        GGStaticTypes::PERSON_TYPE_ADT => 2,              // adults count
        GGStaticTypes::PERSON_TYPE_CHD => 1,              // child count
    ],
];

$results = $service->searchHotels($model);
var_dump($results);

```
Available service public methods
For more details check api documentation
```php
// Service config helpers
public function setMaxResults(int $maxResults)               // Max number of search results
public function getMaxResults()
public function setTimeout(int $timeout)                     // Request timeout in seconds
public function getTimeout()
public function setResponseFormat(string $responseFormat)    // Response format, available JSON and XML
public function getResponseFormat()
public function setParseResult(bool $parseResult)            // Use or not internal XML parser
public function getParseResult()


// Service API methods
public function searchHotels(GGSearchModel $model)
public function searchHotelsGeo(GGSearchModel $model)
public function getHotelInfo($hotelCode)
public function getHotelInfoGeo($hotelCode)
public function insertBooking($agentReference, $hotelCode, $hotelCheckIn, $hotelNights, $hasAlternative, $leaderId, $bookingRooms)
public function cancelBooking($bookingId)
public function searchBooking($bookingId)
public function searchBookingAdvanced($dateFrom = "", $dateTo = "", $paxName = "", $cityCode = "", $nights = "", $hotelName = "")
public function checkBookingStatus($bookingId)
public function checkBookingsStatus($bookingsId = [])
public function checkBookingValuation($hotelCode, $arrivalDate)
public function getBookingVoucher($bookingId, $emergencyPhone = true)
public function getPiggyPoints($bookingId)
public function getPaymentService($bookingId, $returnUrl)
public function getPriceBreakdown($hotelCode)
public function getBookingAmendmentInfo($bookingId)
public function bookingAmendmentRequest($bookingId)

```

### TO DO List
* Configs fom .env or yaml
* OOP Parser Response Models
* Verify parser by API version
* Testing
* Documentation
