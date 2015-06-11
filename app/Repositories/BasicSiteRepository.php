<?php
/**
 * Created by PhpStorm.
 * User: piyush sharma
 * Date: 28-04-2015
 * Time: 21:14
 */

namespace talenthub\Repositories;


class BasicSiteRepository {


    /**
 * Defining user types involved in the website
 *
 * @return array of user types available having index as used
 */
    public static function getUserType()
    {
        return array(
            SiteConstants::USER_TALENT,
            SiteConstants::USER_PARENT,
            SiteConstants::USER_MANAGER_COACH,
            SiteConstants::USER_MANAGER_AGENT,
            SiteConstants::USER_MANAGER_SCOUT
        );
    }


    /**
     * Retuning Manager Types
     * @return array
     */
    public static function getManagerTypes()
    {
        return [
            '1'=>SiteConstants::USER_MANAGER_COACH,
            '2'=>SiteConstants::USER_MANAGER_AGENT,
            '3'=>SiteConstants::USER_MANAGER_SCOUT,
        ];
    }




    /**
     * Defining user management level type involved in the website
     *
     * @return array of user management level type available having index as used
     */
    public static function getUserManagementLevelType($user_type)
    {
        if($user_type==SiteConstants::USER_TALENT)
        {
            return array(1=>SiteConstants::USER_TALENT_MANAGEMENT_LEVEL_STUDENT, 2=>SiteConstants::USER_TALENT_MANAGEMENT_LEVEL_ASPIRING_PRO,
                3=>SiteConstants::USER_TALENT_MANAGEMENT_LEVEL_STUDENT_ASPIRING_PRO);
        }
        else if(in_array($user_type, array(SiteConstants::USER_MANAGER,SiteConstants::USER_MANAGER_COACH, SiteConstants::USER_MANAGER_AGENT,
                SiteConstants::USER_MANAGER_SCOUT)))
        {
            return array(1=>SiteConstants::USER_MANAGER_MANAGEMENT_LEVEL_PRO, 2=>SiteConstants::USER_MANAGER_MANAGEMENT_LEVEL_SEMI_PRO,
                3=>SiteConstants::USER_MANAGER_MANAGEMENT_LEVEL_AMATEUR, 4=>SiteConstants::USER_MANAGER_MANAGEMENT_LEVEL_UNIVERSITY,
                5=>SiteConstants::USER_MANAGER_MANAGEMENT_LEVEL_HIGH_SCHOOL);
        }
        return false;
    }


    /**
     * Sports need to be involved in the application
     *
     * @return array
     */
    public static function getSportTypes()
    {
        return array(
            0 => '-- Select Sport --',
            1 => SportsRepository::BASEBALL,
            2 => SportsRepository::BASKETBALL,
            3 => SportsRepository::FOOTBALL,
            4 => SportsRepository::RUGBY,
            5 => SportsRepository::SOCCER,
            6 => SportsRepository::SWIMMING,
            7 => SportsRepository::TENNIS,
            8 => SportsRepository::TRACK_FIELD,
            9 => SportsRepository::SOFTBALL,
            10 => SportsRepository::WATERPOLO
        );
    }


    /**
     *Career Information Types i.e. club or school as defined in SiteConstants
     */
    public static function getCareerInformationTypes()
    {
        return array(
            0=>'-- Select Type --',
            1=>SiteConstants::CAREER_TYPE_CLUB,
            2=>SiteConstants::CAREER_TYPE_SCHOOL
        );
    }


    /**
     *
     * @return array of countries
     */
    public static function getListOfCountries($withNumericIndex = true)
    {
        if($withNumericIndex)
        {
            return array(
                0=>"-- Select Country --",
                1=>"Afghanistan",
                2=>"Akrotiri",
                3=>"Albania",
                4=>"Algeria",
                5=>"American Samoa",
                6=>"Andorra",
                7=>"Angola",
                8=>"Anguilla",
                9=>"Antarctica",
                10=>"Antigua and Barbuda",
                11=>"Argentina",
                12=>"Armenia",
                13=>"Aruba",
                14=>"Ashmore and Cartier Islands",
                15=>"Australia",
                16=>"Austria",
                17=>"Azerbaijan",
                18=>"Bahamas, The",
                19=>"Bahrain",
                20=>"Bangladesh",
                21=>"Barbados",
                22=>"Bassas da India",
                23=>"Belarus",
                24=>"Belgium",
                25=>"Belize",
                26=>"Benin",
                27=>"Bermuda",
                28=>"Bhutan",
                29=>"Bolivia",
                30=>"Bosnia and Herzegovina",
                31=>"Botswana",
                32=>"Bouvet Island",
                33=>"Brazil",
                34=>"British Indian Ocean Territory",
                35=>"British Virgin Islands",
                36=>"Brunei",
                37=>"Bulgaria",
                38=>"Burkina Faso",
                39=>"Burma",
                40=>"Burundi",
                41=>"Cambodia",
                42=>"Cameroon",
                43=>"Canada",
                44=>"Cape Verde",
                45=>"Cayman Islands",
                46=>"Central African Republic",
                47=>"Chad",
                48=>"Chile",
                49=>"China",
                50=>"Christmas Island",
                51=>"Clipperton Island",
                52=>"Cocos (Keeling) Islands",
                53=>"Colombia",
                54=>"Comoros",
                55=>"Congo, Democratic Republic of the",
                56=>"Congo, Republic of the",
                57=>"Cook Islands",
                58=>"Coral Sea Islands",
                59=>"Costa Rica",
                60=>"Cote d'Ivoire",
                61=>"Croatia",
                62=>"Cuba",
                63=>"Cyprus",
                64=>"Czech Republic",
                65=>"Denmark",
                66=>"Dhekelia",
                67=>"Djibouti",
                68=>"Dominica",
                69=>"Dominican Republic",
                70=>"Ecuador",
                71=>"Egypt",
                72=>"El Salvador",
                73=>"Equatorial Guinea",
                74=>"Eritrea",
                75=>"Estonia",
                76=>"Ethiopia",
                77=>"Europa Island",
                78=>"Falkland Islands (Islas Malvinas)",
                79=>"Faroe Islands",
                80=>"Fiji",
                81=>"Finland",
                82=>"France",
                83=>"French Guiana",
                84=>"French Polynesia",
                85=>"French Southern and Antarctic Lands",
                86=>"Gabon",
                87=>"Gambia, The",
                88=>"Gaza Strip",
                89=>"Georgia",
                90=>"Germany",
                91=>"Ghana",
                92=>"Gibraltar",
                93=>"Glorioso Islands",
                94=>"Greece",
                95=>"Greenland",
                96=>"Grenada",
                97=>"Guadeloupe",
                98=>"Guam",
                99=>"Guatemala",
                100=>"Guernsey",
                101=>"Guinea",
                102=>"Guinea-Bissau",
                103=>"Guyana",
                104=>"Haiti",
                105=>"Heard Island and McDonald Islands",
                106=>"Holy See (Vatican City)",
                107=>"Honduras",
                108=>"Hong Kong",
                109=>"Hungary",
                110=>"Iceland",
                111=>"India",
                112=>"Indonesia",
                113=>"Iran",
                114=>"Iraq",
                115=>"Ireland",
                116=>"Isle of Man",
                117=>"Israel",
                118=>"Italy",
                119=>"Jamaica",
                120=>"Jan Mayen",
                121=>"Japan",
                122=>"Jersey",
                123=>"Jordan",
                124=>"Juan de Nova Island",
                125=>"Kazakhstan",
                126=>"Kenya",
                127=>"Kiribati",
                128=>"Korea, North",
                129=>"Korea, South",
                130=>"Kuwait",
                131=>"Kyrgyzstan",
                132=>"Laos",
                133=>"Latvia",
                134=>"Lebanon",
                135=>"Lesotho",
                136=>"Liberia",
                137=>"Libya",
                138=>"Liechtenstein",
                139=>"Lithuania",
                140=>"Luxembourg",
                141=>"Macau",
                142=>"Macedonia",
                143=>"Madagascar",
                144=>"Malawi",
                145=>"Malaysia",
                146=>"Maldives",
                147=>"Mali",
                148=>"Malta",
                149=>"Marshall Islands",
                150=>"Martinique",
                151=>"Mauritania",
                152=>"Mauritius",
                153=>"Mayotte",
                154=>"Mexico",
                155=>"Micronesia, Federated States of",
                156=>"Moldova",
                157=>"Monaco",
                158=>"Mongolia",
                159=>"Montserrat",
                160=>"Morocco",
                161=>"Mozambique",
                162=>"Namibia",
                163=>"Nauru",
                164=>"Navassa Island",
                165=>"Nepal",
                166=>"Netherlands",
                167=>"Netherlands Antilles",
                168=>"New Caledonia",
                169=>"New Zealand",
                170=>"Nicaragua",
                171=>"Niger",
                172=>"Nigeria",
                173=>"Niue",
                174=>"Norfolk Island",
                175=>"Northern Mariana Islands",
                176=>"Norway",
                177=>"Oman",
                178=>"Pakistan",
                179=>"Palau",
                180=>"Panama",
                181=>"Papua New Guinea",
                182=>"Paracel Islands",
                183=>"Paraguay",
                184=>"Peru",
                185=>"Philippines",
                186=>"Pitcairn Islands",
                187=>"Poland",
                188=>"Portugal",
                189=>"Puerto Rico",
                190=>"Qatar",
                191=>"Reunion",
                192=>"Romania",
                193=>"Russia",
                194=>"Rwanda",
                195=>"Saint Helena",
                196=>"Saint Kitts and Nevis",
                197=>"Saint Lucia",
                198=>"Saint Pierre and Miquelon",
                199=>"Saint Vincent and the Grenadines",
                200=>"Samoa",
                201=>"San Marino",
                202=>"Sao Tome and Principe",
                203=>"Saudi Arabia",
                204=>"Senegal",
                205=>"Serbia and Montenegro",
                206=>"Seychelles",
                207=>"Sierra Leone",
                208=>"Singapore",
                209=>"Slovakia",
                210=>"Slovenia",
                211=>"Solomon Islands",
                212=>"Somalia",
                213=>"South Africa",
                214=>"South Georgia and the South Sandwich Islands",
                215=>"Spain",
                216=>"Spratly Islands",
                217=>"Sri Lanka",
                218=>"Sudan",
                219=>"Suriname",
                220=>"Svalbard",
                221=>"Swaziland",
                222=>"Sweden",
                223=>"Switzerland",
                224=>"Syria",
                225=>"Taiwan",
                226=>"Tajikistan",
                227=>"Tanzania",
                228=>"Thailand",
                229=>"Timor-Leste",
                230=>"Togo",
                231=>"Tokelau",
                232=>"Tonga",
                233=>"Trinidad and Tobago",
                234=>"Tromelin Island",
                235=>"Tunisia",
                236=>"Turkey",
                237=>"Turkmenistan",
                238=>"Turks and Caicos Islands",
                239=>"Tuvalu",
                240=>"Uganda",
                241=>"Ukraine",
                242=>"United Arab Emirates",
                243=>"United Kingdom",
                244=>"United States",
                245=>"Uruguay",
                246=>"Uzbekistan",
                247=>"Vanuatu",
                248=>"Venezuela",
                249=>"Vietnam",
                250=>"Virgin Islands",
                251=>"Wake Island",
                252=>"Wallis and Futuna",
                253=>"West Bank",
                254=>"Western Sahara",
                255=>"Yemen",
                256=>"Zambia",
                257=>"Zimbabwe",

            );
        }
        return array(
            "-- Select Country --"=>"-- Select Country --",
            "Afghanistan"=>"Afghanistan",
            "Akrotiri"=>"Akrotiri",
            "Albania"=>"Albania",
            "Algeria"=>"Algeria",
            "American Samoa"=>"American Samoa",
            "Andorra"=>"Andorra",
            "Angola"=>"Angola",
            "Anguilla"=>"Anguilla",
            "Antarctica"=>"Antarctica",
            "Antigua and Barbuda"=>"Antigua and Barbuda",
            "Argentina"=>"Argentina",
            "Armenia"=>"Armenia",
            "Aruba"=>"Aruba",
            "Ashmore and Cartier Islands"=>"Ashmore and Cartier Islands",
            "Australia"=>"Australia",
            "Austria"=>"Austria",
            "Azerbaijan"=>"Azerbaijan",
            "Bahamas, The"=>"Bahamas, The",
            "Bahrain"=>"Bahrain",
            "Bangladesh"=>"Bangladesh",
            "Barbados"=>"Barbados",
            "Bassas da India"=>"Bassas da India",
            "Belarus"=>"Belarus",
            "Belgium"=>"Belgium",
            "Belize"=>"Belize",
            "Benin"=>"Benin",
            "Bermuda"=>"Bermuda",
            "Bhutan"=>"Bhutan",
            "Bolivia"=>"Bolivia",
            "Bosnia and Herzegovina"=>"Bosnia and Herzegovina",
            "Botswana"=>"Botswana",
            "Bouvet Island"=>"Bouvet Island",
            "Brazil"=>"Brazil",
            "British Indian Ocean Territory"=>"British Indian Ocean Territory",
            "British Virgin Islands"=>"British Virgin Islands",
            "Brunei"=>"Brunei",
            "Bulgaria"=>"Bulgaria",
            "Burkina Faso"=>"Burkina Faso",
            "Burma"=>"Burma",
            "Burundi"=>"Burundi",
            "Cambodia"=>"Cambodia",
            "Cameroon"=>"Cameroon",
            "Canada"=>"Canada",
            "Cape Verde"=>"Cape Verde",
            "Cayman Islands"=>"Cayman Islands",
            "Central African Republic"=>"Central African Republic",
            "Chad"=>"Chad",
            "Chile"=>"Chile",
            "China"=>"China",
            "Christmas Island"=>"Christmas Island",
            "Clipperton Island"=>"Clipperton Island",
            "Cocos (Keeling) Islands"=>"Cocos (Keeling) Islands",
            "Colombia"=>"Colombia",
            "Comoros"=>"Comoros",
            "Congo, Democratic Republic of the"=>"Congo, Democratic Republic of the",
            "Congo, Republic of the"=>"Congo, Republic of the",
            "Cook Islands"=>"Cook Islands",
            "Coral Sea Islands"=>"Coral Sea Islands",
            "Costa Rica"=>"Costa Rica",
            "Cote d'Ivoire"=>"Cote d'Ivoire",
            "Croatia"=>"Croatia",
            "Cuba"=>"Cuba",
            "Cyprus"=>"Cyprus",
            "Czech Republic"=>"Czech Republic",
            "Denmark"=>"Denmark",
            "Dhekelia"=>"Dhekelia",
            "Djibouti"=>"Djibouti",
            "Dominica"=>"Dominica",
            "Dominican Republic"=>"Dominican Republic",
            "Ecuador"=>"Ecuador",
            "Egypt"=>"Egypt",
            "El Salvador"=>"El Salvador",
            "Equatorial Guinea"=>"Equatorial Guinea",
            "Eritrea"=>"Eritrea",
            "Estonia"=>"Estonia",
            "Ethiopia"=>"Ethiopia",
            "Europa Island"=>"Europa Island",
            "Falkland Islands (Islas Malvinas)"=>"Falkland Islands (Islas Malvinas)",
            "Faroe Islands"=>"Faroe Islands",
            "Fiji"=>"Fiji",
            "Finland"=>"Finland",
            "France"=>"France",
            "French Guiana"=>"French Guiana",
            "French Polynesia"=>"French Polynesia",
            "French Southern and Antarctic Lands"=>"French Southern and Antarctic Lands",
            "Gabon"=>"Gabon",
            "Gambia, The"=>"Gambia, The",
            "Gaza Strip"=>"Gaza Strip",
            "Georgia"=>"Georgia",
            "Germany"=>"Germany",
            "Ghana"=>"Ghana",
            "Gibraltar"=>"Gibraltar",
            "Glorioso Islands"=>"Glorioso Islands",
            "Greece"=>"Greece",
            "Greenland"=>"Greenland",
            "Grenada"=>"Grenada",
            "Guadeloupe"=>"Guadeloupe",
            "Guam"=>"Guam",
            "Guatemala"=>"Guatemala",
            "Guernsey"=>"Guernsey",
            "Guinea"=>"Guinea",
            "Guinea-Bissau"=>"Guinea-Bissau",
            "Guyana"=>"Guyana",
            "Haiti"=>"Haiti",
            "Heard Island and McDonald Islands"=>"Heard Island and McDonald Islands",
            "Holy See (Vatican City)"=>"Holy See (Vatican City)",
            "Honduras"=>"Honduras",
            "Hong Kong"=>"Hong Kong",
            "Hungary"=>"Hungary",
            "Iceland"=>"Iceland",
            "India"=>"India",
            "Indonesia"=>"Indonesia",
            "Iran"=>"Iran",
            "Iraq"=>"Iraq",
            "Ireland"=>"Ireland",
            "Isle of Man"=>"Isle of Man",
            "Israel"=>"Israel",
            "Italy"=>"Italy",
            "Jamaica"=>"Jamaica",
            "Jan Mayen"=>"Jan Mayen",
            "Japan"=>"Japan",
            "Jersey"=>"Jersey",
            "Jordan"=>"Jordan",
            "Juan de Nova Island"=>"Juan de Nova Island",
            "Kazakhstan"=>"Kazakhstan",
            "Kenya"=>"Kenya",
            "Kiribati"=>"Kiribati",
            "Korea, North"=>"Korea, North",
            "Korea, South"=>"Korea, South",
            "Kuwait"=>"Kuwait",
            "Kyrgyzstan"=>"Kyrgyzstan",
            "Laos"=>"Laos",
            "Latvia"=>"Latvia",
            "Lebanon"=>"Lebanon",
            "Lesotho"=>"Lesotho",
            "Liberia"=>"Liberia",
            "Libya"=>"Libya",
            "Liechtenstein"=>"Liechtenstein",
            "Lithuania"=>"Lithuania",
            "Luxembourg"=>"Luxembourg",
            "Macau"=>"Macau",
            "Macedonia"=>"Macedonia",
            "Madagascar"=>"Madagascar",
            "Malawi"=>"Malawi",
            "Malaysia"=>"Malaysia",
            "Maldives"=>"Maldives",
            "Mali"=>"Mali",
            "Malta"=>"Malta",
            "Marshall Islands"=>"Marshall Islands",
            "Martinique"=>"Martinique",
            "Mauritania"=>"Mauritania",
            "Mauritius"=>"Mauritius",
            "Mayotte"=>"Mayotte",
            "Mexico"=>"Mexico",
            "Micronesia, Federated States of"=>"Micronesia, Federated States of",
            "Moldova"=>"Moldova",
            "Monaco"=>"Monaco",
            "Mongolia"=>"Mongolia",
            "Montserrat"=>"Montserrat",
            "Morocco"=>"Morocco",
            "Mozambique"=>"Mozambique",
            "Namibia"=>"Namibia",
            "Nauru"=>"Nauru",
            "Navassa Island"=>"Navassa Island",
            "Nepal"=>"Nepal",
            "Netherlands"=>"Netherlands",
            "Netherlands Antilles"=>"Netherlands Antilles",
            "New Caledonia"=>"New Caledonia",
            "New Zealand"=>"New Zealand",
            "Nicaragua"=>"Nicaragua",
            "Niger"=>"Niger",
            "Nigeria"=>"Nigeria",
            "Niue"=>"Niue",
            "Norfolk Island"=>"Norfolk Island",
            "Northern Mariana Islands"=>"Northern Mariana Islands",
            "Norway"=>"Norway",
            "Oman"=>"Oman",
            "Pakistan"=>"Pakistan",
            "Palau"=>"Palau",
            "Panama"=>"Panama",
            "Papua New Guinea"=>"Papua New Guinea",
            "Paracel Islands"=>"Paracel Islands",
            "Paraguay"=>"Paraguay",
            "Peru"=>"Peru",
            "Philippines"=>"Philippines",
            "Pitcairn Islands"=>"Pitcairn Islands",
            "Poland"=>"Poland",
            "Portugal"=>"Portugal",
            "Puerto Rico"=>"Puerto Rico",
            "Qatar"=>"Qatar",
            "Reunion"=>"Reunion",
            "Romania"=>"Romania",
            "Russia"=>"Russia",
            "Rwanda"=>"Rwanda",
            "Saint Helena"=>"Saint Helena",
            "Saint Kitts and Nevis"=>"Saint Kitts and Nevis",
            "Saint Lucia"=>"Saint Lucia",
            "Saint Pierre and Miquelon"=>"Saint Pierre and Miquelon",
            "Saint Vincent and the Grenadines"=>"Saint Vincent and the Grenadines",
            "Samoa"=>"Samoa",
            "San Marino"=>"San Marino",
            "Sao Tome and Principe"=>"Sao Tome and Principe",
            "Saudi Arabia"=>"Saudi Arabia",
            "Senegal"=>"Senegal",
            "Serbia and Montenegro"=>"Serbia and Montenegro",
            "Seychelles"=>"Seychelles",
            "Sierra Leone"=>"Sierra Leone",
            "Singapore"=>"Singapore",
            "Slovakia"=>"Slovakia",
            "Slovenia"=>"Slovenia",
            "Solomon Islands"=>"Solomon Islands",
            "Somalia"=>"Somalia",
            "South Africa"=>"South Africa",
            "South Georgia and the South Sandwich Islands"=>"South Georgia and the South Sandwich Islands",
            "Spain"=>"Spain",
            "Spratly Islands"=>"Spratly Islands",
            "Sri Lanka"=>"Sri Lanka",
            "Sudan"=>"Sudan",
            "Suriname"=>"Suriname",
            "Svalbard"=>"Svalbard",
            "Swaziland"=>"Swaziland",
            "Sweden"=>"Sweden",
            "Switzerland"=>"Switzerland",
            "Syria"=>"Syria",
            "Taiwan"=>"Taiwan",
            "Tajikistan"=>"Tajikistan",
            "Tanzania"=>"Tanzania",
            "Thailand"=>"Thailand",
            "Timor-Leste"=>"Timor-Leste",
            "Togo"=>"Togo",
            "Tokelau"=>"Tokelau",
            "Tonga"=>"Tonga",
            "Trinidad and Tobago"=>"Trinidad and Tobago",
            "Tromelin Island"=>"Tromelin Island",
            "Tunisia"=>"Tunisia",
            "Turkey"=>"Turkey",
            "Turkmenistan"=>"Turkmenistan",
            "Turks and Caicos Islands"=>"Turks and Caicos Islands",
            "Tuvalu"=>"Tuvalu",
            "Uganda"=>"Uganda",
            "Ukraine"=>"Ukraine",
            "United Arab Emirates"=>"United Arab Emirates",
            "United Kingdom"=>"United Kingdom",
            "United States"=>"United States",
            "Uruguay"=>"Uruguay",
            "Uzbekistan"=>"Uzbekistan",
            "Vanuatu"=>"Vanuatu",
            "Venezuela"=>"Venezuela",
            "Vietnam"=>"Vietnam",
            "Virgin Islands"=>"Virgin Islands",
            "Wake Island"=>"Wake Island",
            "Wallis and Futuna"=>"Wallis and Futuna",
            "West Bank"=>"West Bank",
            "Western Sahara"=>"Western Sahara",
            "Yemen"=>"Yemen",
            "Zambia"=>"Zambia",
            "Zimbabwe"=>"Zimbabwe",

        );
    }


    /**
     * American States
     * @return array
     */
    public static function getAmericanState($withNumericIndex = true)
    {
        if ($withNumericIndex) {
            return [
                1 => "Alabama",
                2 => "Alaska",
                3 => "American Samoa",
                4 => "Arizona",
                5 => "Arkansas",
                6 => "California",
                7 => "Colorado",
                8 => "Connecticut",
                9 => "District of Columbia",
                10 => "Delaware",
                11 => "Florida",
                12 => "Georgia",
                13 => "Guam",
                14 => "Hawaii",
                15 => "Idaho",
                16 => "Illinois",
                17 => "Indiana",
                18 => "Iowa",
                19 => "Kansas",
                20 => "Kentucky",
                21 => "Louisiana",
                22 => "Maine",
                23 => "Maryland",
                24 => "Massachusetts",
                25 => "Michigan",
                26 => "Minnesota",
                27 => "Mississippi",
                28 => "Missouri",
                29 => "Montana",
                30 => "Nebraska",
                31 => "Nevada",
                32 => "New Hampshire",
                33 => "New Jersey",
                34 => "New Mexico",
                35 => "New York",
                36 => "North Carolina",
                37 => "North Marianas Islands",
                38 => "North Dakota",
                39 => "Ohio",
                40 => "Oklahoma",
                41 => "Oregon",
                42 => "Pennsylvania",
                43 => "Puerto Rico",
                44 => "Rhode Island",
                45 => "South Carolina",
                46 => "South Dakota",
                47 => "Tennessee",
                48 => "Texas",
                49 => "Utah",
                50 => "Vermont",
                51 => "Virginia",
                52 => "Virgin Islands",
                53 => "Washington",
                54 => "West Virginia",
                55 => "Wisconsin",
                56 => "Wyoming",
            ];
        }
        else
        {
            return [
                "Alabama" => "Alabama",
                "Alaska" => "Alaska",
                "American Samoa" => "American Samoa",
                "Arizona" => "Arizona",
                "Arkansas" => "Arkansas",
                "California" => "California",
                "Colorado" => "Colorado",
                "Connecticut" => "Connecticut",
                "District of Columbia" => "District of Columbia",
                "Delaware" => "Delaware",
                "Florida" => "Florida",
                "Georgia" => "Georgia",
                "Guam" => "Guam",
                "Hawaii" => "Hawaii",
                "Idaho" => "Idaho",
                "Illinois" => "Illinois",
                "Indiana" => "Indiana",
                "Iowa" => "Iowa",
                "Kansas" => "Kansas",
                "Kentucky" => "Kentucky",
                "Louisiana" => "Louisiana",
                "Maine" => "Maine",
                "Maryland" => "Maryland",
                "Massachusetts" => "Massachusetts",
                "Michigan" => "Michigan",
                "Minnesota" => "Minnesota",
                "Mississippi" => "Mississippi",
                "Missouri" => "Missouri",
                "Montana" => "Montana",
                "Nebraska" => "Nebraska",
                "Nevada" => "Nevada",
                "New Hampshire" => "New Hampshire",
                "New Jersey" => "New Jersey",
                "New Mexico" => "New Mexico",
                "New York" => "New York",
                "North Carolina" => "North Carolina",
                "North Marianas Islands" => "North Marianas Islands",
                "North Dakota" => "North Dakota",
                "Ohio" => "Ohio",
                "Oklahoma" => "Oklahoma",
                "Oregon" => "Oregon",
                "Pennsylvania" => "Pennsylvania",
                "Puerto Rico" => "Puerto Rico",
                "Rhode Island" => "Rhode Island",
                "South Carolina" => "South Carolina",
                "South Dakota" => "South Dakota",
                "Tennessee" => "Tennessee",
                "Texas" => "Texas",
                "Utah" => "Utah",
                "Vermont" => "Vermont",
                "Virginia" => "Virginia",
                "Virgin Islands" => "Virgin Islands",
                "Washington" => "Washington",
                "West Virginia" => "West Virginia",
                "Wisconsin" => "Wisconsin",
                "Wyoming" => "Wyoming"
            ];
        }
    }

}