<?php

namespace ustmaestro\goglobalapi\lib;

/**
 * GoGlobal static types and definitions
 */
class GGStaticTypes
{
    /**
     * Request Type codes and descriptions
     */
    const REQUEST_TYPE = [
        1 => "HOTEL_SEARCH_REQUEST",                   // Availability Search
        11 => "HOTEL_SEARCH_REQUEST",                  // Availability Search /W Geo

        6 => "HOTEL_INFO_REQUEST",                     // Hotel Information
        61 => "HOTEL_INFO_REQUEST",                    // Hotel Information /W Geo

        2 => "BOOKING_INSERT_REQUEST",                 // Booking Insert
        3 => "BOOKING_CANCEL_REQUEST",                 // Booking Cancellation
        4 => "BOOKING_SEARCH_REQUEST",                 // Booking Search
        10 => "ADV_BOOKING_SEARCH_REQUEST",            // Booking Search Adv
        5 => "BOOKING_STATUS_REQUEST",                 // Booking Status
        8 => "VOUCHER_DETAILS_REQUEST",                // Voucher Details
        9 => "BOOKING_VALUATION_REQUEST",              // Booking Valuation
        12 => "PIGGIBANK_STATUS_REQUEST",              // PiggyBank Details
        13 => "BOOKING_PAYMENT_REQUEST",               // Booking Payment
        14 => "PRICE_BREAKDOWN_REQUEST",               // Price Breakdown
        15 => "BOOKING_INFO_FOR_AMENDMENT_REQUEST",    // Amendment Options
        16 => "BOOKING_AMENDMENT_REQUEST",             // Amendment Request

        17 => "TRANSFER_SEARCH_REQUEST",               // Transfer Search
        19 => "TB_INSERT_REQUEST"                      // Transfer Booking
    ];

    const PERSON_TYPE_ADT = "adult";
    const PERSON_TYPE_CHD = "child";
    const PERSON_TYPE_CODES = [
        self::PERSON_TYPE_ADT => "Adult",
        self::PERSON_TYPE_CHD => "Child",
    ];

    /**
     * Room Type codes and description
     */
    const ROOM_TYPE_SGL = "SGL";
    const ROOM_TYPE_DBL = "DBL";
    const ROOM_TYPE_TWN = "TWN";
    const ROOM_TYPE_TPL = "TPL";
    const ROOM_TYPE_QDR = "QDR";
    const ROOM_TYPE_DBLSGL = "DBLSGL";
    const ROOM_TYPE_CODES = [
        self::ROOM_TYPE_SGL => "Single room",
        self::ROOM_TYPE_DBL => "Double room",
        self::ROOM_TYPE_TWN => "Twin room",
        self::ROOM_TYPE_TPL => "Triple room",
        self::ROOM_TYPE_QDR => "Quadruple room",
        self::ROOM_TYPE_DBLSGL => "Double for Single Use"
    ];

    /**
     * Room Basis codes and description
     * Used for filtering hotel results
     */
    const ROOM_BASIS_BB = "BB";
    const ROOM_BASIS_CB = "CB";
    const ROOM_BASIS_AI = "AI";
    const ROOM_BASIS_FB = "FB";
    const ROOM_BASIS_HB = "HB";
    const ROOM_BASIS_RO = "RO";
    const ROOM_BASIS_BD = "BD";
    const ROOM_BASIS_UA = "UA";
    const ROOM_BASIS_CODES = [
        self::ROOM_BASIS_BB => "BED AND BREAKFAST",        // Room with bed and breakfast
        self::ROOM_BASIS_CB => "CONTINENTAL BREAKFAST",    // Room with continental breakfast
        self::ROOM_BASIS_AI => "ALL-INCLUSIVE",            // Room with all-inclusive
        self::ROOM_BASIS_FB => "FULL-BOARD",               // Room with full-board
        self::ROOM_BASIS_HB => "HALF-BOARD",               // Room with half-board
        self::ROOM_BASIS_RO => "ROOM ONLY",                // Room only
        self::ROOM_BASIS_BD => "BED AND DINNER",           // Room with bed and dinner
        self::ROOM_BASIS_UA => "ULTRA ALL-INCLUSIVE"       // Room with ultra all-inclusive
    ];

    /**
     * Star Rating codes and description
     * Used for filtering hotel results
     */
    const STAR_RATING_1 = 1;
    const STAR_RATING_2 = 2;
    const STAR_RATING_3 = 3;
    const STAR_RATING_4 = 4;
    const STAR_RATING_5 = 5;
    const STAR_RATING_6 = 6;
    const STAR_RATING_7 = 7;
    const STAR_RATING_8 = 8;
    const STAR_RATING_9 = 9;
    const STAR_RATING_10 = 10;
    const STAR_RATING_11 = 11;
    const STAR_RATING_CODES = [
        self::STAR_RATING_1 => "1 star",
        self::STAR_RATING_2 => "1.5 stars",
        self::STAR_RATING_3 => "2 stars",
        self::STAR_RATING_4 => "2.5 stars",
        self::STAR_RATING_5 => "3 stars",
        self::STAR_RATING_6 => "3.5 stars",
        self::STAR_RATING_7 => "4 stars",
        self::STAR_RATING_8 => "4.5 star",
        self::STAR_RATING_9 => "5 stars",
        self::STAR_RATING_10 => "5.5 stars",
        self::STAR_RATING_11 => "6 stars"
    ];

    /**
     * Sort Order codes and description
     * Used for filtering hotel results
     */
    const SORT_ORDER_ASC_PRICE = 1;
    const SORT_ORDER_DESC_PRICE = 2;
    const SORT_ORDER_ASC_CLX = 3;
    const SORT_ORDER_DESC_CLX = 4;
    const SORT_ORDER_CODES = [
        self::SORT_ORDER_ASC_PRICE => "By price asc",          // Sort first by lowest prices
        self::SORT_ORDER_DESC_PRICE => "By price desc",        // Sort first by high prices
        self::SORT_ORDER_ASC_CLX  => "By CXL deadline asc",    // Sort first by smallest cancellation dates
        self::SORT_ORDER_DESC_CLX  => "By CXL deadline desc",  // Sort first by latest cancellation dates
    ];

    /**
     * Booking Status codes and description
     * Used for filtering or info in booking results
     */
    const BOOKING_STATUS_RQ = "RQ";
    const BOOKING_STATUS_C = "C";
    const BOOKING_STATUS_RX = "RX";
    const BOOKING_STATUS_X = "X";
    const BOOKING_STATUS_RJ = "RJ";
    const BOOKING_STATUS_VCH = "VCH";
    const BOOKING_STATUS_VRQ = "VRQ";
    const BOOKING_STATUS_CODES = [
        self::BOOKING_STATUS_RQ => "Requested",             // Booking is created and waits next status
        self::BOOKING_STATUS_C => "Confirmed",              // Booking is confirmed
        self::BOOKING_STATUS_RX => "Req. Cancellation",     // Booking cancellation requested and waits next status
        self::BOOKING_STATUS_X => "Cancelled",              // Booking is cancelled
        self::BOOKING_STATUS_RJ => "Rejected",              // Booking is rejected
        self::BOOKING_STATUS_VCH => "Voucher Issued",       // Booking voucher issued
        self::BOOKING_STATUS_VRQ => "Voucher Req."          // Booking voucher was requested and waits next status
    ];

    /**
     * Amendment Categories codes and description
     * Used for amendment request
     */
    const AMENDMENT_TYPE_STANDARD = "STANDARD";
    const AMENDMENT_TYPE_SUPERIOR = "SUPERIOR";
    const AMENDMENT_TYPE_DELUXE = "DELUXE";
    const AMENDMENT_TYPE_LUXURY = "LUXURY";
    const AMENDMENT_TYPE_PREMIUM = "PREMIUM";
    const AMENDMENT_TYPE_JUNIOR_SUITE = "JUNIOR SUITE";
    const AMENDMENT_TYPE_MINI_SUITE = "MINI SUITE";
    const AMENDMENT_TYPE_SUITE = "SUITE";
    const AMENDMENT_TYPE_STUDIO = "STUDIO";
    const AMENDMENT_TYPE_EXECUTIVE = "EXECUTIVE";
    const AMENDMENT_TYPE_CODES = [
        self::AMENDMENT_TYPE_STANDARD => "STANDARD",
        self::AMENDMENT_TYPE_SUPERIOR => "SUPERIOR",
        self::AMENDMENT_TYPE_DELUXE => "DELUXE",
        self::AMENDMENT_TYPE_LUXURY => "LUXURY",
        self::AMENDMENT_TYPE_PREMIUM => "PREMIUM",
        self::AMENDMENT_TYPE_JUNIOR_SUITE => "JUNIOR SUITE",
        self::AMENDMENT_TYPE_SUITE => "SUITE",
        self::AMENDMENT_TYPE_MINI_SUITE => "MINI SUITE",
        self::AMENDMENT_TYPE_STUDIO => "STUDIO",
        self::AMENDMENT_TYPE_EXECUTIVE => "EXECUTIVE"
    ];

    /**
     * Error Codes
     */
    const ERROR_CODES = [
        100 => "General Errors",
        101 => "Error Parsing XML. Empty XML!",
        102 => "Error Parsing XML. Wrong Structure!",
        103 => "Cannot parse Operation!",
        104 => "Invalid Agency Header Details!",
        105 => "Could Not Retrieve Info - Try Again Later!",
        200 => "Hotel Search Errors",
        201 => "Cannot parse City Code!",
        202 => "Cannot parse Arrival Date!",
        203 => "Cannot parse Nights!",
        204 => "Cannot parse Total Number Of Rooms!",
        205 => "Cannot parse Type Of Room!",
        206 => "Cannot parse Number Of Rooms!",
        207 => "Wrong Room Type Specified!",
        208 => "Invalid Date Format!",
        209 => "The Specified City Code Does Not Exist!",
        210 => "Maximum Number Of Nights Is 99!",
        211 => "Too many Extra Beds or Cots",
        212 => "Children can only be in a Double or Twin Room",
        213 => "Cots can only be in a Double or Twin Room",
        214 => "We only allow search for 3 rooms in one search.",
        215 => "Wrong Child Age Specified.! The Child Age must be between 2 and 10.",
        216 => "Invalid destination",
        300 => "Booking Insert Errors",
        301 => "Cannot parse Agent Reference!",
        302 => "Cannot parse Hotel Search Code!",
        303 => "Cannot parse Arrival Date!",
        304 => "Cannot parse Nights!",
        305 => "Cannot parse NoAlternativeHotel!",
        306 => "Cannot parse LeaderPersonID!",
        307 => "Cannot parse RoomTypes!",
        308 => "Cannot parse Type Of Room!",
        309 => "Too many Extra Beds or Cots or Too many Cots",
        310 => "Wrong Room Type Specified!",
        311 => "Invalid Date Format!",
        312 => "The Specified Hotel Search Code Does Not Exist!",
        313 => "Maximum Number Of Nights Is 99!",
        314 => "Error In Sent XML. Wrong Structure!",
        315 => "Error Parsing Returned XML. Wrong Structure!",
        400 => "Booking Cancel Errors",
        401 => "Cannot parse GoBookingCode!",
        402 => "The Specified GoBookingCode Does Not Exist!",
        500 => "Booking Search Errors",
        501 => "Cannot parse GoBookingCode!",
        502 => "The Specified GoBookingCode Does Not Exist!",
        600 => "Booking Status Errors",
        601 => "Cannot parse Bookings!",
        700 => "Voucher request allowed only for booking on status confirm",
        701 => "this agency is not allowed to issue voucher -  Voucher Request was sent to Handling Office",
        702 => "credit limit is exceeded",
        703 => "Cannot cancel the booking at this time, please try later",
        704 => "This booking cannot be amended, please contact your Handling office for details"
    ];

    /**
     *  Messages
     */
    const MESSAGES = [
        1 => "No Hotels Were Found Matching Searched Criteria!",
        2 => "The Cancellation Date is After The Cancellation Deadline. Please Contact Your Handling Office.",
        3 => "There Was No Booking(s) Found In The System Corresponding To The Given Booking Code(s)!",
        4 => "No bookings were found"
    ];
}
