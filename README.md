### goglobalapi
This is GoGlobal WebService api library written in PHP.

### Install
The preferred way to install this extension is through <a href="http://getcomposer.org/download/">composer</a>.

Either run
```php
php composer.phar require --prefer-dist ustmaestro/goglobalapi "dev-master"
```
or add
```php
"ustmaestro/goglobalapi": "dev-master"
```
to the require section of your composer.json file.

### Usage

If you do not use composer add following line, that will register ustmaestro\goglobalapi namespace
```php
require_once( path_to_loader_class . '/Loader.php');
```
An example o using GGService
```php
use ustmaestro\goglobalapi\GGService;
use ustmaestro\goglobalapi\lib\GGSearchModel;

$config['agency'] = 'Your GG Agency ID';
$config['user'] = 'Your GG Username';
$config['password'] = 'Your GG Password';

$service = new GGService($config);
$model = new GGSearchModel();

$model->city_code = 2016;       // GG internal city code
$model->check_in = '20161003';  // check in date
$model->nights = 3;             // number of nights

$model->rooms_number = 2;       // number of rooms

$model->rooms_persons = [       // persons count by rooms !!! attention index starts with -1-
    '1' => [                    // room 1
        'adult' => 1,           // adults count
        'child' => 0,           // childrens count
    ],
    '2' => [                    // room 1
        'adult' => 2,           // adults count
        'child' => 1,           // childrens count
    ],
];

$results =  $service->searchHotels($model);
var_dump($results);

```
Available service public methods
For more details check api documentation
```php
public function setMaxResults($max)
public function getMaxResults()
public function setTimeout($timeout)
public function getTimeout($timeout)

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
public function getPiggyPoints($bookingId, $emergencyPhone = true)
public function getPaymentService($bookingId, $returnUrl)
public function getPriceBreakdown($hotelCode)
public function getBookingAmendementInfo($bookingId)

```
