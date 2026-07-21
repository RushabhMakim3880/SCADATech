<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LocationMaster extends Seeder
{
    public $priority = 1;

    public function run()
    {
        return;
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $locationData = [
            21 =>
            [
                'name' => 'Odisha',
                'childs' =>
                [
                    360 =>
                    [
                        'name' => 'Kendrapara',
                        'childs' =>
                        [
                            0 => 'Aali',
                            1 => 'Derabish',
                            2 => 'Jamboo Marine',
                            3 => 'Kendrapara',
                            4 => 'Kendrapara Sadar',
                            5 => 'Mahakalapada',
                            6 => 'Marsaghai',
                            7 => 'Nikirai',
                            8 => 'Patkura',
                            9 => 'Pattamundai',
                            10 => 'Rajkanika',
                            11 => 'Rajnagar',
                        ],
                    ],
                    355 =>
                    [
                        'name' => 'Jagatsinghapur',
                        'childs' =>
                        [
                            0 => 'Abhyachandpur',
                            1 => 'Balikuda',
                            2 => 'Biridi',
                            3 => 'Ersama',
                            4 => 'Jagatsinghapur',
                            5 => 'Kujang',
                            6 => 'Naugaon',
                            7 => 'Paradeep Lock',
                            8 => 'Paradip',
                            9 => 'Tirtol',
                        ],
                    ],
                    353 =>
                    [
                        'name' => 'Gajapati',
                        'childs' =>
                        [
                            0 => 'Adava',
                            1 => 'Garabandha',
                            2 => 'Gurandi',
                            3 => 'Kasinagar',
                            4 => 'Mohana',
                            5 => 'Paralakhemundi',
                            6 => 'Ramagiri',
                            7 => 'Rayagada',
                            8 => 'R.Udayagiri',
                            9 => 'Serango',
                        ],
                    ],
                    348 =>
                    [
                        'name' => 'Bhadrak',
                        'childs' =>
                        [
                            0 => 'Agarpada',
                            1 => 'Bansada',
                            2 => 'Bant',
                            3 => 'Basudebpur',
                            4 => 'Bhadrak Rural',
                            5 => 'Bhandari Pokhari',
                            6 => 'Chandabali',
                            7 => 'Dhamanagar',
                            8 => 'Dhamara Marine',
                            9 => 'Dhusuri',
                            10 => 'Naikanidihi',
                            11 => 'Tihidi',
                        ],
                    ],
                    371 =>
                    [
                        'name' => 'Sambalpur',
                        'childs' =>
                        [
                            0 => 'Ainthapali',
                            1 => 'Burla',
                            2 => 'Charamal',
                            3 => 'Dhama',
                            4 => 'Dhanupali',
                            5 => 'Govindpur',
                            6 => 'Hirakud',
                            7 => 'Jamankira',
                            8 => 'Jujomura',
                            9 => 'Katarbaga',
                            10 => 'Kisinda',
                            11 => 'Kochinda',
                            12 => 'Mahulpalli',
                            13 => 'Naktideul',
                            14 => 'Rairakhol',
                            15 => 'Rengali',
                            16 => 'Sadar',
                            17 => 'Sambalpur (M]',
                            18 => 'Sasan',
                            19 => 'Thelkoloi',
                        ],
                    ],
                    362 =>
                    [
                        'name' => 'Khordha',
                        'childs' =>
                        [
                            0 => 'Airfield (Kapila Prasad]',
                            1 => 'Balianta',
                            2 => 'Balipatna',
                            3 => 'Balugaon',
                            4 => 'Banapur',
                            5 => 'Begunia',
                            6 => 'Bhubaneswar (M.Corp.]',
                            7 => 'Bolagad',
                            8 => 'Chandaka',
                            9 => 'Jankia',
                            10 => 'Jatani',
                            11 => 'Khandagiri',
                            12 => 'Khordha',
                            13 => 'Khordha Sadar',
                            14 => 'Lingaraj',
                            15 => 'Mancheswar',
                            16 => 'Nandankanan',
                            17 => 'Nirakarpur',
                            18 => 'Saheednagar',
                            19 => 'Tamando',
                            20 => 'Tangi',
                            21 => 'Uttara P.S',
                        ],
                    ],
                    347 =>
                    [
                        'name' => 'Bargarh',
                        'childs' =>
                        [
                            0 => 'Ambabhona',
                            1 => 'Attabira',
                            2 => 'Barapali',
                            3 => 'Bargarh',
                            4 => 'Bargarh Sadar',
                            5 => 'Bhatli',
                            6 => 'Bheden',
                            7 => 'Bijepur',
                            8 => 'Burdenp.S.',
                            9 => 'Gaisilet',
                            10 => 'Jharbandha',
                            11 => 'Melchhamunda',
                            12 => 'Padmapur',
                            13 => 'Paikamal',
                            14 => 'Sohela',
                        ],
                    ],
                    370 =>
                    [
                        'name' => 'Rayagada',
                        'childs' =>
                        [
                            0 => 'Ambadala',
                            1 => 'Andirakanch',
                            2 => 'Bishamakatak',
                            3 => 'Chandrapur',
                            4 => 'Doraguda',
                            5 => 'Gudari',
                            6 => 'Gunupur',
                            7 => 'Kalyanasingpur',
                            8 => 'Kashipur',
                            9 => 'Muniguda',
                            10 => 'Padmapur',
                            11 => 'Puttasing',
                            12 => 'Rayagada',
                            13 => 'Seskhal',
                            14 => 'Tikiri',
                        ],
                    ],
                    361 =>
                    [
                        'name' => 'Kendujhar',
                        'childs' =>
                        [
                            0 => 'Anandapur',
                            1 => 'Bamebari',
                            2 => 'Barbil',
                            3 => 'Baria',
                            4 => 'Bolani',
                            5 => 'Champua',
                            6 => 'Daitari',
                            7 => 'Ghasipura',
                            8 => 'Ghatgaon',
                            9 => 'Harichandanpur',
                            10 => 'Jhumpura',
                            11 => 'Joda',
                            12 => 'Kanjipani',
                            13 => 'Kendujhar Sadar',
                            14 => 'Kendujhar Town',
                            15 => 'Nandipada',
                            16 => 'Nayakote',
                            17 => 'Pandapara',
                            18 => 'Patana',
                            19 => 'Rugudi',
                            20 => 'Sainkul',
                            21 => 'Soso',
                            22 => 'Telkoi',
                            23 => 'Turumunga',
                        ],
                    ],
                    344 =>
                    [
                        'name' => 'Anugul',
                        'childs' =>
                        [
                            0 => 'Anugul',
                            1 => 'Athmallik',
                            2 => 'Banarpal',
                            3 => 'Bantala',
                            4 => 'Bikrampur',
                            5 => 'Chhendipada',
                            6 => 'Colliery',
                            7 => 'Handapa',
                            8 => 'Industrial',
                            9 => 'Jarapada',
                            10 => 'Kaniha',
                            11 => 'Khamar',
                            12 => 'Kiakata',
                            13 => 'Kishorenagar',
                            14 => 'Nalco',
                            15 => 'N.T.P.C.',
                            16 => 'Palalahada',
                            17 => 'Purunakot',
                            18 => 'Rengali Damsite',
                            19 => 'Samal Barrage',
                            20 => 'Talcher Sadar',
                            21 => 'Thakurgarh',
                        ],
                    ],
                    354 =>
                    [
                        'name' => 'Ganjam',
                        'childs' =>
                        [
                            0 => 'Arjyapalli Marine',
                            1 => 'Asika',
                            2 => 'Badagada',
                            3 => 'Bhanjanagar',
                            4 => 'Brahmapur (M.Corp.]',
                            5 => 'Brahmapur Sadar',
                            6 => 'Buguda',
                            7 => 'Chamakhandi',
                            8 => 'Chhatrapur',
                            9 => 'Dharakote',
                            10 => 'Digapahandi',
                            11 => 'Gangapur',
                            12 => 'Ganjam',
                            13 => 'Golanthara',
                            14 => 'Gopalpur',
                            15 => 'Hinjili',
                            16 => 'Jagannath Prasad',
                            17 => 'Jarada',
                            18 => 'Kabisuryanagar',
                            19 => 'Khalikote',
                            20 => 'Kodala',
                            21 => 'Nuagaon',
                            22 => 'Patapur',
                            23 => 'Polasara',
                            24 => 'Purusottampur',
                            25 => 'Rambha',
                            26 => 'Seragad',
                            27 => 'Surada',
                            28 => 'Tarasingi',
                        ],
                    ],
                    369 =>
                    [
                        'name' => 'Puri',
                        'childs' =>
                        [
                            0 => 'Astaranga',
                            1 => 'Brahmagiri',
                            2 => 'Chandanpur',
                            3 => 'Delanga',
                            4 => 'Gadisagada',
                            5 => 'Gop',
                            6 => 'Kakatpur',
                            7 => 'Konark',
                            8 => 'Krushna Prasad',
                            9 => 'Nimapada',
                            10 => 'Pipili',
                            11 => 'Puri (M]',
                            12 => 'Ramachandi',
                            13 => 'Sadar',
                            14 => 'Satyabadi',
                        ],
                    ],
                    350 =>
                    [
                        'name' => 'Cuttack',
                        'childs' =>
                        [
                            0 => 'Athagad',
                            1 => 'Badamba',
                            2 => 'Baidyeswar',
                            3 => 'Banki',
                            4 => 'Barang',
                            5 => 'Choudwar',
                            6 => 'C.R.R.I.',
                            7 => 'Cuttack (M.Corp.] P.S',
                            8 => 'Cuttack Sadar',
                            9 => 'Gobindpur',
                            10 => 'Gurudijhatia',
                            11 => 'Jagatpur',
                            12 => 'Kandarpur',
                            13 => 'Kanpur',
                            14 => 'Khuntuni',
                            15 => 'Kishannagar',
                            16 => 'Mahanga',
                            17 => 'Narasinghpur',
                            18 => 'Nemalo',
                            19 => 'Niali',
                            20 => 'Nischintakoili',
                            21 => 'Olatapur',
                            22 => 'Salepur',
                            23 => 'Tangi',
                            24 => 'Tigiria',
                        ],
                    ],
                    356 =>
                    [
                        'name' => 'Jajapur',
                        'childs' =>
                        [
                            0 => 'Badachana',
                            1 => 'Balichandrapur',
                            2 => 'Bari - Ramachandrapur',
                            3 => 'Binjharpur',
                            4 => 'Dharmasala',
                            5 => 'Jajapur',
                            6 => 'Jajapur Road',
                            7 => 'Jajpur Sadar',
                            8 => 'Jakhapura',
                            9 => 'Jenapur',
                            10 => 'Kaliapani',
                            11 => 'Kalinganagar',
                            12 => 'Korai',
                            13 => 'Kuakhia',
                            14 => 'Mangalpur',
                            15 => 'Panikoili',
                            16 => 'Sukinda',
                            17 => 'Tomka',
                        ],
                    ],
                    365 =>
                    [
                        'name' => 'Mayurbhanj',
                        'childs' =>
                        [
                            0 => 'Badampahar',
                            1 => 'Bahalda',
                            2 => 'Baisinga',
                            3 => 'Bangiriposi',
                            4 => 'Baripada (M]',
                            5 => 'Baripada Sadar',
                            6 => 'Baripada Town',
                            7 => 'Barsahi',
                            8 => 'Betanati',
                            9 => 'Bisoi',
                            10 => 'Chandua',
                            11 => 'Ghagarbeda',
                            12 => 'Gorumahisani',
                            13 => 'Jamda',
                            14 => 'Jashipur',
                            15 => 'Jharpokharia',
                            16 => 'Kaptipada',
                            17 => 'Karanjia',
                            18 => 'Khunta',
                            19 => 'Koliana',
                            20 => 'Mahuldiha',
                            21 => 'Muruda',
                            22 => 'Rairangpur',
                            23 => 'Rairangpur Town',
                            24 => 'Raruan',
                            25 => 'Rasagobindapur',
                            26 => 'Sharata',
                            27 => 'Suliapada',
                            28 => 'Thakurmunda',
                            29 => 'Tiring',
                            30 => 'Udala',
                        ],
                    ],
                    357 =>
                    [
                        'name' => 'Jharsuguda',
                        'childs' =>
                        [
                            0 => 'Badmal',
                            1 => 'Banaharapali',
                            2 => 'Belpahar',
                            3 => 'Brajarajnagar',
                            4 => 'Jharsuguda',
                            5 => 'Kolabira',
                            6 => 'Laikera',
                            7 => 'Lakhanpur',
                            8 => 'Orient',
                            9 => 'Rengali',
                        ],
                    ],
                    345 =>
                    [
                        'name' => 'Balangir',
                        'childs' =>
                        [
                            0 => 'Balangir',
                            1 => 'Bangomunda',
                            2 => 'Belpara',
                            3 => 'Kantabanji',
                            4 => 'Khaprakhol',
                            5 => 'Loisinga',
                            6 => 'Patnagarh',
                            7 => 'Saintala',
                            8 => 'Sindhekela',
                            9 => 'Titlagarh',
                            10 => 'Turekela',
                            11 => 'Tushura',
                        ],
                    ],
                    346 =>
                    [
                        'name' => 'Baleshwar',
                        'childs' =>
                        [
                            0 => 'Balaramgadi Marine',
                            1 => 'Baleshwar (M]',
                            2 => 'Baleshwar Sadar',
                            3 => 'Baliapal',
                            4 => 'Bampada',
                            5 => 'Basta',
                            6 => 'Berhampur',
                            7 => 'Bhograi',
                            8 => 'Chandipur',
                            9 => 'Jaleswar',
                            10 => 'Kamarda',
                            11 => 'Khaira',
                            12 => 'Nilagiri',
                            13 => 'Oupada',
                            14 => 'Raibania',
                            15 => 'Remuna',
                            16 => 'Rupsa',
                            17 => 'Sahadevkhunta',
                            18 => 'Similia',
                            19 => 'Singla',
                            20 => 'Soro',
                        ],
                    ],
                    359 =>
                    [
                        'name' => 'Kandhamal',
                        'childs' =>
                        [
                            0 => 'Baliguda',
                            1 => 'Belaghar',
                            2 => 'Brahmanigaon',
                            3 => 'Chakapada',
                            4 => 'Daringbadi',
                            5 => 'Gochhapada',
                            6 => 'G.Udayagiri',
                            7 => 'Khajuripada',
                            8 => 'Kotagarh',
                            9 => 'Nuagaon',
                            10 => 'Phiringia',
                            11 => 'Phulabani',
                            12 => 'Phulabani Town',
                            13 => 'Raikia',
                            14 => 'Sarangagarh',
                            15 => 'Tikabali',
                            16 => 'Tumudibandha',
                        ],
                    ],
                    352 =>
                    [
                        'name' => 'Dhenkanal',
                        'childs' =>
                        [
                            0 => 'Balimi',
                            1 => 'Bhuban',
                            2 => 'Bhusan Steel Limited',
                            3 => 'Dhenkanal Sadar',
                            4 => 'Gandia',
                            5 => 'Hindol',
                            6 => 'Kamakshyanagar',
                            7 => 'Kankadahad',
                            8 => 'Motunga',
                            9 => 'Nihalprasad',
                            10 => 'Parajang',
                            11 => 'Rasol',
                            12 => 'Tumusingha',
                        ],
                    ],
                    363 =>
                    [
                        'name' => 'Koraput',
                        'childs' =>
                        [
                            0 => 'Bandhugaon',
                            1 => 'Bhairabsingipur',
                            2 => 'Boipariguda',
                            3 => 'Boriguma',
                            4 => 'Damonjodi',
                            5 => 'Dasamantapur',
                            6 => 'Jeypore',
                            7 => 'Kakiriguma',
                            8 => 'Koraput',
                            9 => 'Koraput Town',
                            10 => 'Kotiya',
                            11 => 'Kotpad',
                            12 => 'Kundura',
                            13 => 'Lakshmipur',
                            14 => 'Machh Kund',
                            15 => 'Nandapur',
                            16 => 'Narayanpatana',
                            17 => 'Padua',
                            18 => 'Pottangi',
                            19 => 'Similiguda',
                            20 => 'Sunabeda',
                        ],
                    ],
                    373 =>
                    [
                        'name' => 'Sundargarh',
                        'childs' =>
                        [
                            0 => 'Banei',
                            1 => 'Baragaon',
                            2 => 'Bhasma',
                            3 => 'Biramitrapur',
                            4 => 'Bisra',
                            5 => 'Bondamunda',
                            6 => 'Brahmani Tarang',
                            7 => 'Chandiposh',
                            8 => 'Dharuadihi',
                            9 => 'Gurundia',
                            10 => 'Hatibari',
                            11 => 'Hemgir',
                            12 => 'Kamarposh Balang',
                            13 => 'Kinjirkela',
                            14 => 'Koida',
                            15 => 'Kutra',
                            16 => 'Lahunipara',
                            17 => 'Lathikata',
                            18 => 'Lephripara',
                            19 => 'Mahulapada',
                            20 => 'Raghunathapali',
                            21 => 'Raiboga',
                            22 => 'Rajagangapur',
                            23 => 'Raurkela (Its]P.S.',
                            24 => 'Raurkela (M]',
                            25 => 'Sundargarh',
                            26 => 'Sundargarh Town',
                            27 => 'Talasara',
                            28 => 'Tangarapali',
                            29 => 'Tikaetpali',
                        ],
                    ],
                    367 =>
                    [
                        'name' => 'Nayagarh',
                        'childs' =>
                        [
                            0 => 'Banigochha',
                            1 => 'Dasapalla',
                            2 => 'Fategarh',
                            3 => 'Gania',
                            4 => 'Itamati',
                            5 => 'Khandapada',
                            6 => 'Nayagarh',
                            7 => 'Nayagarh Sadar',
                            8 => 'Nuagaon',
                            9 => 'Odagaon',
                            10 => 'Ranapur',
                            11 => 'Sarankul',
                        ],
                    ],
                    351 =>
                    [
                        'name' => 'Deogarh',
                        'childs' =>
                        [
                            0 => 'Barkot',
                            1 => 'Debagarh',
                            2 => 'Kundheigola',
                            3 => 'Reamal',
                        ],
                    ],
                    349 =>
                    [
                        'name' => 'Boudh',
                        'childs' =>
                        [
                            0 => 'Baudh Sadar',
                            1 => 'Baunsuni',
                            2 => 'Harbhanga',
                            3 => 'Kantamal',
                            4 => 'Manamunda',
                            5 => 'Puruna Katak',
                        ],
                    ],
                    372 =>
                    [
                        'name' => 'Sonepur',
                        'childs' =>
                        [
                            0 => 'Binika',
                            1 => 'Biramaharajpur',
                            2 => 'Dunguripali',
                            3 => 'Rampur',
                            4 => 'Sonapur',
                            5 => 'Subalaya',
                            6 => 'Tarbha',
                            7 => 'Ulunda',
                        ],
                    ],
                    358 =>
                    [
                        'name' => 'Kalahandi',
                        'childs' =>
                        [
                            0 => 'Biswanathpur',
                            1 => 'Dharamgarh',
                            2 => 'Golamunda',
                            3 => 'Jayapatna',
                            4 => 'Junagarh',
                            5 => 'Kegaon',
                            6 => 'Kesinga',
                            7 => 'Kokasara',
                            8 => 'Lanjigarh',
                            9 => 'Madanpur Rampur',
                            10 => 'Narala',
                            11 => 'Sadar',
                            12 => 'Thuamul Rampur',
                        ],
                    ],
                    368 =>
                    [
                        'name' => 'Nuapada',
                        'childs' =>
                        [
                            0 => 'Boden',
                            1 => 'Jonk',
                            2 => 'Khariar',
                            3 => 'Komna',
                            4 => 'Nuapada',
                            5 => 'Sinapali',
                        ],
                    ],
                    366 =>
                    [
                        'name' => 'Nabarangpur',
                        'childs' =>
                        [
                            0 => 'Chandahandi',
                            1 => 'Dabugan',
                            2 => 'Jharigan',
                            3 => 'Khatiguda',
                            4 => 'Kodinga',
                            5 => 'Kosagumuda',
                            6 => 'Kundei',
                            7 => 'Nabarangapur P.S',
                            8 => 'Paparahandi',
                            9 => 'Raighar',
                            10 => 'Tentulikhunti',
                            11 => 'Umarkote',
                        ],
                    ],
                    364 =>
                    [
                        'name' => 'Malkangiri',
                        'childs' =>
                        [
                            0 => 'Chitrakonda',
                            1 => 'Jodamba',
                            2 => 'Kalimela',
                            3 => 'Malkangiri',
                            4 => 'Mathili',
                            5 => 'Motu',
                            6 => 'Mudulipada',
                            7 => 'M.V. 79',
                            8 => 'Orkel',
                            9 => 'Paparmetla',
                            10 => 'Podia',
                        ],
                    ],
                ],
            ],
            12 =>
            [
                'name' => 'Arunachal Pradesh',
                'childs' =>
                [
                    243 =>
                    [
                        'name' => 'West Siang',
                        'childs' =>
                        [
                            0 => 'Aalo Hq',
                            1 => 'Bagra Circle',
                            2 => 'Darak Circle',
                            3 => 'Kamba Adc',
                            4 => 'Kombo Circle',
                            5 => 'Liromoba Eac',
                            6 => 'Nikte Kodum Circle',
                            7 => 'Yomcha Adc',
                        ],
                    ],
                    230 =>
                    [
                        'name' => 'Dibang Valley',
                        'childs' =>
                        [
                            0 => 'Anelih',
                            1 => 'Anini',
                            2 => 'Dambuen Circle',
                            3 => 'Etalin',
                            4 => 'Kronli (Arzoo Circle]',
                            5 => 'Mipi',
                        ],
                    ],
                    242 =>
                    [
                        'name' => 'West Kameng',
                        'childs' =>
                        [
                            0 => 'Balemu Circle',
                            1 => 'Bhalukpong Eac',
                            2 => 'Bomdila Hq',
                            3 => 'Buragaon Eac',
                            4 => 'Dirang Adc',
                            5 => 'Jamiri Circle',
                            6 => 'Kalaktang Adc',
                            7 => 'Kamengbari Doimara Circle',
                            8 => 'Rupa Sdo',
                            9 => 'Shergaon Circle',
                            10 => 'Singchung Adc',
                            11 => 'Thembang Circle',
                            12 => 'Thrizino Adc',
                        ],
                    ],
                    237 =>
                    [
                        'name' => 'Papum Pare',
                        'childs' =>
                        [
                            0 => 'Balijan Adc',
                            1 => 'Banderdewa Circle',
                            2 => 'Borum',
                            3 => 'Doimukh Sdo',
                            4 => 'Gumto Circle',
                            5 => 'Itanagar Eac',
                            6 => 'Kakoi Circle',
                            7 => 'Kimin Sdo',
                            8 => 'Leporiang Circle',
                            9 => 'Mengio Eac',
                            10 => 'Naharlagun Eac',
                            11 => 'Parang Circle',
                            12 => 'Sagalee Adc',
                            13 => 'Sangdupota Basar Nello Circle',
                            14 => 'Silsango Circle',
                            15 => 'Taraso Circle',
                            16 => 'Toru Circle',
                        ],
                    ],
                    231 =>
                    [
                        'name' => 'East Kameng',
                        'childs' =>
                        [
                            0 => 'Bameng Adc',
                            1 => 'Chayangtajo Adc',
                            2 => 'Debeyar Co Circle',
                            3 => 'Gyawe Purang Circle',
                            4 => 'Khenawa Circle',
                            5 => 'Pakoti Circle',
                            6 => 'Pipu Dipu Circle',
                            7 => 'Richukrong Circle',
                            8 => 'Sawa Circle',
                            9 => 'Seppa Hq',
                        ],
                    ],
                    787 =>
                    [
                        'name' => 'Bichom',
                        'childs' =>
                        [
                            0 => 'Bana Eac',
                            1 => 'Lada Circle',
                            2 => 'Nafra Adc',
                        ],
                    ],
                    239 =>
                    [
                        'name' => 'Tirap',
                        'childs' =>
                        [
                            0 => 'Bari Basip Circle',
                            1 => 'Borduria Circle',
                            2 => 'Dadam Circle',
                            3 => 'Deomali Adc',
                            4 => 'Khonsa Hq',
                            5 => 'Laju Sdo',
                            6 => 'Longo Circle',
                            7 => 'Soha Circle',
                        ],
                    ],
                    241 =>
                    [
                        'name' => 'Upper Subansiri',
                        'childs' =>
                        [
                            0 => 'Baririjo Circle',
                            1 => 'Chetam Peer Yapu Circle',
                            2 => 'Daporijo Hq',
                            3 => 'Dumporijo Adc',
                            4 => 'Giba Circle',
                            5 => 'Gite Ripa Circle',
                            6 => 'Limeking Circle',
                            7 => 'Maro Circle',
                            8 => 'Nacho Sdo',
                            9 => 'Nilling Circle',
                            10 => 'Payeng Circle',
                            11 => 'Segi Gusar Circle',
                            12 => 'Siyum Adc',
                            13 => 'Taksing Circle',
                            14 => 'Taliha Adc',
                        ],
                    ],
                    724 =>
                    [
                        'name' => 'Leparada',
                        'childs' =>
                        [
                            0 => 'Basar Adc',
                            1 => 'Daring Circle',
                            2 => 'Sago',
                            3 => 'Tirbin Eac',
                        ],
                    ],
                    232 =>
                    [
                        'name' => 'East Siang',
                        'childs' =>
                        [
                            0 => 'Bilat Circle',
                            1 => 'Mebo Adc',
                            2 => 'Namsing Circle',
                            3 => 'Oyan Circle',
                            4 => 'Pasighat Hq',
                            5 => 'Ruksin Adc',
                        ],
                    ],
                    679 =>
                    [
                        'name' => 'Siang',
                        'childs' =>
                        [
                            0 => 'Boleng Adc',
                            1 => 'Jomlo Mobuk Circle',
                            2 => 'Kaying Eac',
                            3 => 'Kebang Circle',
                            4 => 'Pangin Eac',
                            5 => 'Payum Circle',
                            6 => 'Rebo Perging Circle',
                            7 => 'Riga Eac',
                            8 => 'Rumgong Adc',
                        ],
                    ],
                    238 =>
                    [
                        'name' => 'Tawang',
                        'childs' =>
                        [
                            0 => 'Bongkhar Circle',
                            1 => 'Dudunghar Circle',
                            2 => 'Jang Adc',
                            3 => 'Kitpi Circle',
                            4 => 'Lhou Circle',
                            5 => 'Lumla Adc',
                            6 => 'Mukto Circle',
                            7 => 'Tawang Hq',
                            8 => 'Thingbu Circle',
                            9 => 'Zemithang Circle',
                        ],
                    ],
                    229 =>
                    [
                        'name' => 'Changlang',
                        'childs' =>
                        [
                            0 => 'Bordumsa Adc',
                            1 => 'Changlang Hq',
                            2 => 'Diyun Eac',
                            3 => 'Jairampur Adc',
                            4 => 'Kantang Circle',
                            5 => 'Kharsang Circle',
                            6 => 'Khimiyang Eac',
                            7 => 'Lyngok Longtoi Circle',
                            8 => 'Manmao Eac',
                            9 => 'Miao Adc',
                            10 => 'Nampong Sdo',
                            11 => 'Namtok Eac',
                            12 => 'Renuk Circle',
                            13 => 'Tikhak Rima Putok Circle',
                            14 => 'Vijoynagar Eac',
                            15 => 'Yatdam Circle',
                        ],
                    ],
                    628 =>
                    [
                        'name' => 'Anjaw',
                        'childs' =>
                        [
                            0 => 'Chaglagam Circle',
                            1 => 'Goiliang Circle',
                            2 => 'Hawai Hq',
                            3 => 'Hayuliang Adc',
                            4 => 'Kibithoo Circle',
                            5 => 'Manchal Circle',
                            6 => 'Metengliang Circle',
                            7 => 'Walong Circle',
                        ],
                    ],
                    677 =>
                    [
                        'name' => 'Kra Daadi',
                        'childs' =>
                        [
                            0 => 'Chambang Circle',
                            1 => 'Gangte Circle',
                            2 => 'Palin Circle',
                            3 => 'Pipsorang Circle',
                            4 => 'Tali Adc',
                            5 => 'Tarak Lengdi Cicle',
                            6 => 'Yangte Circle',
                        ],
                    ],
                    678 =>
                    [
                        'name' => 'Namsai',
                        'childs' =>
                        [
                            0 => 'Chongkham Eac',
                            1 => 'Lathao Circle',
                            2 => 'Lekang Mahadevpur Circle',
                            3 => 'Namsai Hq',
                            4 => 'Piyong Circle',
                        ],
                    ],
                    235 =>
                    [
                        'name' => 'Lower Dibang Valley',
                        'childs' =>
                        [
                            0 => 'Dambuk Adc',
                            1 => 'Desali Circle',
                            2 => 'Hunli Sdo',
                            3 => 'Koronu Circle',
                            4 => 'Parbuk Sdo',
                            5 => 'Roing Hq',
                            6 => 'Tinali Paglam Circle',
                        ],
                    ],
                    233 =>
                    [
                        'name' => 'Kurung Kumey',
                        'childs' =>
                        [
                            0 => 'Damin Circle',
                            1 => 'Koloriang Hq',
                            2 => 'Nyapin Adc',
                            3 => 'Nyobia Circle',
                            4 => 'Patuk Adc',
                            5 => 'Phassang Circle',
                            6 => 'Polosang Circle',
                            7 => 'Sangram Sdo',
                            8 => 'Sarli Circle',
                        ],
                    ],
                    786 =>
                    [
                        'name' => 'Keyi Panyor',
                        'childs' =>
                        [
                            0 => 'Deed Circle',
                            1 => 'Pistana Circle',
                            2 => 'Yachuli Adc',
                            3 => 'Yazali Circle',
                        ],
                    ],
                    723 =>
                    [
                        'name' => 'Pakke Kessang',
                        'childs' =>
                        [
                            0 => 'Dissing Passo Circle',
                            1 => 'Pakke Kessang Eac',
                            2 => 'Passa Valley Circle',
                            3 => 'Pizirang Veo Circle',
                            4 => 'Seijosa Adc',
                        ],
                    ],
                    718 =>
                    [
                        'name' => 'Kamle',
                        'childs' =>
                        [
                            0 => 'Dollungmukh Circle',
                            1 => 'Gepen Circle',
                            2 => 'Kamporijo Circle',
                            3 => 'Puchi Geko Circle',
                            4 => 'Raga Adc',
                        ],
                    ],
                    240 =>
                    [
                        'name' => 'Upper Siang',
                        'childs' =>
                        [
                            0 => 'Geku Eac',
                            1 => 'Gelling Circle',
                            2 => 'Jengging Circle',
                            3 => 'Katan Circle',
                            4 => 'Mariyang Adc',
                            5 => 'Migging Circle',
                            6 => 'Mopom Adipasi Circle',
                            7 => 'Palling Circle',
                            8 => 'Singa Circle',
                            9 => 'Tuting Adc',
                            10 => 'Yingkiong Circle',
                        ],
                    ],
                    719 =>
                    [
                        'name' => 'Lower Siang',
                        'childs' =>
                        [
                            0 => 'Gensi Eac',
                            1 => 'Kangku Circle',
                            2 => 'Kora Circle',
                            3 => 'Koyu Eac',
                            4 => 'Likabali Sdo',
                            5 => 'Nari Adc',
                            6 => 'New Seren Circle',
                            7 => 'Sibe Circle',
                        ],
                    ],
                    666 =>
                    [
                        'name' => 'Longding',
                        'childs' =>
                        [
                            0 => 'Kanubari Adc',
                            1 => 'Lawnu Circle',
                            2 => 'Longding Hq',
                            3 => 'Pangchao Sdo',
                            4 => 'Pumao Circle',
                            5 => 'Wakka Sdo',
                        ],
                    ],
                    725 =>
                    [
                        'name' => 'Shi Yomi',
                        'childs' =>
                        [
                            0 => 'Mechuka Adc',
                            1 => 'Monigong Eac',
                            2 => 'Pidi Circle',
                            3 => 'Tato Eac',
                        ],
                    ],
                    236 =>
                    [
                        'name' => 'Lower Subansiri',
                        'childs' =>
                        [
                            0 => 'Old Ziro Sdo',
                            1 => 'Ziro Hq',
                        ],
                    ],
                    234 =>
                    [
                        'name' => 'Lohit',
                        'childs' =>
                        [
                            0 => 'Sunpura Circle',
                            1 => 'Tezu Hq',
                            2 => 'Wakro Circle',
                        ],
                    ],
                ],
            ],
            22 =>
            [
                'name' => 'Chhattisgarh',
                'childs' =>
                [
                    381 =>
                    [
                        'name' => 'Uttar Bastar Kanker',
                        'childs' =>
                        [
                            0 => 'Aamabeda',
                            1 => 'Antagarh',
                            2 => 'Bande',
                            3 => 'Bhanupratappur',
                            4 => 'Charama',
                            5 => 'Durgkondal',
                            6 => 'Kanker',
                            7 => 'Koylibeda',
                            8 => 'Narharpur',
                            9 => 'Pakhanjur',
                            10 => 'Sarona',
                        ],
                    ],
                    387 =>
                    [
                        'name' => 'Raipur',
                        'childs' =>
                        [
                            0 => 'Abhanpur',
                            1 => 'Arang',
                            2 => 'Dharsiwa',
                            3 => 'Gobra Nawapara',
                            4 => 'Kharora',
                            5 => 'Mandir Hasoud',
                            6 => 'Raipur',
                            7 => 'Tilda',
                        ],
                    ],
                    762 =>
                    [
                        'name' => 'Sakti',
                        'childs' =>
                        [
                            0 => 'Adbhar',
                            1 => 'Bhothiya',
                            2 => 'Chandrapur',
                            3 => 'Dabhra',
                            4 => 'Hasoud',
                            5 => 'Jaijaipur',
                            6 => 'Malkharoda',
                            7 => 'Naya Baradwar',
                            8 => 'Sakti',
                        ],
                    ],
                    378 =>
                    [
                        'name' => 'Durg',
                        'childs' =>
                        [
                            0 => 'Ahiwara',
                            1 => 'Bhilai-3',
                            2 => 'Bori',
                            3 => 'Dhamdha',
                            4 => 'Durg',
                            5 => 'Patan',
                        ],
                    ],
                    383 =>
                    [
                        'name' => 'Korba',
                        'childs' =>
                        [
                            0 => 'Ajgarbahar',
                            1 => 'Barpali',
                            2 => 'Bhaisma',
                            3 => 'Darri',
                            4 => 'Dipka',
                            5 => 'Hardibazar',
                            6 => 'Kartala',
                            7 => 'Katghora',
                            8 => 'Korba',
                            9 => 'Pali',
                            10 => 'Pasan',
                            11 => 'Poundi-Uproda',
                        ],
                    ],
                    379 =>
                    [
                        'name' => 'Janjgir-Champa',
                        'childs' =>
                        [
                            0 => 'Akaltara',
                            1 => 'Baloda',
                            2 => 'Bamhnidih',
                            3 => 'Champa',
                            4 => 'Janjgir',
                            5 => 'Nawagarh',
                            6 => 'Pamgarh',
                            7 => 'Saragaon',
                            8 => 'Shivrinarayan',
                        ],
                    ],
                    761 =>
                    [
                        'name' => 'Mohla-Manpur-Ambagarh Chouki',
                        'childs' =>
                        [
                            0 => 'Ambagarh Chouki',
                            1 => 'Aundhi',
                            2 => 'Khadgaon',
                            3 => 'Manpur',
                            4 => 'Mohla',
                        ],
                    ],
                    389 =>
                    [
                        'name' => 'Surguja',
                        'childs' =>
                        [
                            0 => 'Ambikapur',
                            1 => 'Batouli',
                            2 => 'Darima',
                            3 => 'Lakhanpur',
                            4 => 'Lundra',
                            5 => 'Mainpat',
                            6 => 'Sitapur',
                            7 => 'Udaypur',
                        ],
                    ],
                    645 =>
                    [
                        'name' => 'Gariyaband',
                        'childs' =>
                        [
                            0 => 'Amlipadar',
                            1 => 'Chhura',
                            2 => 'Devbhog',
                            3 => 'Fingeshwar',
                            4 => 'Gariyaband',
                            5 => 'Mainpur',
                            6 => 'Rajim',
                        ],
                    ],
                    646 =>
                    [
                        'name' => 'Balod',
                        'childs' =>
                        [
                            0 => 'Arjunda',
                            1 => 'Balod',
                            2 => 'Daundi',
                            3 => 'Daundilohara',
                            4 => 'Gunderdehi',
                            5 => 'Gurur',
                            6 => 'Marri Bangla(Deori]',
                        ],
                    ],
                    376 =>
                    [
                        'name' => 'Dakshin Bastar Dantewada',
                        'childs' =>
                        [
                            0 => 'Bade Bacheli',
                            1 => 'Barsur',
                            2 => 'Dantewada',
                            3 => 'Gidam',
                            4 => 'Katekalyan',
                            5 => 'Kuakonda',
                        ],
                    ],
                    643 =>
                    [
                        'name' => 'Kondagaon',
                        'childs' =>
                        [
                            0 => 'Baderajpur',
                            1 => 'Dhanora',
                            2 => 'Keshkal',
                            3 => 'Kondagaon',
                            4 => 'Makdi',
                            5 => 'Mardapal',
                            6 => 'Pharasgaon',
                        ],
                    ],
                    380 =>
                    [
                        'name' => 'Jashpur',
                        'childs' =>
                        [
                            0 => 'Bagbahar',
                            1 => 'Bagicha',
                            2 => 'Duldula',
                            3 => 'Farsabahar',
                            4 => 'Jashpur',
                            5 => 'Kansabel',
                            6 => 'Kunkuri',
                            7 => 'Manora',
                            8 => 'Pathalgaon',
                            9 => 'Sanna',
                        ],
                    ],
                    385 =>
                    [
                        'name' => 'Mahasamund',
                        'childs' =>
                        [
                            0 => 'Bagbahra',
                            1 => 'Basna',
                            2 => 'Komakhan',
                            3 => 'Mahasamund',
                            4 => 'Pithora',
                            5 => 'Saraipali',
                        ],
                    ],
                    384 =>
                    [
                        'name' => 'Korea',
                        'childs' =>
                        [
                            0 => 'Baikunthpur',
                            1 => 'Patna',
                            2 => 'Pondi(Bachra]',
                            3 => 'Sonhat',
                        ],
                    ],
                    374 =>
                    [
                        'name' => 'Bastar',
                        'childs' =>
                        [
                            0 => 'Bakavand',
                            1 => 'Bastanar',
                            2 => 'Bastar',
                            3 => 'Bhanpuri',
                            4 => 'Darbha',
                            5 => 'Jagdalpur',
                            6 => 'Karpawand',
                            7 => 'Lohandiguda',
                            8 => 'Nangur',
                            9 => 'Tokapal',
                        ],
                    ],
                    644 =>
                    [
                        'name' => 'Balodabazar-Bhatapara',
                        'childs' =>
                        [
                            0 => 'Balodabazar',
                            1 => 'Bhatapara',
                            2 => 'Kasdol',
                            3 => 'Lavan',
                            4 => 'Palari',
                            5 => 'Simga',
                            6 => 'Sonakhan',
                            7 => 'Suhela',
                            8 => 'Tundara',
                        ],
                    ],
                    649 =>
                    [
                        'name' => 'Balrampur-Ramanujganj',
                        'childs' =>
                        [
                            0 => 'Balrampur',
                            1 => 'Chalgali',
                            2 => 'Chando',
                            3 => 'Daura-Kochali',
                            4 => 'Kusmi(Samri]',
                            5 => 'Raghunathnagar',
                            6 => 'Rajpur',
                            7 => 'Ramanujganj',
                            8 => 'Ramchandrapur',
                            9 => 'Samri',
                            10 => 'Shankargarh',
                            11 => 'Wadrafnagar',
                        ],
                    ],
                    763 =>
                    [
                        'name' => 'Sarangarh-Bilaigarh',
                        'childs' =>
                        [
                            0 => 'Baramkela',
                            1 => 'Bhatgaon',
                            2 => 'Bilaigarh',
                            3 => 'Sarangarh',
                            4 => 'Sariya',
                            5 => 'Sarsiwa',
                        ],
                    ],
                    377 =>
                    [
                        'name' => 'Dhamtari',
                        'childs' =>
                        [
                            0 => 'Belargaon',
                            1 => 'Bhakhara',
                            2 => 'Dhamtari',
                            3 => 'Kukrel',
                            4 => 'Kurud',
                            5 => 'Magarlod',
                            6 => 'Nagri',
                        ],
                    ],
                    375 =>
                    [
                        'name' => 'Bilaspur',
                        'childs' =>
                        [
                            0 => 'Belgahna',
                            1 => 'Beltara',
                            2 => 'Bilaspur',
                            3 => 'Bilha',
                            4 => 'Bodri',
                            5 => 'Kota',
                            6 => 'Masturi',
                            7 => 'Pachpedi',
                            8 => 'Ratanpur',
                            9 => 'Sankari',
                            10 => 'Seepat',
                            11 => 'Takhatpur',
                        ],
                    ],
                    650 =>
                    [
                        'name' => 'Bemetara',
                        'childs' =>
                        [
                            0 => 'Bemetara',
                            1 => 'Berla',
                            2 => 'Bhimbhauri',
                            3 => 'Darhi',
                            4 => 'Devkar',
                            5 => 'Nandghat',
                            6 => 'Nawagarh',
                            7 => 'Saja',
                            8 => 'Thankhamariya',
                        ],
                    ],
                    636 =>
                    [
                        'name' => 'Bijapur',
                        'childs' =>
                        [
                            0 => 'Bhairamgarh',
                            1 => 'Bhopalpattnam',
                            2 => 'Bijapur',
                            3 => 'Gangaloor',
                            4 => 'Kutru',
                            5 => 'Usur',
                        ],
                    ],
                    648 =>
                    [
                        'name' => 'Surajpur',
                        'childs' =>
                        [
                            0 => 'Bhaiyathan',
                            1 => 'Bhatgaon',
                            2 => 'Biharpur',
                            3 => 'Latori',
                            4 => 'Odgi',
                            5 => 'Pratappur',
                            6 => 'Premnagar',
                            7 => 'Ramanujnagar',
                            8 => 'Surajpur',
                        ],
                    ],
                    760 =>
                    [
                        'name' => 'Manendragarh-Chirmiri-Bharatpur(M C B]',
                        'childs' =>
                        [
                            0 => 'Bharatpur',
                            1 => 'Chirmiri',
                            2 => 'Kelhari',
                            3 => 'Khadganva',
                            4 => 'Kotadol',
                            5 => 'Manendragarh',
                        ],
                    ],
                    382 =>
                    [
                        'name' => 'Kabeerdham',
                        'childs' =>
                        [
                            0 => 'Bodla',
                            1 => 'Kawardha',
                            2 => 'Kukdur',
                            3 => 'Kunda',
                            4 => 'Pandariya',
                            5 => 'Pipariya',
                            6 => 'Rengakharkala',
                            7 => 'Sahaspur Lohara',
                        ],
                    ],
                    386 =>
                    [
                        'name' => 'Raigarh',
                        'childs' =>
                        [
                            0 => 'Chhal',
                            1 => 'Dharamjaigarh',
                            2 => 'Gharghoda',
                            3 => 'Kapu',
                            4 => 'Kharsia',
                            5 => 'Lailunga',
                            6 => 'Mukdega',
                            7 => 'Pusour',
                            8 => 'Raigarh',
                            9 => 'Tamnar',
                        ],
                    ],
                    642 =>
                    [
                        'name' => 'Sukma',
                        'childs' =>
                        [
                            0 => 'Chhindgarh',
                            1 => 'Dornapal',
                            2 => 'Gadiras',
                            3 => 'Jagargunda',
                            4 => 'Konta',
                            5 => 'Sukma',
                            6 => 'Tongpal',
                        ],
                    ],
                    637 =>
                    [
                        'name' => 'Narayanpur',
                        'childs' =>
                        [
                            0 => 'Chhotedongar',
                            1 => 'Kohkameta',
                            2 => 'Narayanpur',
                            3 => 'Orchha',
                        ],
                    ],
                    759 =>
                    [
                        'name' => 'Khairagarh-Chhuikhadan-Gandai',
                        'childs' =>
                        [
                            0 => 'Chhuikhadan',
                            1 => 'Gandai',
                            2 => 'Khairagarh',
                            3 => 'Salhewara',
                        ],
                    ],
                    388 =>
                    [
                        'name' => 'Rajnandgaon',
                        'childs' =>
                        [
                            0 => 'Chhuriya',
                            1 => 'Dongargaon',
                            2 => 'Dongargarh',
                            3 => 'Ghumka',
                            4 => 'Kumarda',
                            5 => 'Lal Bahadur Nagar',
                            6 => 'Rajnandgaon',
                        ],
                    ],
                    647 =>
                    [
                        'name' => 'Mungeli',
                        'childs' =>
                        [
                            0 => 'Jarhagaon',
                            1 => 'Lalpur Thana',
                            2 => 'Lormi',
                            3 => 'Mungeli',
                            4 => 'Pathariya',
                            5 => 'Sargaon',
                        ],
                    ],
                    734 =>
                    [
                        'name' => 'Gaurela-Pendra-Marwahi',
                        'childs' =>
                        [
                            0 => 'Marwahi',
                            1 => 'Pendra',
                            2 => 'Pendra Road',
                            3 => 'Sakola',
                        ],
                    ],
                ],
            ],
            8 =>
            [
                'name' => 'Rajasthan',
                'childs' =>
                [
                    772 =>
                    [
                        'name' => 'Phalodi',
                        'childs' =>
                        [
                            0 => 'Aau',
                            1 => 'Bap',
                            2 => 'Bapini',
                            3 => 'Dechoo',
                            4 => 'Ghantiyali',
                            5 => 'Lohawat',
                            6 => 'Phalodi',
                            7 => 'Setrawa',
                        ],
                    ],
                    88 =>
                    [
                        'name' => 'Banswara',
                        'childs' =>
                        [
                            0 => 'Abapura',
                            1 => 'Anandpuri',
                            2 => 'Arthuna',
                            3 => 'Bagidora',
                            4 => 'Banswara',
                            5 => 'Chhoti Sarwan',
                            6 => 'Gangadtalai',
                            7 => 'Ganora',
                            8 => 'Garhi',
                            9 => 'Ghatol',
                            10 => 'Kushalgarh',
                            11 => 'Sajjangarh',
                        ],
                    ],
                    115 =>
                    [
                        'name' => 'Sirohi',
                        'childs' =>
                        [
                            0 => 'Abu Road',
                            1 => 'Deldar',
                            2 => 'Pindwara',
                            3 => 'Reodar',
                            4 => 'Sheoganj',
                            5 => 'Sirohi',
                        ],
                    ],
                    104 =>
                    [
                        'name' => 'Jalore',
                        'childs' =>
                        [
                            0 => 'Ahore',
                            1 => 'Bhadrajun',
                            2 => 'Bhinmal',
                            3 => 'Jalore',
                            4 => 'Jaswantpura',
                            5 => 'Sayla',
                        ],
                    ],
                    86 =>
                    [
                        'name' => 'Ajmer',
                        'childs' =>
                        [
                            0 => 'Ajmer',
                            1 => 'Arain',
                            2 => 'Bhinay',
                            3 => 'Kekri',
                            4 => 'Kishangarh',
                            5 => 'Nasirabad',
                            6 => 'Peesangan',
                            7 => 'Pushkar',
                            8 => 'Roopangarh',
                            9 => 'Sarwar',
                            10 => 'Sawar',
                            11 => 'Tantoti',
                        ],
                    ],
                    105 =>
                    [
                        'name' => 'Jhalawar',
                        'childs' =>
                        [
                            0 => 'Aklera',
                            1 => 'Asnawar',
                            2 => 'Bakani',
                            3 => 'Dag',
                            4 => 'Gangdhar',
                            5 => 'Jhalrapatan',
                            6 => 'Khanpur',
                            7 => 'Manohar Thana',
                            8 => 'Pachpahar',
                            9 => 'Pirawa',
                            10 => 'Raipur',
                            11 => 'Sunel',
                        ],
                    ],
                    116 =>
                    [
                        'name' => 'Tonk',
                        'childs' =>
                        [
                            0 => 'Aligarh',
                            1 => 'Deoli',
                            2 => 'Dooni',
                            3 => 'Malpura',
                            4 => 'Nagarfort',
                            5 => 'Niwai',
                            6 => 'Peeplu',
                            7 => 'Todaraisingh',
                            8 => 'Tonk',
                            9 => 'Uniara',
                        ],
                    ],
                    87 =>
                    [
                        'name' => 'Alwar',
                        'childs' =>
                        [
                            0 => 'Alwar',
                            1 => 'Govindgarh',
                            2 => 'Kathumar',
                            3 => 'Lachhmangarh',
                            4 => 'Malakhera',
                            5 => 'Nauganwa',
                            6 => 'Pratapgarh',
                            7 => 'Rajgarh',
                            8 => 'Ramgarh',
                            9 => 'Reni',
                            10 => 'Tehla',
                            11 => 'Thanagazi',
                        ],
                    ],
                    102 =>
                    [
                        'name' => 'Jaipur',
                        'childs' =>
                        [
                            0 => 'Amber',
                            1 => 'Jaipur',
                            2 => 'Kalwar',
                            3 => 'Sanganer',
                        ],
                    ],
                    112 =>
                    [
                        'name' => 'Rajsamand',
                        'childs' =>
                        [
                            0 => 'Amet',
                            1 => 'Bhim',
                            2 => 'Delwara',
                            3 => 'Deogarh',
                            4 => 'Garhbor',
                            5 => 'Khamnor',
                            6 => 'Kumbhalgarh',
                            7 => 'Kunwariya',
                            8 => 'Nathdwara',
                            9 => 'Railmagra',
                            10 => 'Rajsamand',
                            11 => 'Sardargarh',
                        ],
                    ],
                    783 =>
                    [
                        'name' => 'Jaipur (Gramin]',
                        'childs' =>
                        [
                            0 => 'Andhi',
                            1 => 'Bassi',
                            2 => 'Chaksu',
                            3 => 'Chomu',
                            4 => 'Jalsu',
                            5 => 'Jamwaramgarh',
                            6 => 'Jobner',
                            7 => 'Kishangarh Renwal',
                            8 => 'Kotkhawda',
                            9 => 'Madhorajpura',
                            10 => 'Phulera (Hq.Sambhar]',
                            11 => 'Rampura Dabri',
                            12 => 'Shahpura',
                            13 => 'Toonga',
                        ],
                    ],
                    89 =>
                    [
                        'name' => 'Baran',
                        'childs' =>
                        [
                            0 => 'Anta',
                            1 => 'Atru',
                            2 => 'Baran',
                            3 => 'Chhabra',
                            4 => 'Chhipabarod',
                            5 => 'Kishanganj',
                            6 => 'Mangrol',
                            7 => 'Shahbad',
                        ],
                    ],
                    92 =>
                    [
                        'name' => 'Bhilwara',
                        'childs' =>
                        [
                            0 => 'Antali',
                            1 => 'Asind',
                            2 => 'Bhilwara',
                            3 => 'Bijoliya',
                            4 => 'Hameergarh',
                            5 => 'Hurda',
                            6 => 'Kareda',
                            7 => 'Mandal',
                            8 => 'Mandalgarh',
                            9 => 'Raipur',
                            10 => 'Sahara',
                            11 => 'Sawaipur',
                        ],
                    ],
                    100 =>
                    [
                        'name' => 'Ganganagar',
                        'childs' =>
                        [
                            0 => 'Anupgarh',
                            1 => 'Gajsinghpur',
                            2 => 'Ganganagar',
                            3 => 'Gharsana',
                            4 => 'Karanpur',
                            5 => 'Padampur',
                            6 => 'Raisinghnagar',
                            7 => 'Rawla',
                            8 => 'Sadulshahar',
                            9 => 'Suratgarh',
                            10 => 'Vijainagar',
                        ],
                    ],
                    629 =>
                    [
                        'name' => 'Pratapgarh',
                        'childs' =>
                        [
                            0 => 'Arnod',
                            1 => 'Chhoti Sadri',
                            2 => 'Dalot',
                            3 => 'Dhariawad',
                            4 => 'Peepalkhoont',
                            5 => 'Pratapgarh',
                            6 => 'Suhagpura',
                        ],
                    ],
                    99 =>
                    [
                        'name' => 'Dungarpur',
                        'childs' =>
                        [
                            0 => 'Aspur',
                            1 => 'Bichhiwara',
                            2 => 'Chikhali',
                            3 => 'Dovda',
                            4 => 'Dungarpur',
                            5 => 'Galiyakot',
                            6 => 'Gamri Ahara',
                            7 => 'Jhonthripal',
                            8 => 'Obri',
                            9 => 'Paldewal',
                            10 => 'Sabla',
                            11 => 'Sagwara',
                            12 => 'Simalwara',
                        ],
                    ],
                    117 =>
                    [
                        'name' => 'Udaipur',
                        'childs' =>
                        [
                            0 => 'Badgaon',
                            1 => 'Barapal',
                            2 => 'Bhinder',
                            3 => 'Ghasa',
                            4 => 'Girwa',
                            5 => 'Gogunda',
                            6 => 'Jhadol',
                            7 => 'Kanod',
                            8 => 'Kherwara',
                            9 => 'Kotra',
                            10 => 'Kurabar',
                            11 => 'Mavli',
                            12 => 'Nayagaon',
                            13 => 'Phalasiyan',
                            14 => 'Rishabhdeo',
                            15 => 'Sayra',
                            16 => 'Vallabhnagar',
                        ],
                    ],
                    774 =>
                    [
                        'name' => 'Beawar',
                        'childs' =>
                        [
                            0 => 'Badnor',
                            1 => 'Beawar',
                            2 => 'Jaitaran',
                            3 => 'Masuda',
                            4 => 'Raipur',
                            5 => 'Tatgarh',
                            6 => 'Vijaynagar',
                        ],
                    ],
                    779 =>
                    [
                        'name' => 'Sanchore',
                        'childs' =>
                        [
                            0 => 'Bagora',
                            1 => 'Chitalwana',
                            2 => 'Raniwara',
                            3 => 'Sanchore',
                        ],
                    ],
                    97 =>
                    [
                        'name' => 'Dausa',
                        'childs' =>
                        [
                            0 => 'Bahrawanda',
                            1 => 'Baijupara',
                            2 => 'Bandikui',
                            3 => 'Baswa',
                            4 => 'Bhandarej',
                            5 => 'Dausa',
                            6 => 'Kundal',
                            7 => 'Lalsot',
                            8 => 'Lawan',
                            9 => 'Mahwa',
                            10 => 'Mandawar',
                            11 => 'Nangal Rajawatan',
                            12 => 'Nirjharna',
                            13 => 'Paparda',
                            14 => 'Rahuwas',
                            15 => 'Ramgarh Pachwara',
                            16 => 'Sainthal',
                            17 => 'Sikrai',
                        ],
                    ],
                    93 =>
                    [
                        'name' => 'Bikaner',
                        'childs' =>
                        [
                            0 => 'Bajju',
                            1 => 'Bikaner',
                            2 => 'Chhatargarh',
                            3 => 'Hadan',
                            4 => 'Jasrasar',
                            5 => 'Khajuwala',
                            6 => 'Kolayat',
                            7 => 'Lunkaransar',
                            8 => 'Nokha',
                            9 => 'Poogal',
                            10 => 'Sridungargarh',
                        ],
                    ],
                    778 =>
                    [
                        'name' => 'Jodhpur (Gramin]',
                        'childs' =>
                        [
                            0 => 'Balesar',
                            1 => 'Baori',
                            2 => 'Bhopalgarh',
                            3 => 'Bilara',
                            4 => 'Chamu',
                            5 => 'Jhanwar',
                            6 => 'Kudi Bhagtasni',
                            7 => 'Luni',
                            8 => 'Osian',
                            9 => 'Pipar Shahar',
                            10 => 'Sekhala',
                            11 => 'Shergarh',
                            12 => 'Tinwari',
                        ],
                    ],
                    108 =>
                    [
                        'name' => 'Karauli',
                        'childs' =>
                        [
                            0 => 'Balghat',
                            1 => 'Hindaun',
                            2 => 'Karauli',
                            3 => 'Mandrayal',
                            4 => 'Masalpur',
                            5 => 'Nadoti',
                            6 => 'Sapotra',
                            7 => 'Shrimahaveer Ji',
                            8 => 'Suroth',
                            9 => 'Todabhim',
                        ],
                    ],
                    111 =>
                    [
                        'name' => 'Pali',
                        'childs' =>
                        [
                            0 => 'Bali',
                            1 => 'Desuri',
                            2 => 'Marwar Junction',
                            3 => 'Pali',
                            4 => 'Rani',
                            5 => 'Rohat',
                            6 => 'Sojat',
                            7 => 'Sumerpur',
                        ],
                    ],
                    113 =>
                    [
                        'name' => 'Sawai Madhopur',
                        'childs' =>
                        [
                            0 => 'Bamanwas',
                            1 => 'Barnala',
                            2 => 'Bonli',
                            3 => 'Chauth Ka Barwara',
                            4 => 'Gangapur',
                            5 => 'Khandar',
                            6 => 'Malarna Doongar',
                            7 => 'Mitrapura',
                            8 => 'Sawai Madhopur',
                            9 => 'Talawara',
                            10 => 'Wazeerpur',
                        ],
                    ],
                    780 =>
                    [
                        'name' => 'Shahpura',
                        'childs' =>
                        [
                            0 => 'Banera',
                            1 => 'Jahazpur',
                            2 => 'Kachhola',
                            3 => 'Kotri',
                            4 => 'Phooliya Kalan',
                            5 => 'Shahpura',
                        ],
                    ],
                    782 =>
                    [
                        'name' => 'Kotputli-Behror',
                        'childs' =>
                        [
                            0 => 'Bansur',
                            1 => 'Behror',
                            2 => 'Kotputli',
                            3 => 'Mandhan',
                            4 => 'Narayanpur',
                            5 => 'Neemrana',
                            6 => 'Paota',
                            7 => 'Viratnagar',
                        ],
                    ],
                    98 =>
                    [
                        'name' => 'Dholpur',
                        'childs' =>
                        [
                            0 => 'Bari',
                            1 => 'Basai Nawab',
                            2 => 'Baseri',
                            3 => 'Dholpur',
                            4 => 'Maniya',
                            5 => 'Rajakhera',
                            6 => 'Sarmathura',
                            7 => 'Sepau',
                        ],
                    ],
                    95 =>
                    [
                        'name' => 'Chittorgarh',
                        'childs' =>
                        [
                            0 => 'Bari Sadri',
                            1 => 'Bassi',
                            2 => 'Begun',
                            3 => 'Bhadesar',
                            4 => 'Bhupalsagar',
                            5 => 'Chittorgarh',
                            6 => 'Dungla',
                            7 => 'Gangrar',
                            8 => 'Kapasan',
                            9 => 'Nimbahera',
                            10 => 'Rashmi',
                            11 => 'Rawatbhata',
                        ],
                    ],
                    90 =>
                    [
                        'name' => 'Barmer',
                        'childs' =>
                        [
                            0 => 'Barmer',
                            1 => 'Barmer Gramin',
                            2 => 'Batadoo',
                            3 => 'Chohtan',
                            4 => 'Dhanau',
                            5 => 'Dhorimanna',
                            6 => 'Gadraroad',
                            7 => 'Gudha Malani',
                            8 => 'Nokhra',
                            9 => 'Ramsar',
                            10 => 'Sedwa',
                            11 => 'Sheo',
                        ],
                    ],
                    91 =>
                    [
                        'name' => 'Bharatpur',
                        'childs' =>
                        [
                            0 => 'Bayana',
                            1 => 'Bharatpur',
                            2 => 'Bhusawar',
                            3 => 'Nadbai',
                            4 => 'Rudawal',
                            5 => 'Rupbas',
                            6 => 'Uchchain',
                            7 => 'Weir',
                        ],
                    ],
                    775 =>
                    [
                        'name' => 'Balotra',
                        'childs' =>
                        [
                            0 => 'Baytoo',
                            1 => 'Gida',
                            2 => 'Kalyanpur',
                            3 => 'Pachpadra',
                            4 => 'Patodi',
                            5 => 'Samdari',
                            6 => 'Sindhari',
                            7 => 'Siwana',
                        ],
                    ],
                    101 =>
                    [
                        'name' => 'Hanumangarh',
                        'childs' =>
                        [
                            0 => 'Bhadra',
                            1 => 'Hanumangarh',
                            2 => 'Nohar',
                            3 => 'Pallu',
                            4 => 'Pilibanga',
                            5 => 'Rawatsar',
                            6 => 'Sangaria',
                            7 => 'Tibbi',
                        ],
                    ],
                    96 =>
                    [
                        'name' => 'Churu',
                        'childs' =>
                        [
                            0 => 'Bhanipura',
                            1 => 'Bidasar',
                            2 => 'Churu',
                            3 => 'Rajaldesar',
                            4 => 'Rajgarh',
                            5 => 'Ratangarh',
                            6 => 'Sardarshahar',
                            7 => 'Sidhmukh',
                            8 => 'Sujangarh',
                            9 => 'Taranagar',
                        ],
                    ],
                    103 =>
                    [
                        'name' => 'Jaisalmer',
                        'childs' =>
                        [
                            0 => 'Bhaniyana',
                            1 => 'Fatehgarh',
                            2 => 'Jaisalmer',
                            3 => 'Phalsoond',
                            4 => 'Pokaran',
                            5 => 'Ramgarh',
                            6 => 'Sam',
                        ],
                    ],
                    106 =>
                    [
                        'name' => 'Jhunjhunu',
                        'childs' =>
                        [
                            0 => 'Bissau',
                            1 => 'Buhana',
                            2 => 'Chirawa',
                            3 => 'Gudhagorji',
                            4 => 'Jhunjhunu',
                            5 => 'Malsisar',
                            6 => 'Mandawa',
                            7 => 'Nawalgarh',
                            8 => 'Pilani',
                            9 => 'Surajgarh',
                        ],
                    ],
                    94 =>
                    [
                        'name' => 'Bundi',
                        'childs' =>
                        [
                            0 => 'Bundi',
                            1 => 'Hindoli',
                            2 => 'Indragarh',
                            3 => 'Keshoraipatan',
                            4 => 'Nainwa',
                            5 => 'Raithal',
                            6 => 'Talera',
                        ],
                    ],
                    109 =>
                    [
                        'name' => 'Kota',
                        'childs' =>
                        [
                            0 => 'Chechat',
                            1 => 'Digod',
                            2 => 'Kanwas',
                            3 => 'Ladpura',
                            4 => 'Pipalda',
                            5 => 'Ramganj Mandi',
                            6 => 'Sangod',
                        ],
                    ],
                    768 =>
                    [
                        'name' => 'Didwana-Kuchaman',
                        'childs' =>
                        [
                            0 => 'Chhoti Khatoo',
                            1 => 'Didwana',
                            2 => 'Kuchaman City',
                            3 => 'Ladnu',
                            4 => 'Makrana',
                            5 => 'Maulasar',
                            6 => 'Nawa',
                            7 => 'Parbatsar',
                        ],
                    ],
                    114 =>
                    [
                        'name' => 'Sikar',
                        'childs' =>
                        [
                            0 => 'Danta Ramgarh',
                            1 => 'Dhod',
                            2 => 'Fatehpur',
                            3 => 'Khandela',
                            4 => 'Lachhmangarh',
                            5 => 'Nechhwa',
                            6 => 'Ramgarh Shekhawati',
                            7 => 'Reengus',
                            8 => 'Sikar',
                            9 => 'Sikar Gramin',
                        ],
                    ],
                    767 =>
                    [
                        'name' => 'Deeg',
                        'childs' =>
                        [
                            0 => 'Deeg',
                            1 => 'Janoothar',
                            2 => 'Jurhara',
                            3 => 'Kaman',
                            4 => 'Kumher',
                            5 => 'Nagar',
                            6 => 'Pahari',
                            7 => 'Rarah',
                            8 => 'Sikri',
                        ],
                    ],
                    110 =>
                    [
                        'name' => 'Nagaur',
                        'childs' =>
                        [
                            0 => 'Degana',
                            1 => 'Deh',
                            2 => 'Jayal',
                            3 => 'Kheenvsar',
                            4 => 'Merta',
                            5 => 'Mundwa',
                            6 => 'Nagaur',
                            7 => 'Riyan Bari',
                            8 => 'Sanjoo',
                        ],
                    ],
                    769 =>
                    [
                        'name' => 'Dudu',
                        'childs' =>
                        [
                            0 => 'Dudu',
                            1 => 'Mauzamabad',
                            2 => 'Phagi',
                        ],
                    ],
                    770 =>
                    [
                        'name' => 'Khairthal-Tijara',
                        'childs' =>
                        [
                            0 => 'Harsoli',
                            1 => 'Khairthal',
                            2 => 'Kishangarhbas',
                            3 => 'Kotkasim',
                            4 => 'Mundawar',
                            5 => 'Tapukara',
                            6 => 'Tijara',
                        ],
                    ],
                    777 =>
                    [
                        'name' => 'Salumbar',
                        'childs' =>
                        [
                            0 => 'Jhallara',
                            1 => 'Lasadiya',
                            2 => 'Salumbar',
                            3 => 'Sarada',
                            4 => 'Semari',
                        ],
                    ],
                    107 =>
                    [
                        'name' => 'Jodhpur',
                        'childs' =>
                        [
                            0 => 'Jodhpur',
                        ],
                    ],
                    773 =>
                    [
                        'name' => 'Neem Ka Thana',
                        'childs' =>
                        [
                            0 => 'Khetri',
                            1 => 'Neem Ka Thana',
                            2 => 'Patan',
                            3 => 'Shrimadhopur',
                            4 => 'Udaipurwati',
                        ],
                    ],
                    776 =>
                    [
                        'name' => 'Anupgarh',
                        'childs' =>
                        [],
                    ],
                ],
            ],
            24 =>
            [
                'name' => 'Gujarat',
                'childs' =>
                [
                    449 =>
                    [
                        'name' => 'Kachchh',
                        'childs' =>
                        [
                            0 => 'Abdasa',
                            1 => 'Anjar',
                            2 => 'Bhachau',
                            3 => 'Bhuj',
                            4 => 'Gandhidham',
                            5 => 'Lakhpat',
                            6 => 'Mandvi',
                            7 => 'Mundra',
                            8 => 'Nakhatrana',
                            9 => 'Rapar',
                        ],
                    ],
                    459 =>
                    [
                        'name' => 'Surat',
                        'childs' =>
                        [
                            0 => 'Adajan',
                            1 => 'Bardoli',
                            2 => 'Chorasi',
                            3 => 'Kamrej',
                            4 => 'Katargam',
                            5 => 'Mahuva',
                            6 => 'Majura',
                            7 => 'Mandvi',
                            8 => 'Mangrol',
                            9 => 'Olpad',
                            10 => 'Palsana',
                            11 => 'Puna',
                            12 => 'Udhna',
                            13 => 'Umarpada',
                        ],
                    ],
                    444 =>
                    [
                        'name' => 'Dangs',
                        'childs' =>
                        [
                            0 => 'Ahwa',
                            1 => 'Subir',
                            2 => 'Waghai',
                        ],
                    ],
                    441 =>
                    [
                        'name' => 'Banas Kantha',
                        'childs' =>
                        [
                            0 => 'Amirgadh',
                            1 => 'Bhabhar',
                            2 => 'Danta',
                            3 => 'Dantiwada',
                            4 => 'Deesa',
                            5 => 'Deesa City',
                            6 => 'Deodar',
                            7 => 'Dhanera',
                            8 => 'Kankrej',
                            9 => 'Lakhani',
                            10 => 'Palanpur',
                            11 => 'Palanpur City',
                            12 => 'Suigam',
                            13 => 'Tharad',
                            14 => 'Vadgam',
                            15 => 'Vav',
                        ],
                    ],
                    442 =>
                    [
                        'name' => 'Bharuch',
                        'childs' =>
                        [
                            0 => 'Amod',
                            1 => 'Anklesvar',
                            2 => 'Bharuch',
                            3 => 'Hansot',
                            4 => 'Jambusar',
                            5 => 'Jhagadia',
                            6 => 'Netrang',
                            7 => 'Vagra',
                            8 => 'Valia',
                        ],
                    ],
                    439 =>
                    [
                        'name' => 'Amreli',
                        'childs' =>
                        [
                            0 => 'Amreli',
                            1 => 'Amreli-City',
                            2 => 'Babra',
                            3 => 'Bagasara',
                            4 => 'Dhari',
                            5 => 'Jafrabad',
                            6 => 'Khambha',
                            7 => 'Kunkavav Vadia',
                            8 => 'Lathi',
                            9 => 'Lilia',
                            10 => 'Rajula',
                            11 => 'Savar Kundla',
                        ],
                    ],
                    440 =>
                    [
                        'name' => 'Anand',
                        'childs' =>
                        [
                            0 => 'Anand City',
                            1 => 'Anand Rural',
                            2 => 'Anklav',
                            3 => 'Borsad',
                            4 => 'Khambhat',
                            5 => 'Petlad',
                            6 => 'Sojitra',
                            7 => 'Tarapur',
                            8 => 'Umreth',
                        ],
                    ],
                    438 =>
                    [
                        'name' => 'Ahmedabad',
                        'childs' =>
                        [
                            0 => 'Asarva',
                            1 => 'Bavla',
                            2 => 'Daskroi',
                            3 => 'Detroj-Rampura',
                            4 => 'Dhandhuka',
                            5 => 'Dholera',
                            6 => 'Dholka',
                            7 => 'Ghatlodiya',
                            8 => 'Mandal',
                            9 => 'Maninagar',
                            10 => 'Sabarmati',
                            11 => 'Sanand',
                            12 => 'Vatva',
                            13 => 'Vejalpur',
                            14 => 'Viramgam',
                        ],
                    ],
                    669 =>
                    [
                        'name' => 'Mahisagar',
                        'childs' =>
                        [
                            0 => 'Balasinor',
                            1 => 'Kadana',
                            2 => 'Khanpur',
                            3 => 'Lunawada',
                            4 => 'Santrampur',
                            5 => 'Virpur',
                        ],
                    ],
                    453 =>
                    [
                        'name' => 'Navsari',
                        'childs' =>
                        [
                            0 => 'Bansda',
                            1 => 'Chikhli',
                            2 => 'Gandevi',
                            3 => 'Jalalpore',
                            4 => 'Khergam',
                            5 => 'Navsari',
                        ],
                    ],
                    676 =>
                    [
                        'name' => 'Botad',
                        'childs' =>
                        [
                            0 => 'Barwala',
                            1 => 'Botad',
                            2 => 'Botad City',
                            3 => 'Gadhada',
                            4 => 'Ranpur',
                        ],
                    ],
                    672 =>
                    [
                        'name' => 'Arvalli',
                        'childs' =>
                        [
                            0 => 'Bayad',
                            1 => 'Bhiloda',
                            2 => 'Dhansura',
                            3 => 'Malpur',
                            4 => 'Meghraj',
                            5 => 'Modasa',
                        ],
                    ],
                    451 =>
                    [
                        'name' => 'Mahesana',
                        'childs' =>
                        [
                            0 => 'Becharaji',
                            1 => 'Jotana',
                            2 => 'Kadi',
                            3 => 'Kheralu',
                            4 => 'Mahesana',
                            5 => 'Mahesana City',
                            6 => 'Satlasana',
                            7 => 'Unjha',
                            8 => 'Vadnagar',
                            9 => 'Vijapur',
                            10 => 'Visnagar',
                        ],
                    ],
                    674 =>
                    [
                        'name' => 'Devbhumi Dwarka',
                        'childs' =>
                        [
                            0 => 'Bhanvad',
                            1 => 'Kalyanpur',
                            2 => 'Khambhalia',
                            3 => 'Okhamandal',
                        ],
                    ],
                    443 =>
                    [
                        'name' => 'Bhavnagar',
                        'childs' =>
                        [
                            0 => 'Bhavnagar',
                            1 => 'Bhavnagar City',
                            2 => 'Gariadhar',
                            3 => 'Ghogha',
                            4 => 'Jesar',
                            5 => 'Mahuva',
                            6 => 'Palitana',
                            7 => 'Sihor',
                            8 => 'Talaja',
                            9 => 'Umrala',
                            10 => 'Vallabhipur',
                        ],
                    ],
                    448 =>
                    [
                        'name' => 'Junagadh',
                        'childs' =>
                        [
                            0 => 'Bhesan',
                            1 => 'Junagadh',
                            2 => 'Junagadh City',
                            3 => 'Keshod',
                            4 => 'Malia Hatina',
                            5 => 'Manavadar',
                            6 => 'Mangrol',
                            7 => 'Mendarda',
                            8 => 'Vanthali',
                            9 => 'Visavadar',
                        ],
                    ],
                    668 =>
                    [
                        'name' => 'Chhotaudepur',
                        'childs' =>
                        [
                            0 => 'Bodeli',
                            1 => 'Chhota Udaipur',
                            2 => 'Jetpur Pavi',
                            3 => 'Kavant',
                            4 => 'Nasvadi',
                            5 => 'Sankheda',
                        ],
                    ],
                    455 =>
                    [
                        'name' => 'Patan',
                        'childs' =>
                        [
                            0 => 'Chanasma',
                            1 => 'Harij',
                            2 => 'Patan',
                            3 => 'Patan City',
                            4 => 'Radhanpur',
                            5 => 'Sami',
                            6 => 'Santalpur',
                            7 => 'Saraswati',
                            8 => 'Shankheshvar',
                            9 => 'Sidhpur',
                        ],
                    ],
                    460 =>
                    [
                        'name' => 'Surendranagar',
                        'childs' =>
                        [
                            0 => 'Chotila',
                            1 => 'Chuda',
                            2 => 'Dasada',
                            3 => 'Dhrangadhra',
                            4 => 'Lakhtar',
                            5 => 'Limbdi',
                            6 => 'Muli',
                            7 => 'Sayla',
                            8 => 'Surendranagar City',
                            9 => 'Thangadh',
                            10 => 'Wadhwan',
                        ],
                    ],
                    461 =>
                    [
                        'name' => 'Vadodara',
                        'childs' =>
                        [
                            0 => 'Dabhoi',
                            1 => 'Desar',
                            2 => 'Karjan',
                            3 => 'Padra',
                            4 => 'Savli',
                            5 => 'Sinor',
                            6 => 'Vadodara East',
                            7 => 'Vadodara North',
                            8 => 'Vadodara Rural',
                            9 => 'Vadodara South',
                            10 => 'Vadodara West',
                            11 => 'Vaghodia',
                        ],
                    ],
                    452 =>
                    [
                        'name' => 'Narmada',
                        'childs' =>
                        [
                            0 => 'Dediapada',
                            1 => 'Garudeshwar',
                            2 => 'Nandod',
                            3 => 'Sagbara',
                            4 => 'Tilakwada',
                        ],
                    ],
                    446 =>
                    [
                        'name' => 'Gandhinagar',
                        'childs' =>
                        [
                            0 => 'Dehgam',
                            1 => 'Gandhinagar',
                            2 => 'Kalol City',
                            3 => 'Kalol Gandhinagar',
                            4 => 'Mansa',
                        ],
                    ],
                    445 =>
                    [
                        'name' => 'Dahod',
                        'childs' =>
                        [
                            0 => 'Devgadbaria',
                            1 => 'Dhanpur',
                            2 => 'Dohad',
                            3 => 'Fatepura',
                            4 => 'Garbada',
                            5 => 'Jhalod',
                            6 => 'Limkheda',
                            7 => 'Sanjeli',
                            8 => 'Singvad',
                        ],
                    ],
                    462 =>
                    [
                        'name' => 'Valsad',
                        'childs' =>
                        [
                            0 => 'Dharampur',
                            1 => 'Kaprada',
                            2 => 'Pardi',
                            3 => 'Umbergaon',
                            4 => 'Valsad',
                            5 => 'Vapi',
                        ],
                    ],
                    457 =>
                    [
                        'name' => 'Rajkot',
                        'childs' =>
                        [
                            0 => 'Dhoraji',
                            1 => 'Gondal',
                            2 => 'Gondal City',
                            3 => 'Jamkandorna',
                            4 => 'Jasdan',
                            5 => 'Jetpur',
                            6 => 'Jetpur City',
                            7 => 'Kotda Sangani',
                            8 => 'Lodhika',
                            9 => 'Paddhari',
                            10 => 'Rajkot Taluka',
                            11 => 'Rajkot East',
                            12 => 'Rajkot South',
                            13 => 'Rajkot West',
                            14 => 'Upleta',
                            15 => 'Vinchchiya',
                        ],
                    ],
                    447 =>
                    [
                        'name' => 'Jamnagar',
                        'childs' =>
                        [
                            0 => 'Dhrol',
                            1 => 'Jamjodhpur',
                            2 => 'Jamnagar City',
                            3 => 'Jamnagar Rural',
                            4 => 'Jodiya',
                            5 => 'Kalavad',
                            6 => 'Lalpur',
                        ],
                    ],
                    641 =>
                    [
                        'name' => 'Tapi',
                        'childs' =>
                        [
                            0 => 'Dolvan',
                            1 => 'Kukarmunda',
                            2 => 'Nizar',
                            3 => 'Songadh',
                            4 => 'Uchchhal',
                            5 => 'Valod',
                            6 => 'Vyara',
                        ],
                    ],
                    450 =>
                    [
                        'name' => 'Kheda',
                        'childs' =>
                        [
                            0 => 'Galteshwar',
                            1 => 'Kapadvanj',
                            2 => 'Kathlal',
                            3 => 'Kheda',
                            4 => 'Mahudha',
                            5 => 'Matar',
                            6 => 'Mehmedabad',
                            7 => 'Nadiad',
                            8 => 'Nadiad City',
                            9 => 'Thasra',
                            10 => 'Vaso',
                        ],
                    ],
                    454 =>
                    [
                        'name' => 'Panch Mahals',
                        'childs' =>
                        [
                            0 => 'Ghoghamba',
                            1 => 'Godhra',
                            2 => 'Halol',
                            3 => 'Jambughoda',
                            4 => 'Kalol',
                            5 => 'Morwa (Hadaf]',
                            6 => 'Shehera',
                        ],
                    ],
                    675 =>
                    [
                        'name' => 'Gir Somnath',
                        'childs' =>
                        [
                            0 => 'Gir Gadhda',
                            1 => 'Kodinar',
                            2 => 'Patan-Veraval',
                            3 => 'Sutrapada',
                            4 => 'Talala',
                            5 => 'Una',
                            6 => 'Veraval City',
                        ],
                    ],
                    673 =>
                    [
                        'name' => 'Morbi',
                        'childs' =>
                        [
                            0 => 'Halvad',
                            1 => 'Maliya',
                            2 => 'Morbi City',
                            3 => 'Morvi',
                            4 => 'Tankara',
                            5 => 'Wankaner',
                        ],
                    ],
                    458 =>
                    [
                        'name' => 'Sabar Kantha',
                        'childs' =>
                        [
                            0 => 'Himatnagar',
                            1 => 'Idar',
                            2 => 'Khedbrahma',
                            3 => 'Poshina',
                            4 => 'Prantij',
                            5 => 'Talod',
                            6 => 'Vadali',
                            7 => 'Vijaynagar',
                        ],
                    ],
                    456 =>
                    [
                        'name' => 'Porbandar',
                        'childs' =>
                        [
                            0 => 'Kutiyana',
                            1 => 'Porabandar City',
                            2 => 'Porbandar',
                            3 => 'Ranavav',
                        ],
                    ],
                ],
            ],
            36 =>
            [
                'name' => 'Telangana',
                'childs' =>
                [
                    518 =>
                    [
                        'name' => 'Ranga Reddy',
                        'childs' =>
                        [
                            0 => 'Abdullapurmet',
                            1 => 'Amangal',
                            2 => 'Balapur',
                            3 => 'Chevella',
                            4 => 'Farooqnagar',
                            5 => 'Gandipet',
                            6 => 'Hayathnagar',
                            7 => 'Ibrahimpatnam',
                            8 => 'Jilled Chowdergudem',
                            9 => 'Kadthal',
                            10 => 'Kandukur',
                            11 => 'Keshampet',
                            12 => 'Kondurg',
                            13 => 'Kothur',
                            14 => 'Madgul',
                            15 => 'Maheshwaram',
                            16 => 'Manchal',
                            17 => 'Moinabad',
                            18 => 'Nandigama',
                            19 => 'Rajendranagar',
                            20 => 'Saroornagar',
                            21 => 'Serilingampally',
                            22 => 'Shabad',
                            23 => 'Shamshabad',
                            24 => 'Shankarpalle',
                            25 => 'Thalakondapally',
                            26 => 'Yacharam',
                        ],
                    ],
                    694 =>
                    [
                        'name' => 'Nagarkurnool',
                        'childs' =>
                        [
                            0 => 'Achampet',
                            1 => 'Amrabad',
                            2 => 'Balmoor',
                            3 => 'Bijinapally',
                            4 => 'Charakonda',
                            5 => 'Kalwakurthy',
                            6 => 'Kodair',
                            7 => 'Kollapur',
                            8 => 'Lingal',
                            9 => 'Nagarkurnool',
                            10 => 'Padara',
                            11 => 'Peddakothapally',
                            12 => 'Pentlavelli',
                            13 => 'Tadoor',
                            14 => 'Telkapally',
                            15 => 'Thimmajipet',
                            16 => 'Uppununthala',
                            17 => 'Urkonda',
                            18 => 'Vangoor',
                            19 => 'Veldanda',
                        ],
                    ],
                    514 =>
                    [
                        'name' => 'Nalgonda',
                        'childs' =>
                        [
                            0 => 'Adavidevulapalli',
                            1 => 'Anumula',
                            2 => 'Chandampeta',
                            3 => 'Chandur',
                            4 => 'Chinthapally',
                            5 => 'Chityal',
                            6 => 'Dameracherla',
                            7 => 'Devarakonda',
                            8 => 'Gundlapally',
                            9 => 'Gurrampode',
                            10 => 'Kanagal',
                            11 => 'Kattangur',
                            12 => 'Kethepally',
                            13 => 'Kondamallepally',
                            14 => 'Madugulapally',
                            15 => 'Marriguda',
                            16 => 'Miryalaguda',
                            17 => 'Munugode',
                            18 => 'Nakrekal',
                            19 => 'Nalgonda',
                            20 => 'Nampally',
                            21 => 'Narketpally',
                            22 => 'Neredugommu',
                            23 => 'Nidmanoor',
                            24 => 'Pedda Adesherlapally',
                            25 => 'Peddavoora',
                            26 => 'Shaligouraram',
                            27 => 'Thipparthy',
                            28 => 'Tirumalagiri Sagar',
                            29 => 'Tripuraram',
                            30 => 'Vemulapally',
                        ],
                    ],
                    697 =>
                    [
                        'name' => 'Yadadri Bhuvanagiri',
                        'childs' =>
                        [
                            0 => 'Adda Guduru',
                            1 => 'Alair',
                            2 => 'Atmakur (M]',
                            3 => 'Bhongir',
                            4 => 'Bibinagar',
                            5 => 'Bommalaramaram',
                            6 => 'B Pochampally',
                            7 => 'Choutuppal',
                            8 => 'Gundala',
                            9 => 'Motakonduru',
                            10 => 'Mothkur',
                            11 => 'Narayanpur',
                            12 => 'Rajapeta',
                            13 => 'Ramannapet',
                            14 => 'Thurkapally',
                            15 => 'Valigonda',
                            16 => 'Yadagirigutta',
                        ],
                    ],
                    512 =>
                    [
                        'name' => 'Mahabubnagar',
                        'childs' =>
                        [
                            0 => 'Addakal',
                            1 => 'Balanagar',
                            2 => 'Bhoothpur',
                            3 => 'Chinnachintakunta',
                            4 => 'Devarakadra',
                            5 => 'Gandeed',
                            6 => 'Hanwada',
                            7 => 'Jadcherla',
                            8 => 'Koilkonda',
                            9 => 'Mahabubnagar Rural',
                            10 => 'Mahabubnagar Urban',
                            11 => 'Midjil',
                            12 => 'Mohammadabad',
                            13 => 'Moosapet',
                            14 => 'Nawabpet',
                            15 => 'Rajapur',
                        ],
                    ],
                    501 =>
                    [
                        'name' => 'Adilabad',
                        'childs' =>
                        [
                            0 => 'Adilabad Rural',
                            1 => 'Adilabad Urban',
                            2 => 'Bazarhatnoor',
                            3 => 'Bela',
                            4 => 'Bheempur',
                            5 => 'Boath',
                            6 => 'Gadiguda',
                            7 => 'Gudihatnoor',
                            8 => 'Ichoda',
                            9 => 'Inderavelly',
                            10 => 'Jainath',
                            11 => 'Mavala',
                            12 => 'Narnoor',
                            13 => 'Neradigonda',
                            14 => 'Sirikonda',
                            15 => 'Talamadugu',
                            16 => 'Tamsi',
                            17 => 'Utnoor',
                        ],
                    ],
                    692 =>
                    [
                        'name' => 'Siddipet',
                        'childs' =>
                        [
                            0 => 'Akkannapet',
                            1 => 'Bejjanki',
                            2 => 'Cherial',
                            3 => 'Chinnakodur',
                            4 => 'Dhoolmitta',
                            5 => 'Doultabad',
                            6 => 'Dubbak',
                            7 => 'Gajwel',
                            8 => 'Husnabad',
                            9 => 'Jagdevpur',
                            10 => 'Koheda',
                            11 => 'Komuravelli',
                            12 => 'Kondapak',
                            13 => 'Maddur',
                            14 => 'Markook',
                            15 => 'Mirdoddi',
                            16 => 'Mulug',
                            17 => 'Nangnoor',
                            18 => 'Narayanaraopet',
                            19 => 'Raipole',
                            20 => 'Siddipet Rural',
                            21 => 'Siddipet Urban',
                            22 => 'Thoguta',
                            23 => 'Wargal',
                        ],
                    ],
                    695 =>
                    [
                        'name' => 'Jogulamba Gadwal',
                        'childs' =>
                        [
                            0 => 'Alampur',
                            1 => 'Dharoor',
                            2 => 'Gadwal',
                            3 => 'Ghattu',
                            4 => 'Ieeja',
                            5 => 'Itikyala',
                            6 => 'Kaloor Thimmandoddi',
                            7 => 'Maldakal',
                            8 => 'Manopad',
                            9 => 'Rajoli',
                            10 => 'Undavelly',
                            11 => 'Waddepally',
                        ],
                    ],
                    513 =>
                    [
                        'name' => 'Medak',
                        'childs' =>
                        [
                            0 => 'Alladurg',
                            1 => 'Chegunta',
                            2 => 'Chilpched',
                            3 => 'Havelighanapur',
                            4 => 'Kowdipally',
                            5 => 'Kulcharam',
                            6 => 'Manoharabad',
                            7 => 'Masaipet',
                            8 => 'Medak',
                            9 => 'Narsapur',
                            10 => 'Narsingi',
                            11 => 'Nizampet',
                            12 => 'Papannapet',
                            13 => 'Ramayampet',
                            14 => 'Regode',
                            15 => 'Shankarampet (A]',
                            16 => 'Shankarampet (R]',
                            17 => 'Shivampet',
                            18 => 'Tekmal',
                            19 => 'Toopran',
                            20 => 'Yeldurthy',
                        ],
                    ],
                    690 =>
                    [
                        'name' => 'Bhadradri Kothagudem',
                        'childs' =>
                        [
                            0 => 'Allapalli',
                            1 => 'Annapureddypalli',
                            2 => 'Aswapuram',
                            3 => 'Aswaraopeta',
                            4 => 'Bhadrachalam',
                            5 => 'Burgampahad',
                            6 => 'Chandrugonda',
                            7 => 'Cherla',
                            8 => 'Chunchupalli',
                            9 => 'Dammapeta',
                            10 => 'Dummugudem',
                            11 => 'Gundala',
                            12 => 'Julurpadu',
                            13 => 'Karakagudem',
                            14 => 'Kothagudem',
                            15 => 'Laxmidevipalli',
                            16 => 'Manuguru',
                            17 => 'Mulkalapally',
                            18 => 'Palvoncha',
                            19 => 'Pinapaka',
                            20 => 'Sujathanagar',
                            21 => 'Tekulapalli',
                            22 => 'Yellandu',
                        ],
                    ],
                    700 =>
                    [
                        'name' => 'Medchal Malkajgiri',
                        'childs' =>
                        [
                            0 => 'Alwal',
                            1 => 'Bachupally',
                            2 => 'Balanagar',
                            3 => 'Gandimaisamma Dundigal',
                            4 => 'Ghatkesar',
                            5 => 'Kapra',
                            6 => 'Keesara',
                            7 => 'Kukatpally',
                            8 => 'Malkajgiri',
                            9 => 'Medchal',
                            10 => 'Medipally',
                            11 => 'Muduchinthalapally',
                            12 => 'Qutballapur',
                            13 => 'Shamirpet',
                            14 => 'Uppal',
                        ],
                    ],
                    693 =>
                    [
                        'name' => 'Wanaparthy',
                        'childs' =>
                        [
                            0 => 'Amarchintha',
                            1 => 'Atmakur',
                            2 => 'Chinnambavi',
                            3 => 'Ghanpur',
                            4 => 'Gopalpeta',
                            5 => 'Kothakota',
                            6 => 'Madanapur',
                            7 => 'Pangal',
                            8 => 'Pebbair',
                            9 => 'Peddamandadi',
                            10 => 'Revally',
                            11 => 'Srirangapur',
                            12 => 'Wanaparthy',
                            13 => 'Weepangandla',
                        ],
                    ],
                    507 =>
                    [
                        'name' => 'Hyderabad',
                        'childs' =>
                        [
                            0 => 'Amberpet',
                            1 => 'Ameerpet',
                            2 => 'Asif Nagar',
                            3 => 'Bahadurpura',
                            4 => 'Bandlaguda',
                            5 => 'Charminar',
                            6 => 'Golconda',
                            7 => 'Himayatnagar',
                            8 => 'Khairatabad',
                            9 => 'Marredpally',
                            10 => 'Musheerabad',
                            11 => 'Nampally',
                            12 => 'Saidabad',
                            13 => 'Secunderabad',
                            14 => 'Shaikpet',
                            15 => 'Tirumalgiri',
                        ],
                    ],
                    691 =>
                    [
                        'name' => 'Sangareddy',
                        'childs' =>
                        [
                            0 => 'Ameenpur',
                            1 => 'Andole',
                            2 => 'Chowtakur',
                            3 => 'Gummadidala',
                            4 => 'Hathnoora',
                            5 => 'Jharasangam',
                            6 => 'Jinnaram',
                            7 => 'Kalher',
                            8 => 'Kandi',
                            9 => 'Kangti',
                            10 => 'Kohir',
                            11 => 'Kondapur',
                            12 => 'Manoor',
                            13 => 'Mogudampally',
                            14 => 'Munipally',
                            15 => 'Nagalgidda',
                            16 => 'Narayankhed',
                            17 => 'Nyalkal',
                            18 => 'Patancheru',
                            19 => 'Pulkal',
                            20 => 'Raikode',
                            21 => 'Ramchandrapuram',
                            22 => 'Sadasivpet',
                            23 => 'Sangareddy',
                            24 => 'Sirgapoor',
                            25 => 'Vatpally',
                            26 => 'Zahirabad',
                        ],
                    ],
                    696 =>
                    [
                        'name' => 'Suryapet',
                        'childs' =>
                        [
                            0 => 'Ananthagiri',
                            1 => 'Atmakur (S]',
                            2 => 'Chilkur',
                            3 => 'Chinthalapalem',
                            4 => 'Chivemla',
                            5 => 'Garidepally',
                            6 => 'Huzurnagar',
                            7 => 'Jajireddygudem',
                            8 => 'Kodad',
                            9 => 'Maddirala',
                            10 => 'Mattampally',
                            11 => 'Mellachervu',
                            12 => 'Mothey',
                            13 => 'Munagala',
                            14 => 'Nadigudem',
                            15 => 'Nagaram',
                            16 => 'Nereducherla',
                            17 => 'Nuthanakal',
                            18 => 'Palakeedu',
                            19 => 'Penpahad',
                            20 => 'Suryapet',
                            21 => 'Thirumalagiri',
                            22 => 'Thungaturthy',
                        ],
                    ],
                    682 =>
                    [
                        'name' => 'Peddapalli',
                        'childs' =>
                        [
                            0 => 'Anthergaon',
                            1 => 'Dharmaram',
                            2 => 'Eligaid',
                            3 => 'Julapalli',
                            4 => 'Kamanpur',
                            5 => 'Manthani',
                            6 => 'Mutharam (Manthani]',
                            7 => 'Odela',
                            8 => 'Palakurthy',
                            9 => 'Peddapalli',
                            10 => 'Ramagiri',
                            11 => 'Ramagundam',
                            12 => 'Srirampur',
                            13 => 'Sulthanabad',
                        ],
                    ],
                    516 =>
                    [
                        'name' => 'Nizamabad',
                        'childs' =>
                        [
                            0 => 'Armoor',
                            1 => 'Balkonda',
                            2 => 'Bheemgal',
                            3 => 'Bodhan',
                            4 => 'Chandur',
                            5 => 'Dharpally',
                            6 => 'Dichpally',
                            7 => 'Indalwai',
                            8 => 'Jakranpally',
                            9 => 'Kammarpally',
                            10 => 'Kotagiri',
                            11 => 'Makloor',
                            12 => 'Mendora',
                            13 => 'Morthad',
                            14 => 'Mosara',
                            15 => 'Mugpal',
                            16 => 'Mupkal',
                            17 => 'Nandipet',
                            18 => 'Navipet',
                            19 => 'Nizamabad North',
                            20 => 'Nizamabad Rural',
                            21 => 'Nizamabad South',
                            22 => 'Renjal',
                            23 => 'Rudrur',
                            24 => 'Sirikonda',
                            25 => 'Vailpoor',
                            26 => 'Varni',
                            27 => 'Yedapally',
                            28 => 'Yergatla',
                        ],
                    ],
                    699 =>
                    [
                        'name' => 'Kumuram Bheem Asifabad',
                        'childs' =>
                        [
                            0 => 'Asifabad',
                            1 => 'Bejjur',
                            2 => 'Chintalamanepally',
                            3 => 'Dahegoan',
                            4 => 'Jainoor',
                            5 => 'Kagaznagar',
                            6 => 'Kerameri',
                            7 => 'Koutala',
                            8 => 'Lingapur',
                            9 => 'Penchicalpet',
                            10 => 'Rebbena',
                            11 => 'Sirpur (T]',
                            12 => 'Sirpur U',
                            13 => 'Tiryani',
                            14 => 'Wankidi',
                        ],
                    ],
                    686 =>
                    [
                        'name' => 'Hanumakonda',
                        'childs' =>
                        [
                            0 => 'Athmakur',
                            1 => 'Bheemadevarpalli',
                            2 => 'Damera',
                            3 => 'Dharmasagar',
                            4 => 'Elkathurthy',
                            5 => 'Hanamkonda',
                            6 => 'Hasanparthy',
                            7 => 'Inavole',
                            8 => 'Kamalapur',
                            9 => 'Khazipet',
                            10 => 'Nadikuda',
                            11 => 'Parkal',
                            12 => 'Shayampet',
                            13 => 'Velair',
                        ],
                    ],
                    689 =>
                    [
                        'name' => 'Jangoan',
                        'childs' =>
                        [
                            0 => 'Bachannapet',
                            1 => 'Chilpur',
                            2 => 'Devaruppula',
                            3 => 'Ghanpur (Station]',
                            4 => 'Jangoan',
                            5 => 'Kodakandla',
                            6 => 'Lingalaghanpur',
                            7 => 'Narmetta',
                            8 => 'Palakurthy',
                            9 => 'Ragunathpally',
                            10 => 'Tharigoppula',
                            11 => 'Zaffergadh',
                        ],
                    ],
                    685 =>
                    [
                        'name' => 'Kamareddy',
                        'childs' =>
                        [
                            0 => 'Banswada',
                            1 => 'Bhiknoor',
                            2 => 'Bibipet',
                            3 => 'Bichkunda',
                            4 => 'Birkur',
                            5 => 'Domakonda',
                            6 => 'Gandhari',
                            7 => 'Jukkal',
                            8 => 'Kamareddy',
                            9 => 'Lingampet',
                            10 => 'Machareddy',
                            11 => 'Madnoor',
                            12 => 'Nagireddypet',
                            13 => 'Nasurullabad',
                            14 => 'Nizamsagar',
                            15 => 'Pedda Kodapgal',
                            16 => 'Pitlam',
                            17 => 'Rajampet',
                            18 => 'Ramareddy',
                            19 => 'Sadashivanagar',
                            20 => 'Tadwai',
                            21 => 'Yellareddy',
                        ],
                    ],
                    698 =>
                    [
                        'name' => 'Vikarabad',
                        'childs' =>
                        [
                            0 => 'Bantwaram',
                            1 => 'Basheerabad',
                            2 => 'Bomaraspeta',
                            3 => 'Chowdapur',
                            4 => 'Dharur',
                            5 => 'Doma',
                            6 => 'Doulathabad',
                            7 => 'Kodangal',
                            8 => 'Kotepally',
                            9 => 'Kulkacharla',
                            10 => 'Marpalle',
                            11 => 'Mominpet',
                            12 => 'Nawabpet',
                            13 => 'Pargi',
                            14 => 'Peddemul',
                            15 => 'Pudur',
                            16 => 'Tandur',
                            17 => 'Vikarabad',
                            18 => 'Yelal',
                        ],
                    ],
                    680 =>
                    [
                        'name' => 'Nirmal',
                        'childs' =>
                        [
                            0 => 'Basar',
                            1 => 'Bhainsa',
                            2 => 'Dasturabad',
                            3 => 'Dilawarpur',
                            4 => 'Kaddam Peddur',
                            5 => 'Khanapur',
                            6 => 'Kubeer',
                            7 => 'Kuntala',
                            8 => 'Laxmanchanda',
                            9 => 'Lokeswaram',
                            10 => 'Mamada',
                            11 => 'Mudhole',
                            12 => 'Narsapur G',
                            13 => 'Nirmal Rural',
                            14 => 'Nirmal U',
                            15 => 'Pembi',
                            16 => 'Sarangapur',
                            17 => 'Soan',
                            18 => 'Tanoor',
                        ],
                    ],
                    688 =>
                    [
                        'name' => 'Mahabubabad',
                        'childs' =>
                        [
                            0 => 'Bayyaram',
                            1 => 'Chinnagudur',
                            2 => 'Danthalapalle',
                            3 => 'Dornakal',
                            4 => 'Gangaram',
                            5 => 'Garla',
                            6 => 'Gudur',
                            7 => 'Kesamudram',
                            8 => 'Kothaguda',
                            9 => 'Kuravi',
                            10 => 'Mahabubabad',
                            11 => 'Maripeda',
                            12 => 'Narsimhulapet',
                            13 => 'Nellikudur',
                            14 => 'Peddavangara',
                            15 => 'Thorrur',
                        ],
                    ],
                    681 =>
                    [
                        'name' => 'Jagitial',
                        'childs' =>
                        [
                            0 => 'Beerpur',
                            1 => 'Buggaram',
                            2 => 'Dharmapuri',
                            3 => 'Gollapalli',
                            4 => 'Ibrahimpatnam',
                            5 => 'Jagitial',
                            6 => 'Jagitial Rural',
                            7 => 'Kathlapur',
                            8 => 'Kodimial',
                            9 => 'Korutla',
                            10 => 'Mallapur',
                            11 => 'Mallial',
                            12 => 'Medipalli',
                            13 => 'Metpalli',
                            14 => 'Pegadapalli',
                            15 => 'Raikal',
                            16 => 'Sarangapur',
                            17 => 'Velgatur',
                        ],
                    ],
                    684 =>
                    [
                        'name' => 'Mancherial',
                        'childs' =>
                        [
                            0 => 'Bellampally',
                            1 => 'Bheemaram',
                            2 => 'Bheemini',
                            3 => 'Chennur',
                            4 => 'Dandepally',
                            5 => 'Hajipur',
                            6 => 'Jaipur',
                            7 => 'Jannaram',
                            8 => 'Kannepally',
                            9 => 'Kasipet',
                            10 => 'Kotapally',
                            11 => 'Luxettipet',
                            12 => 'Mancherial',
                            13 => 'Mandamarri',
                            14 => 'Naspur',
                            15 => 'Nennel',
                            16 => 'Tandur',
                            17 => 'Vemanpally',
                        ],
                    ],
                    687 =>
                    [
                        'name' => 'Jayashankar Bhupalapally',
                        'childs' =>
                        [
                            0 => 'Bhupalpally',
                            1 => 'Chityal',
                            2 => 'Ghanpur (Mulug]',
                            3 => 'Kataram',
                            4 => 'Mahadevpur',
                            5 => 'Malharrao',
                            6 => 'Mogullapally',
                            7 => 'Mutharam Mahadevpur',
                            8 => 'Palimela',
                            9 => 'Regonda',
                            10 => 'Tekumatla',
                        ],
                    ],
                    683 =>
                    [
                        'name' => 'Rajanna Sircilla',
                        'childs' =>
                        [
                            0 => 'Boinpalli',
                            1 => 'Chandurthi',
                            2 => 'Gambhiraopet',
                            3 => 'Illanthakunta',
                            4 => 'Konaraopet',
                            5 => 'Mustabad',
                            6 => 'Rudrangi',
                            7 => 'Sircilla',
                            8 => 'Thangallapalli',
                            9 => 'Veernapalli',
                            10 => 'Vemulawada',
                            11 => 'Vemulawada Rural',
                            12 => 'Yellareddipet',
                        ],
                    ],
                    509 =>
                    [
                        'name' => 'Khammam',
                        'childs' =>
                        [
                            0 => 'Bonakal',
                            1 => 'Chinthakani',
                            2 => 'Enkoor',
                            3 => 'Kalluru',
                            4 => 'Kamepalli',
                            5 => 'Khammam (Rural]',
                            6 => 'Khammam Urban',
                            7 => 'Konijerla',
                            8 => 'Kusumanchi',
                            9 => 'Madhira',
                            10 => 'Mudigonda',
                            11 => 'Nelakondapalli',
                            12 => 'Penuballi',
                            13 => 'Raghunadhapalem',
                            14 => 'Sathupalli',
                            15 => 'Singareni',
                            16 => 'Thallada',
                            17 => 'Tirumalayapalem',
                            18 => 'Vemsoor',
                            19 => 'Wyra',
                            20 => 'Yerrupalem',
                        ],
                    ],
                    522 =>
                    [
                        'name' => 'Warangal',
                        'childs' =>
                        [
                            0 => 'Chennaraopet',
                            1 => 'Duggondi',
                            2 => 'Geesugonda',
                            3 => 'Khanapur',
                            4 => 'Khila Warangal',
                            5 => 'Nallabelli',
                            6 => 'Narsampet',
                            7 => 'Nekkonda',
                            8 => 'Parvathagiri',
                            9 => 'Rayaparthy',
                            10 => 'Sangem',
                            11 => 'Warangal',
                            12 => 'Wardhannapet',
                        ],
                    ],
                    508 =>
                    [
                        'name' => 'Karimnagar',
                        'childs' =>
                        [
                            0 => 'Chigurumamidi',
                            1 => 'Choppadandi',
                            2 => 'Ellandakunta',
                            3 => 'Gangadhara',
                            4 => 'Ganneruvaram',
                            5 => 'Huzurabad',
                            6 => 'Jammikunta',
                            7 => 'Karimnagar',
                            8 => 'Karimnagar Rural',
                            9 => 'Kothapally',
                            10 => 'Manakondur',
                            11 => 'Ramadugu',
                            12 => 'Shankarapatnam',
                            13 => 'Thimmapur LMD',
                            14 => 'Veenavanka',
                            15 => 'V.Saidapur',
                        ],
                    ],
                    721 =>
                    [
                        'name' => 'Narayanpet',
                        'childs' =>
                        [
                            0 => 'Damargidda',
                            1 => 'Dhanwada',
                            2 => 'Kosgi',
                            3 => 'Krishna',
                            4 => 'Maddur',
                            5 => 'Maganoor',
                            6 => 'Makthal',
                            7 => 'Marikal',
                            8 => 'Narayanpet',
                            9 => 'Narwa',
                            10 => 'Utkoor',
                        ],
                    ],
                    720 =>
                    [
                        'name' => 'Mulugu',
                        'childs' =>
                        [
                            0 => 'Eturunagaram',
                            1 => 'Govindaraopet',
                            2 => 'Kannaigudem',
                            3 => 'Mangapet',
                            4 => 'Mulug',
                            5 => 'Tadvai',
                            6 => 'Venkatapur',
                            7 => 'Venkatapuram',
                            8 => 'Wajedu',
                        ],
                    ],
                ],
            ],
            3 =>
            [
                'name' => 'Punjab',
                'childs' =>
                [
                    651 =>
                    [
                        'name' => 'Fazilka',
                        'childs' =>
                        [
                            0 => 'Abohar',
                            1 => 'Fazilka',
                            2 => 'Jalalabad',
                        ],
                    ],
                    34 =>
                    [
                        'name' => 'Jalandhar',
                        'childs' =>
                        [
                            0 => 'Adampur',
                            1 => 'Jalandhar - I',
                            2 => 'Jalandhar - II',
                            3 => 'Nakodar',
                            4 => 'Phillaur',
                            5 => 'Shahkot',
                        ],
                    ],
                    737 =>
                    [
                        'name' => 'Malerkotla',
                        'childs' =>
                        [
                            0 => 'Ahmedgarh',
                            1 => 'Amargarh',
                            2 => 'Malerkotla',
                        ],
                    ],
                    27 =>
                    [
                        'name' => 'Amritsar',
                        'childs' =>
                        [
                            0 => 'Ajnala',
                            1 => 'Amritsar -I',
                            2 => 'Amritsar- II',
                            3 => 'Baba Bakala',
                            4 => 'Lopoke',
                            5 => 'Majitha',
                        ],
                    ],
                    30 =>
                    [
                        'name' => 'Fatehgarh Sahib',
                        'childs' =>
                        [
                            0 => 'Amloh',
                            1 => 'Bassi Pathana',
                            2 => 'Fatehgarh Sahib',
                            3 => 'Khamanon',
                        ],
                    ],
                    42 =>
                    [
                        'name' => 'Rupnagar',
                        'childs' =>
                        [
                            0 => 'Anandpur Sahib',
                            1 => 'Chamkaur Sahib',
                            2 => 'Morinda',
                            3 => 'Nangal',
                            4 => 'Rup Nagar',
                        ],
                    ],
                    38 =>
                    [
                        'name' => 'Moga',
                        'childs' =>
                        [
                            0 => 'Bagha Purana',
                            1 => 'Dharamkot',
                            2 => 'Moga',
                            3 => 'Nihal Singhwala',
                        ],
                    ],
                    40 =>
                    [
                        'name' => 'Shahid Bhagat Singh Nagar',
                        'childs' =>
                        [
                            0 => 'Balachaur',
                            1 => 'Banga',
                            2 => 'Nawanshahr',
                        ],
                    ],
                    605 =>
                    [
                        'name' => 'Barnala',
                        'childs' =>
                        [
                            0 => 'Barnala',
                            1 => 'Mehal Kalan',
                            2 => 'Tapa',
                        ],
                    ],
                    32 =>
                    [
                        'name' => 'Gurdaspur',
                        'childs' =>
                        [
                            0 => 'Batala',
                            1 => 'Dera Baba Nanak',
                            2 => 'Dinanagar',
                            3 => 'Fatehgarh Churian',
                            4 => 'Gurdaspur',
                            5 => 'Kalanaur',
                        ],
                    ],
                    28 =>
                    [
                        'name' => 'Bathinda',
                        'childs' =>
                        [
                            0 => 'Bathinda',
                            1 => 'Maur',
                            2 => 'Rampura Phul',
                            3 => 'Talwandi Sabo',
                        ],
                    ],
                    43 =>
                    [
                        'name' => 'Sangrur',
                        'childs' =>
                        [
                            0 => 'Bhawanigarh',
                            1 => 'Dhuri',
                            2 => 'Dirba',
                            3 => 'Lehra',
                            4 => 'Moonak',
                            5 => 'Sangrur',
                            6 => 'Sunam',
                        ],
                    ],
                    609 =>
                    [
                        'name' => 'Tarn Taran',
                        'childs' =>
                        [
                            0 => 'Bhikhiwind',
                            1 => 'Khadur Sahib',
                            2 => 'Patti',
                            3 => 'Tarn Taran',
                        ],
                    ],
                    35 =>
                    [
                        'name' => 'Kapurthala',
                        'childs' =>
                        [
                            0 => 'Bhulath',
                            1 => 'Kapurthala',
                            2 => 'Phagwara',
                            3 => 'Sultanpur Lodhi',
                        ],
                    ],
                    37 =>
                    [
                        'name' => 'Mansa',
                        'childs' =>
                        [
                            0 => 'Budhlada',
                            1 => 'Mansa',
                            2 => 'Sardulgarh',
                        ],
                    ],
                    33 =>
                    [
                        'name' => 'Hoshiarpur',
                        'childs' =>
                        [
                            0 => 'Dasua',
                            1 => 'Garhshankar',
                            2 => 'Hoshiarpur',
                            3 => 'Mukerian',
                            4 => 'Tanda',
                        ],
                    ],
                    608 =>
                    [
                        'name' => 'S.A.S Nagar',
                        'childs' =>
                        [
                            0 => 'Dera Bassi',
                            1 => 'Kharar',
                            2 => 'Sas Nagar (Mohali]',
                        ],
                    ],
                    662 =>
                    [
                        'name' => 'Pathankot',
                        'childs' =>
                        [
                            0 => 'Dhar Kalan',
                            1 => 'Pathankot',
                        ],
                    ],
                    41 =>
                    [
                        'name' => 'Patiala',
                        'childs' =>
                        [
                            0 => 'Dudhan Sadhan',
                            1 => 'Nabha',
                            2 => 'Patiala',
                            3 => 'Patran',
                            4 => 'Rajpura',
                            5 => 'Samana',
                        ],
                    ],
                    29 =>
                    [
                        'name' => 'Faridkot',
                        'childs' =>
                        [
                            0 => 'Faridkot',
                            1 => 'Jaitu',
                            2 => 'Kotkapura',
                        ],
                    ],
                    31 =>
                    [
                        'name' => 'Ferozepur',
                        'childs' =>
                        [
                            0 => 'Firozpur',
                            1 => 'Guruharsahai',
                            2 => 'Zira',
                        ],
                    ],
                    39 =>
                    [
                        'name' => 'Sri Muktsar Sahib',
                        'childs' =>
                        [
                            0 => 'Gidderbaha',
                            1 => 'Malout',
                            2 => 'Sri Muktsar Sahib',
                        ],
                    ],
                    36 =>
                    [
                        'name' => 'Ludhiana',
                        'childs' =>
                        [
                            0 => 'Jagraon',
                            1 => 'Khanna',
                            2 => 'Ludhiana (East]',
                            3 => 'Ludhiana (West]',
                            4 => 'Payal',
                            5 => 'Raikot',
                            6 => 'Samrala',
                        ],
                    ],
                ],
            ],
            13 =>
            [
                'name' => 'Nagaland',
                'childs' =>
                [
                    247 =>
                    [
                        'name' => 'Mon',
                        'childs' =>
                        [
                            0 => 'Aboi',
                            1 => 'Angjangyang',
                            2 => 'Chen',
                            3 => 'Hunta',
                            4 => 'Longshen',
                            5 => 'Mon Sadar',
                            6 => 'Monyakshu',
                            7 => 'Mopong',
                            8 => 'Naginimora',
                            9 => 'Phomching',
                            10 => 'Shangnyu',
                            11 => 'Tizit',
                            12 => 'Tobu',
                            13 => 'Wakching',
                        ],
                    ],
                    251 =>
                    [
                        'name' => 'Zunheboto',
                        'childs' =>
                        [
                            0 => 'Aghunato',
                            1 => 'Akuhaito',
                            2 => 'Akuluto',
                            3 => 'Asuto',
                            4 => 'Atoizu',
                            5 => 'Ghathashi',
                            6 => 'Pughoboto',
                            7 => 'Saptiqa',
                            8 => 'Satakha',
                            9 => 'Satoi',
                            10 => 'Suruhuto',
                            11 => 'Tokiye',
                            12 => 'V.K.',
                            13 => 'Zunheboto Sadar',
                        ],
                    ],
                    250 =>
                    [
                        'name' => 'Wokha',
                        'childs' =>
                        [
                            0 => 'Aitepyong',
                            1 => 'Baghty',
                            2 => 'Bhandari',
                            3 => 'Changpang',
                            4 => 'Chukitong',
                            5 => 'Englan',
                            6 => 'Lotsu',
                            7 => 'Ralan',
                            8 => 'Sanis',
                            9 => 'Sungro',
                            10 => 'Wokha Sadar',
                            11 => 'Wozhuro',
                        ],
                    ],
                    246 =>
                    [
                        'name' => 'Mokokchung',
                        'childs' =>
                        [
                            0 => 'Alongkima',
                            1 => 'Changtongya',
                            2 => 'Chuchuyimlang',
                            3 => 'Kubolong',
                            4 => 'Longchem',
                            5 => 'Mangkolemba',
                            6 => 'Merangmen',
                            7 => 'Ongpangkong',
                            8 => 'Ongpangkong (N]',
                            9 => 'Tsurangkong',
                            10 => 'Tuli',
                        ],
                    ],
                    614 =>
                    [
                        'name' => 'Kiphire',
                        'childs' =>
                        [
                            0 => 'Amahator',
                            1 => 'Khongsa',
                            2 => 'Kiphire Sadar',
                            3 => 'Kiusam',
                            4 => 'Longmatra',
                            5 => 'Pungro',
                            6 => 'Seyochung',
                            7 => 'Sitimi',
                        ],
                    ],
                    244 =>
                    [
                        'name' => 'Dimapur',
                        'childs' =>
                        [
                            0 => 'Aquqhnaqua',
                            1 => 'Dimapur Sadar',
                            2 => 'Kuhoboto',
                            3 => 'Nihokhu',
                        ],
                    ],
                    613 =>
                    [
                        'name' => 'Peren',
                        'childs' =>
                        [
                            0 => 'Athibung',
                            1 => 'Jalukie',
                            2 => 'Kebai Khelma',
                            3 => 'Nsong',
                            4 => 'Pedi (Ngwalwa]',
                            5 => 'Peren',
                            6 => 'Tening',
                        ],
                    ],
                    245 =>
                    [
                        'name' => 'Kohima',
                        'childs' =>
                        [
                            0 => 'Botsa',
                            1 => 'Chiephobozou',
                            2 => 'Jakhama',
                            3 => 'Kezocha',
                            4 => 'Kohima Sadar',
                            5 => 'Sechu-Zubza',
                        ],
                    ],
                    249 =>
                    [
                        'name' => 'Tuensang',
                        'childs' =>
                        [
                            0 => 'Chare',
                            1 => 'Chingmei',
                            2 => 'Longkhim',
                            3 => 'Ngoungchung',
                            4 => 'Noksen',
                            5 => 'Sangsangnyu',
                            6 => 'Tuensang Sadar',
                        ],
                    ],
                    765 =>
                    [
                        'name' => 'Shamator',
                        'childs' =>
                        [
                            0 => 'Chessore',
                            1 => 'Mangko',
                            2 => 'Shamator',
                            3 => 'Sotokur',
                            4 => 'Tsurungto',
                        ],
                    ],
                    248 =>
                    [
                        'name' => 'Phek',
                        'childs' =>
                        [
                            0 => 'Chetheba',
                            1 => 'Chizami',
                            2 => 'Chozuba',
                            3 => 'Khezhakeno',
                            4 => 'Khuza',
                            5 => 'Kikruma',
                            6 => 'Meluri',
                            7 => 'Pfutsero',
                            8 => 'Phek Sadar',
                            9 => 'Phokhungri',
                            10 => 'Phor',
                            11 => 'Razieba',
                            12 => 'Sakraba',
                            13 => 'Sekruzu',
                            14 => 'Weziho',
                            15 => 'Zuketsa',
                        ],
                    ],
                    758 =>
                    [
                        'name' => 'Chumoukedima',
                        'childs' =>
                        [
                            0 => 'Chumukedima',
                            1 => 'Dhansiripar',
                            2 => 'Medziphema',
                        ],
                    ],
                    757 =>
                    [
                        'name' => 'Tseminyu',
                        'childs' =>
                        [
                            0 => 'Chunlikha',
                            1 => 'Tseminyu',
                            2 => 'Tsogin',
                        ],
                    ],
                    615 =>
                    [
                        'name' => 'Longleng',
                        'childs' =>
                        [
                            0 => 'Longleng',
                            1 => 'Namsang',
                            2 => 'Sakshi',
                            3 => 'Tamlu',
                            4 => 'Yongnyah',
                        ],
                    ],
                    764 =>
                    [
                        'name' => 'Niuland',
                        'childs' =>
                        [
                            0 => 'Niuland',
                        ],
                    ],
                    736 =>
                    [
                        'name' => 'Noklak',
                        'childs' =>
                        [
                            0 => 'Nokhu',
                            1 => 'Noklak',
                            2 => 'Panso',
                            3 => 'Thonoknyu',
                        ],
                    ],
                ],
            ],
            27 =>
            [
                'name' => 'Maharashtra',
                'childs' =>
                [
                    468 =>
                    [
                        'name' => 'Amravati',
                        'childs' =>
                        [
                            0 => 'Achalpur',
                            1 => 'Amravati',
                            2 => 'Anjangaon Surji',
                            3 => 'Bhatkuli',
                            4 => 'Chandurbazar',
                            5 => 'Chandur Railway',
                            6 => 'Chikhaldara',
                            7 => 'Daryapur',
                            8 => 'Dhamangaon Railway',
                            9 => 'Dharni',
                            10 => 'Morshi',
                            11 => 'Nandgaon-Khandeshwar',
                            12 => 'Tiosa',
                            13 => 'Warud',
                        ],
                    ],
                    475 =>
                    [
                        'name' => 'Gadchiroli',
                        'childs' =>
                        [
                            0 => 'Aheri',
                            1 => 'Armori',
                            2 => 'Bhamragad',
                            3 => 'Chamorshi',
                            4 => 'Desaiganj (Vadasa]',
                            5 => 'Dhanora',
                            6 => 'Etapalli',
                            7 => 'Gadchiroli',
                            8 => 'Korchi',
                            9 => 'Kurkheda',
                            10 => 'Mulchera',
                            11 => 'Sironcha',
                        ],
                    ],
                    481 =>
                    [
                        'name' => 'Latur',
                        'childs' =>
                        [
                            0 => 'Ahmadpur',
                            1 => 'Ausa',
                            2 => 'Chakur',
                            3 => 'Deoni',
                            4 => 'Jalkot',
                            5 => 'Latur',
                            6 => 'Nilanga',
                            7 => 'Renapur',
                            8 => 'Shirur Anantpal',
                            9 => 'Udgir',
                        ],
                    ],
                    480 =>
                    [
                        'name' => 'Kolhapur',
                        'childs' =>
                        [
                            0 => 'Ajra',
                            1 => 'Bhudargad',
                            2 => 'Chandgad',
                            3 => 'Gadhinglaj',
                            4 => 'Gaganbawada',
                            5 => 'Hatkanangle',
                            6 => 'Kagal',
                            7 => 'Karvir',
                            8 => 'Panhala',
                            9 => 'Radhanagari',
                            10 => 'Shahuwadi',
                            11 => 'Shirol',
                        ],
                    ],
                    496 =>
                    [
                        'name' => 'Solapur',
                        'childs' =>
                        [
                            0 => 'Akkalkot',
                            1 => 'Barshi',
                            2 => 'Karmala',
                            3 => 'Madha',
                            4 => 'Malshiras',
                            5 => 'Mangalvedhe',
                            6 => 'Mohol',
                            7 => 'Pandharpur',
                            8 => 'Sangole',
                            9 => 'Solapur North',
                            10 => 'Solapur South',
                        ],
                    ],
                    486 =>
                    [
                        'name' => 'Nandurbar',
                        'childs' =>
                        [
                            0 => 'Akkalkuwa',
                            1 => 'Akrani',
                            2 => 'Nandurbar',
                            3 => 'Nawapur',
                            4 => 'Shahade',
                            5 => 'Talode',
                        ],
                    ],
                    467 =>
                    [
                        'name' => 'Akola',
                        'childs' =>
                        [
                            0 => 'Akola',
                            1 => 'Akot',
                            2 => 'Balapur',
                            3 => 'Barshitakli',
                            4 => 'Murtijapur',
                            5 => 'Patur',
                            6 => 'Telhara',
                        ],
                    ],
                    466 =>
                    [
                        'name' => 'Ahilyanagar',
                        'childs' =>
                        [
                            0 => 'Akole',
                            1 => 'Jamkhed',
                            2 => 'Karjat',
                            3 => 'Kopargaon',
                            4 => 'Nagar',
                            5 => 'Nevasa',
                            6 => 'Parner',
                            7 => 'Pathardi',
                            8 => 'Rahta',
                            9 => 'Rahuri',
                            10 => 'Sangamner',
                            11 => 'Shevgaon',
                            12 => 'Shrigonda',
                            13 => 'Shrirampur',
                        ],
                    ],
                    491 =>
                    [
                        'name' => 'Raigad',
                        'childs' =>
                        [
                            0 => 'Alibag',
                            1 => 'Karjat',
                            2 => 'Khalapur',
                            3 => 'Mahad',
                            4 => 'Mangaon',
                            5 => 'Mhasla',
                            6 => 'Murud',
                            7 => 'Panvel',
                            8 => 'Pen',
                            9 => 'Poladpur',
                            10 => 'Roha',
                            11 => 'Shrivardhan',
                            12 => 'Sudhagad',
                            13 => 'Tala',
                            14 => 'Uran',
                        ],
                    ],
                    478 =>
                    [
                        'name' => 'Jalgaon',
                        'childs' =>
                        [
                            0 => 'Amalner',
                            1 => 'Bhadgaon',
                            2 => 'Bhusawal',
                            3 => 'Bodvad',
                            4 => 'Chalisgaon',
                            5 => 'Chopda',
                            6 => 'Dharangaon',
                            7 => 'Erandol',
                            8 => 'Jalgaon',
                            9 => 'Jamner',
                            10 => 'Muktainagar (Edlabad]',
                            11 => 'Pachora',
                            12 => 'Parola',
                            13 => 'Raver',
                            14 => 'Yawal',
                        ],
                    ],
                    479 =>
                    [
                        'name' => 'Jalna',
                        'childs' =>
                        [
                            0 => 'Ambad',
                            1 => 'Badnapur',
                            2 => 'Bhokardan',
                            3 => 'Ghansawangi',
                            4 => 'Jafrabad',
                            5 => 'Jalna',
                            6 => 'Mantha',
                            7 => 'Partur',
                        ],
                    ],
                    497 =>
                    [
                        'name' => 'Thane',
                        'childs' =>
                        [
                            0 => 'Ambarnath',
                            1 => 'Bhiwandi',
                            2 => 'Kalyan',
                            3 => 'Murbad',
                            4 => 'Shahapur',
                            5 => 'Thane',
                            6 => 'Ulhasnagar',
                        ],
                    ],
                    490 =>
                    [
                        'name' => 'Pune',
                        'childs' =>
                        [
                            0 => 'Ambegaon',
                            1 => 'Baramati',
                            2 => 'Bhor',
                            3 => 'Daund',
                            4 => 'Haveli',
                            5 => 'Indapur',
                            6 => 'Junnar',
                            7 => 'Khed',
                            8 => 'Mawal',
                            9 => 'Mulshi',
                            10 => 'Pune City',
                            11 => 'Purandhar',
                            12 => 'Shirur',
                            13 => 'Velhe',
                        ],
                    ],
                    470 =>
                    [
                        'name' => 'Beed',
                        'childs' =>
                        [
                            0 => 'Ambejogai',
                            1 => 'Ashti',
                            2 => 'Beed',
                            3 => 'Dharur',
                            4 => 'Georai',
                            5 => 'Kaij',
                            6 => 'Majalgaon',
                            7 => 'Parli',
                            8 => 'Patoda',
                            9 => 'Shirur (Kasar]',
                            10 => 'Wadwani',
                        ],
                    ],
                    476 =>
                    [
                        'name' => 'Gondia',
                        'childs' =>
                        [
                            0 => 'Amgaon',
                            1 => 'Arjuni Morgaon',
                            2 => 'Deori',
                            3 => 'Gondiya',
                            4 => 'Goregaon',
                            5 => 'Sadak-Arjuni',
                            6 => 'Salekasa',
                            7 => 'Tirora',
                        ],
                    ],
                    483 =>
                    [
                        'name' => 'Mumbai Suburban',
                        'childs' =>
                        [
                            0 => 'Andheri',
                            1 => 'Borivali',
                            2 => 'Kurla',
                        ],
                    ],
                    485 =>
                    [
                        'name' => 'Nanded',
                        'childs' =>
                        [
                            0 => 'Ardhapur',
                            1 => 'Bhokar',
                            2 => 'Biloli',
                            3 => 'Deglur',
                            4 => 'Dharmabad',
                            5 => 'Hadgaon',
                            6 => 'Himayatnagar',
                            7 => 'Kandhar',
                            8 => 'Kinwat',
                            9 => 'Loha',
                            10 => 'Mahur',
                            11 => 'Mudkhed',
                            12 => 'Mukhed',
                            13 => 'Naigaon (Khairgaon]',
                            14 => 'Nanded',
                            15 => 'Umri',
                        ],
                    ],
                    500 =>
                    [
                        'name' => 'Yavatmal',
                        'childs' =>
                        [
                            0 => 'Arni',
                            1 => 'Babulgaon',
                            2 => 'Darwha',
                            3 => 'Digras',
                            4 => 'Ghatanji',
                            5 => 'Kalamb',
                            6 => 'Kelapur',
                            7 => 'Mahagaon',
                            8 => 'Maregaon',
                            9 => 'Ner',
                            10 => 'Pusad',
                            11 => 'Ralegaon',
                            12 => 'Umarkhed',
                            13 => 'Wani',
                            14 => 'Yavatmal',
                            15 => 'Zari-Jamani',
                        ],
                    ],
                    498 =>
                    [
                        'name' => 'Wardha',
                        'childs' =>
                        [
                            0 => 'Arvi',
                            1 => 'Ashti',
                            2 => 'Deoli',
                            3 => 'Hinganghat',
                            4 => 'Karanja',
                            5 => 'Samudrapur',
                            6 => 'Seloo',
                            7 => 'Wardha',
                        ],
                    ],
                    493 =>
                    [
                        'name' => 'Sangli',
                        'childs' =>
                        [
                            0 => 'Atpadi',
                            1 => 'Jat',
                            2 => 'Kadegaon',
                            3 => 'Kavathemahankal',
                            4 => 'Khanapur',
                            5 => 'Miraj',
                            6 => 'Palus',
                            7 => 'Shirala',
                            8 => 'Tasgaon',
                            9 => 'Walwa',
                        ],
                    ],
                    477 =>
                    [
                        'name' => 'Hingoli',
                        'childs' =>
                        [
                            0 => 'Aundha (Nagnath]',
                            1 => 'Hingoli',
                            2 => 'Kalamnuri',
                            3 => 'Sengaon',
                            4 => 'Vasmath',
                        ],
                    ],
                    487 =>
                    [
                        'name' => 'Nashik',
                        'childs' =>
                        [
                            0 => 'Baglan',
                            1 => 'Chandvad',
                            2 => 'Deola',
                            3 => 'Dindori',
                            4 => 'Igatpuri',
                            5 => 'Kalwan',
                            6 => 'Malegaon',
                            7 => 'Nandgaon',
                            8 => 'Nashik',
                            9 => 'Niphad',
                            10 => 'Peth',
                            11 => 'Sinnar',
                            12 => 'Surgana',
                            13 => 'Trimbakeshwar',
                            14 => 'Yevla',
                        ],
                    ],
                    473 =>
                    [
                        'name' => 'Chandrapur',
                        'childs' =>
                        [
                            0 => 'Ballarpur',
                            1 => 'Bhadravati',
                            2 => 'Brahmapuri',
                            3 => 'Chandrapur',
                            4 => 'Chimur',
                            5 => 'Gondpipri',
                            6 => 'Jiwati',
                            7 => 'Korpana',
                            8 => 'Mul',
                            9 => 'Nagbhid',
                            10 => 'Pombhurna',
                            11 => 'Rajura',
                            12 => 'Sawali',
                            13 => 'Sindewahi',
                            14 => 'Warora',
                        ],
                    ],
                    471 =>
                    [
                        'name' => 'Bhandara',
                        'childs' =>
                        [
                            0 => 'Bhandara',
                            1 => 'Lakhandur',
                            2 => 'Lakhani',
                            3 => 'Mohadi',
                            4 => 'Pauni',
                            5 => 'Sakoli',
                            6 => 'Tumsar',
                        ],
                    ],
                    484 =>
                    [
                        'name' => 'Nagpur',
                        'childs' =>
                        [
                            0 => 'Bhiwapur',
                            1 => 'Hingna',
                            2 => 'Kalameshwar',
                            3 => 'Kamptee',
                            4 => 'Katol',
                            5 => 'Kuhi',
                            6 => 'Mauda',
                            7 => 'Nagpur (Rural]',
                            8 => 'Nagpur (Urban]',
                            9 => 'Narkhed',
                            10 => 'Parseoni',
                            11 => 'Ramtek',
                            12 => 'Savner',
                            13 => 'Umred',
                        ],
                    ],
                    488 =>
                    [
                        'name' => 'Dharashiv',
                        'childs' =>
                        [
                            0 => 'Bhum',
                            1 => 'Dharashiv',
                            2 => 'Kalamb',
                            3 => 'Lohara',
                            4 => 'Omarga',
                            5 => 'Paranda',
                            6 => 'Tuljapur',
                            7 => 'Washi',
                        ],
                    ],
                    472 =>
                    [
                        'name' => 'Buldhana',
                        'childs' =>
                        [
                            0 => 'Buldana',
                            1 => 'Chikhli',
                            2 => 'Deolgaon Raja',
                            3 => 'Jalgaon (Jamod]',
                            4 => 'Khamgaon',
                            5 => 'Lonar',
                            6 => 'Malkapur',
                            7 => 'Mehkar',
                            8 => 'Motala',
                            9 => 'Nandura',
                            10 => 'Sangrampur',
                            11 => 'Shegaon',
                            12 => 'Sindkhed Raja',
                        ],
                    ],
                    469 =>
                    [
                        'name' => 'Chhatrapati Sambhajinagar',
                        'childs' =>
                        [
                            0 => 'Chhatrapati Sambhajinagar',
                            1 => 'Gangapur',
                            2 => 'Kannad',
                            3 => 'Khuldabad',
                            4 => 'Paithan',
                            5 => 'Phulambri',
                            6 => 'Sillod',
                            7 => 'Soegaon',
                            8 => 'Vaijapur',
                        ],
                    ],
                    492 =>
                    [
                        'name' => 'Ratnagiri',
                        'childs' =>
                        [
                            0 => 'Chiplun',
                            1 => 'Dapoli',
                            2 => 'Guhagar',
                            3 => 'Khed',
                            4 => 'Lanja',
                            5 => 'Mandangad',
                            6 => 'Rajapur',
                            7 => 'Ratnagiri',
                            8 => 'Sangameshwar',
                        ],
                    ],
                    665 =>
                    [
                        'name' => 'Palghar',
                        'childs' =>
                        [
                            0 => 'Dahanu',
                            1 => 'Jawhar',
                            2 => 'Mokhada',
                            3 => 'Palghar',
                            4 => 'Talasari',
                            5 => 'Vasai',
                            6 => 'Vikramgad',
                            7 => 'Wada',
                        ],
                    ],
                    495 =>
                    [
                        'name' => 'Sindhudurg',
                        'childs' =>
                        [
                            0 => 'Devgad',
                            1 => 'Dodamarg',
                            2 => 'Kankavli',
                            3 => 'Kudal',
                            4 => 'Malwan',
                            5 => 'Sawantwadi',
                            6 => 'Vaibhavvadi',
                            7 => 'Vengurla',
                        ],
                    ],
                    474 =>
                    [
                        'name' => 'Dhule',
                        'childs' =>
                        [
                            0 => 'Dhule',
                            1 => 'Sakri',
                            2 => 'Shirpur',
                            3 => 'Sindkhede',
                        ],
                    ],
                    489 =>
                    [
                        'name' => 'Parbhani',
                        'childs' =>
                        [
                            0 => 'Gangakhed',
                            1 => 'Jintur',
                            2 => 'Manwath',
                            3 => 'Palam',
                            4 => 'Parbhani',
                            5 => 'Pathri',
                            6 => 'Purna',
                            7 => 'Selu',
                            8 => 'Sonpeth',
                        ],
                    ],
                    494 =>
                    [
                        'name' => 'Satara',
                        'childs' =>
                        [
                            0 => 'Jaoli',
                            1 => 'Karad',
                            2 => 'Khandala',
                            3 => 'Khatav',
                            4 => 'Koregaon',
                            5 => 'Mahabaleshwar',
                            6 => 'Man',
                            7 => 'Patan',
                            8 => 'Phaltan',
                            9 => 'Satara',
                            10 => 'Wai',
                        ],
                    ],
                    499 =>
                    [
                        'name' => 'Washim',
                        'childs' =>
                        [
                            0 => 'Karanja',
                            1 => 'Malegaon',
                            2 => 'Mangrulpir',
                            3 => 'Manora',
                            4 => 'Risod',
                            5 => 'Washim',
                        ],
                    ],
                    482 =>
                    [
                        'name' => 'Mumbai',
                        'childs' =>
                        [],
                    ],
                ],
            ],
            28 =>
            [
                'name' => 'Andhra Pradesh',
                'childs' =>
                [
                    523 =>
                    [
                        'name' => 'West Godavari',
                        'childs' =>
                        [
                            0 => 'Achanta',
                            1 => 'Akividu',
                            2 => 'Attili',
                            3 => 'Bhimavaram',
                            4 => 'Ganapavaram',
                            5 => 'Iragavaram',
                            6 => 'Kalla',
                            7 => 'Mogalthur',
                            8 => 'Narasapuram',
                            9 => 'Palacoderu',
                            10 => 'Palacole',
                            11 => 'Pentapadu',
                            12 => 'Penugonda',
                            13 => 'Penumantra',
                            14 => 'Poduru',
                            15 => 'Tadepalligudem',
                            16 => 'Tanuku',
                            17 => 'Undi',
                            18 => 'Veeravasaram',
                            19 => 'Yelamanchili',
                        ],
                    ],
                    750 =>
                    [
                        'name' => 'Bapatla',
                        'childs' =>
                        [
                            0 => 'Addanki',
                            1 => 'Amarthalur',
                            2 => 'Ballikurava',
                            3 => 'Bapatla',
                            4 => 'Bhattiprolu',
                            5 => 'Cherukupalli',
                            6 => 'Chinaganjam',
                            7 => 'Chirala',
                            8 => 'Inkollu',
                            9 => 'J.Pangulur',
                            10 => 'Karamchedu',
                            11 => 'Karlapalem',
                            12 => 'Kolluru',
                            13 => 'Korisapadu',
                            14 => 'Martur',
                            15 => 'Nagaram',
                            16 => 'Nizampatnam',
                            17 => 'Parchur',
                            18 => 'Pittalavanipalem',
                            19 => 'Repalle',
                            20 => 'Santhamaguluru',
                            21 => 'Tsunduru',
                            22 => 'Vemuru',
                            23 => 'Vetapalem',
                            24 => 'Yeddana Pudi',
                        ],
                    ],
                    745 =>
                    [
                        'name' => 'Alluri Sitharama Raju',
                        'childs' =>
                        [
                            0 => 'Addateegala',
                            1 => 'Ananthagiri',
                            2 => 'Araku Valley',
                            3 => 'Chinthapalli',
                            4 => 'Chinturu',
                            5 => 'Devipatnam',
                            6 => 'Dumbriguda',
                            7 => 'Gangavaram',
                            8 => 'G.Madugula',
                            9 => 'Gudem Kotha Veedhi',
                            10 => 'Hukumpeta',
                            11 => 'Koyyuru',
                            12 => 'Kunavaram',
                            13 => 'Maredumilli',
                            14 => 'Munchingi Puttu',
                            15 => 'Paderu',
                            16 => 'Peda Bayalu',
                            17 => 'Rajavommangi',
                            18 => 'Rampachodavaram',
                            19 => 'Vararamachandrapuram',
                            20 => 'Yetapaka',
                            21 => 'Y. Ramavaram',
                        ],
                    ],
                    511 =>
                    [
                        'name' => 'Kurnool',
                        'childs' =>
                        [
                            0 => 'Adoni',
                            1 => 'Alur',
                            2 => 'Aspari',
                            3 => 'C.Belagal',
                            4 => 'Chippagiri',
                            5 => 'Devanakonda',
                            6 => 'Gonegandla',
                            7 => 'Gudur',
                            8 => 'Halaharvi',
                            9 => 'Holagunda',
                            10 => 'Kallur',
                            11 => 'Kodumur',
                            12 => 'Kosigi',
                            13 => 'Kowthalam',
                            14 => 'Krishnagiri',
                            15 => 'Kurnool Rural',
                            16 => 'Kurnool Urban',
                            17 => 'Maddikera (East]',
                            18 => 'Mantralayam',
                            19 => 'Nandavaram',
                            20 => 'Orvakal',
                            21 => 'Pattikonda',
                            22 => 'Pedda Kadubur',
                            23 => 'Tuggali',
                            24 => 'Veldurthy',
                            25 => 'Yemmiganur',
                        ],
                    ],
                    754 =>
                    [
                        'name' => 'Sri Sathya Sai',
                        'childs' =>
                        [
                            0 => 'Agali',
                            1 => 'Amadagur',
                            2 => 'Amarapuram',
                            3 => 'Bathalapalli',
                            4 => 'Bukkapatnam',
                            5 => 'Chennekothapalli',
                            6 => 'Chilamathur',
                            7 => 'Dharmavaram',
                            8 => 'Gandlapenta',
                            9 => 'Gorantla',
                            10 => 'Gudibanda',
                            11 => 'Hindupur',
                            12 => 'Kadiri',
                            13 => 'Kanaganipalli',
                            14 => 'Kothacheruvu',
                            15 => 'Lepakshi',
                            16 => 'Madakasira',
                            17 => 'Mudigubba',
                            18 => 'Nallachervu',
                            19 => 'Nallamada',
                            20 => 'Nambulapulakunta',
                            21 => 'Obuladevaracheruvu',
                            22 => 'Parigi',
                            23 => 'Penukonda',
                            24 => 'Puttaparthy',
                            25 => 'Ramagiri',
                            26 => 'Roddam',
                            27 => 'Rolla',
                            28 => 'Somandepalli',
                            29 => 'Tadimarri',
                            30 => 'Talupula',
                            31 => 'Tanakal',
                        ],
                    ],
                    748 =>
                    [
                        'name' => 'Eluru',
                        'childs' =>
                        [
                            0 => 'Agiripalli',
                            1 => 'Bheemadole',
                            2 => 'Buttayagudem',
                            3 => 'Chatrai',
                            4 => 'Chintalapudi',
                            5 => 'Denduluru',
                            6 => 'Dwarakatirumala',
                            7 => 'Eluru Rural',
                            8 => 'Eluru Urban',
                            9 => 'Jangareddigudem',
                            10 => 'Jeelugu Milli',
                            11 => 'Kaikaluru',
                            12 => 'Kalidindi',
                            13 => 'Kamavarapukota',
                            14 => 'Koyyalagudem',
                            15 => 'Kukunoor',
                            16 => 'Lingapalem',
                            17 => 'Mandavalli',
                            18 => 'Mudinepalle',
                            19 => 'Musunuru',
                            20 => 'Nidamarru',
                            21 => 'Nuzividu',
                            22 => 'Pedapadu',
                            23 => 'Pedavegi',
                            24 => 'Polavaram',
                            25 => 'T.Narasapuram',
                            26 => 'Unguturu',
                            27 => 'Velairpadu',
                        ],
                    ],
                    747 =>
                    [
                        'name' => 'Dr. B.R. Ambedkar Konaseema',
                        'childs' =>
                        [
                            0 => 'Ainavilli',
                            1 => 'Alamuru',
                            2 => 'Allavaram',
                            3 => 'Amalapuram',
                            4 => 'Ambajipeta',
                            5 => 'Atreyapuram',
                            6 => 'I. Polavaram',
                            7 => 'Kapileswarapuram',
                            8 => 'Katrenikona',
                            9 => 'K Gangavaram',
                            10 => 'Kothapeta',
                            11 => 'Malikipuram',
                            12 => 'Mamidikuduru',
                            13 => 'Mandapeta',
                            14 => 'Mummidivaram',
                            15 => 'P.Gannavaram',
                            16 => 'Ramachandrapuram',
                            17 => 'Ravulapalem',
                            18 => 'Rayavaram',
                            19 => 'Razole',
                            20 => 'Sakhinetipalli',
                            21 => 'Uppalaguptam',
                        ],
                    ],
                    755 =>
                    [
                        'name' => 'Nandyal',
                        'childs' =>
                        [
                            0 => 'Allagadda',
                            1 => 'Atmakur',
                            2 => 'Banaganapalli',
                            3 => 'Bandi Atmakur',
                            4 => 'Bethamcherla',
                            5 => 'Chagalamarri',
                            6 => 'Dhone',
                            7 => 'Dornipadu',
                            8 => 'Gadivemula',
                            9 => 'Gospadu',
                            10 => 'Jupadu Bunglow',
                            11 => 'Koilakuntla',
                            12 => 'Kolimigundla',
                            13 => 'Kothapalle',
                            14 => 'Mahanandi',
                            15 => 'Midthur',
                            16 => 'Nandikotkur',
                            17 => 'Nandyal Rural',
                            18 => 'Nandyal Urban',
                            19 => 'Owk',
                            20 => 'Pagidyala',
                            21 => 'Pamulapadu',
                            22 => 'Panyam',
                            23 => 'Peapully',
                            24 => 'Rudravaram',
                            25 => 'Sanjamala',
                            26 => 'Sirvel',
                            27 => 'Srisailam',
                            28 => 'Uyyalawada',
                            29 => 'Velugodu',
                        ],
                    ],
                    515 =>
                    [
                        'name' => 'Sri Potti Sriramulu Nellore',
                        'childs' =>
                        [
                            0 => 'Allur',
                            1 => 'Ananthasagaram',
                            2 => 'Anumasamudrampeta',
                            3 => 'Atmakur',
                            4 => 'Bogole',
                            5 => 'Buchireddipalem',
                            6 => 'Chejerla',
                            7 => 'Dagadarthi',
                            8 => 'Duttalur',
                            9 => 'Gudluru',
                            10 => 'Indukurpet',
                            11 => 'Jaladanki',
                            12 => 'Kaligiri',
                            13 => 'Kaluvoya',
                            14 => 'Kandukuru',
                            15 => 'Kavali',
                            16 => 'Kodavalur',
                            17 => 'Kondapuram',
                            18 => 'Kovur',
                            19 => 'Lingasamudram',
                            20 => 'Manubolu',
                            21 => 'Marripadu',
                            22 => 'Muthukur',
                            23 => 'Nellore Rural',
                            24 => 'Nellore Urban',
                            25 => 'Podalakur',
                            26 => 'Rapur',
                            27 => 'Sangam',
                            28 => 'Seetharamapuram',
                            29 => 'Sydapuram',
                            30 => 'Thotapalligudur',
                            31 => 'Udayagiri',
                            32 => 'Ulavapadu',
                            33 => 'Varikuntapadu',
                            34 => 'Venkatachalam',
                            35 => 'Vidavalur',
                            36 => 'Vinjamur',
                            37 => 'Voletivaripalem',
                        ],
                    ],
                    519 =>
                    [
                        'name' => 'Srikakulam',
                        'childs' =>
                        [
                            0 => 'Amadalavalasa',
                            1 => 'Burja',
                            2 => 'Etcherla',
                            3 => 'Ganguvarisigadam',
                            4 => 'Gara',
                            5 => 'Hiramandalam',
                            6 => 'Ichchapuram',
                            7 => 'Jalumuru',
                            8 => 'Kanchili',
                            9 => 'Kaviti',
                            10 => 'Kotabommali',
                            11 => 'Kothuru',
                            12 => 'Laveru',
                            13 => 'Laxminarasupeta',
                            14 => 'Mandasa',
                            15 => 'Meliaputti',
                            16 => 'Nandigam',
                            17 => 'Narasannapeta',
                            18 => 'Palasa',
                            19 => 'Pathapatnam',
                            20 => 'Polaki',
                            21 => 'Ponduru',
                            22 => 'Ranastalam',
                            23 => 'Santhabommali',
                            24 => 'Saravakota',
                            25 => 'Sarubujjili',
                            26 => 'Sompeta',
                            27 => 'Srikakulam',
                            28 => 'Tekkali',
                            29 => 'Vajrapukotturu',
                        ],
                    ],
                    751 =>
                    [
                        'name' => 'Palnadu',
                        'childs' =>
                        [
                            0 => 'Amaravathi',
                            1 => 'Atchampet',
                            2 => 'Bellamkonda',
                            3 => 'Bollapalli',
                            4 => 'Chilakaluripet H/O.Purushotha Patnam',
                            5 => 'Dachepalli',
                            6 => 'Durgi',
                            7 => 'Edlapadu',
                            8 => 'Gurajala',
                            9 => 'Ipuru',
                            10 => 'Karempudi',
                            11 => 'Krosuru',
                            12 => 'Machavaram',
                            13 => 'Macherla',
                            14 => 'Muppalla',
                            15 => 'Nadendla',
                            16 => 'Narasaraopet',
                            17 => 'Nekarikallu',
                            18 => 'Nuzendla',
                            19 => 'Pedakurapadu',
                            20 => 'Piduguralla',
                            21 => 'Rajupalem',
                            22 => 'Rentachintala',
                            23 => 'Rompicharla',
                            24 => 'Sattenapalli',
                            25 => 'Savalyapuram H/O Kanamarlapudi',
                            26 => 'Veldurthy',
                            27 => 'Vinukonda',
                        ],
                    ],
                    744 =>
                    [
                        'name' => 'Anakapalli',
                        'childs' =>
                        [
                            0 => 'Anakapalli',
                            1 => 'Atchuthapuram',
                            2 => 'Butchayyapeta',
                            3 => 'Cheedikada',
                            4 => 'Chodavaram',
                            5 => 'Devarapalli',
                            6 => 'Golugonda',
                            7 => 'Kasimkota',
                            8 => 'K.Kotapadu',
                            9 => 'Kotauratla',
                            10 => 'Madugula',
                            11 => 'Makavarapalem',
                            12 => 'Munagapaka',
                            13 => 'Nakkapalli',
                            14 => 'Narsipatnam',
                            15 => 'Nathavaram',
                            16 => 'Paravada',
                            17 => 'Payakaraopeta',
                            18 => 'Rambilli',
                            19 => 'Ravikamatham',
                            20 => 'Rolugunta',
                            21 => 'Sabbavaram',
                            22 => 'S.Rayavaram',
                            23 => 'Yelamanchili',
                        ],
                    ],
                    520 =>
                    [
                        'name' => 'Visakhapatnam',
                        'childs' =>
                        [
                            0 => 'Anandapuram',
                            1 => 'Bheemunipatnam',
                            2 => 'Gajuwaka',
                            3 => 'Gopalapatnam',
                            4 => 'Maharanipeta',
                            5 => 'Mulagada',
                            6 => 'Padmanabham',
                            7 => 'Pedagantyada',
                            8 => 'Pendurthi',
                            9 => 'Seethammadhara',
                            10 => 'Visakhapatnam (Rural]',
                        ],
                    ],
                    502 =>
                    [
                        'name' => 'Ananthapuramu',
                        'childs' =>
                        [
                            0 => 'Anantapur Rural',
                            1 => 'Anantapur Urban',
                            2 => 'Atmakur',
                            3 => 'Beluguppa',
                            4 => 'Bommanahal',
                            5 => 'Brahmasamudram',
                            6 => 'Bukkaraya Samudram',
                            7 => 'D.Hirehal',
                            8 => 'Garladinne',
                            9 => 'Gooty',
                            10 => 'Gummagatta',
                            11 => 'Guntakal',
                            12 => 'Kalyanadurg',
                            13 => 'Kambadur',
                            14 => 'Kanekal',
                            15 => 'Kudair',
                            16 => 'Kundurpi',
                            17 => 'Narpala',
                            18 => 'Pamidi',
                            19 => 'Peddapappur',
                            20 => 'Peddavaduguru',
                            21 => 'Putlur',
                            22 => 'Rapthadu',
                            23 => 'Rayadurg',
                            24 => 'Settur',
                            25 => 'Singanamala',
                            26 => 'Tadipatri',
                            27 => 'Uravakonda',
                            28 => 'Vajrakarur',
                            29 => 'Vidapanakal',
                            30 => 'Yadiki',
                            31 => 'Yellanur',
                        ],
                    ],
                    505 =>
                    [
                        'name' => 'East Godavari',
                        'childs' =>
                        [
                            0 => 'Anaparthi',
                            1 => 'Biccavolu',
                            2 => 'Chagallu',
                            3 => 'Devarapalle',
                            4 => 'Gokavaram',
                            5 => 'Gopalapuram',
                            6 => 'Kadiam',
                            7 => 'Korukonda',
                            8 => 'Kovvur',
                            9 => 'Nallajerla',
                            10 => 'Nidadavole',
                            11 => 'Peravali',
                            12 => 'Rajamahendravaram Rural',
                            13 => 'Rajamahendravaram Urban',
                            14 => 'Rajanagaram',
                            15 => 'Rangampeta',
                            16 => 'Seethanagaram',
                            17 => 'Tallapudi',
                            18 => 'Undrajavaram',
                        ],
                    ],
                    517 =>
                    [
                        'name' => 'Prakasam',
                        'childs' =>
                        [
                            0 => 'Ardhaveedu',
                            1 => 'Bestavaripeta',
                            2 => 'Chandra Sekhara Puram',
                            3 => 'Chimakurthy',
                            4 => 'Cumbum',
                            5 => 'Darsi',
                            6 => 'Donakonda',
                            7 => 'Dornala',
                            8 => 'Giddalur',
                            9 => 'Hanumanthuni Padu',
                            10 => 'Kanigiri',
                            11 => 'Komarolu',
                            12 => 'Konakanamitla',
                            13 => 'Kondapi',
                            14 => 'Kotha Patnam',
                            15 => 'Kurichedu',
                            16 => 'Maddipadu',
                            17 => 'Markapuram',
                            18 => 'Marripudi',
                            19 => 'Mundlamuru',
                            20 => 'Naguluppala Padu',
                            21 => 'Ongole Rural',
                            22 => 'Ongole Urban',
                            23 => 'Pamur',
                            24 => 'Pedacherlo Palle',
                            25 => 'Peddaaraveedu',
                            26 => 'Podili',
                            27 => 'Ponnaluru',
                            28 => 'Pullalacheruvu',
                            29 => 'Racherla',
                            30 => 'Santhanuthala Padu',
                            31 => 'Singarayakonda',
                            32 => 'Tangutur',
                            33 => 'Tarlupadu',
                            34 => 'Thallur',
                            35 => 'Tripuranthakam',
                            36 => 'Veligandla',
                            37 => 'Yerragondapalem',
                            38 => 'Zarugumalli',
                        ],
                    ],
                    749 =>
                    [
                        'name' => 'Ntr',
                        'childs' =>
                        [
                            0 => 'Atlapragada Konduru',
                            1 => 'Chandarlapadu',
                            2 => 'Gaddamanugu Konduru',
                            3 => 'Gampalagudem',
                            4 => 'Ibrahimpatnam',
                            5 => 'Jaggaiahpeta',
                            6 => 'Kanchikacherla',
                            7 => 'Mylavaram',
                            8 => 'Nandigama',
                            9 => 'Penuganchiprolu',
                            10 => 'Reddigudem',
                            11 => 'Tiruvuru',
                            12 => 'Vatsavai',
                            13 => 'Veerullapadu',
                            14 => 'Vijayawada Central',
                            15 => 'Vijayawada East',
                            16 => 'Vijayawada North',
                            17 => 'Vijayawada (Rural]',
                            18 => 'Vijayawada West',
                            19 => 'Vissannapeta',
                        ],
                    ],
                    504 =>
                    [
                        'name' => 'Y.S.R.',
                        'childs' =>
                        [
                            0 => 'Atlur',
                            1 => 'Badvel',
                            2 => 'B.Kodur',
                            3 => 'Brahmamgarimattam',
                            4 => 'Chakrayapet',
                            5 => 'Chapad',
                            6 => 'Chennur',
                            7 => 'Chinthakommadinne',
                            8 => 'Duvvur',
                            9 => 'Gopavaram',
                            10 => 'Jammalamadugu',
                            11 => 'Kadapa',
                            12 => 'Kalasapadu',
                            13 => 'Kamalapuram',
                            14 => 'Khajipeta',
                            15 => 'Kondapuram',
                            16 => 'Lingala',
                            17 => 'Muddanur',
                            18 => 'Mylavaram',
                            19 => 'Peddamudiam',
                            20 => 'Pendlimarry',
                            21 => 'Porumamilla',
                            22 => 'Proddutur',
                            23 => 'Pulivendula',
                            24 => 'Rajupalem',
                            25 => 'Sidhout',
                            26 => 'Simhadripuram',
                            27 => 'S.Mydukur',
                            28 => 'Sri Avadhutha Kasinayana',
                            29 => 'Thondur',
                            30 => 'Vallur',
                            31 => 'Veerapunayunipalle',
                            32 => 'Vempalli',
                            33 => 'Vemula',
                            34 => 'Vontimitta',
                            35 => 'Yerraguntla',
                        ],
                    ],
                    510 =>
                    [
                        'name' => 'Krishna',
                        'childs' =>
                        [
                            0 => 'Avanigadda',
                            1 => 'Bantumilli',
                            2 => 'Bapulapadu',
                            3 => 'Challapalli',
                            4 => 'Gannavaram',
                            5 => 'Ghantasala',
                            6 => 'Gudivada',
                            7 => 'Gudlavalleru',
                            8 => 'Guduru',
                            9 => 'Kankipadu',
                            10 => 'Koduru',
                            11 => 'Kruthivennu',
                            12 => 'Machilipatnam North',
                            13 => 'Machilipatnam South',
                            14 => 'Mopidevi',
                            15 => 'Movva',
                            16 => 'Nagayalanka',
                            17 => 'Nandivada',
                            18 => 'Pamarru',
                            19 => 'Pamidimukkala',
                            20 => 'Pedana',
                            21 => 'Pedaparupudi',
                            22 => 'Penamaluru',
                            23 => 'Thotlavalluru',
                            24 => 'Unguturu',
                            25 => 'Vuyyuru',
                        ],
                    ],
                    521 =>
                    [
                        'name' => 'Vizianagaram',
                        'childs' =>
                        [
                            0 => 'Badangi',
                            1 => 'Bhogapuram',
                            2 => 'Bobbili',
                            3 => 'Bondapalli',
                            4 => 'Cheepurupalli',
                            5 => 'Dattirajeru',
                            6 => 'Denkada',
                            7 => 'Gajapathinagaram',
                            8 => 'Gantyada',
                            9 => 'Garividi',
                            10 => 'Gurla',
                            11 => 'Jami',
                            12 => 'Kothavalasa',
                            13 => 'Lakkavarapukota',
                            14 => 'Mentada',
                            15 => 'Merakamudidam',
                            16 => 'Nellimarla',
                            17 => 'Poosapatirega',
                            18 => 'Rajam',
                            19 => 'Ramabhadrapuram',
                            20 => 'Regidi Amadalavalasa',
                            21 => 'Santhakaviti',
                            22 => 'Srungavarapukota',
                            23 => 'Therlam',
                            24 => 'Vangara',
                            25 => 'Vepada',
                            26 => 'Vizianagaram Rural',
                            27 => 'Vizianagaram Urban',
                        ],
                    ],
                    503 =>
                    [
                        'name' => 'Chittoor',
                        'childs' =>
                        [
                            0 => 'Baireddipalle',
                            1 => 'Bangarupalem',
                            2 => 'Chittoor Rural',
                            3 => 'Chittoor Urban',
                            4 => 'Chowdepalle',
                            5 => 'Gangadhara Nellore',
                            6 => 'Gangavaram',
                            7 => 'Gudipala',
                            8 => 'Gudupalle',
                            9 => 'Irala',
                            10 => 'Karvetinagar',
                            11 => 'Kuppam',
                            12 => 'Nagari',
                            13 => 'Nindra',
                            14 => 'Palamaner',
                            15 => 'Palasamudram',
                            16 => 'Peddapanjani',
                            17 => 'Penumur',
                            18 => 'Pulicherla',
                            19 => 'Punganuru',
                            20 => 'Puthalapattu',
                            21 => 'Ramakuppam',
                            22 => 'Rompicherla',
                            23 => 'Santhipuram',
                            24 => 'Sodam',
                            25 => 'Somala',
                            26 => 'Srirangarajapuram',
                            27 => 'Thavanampalle',
                            28 => 'Vedurukuppam',
                            29 => 'Venkatagirikota',
                            30 => 'Vijayapuram',
                            31 => 'Yadamarri',
                        ],
                    ],
                    752 =>
                    [
                        'name' => 'Tirupati',
                        'childs' =>
                        [
                            0 => 'Balayapalli',
                            1 => 'Buchinaidu Kandriga',
                            2 => 'Chandragiri',
                            3 => 'Chillakur',
                            4 => 'Chinnagottigallu',
                            5 => 'Chittamur',
                            6 => 'Dakkili',
                            7 => 'Doravarisatram',
                            8 => 'Gudur',
                            9 => 'Kota',
                            10 => 'Kumara Venkata Bhupala Puram',
                            11 => 'Nagalapuram',
                            12 => 'Naidupet',
                            13 => 'Narayanavanam',
                            14 => 'Ozili',
                            15 => 'Pakala',
                            16 => 'Pellakur',
                            17 => 'Pichatur',
                            18 => 'Puttur',
                            19 => 'Ramachandrapuram',
                            20 => 'Renigunta',
                            21 => 'Satyavedu',
                            22 => 'Srikalahasti',
                            23 => 'Sullurpeta',
                            24 => 'Tada',
                            25 => 'Thottambedu',
                            26 => 'Tirupati (Rural]',
                            27 => 'Tirupati (Urban]',
                            28 => 'Vadamalapet',
                            29 => 'Vakadu',
                            30 => 'Varadaiahpalem',
                            31 => 'Venkatagiri',
                            32 => 'Yerpedu',
                            33 => 'Yerravaripalem',
                        ],
                    ],
                    743 =>
                    [
                        'name' => 'Parvathipuram Manyam',
                        'childs' =>
                        [
                            0 => 'Balijipeta',
                            1 => 'Bhamini',
                            2 => 'Garugubilli',
                            3 => 'Gummalakshmipuram',
                            4 => 'Jiyyammavalasa',
                            5 => 'Komarada',
                            6 => 'Kurupam',
                            7 => 'Makkuva',
                            8 => 'Pachipenta',
                            9 => 'Palakonda',
                            10 => 'Parvathipuram',
                            11 => 'Salur',
                            12 => 'Seethampeta',
                            13 => 'Seethanagaram',
                            14 => 'Veeraghattam',
                        ],
                    ],
                    753 =>
                    [
                        'name' => 'Annamayya',
                        'childs' =>
                        [
                            0 => 'Beerongi Kothakota',
                            1 => 'Chinnamandem',
                            2 => 'Chitvel',
                            3 => 'Galiveedu',
                            4 => 'Gurramkonda',
                            5 => 'Kalakada',
                            6 => 'Kalikiri',
                            7 => 'Kambhamvaripalle',
                            8 => 'Kodur',
                            9 => 'Kurabalakota',
                            10 => 'Lakkireddipalli',
                            11 => 'Madanapalle',
                            12 => 'Mulakalacheruvu',
                            13 => 'Nandalur',
                            14 => 'Nimmanapalle',
                            15 => 'Obulavaripalle',
                            16 => 'Peddamandyam',
                            17 => 'Pedda Thippasamudram',
                            18 => 'Penagalur',
                            19 => 'Pileru',
                            20 => 'Pullampeta',
                            21 => 'Rajampet',
                            22 => 'Ramapuram',
                            23 => 'Ramasamudram',
                            24 => 'Rayachoti',
                            25 => 'Sambepalle',
                            26 => 'Thamballapalle',
                            27 => 'T.Sundupalle',
                            28 => 'Valmikipuram',
                            29 => 'Veeraballe',
                        ],
                    ],
                    506 =>
                    [
                        'name' => 'Guntur',
                        'childs' =>
                        [
                            0 => 'Chebrolu',
                            1 => 'Duggirala',
                            2 => 'Guntur East',
                            3 => 'Guntur West',
                            4 => 'Kakumanu',
                            5 => 'Kollipara',
                            6 => 'Mangalagiri',
                            7 => 'Medikonduru',
                            8 => 'Pedakakani',
                            9 => 'Pedanandipadu',
                            10 => 'Phirangipuram',
                            11 => 'Ponnur',
                            12 => 'Prathipadu',
                            13 => 'Tadepalli',
                            14 => 'Tadikonda',
                            15 => 'Tenali',
                            16 => 'Thulluru',
                            17 => 'Vatticherukuru',
                        ],
                    ],
                    746 =>
                    [
                        'name' => 'Kakinada',
                        'childs' =>
                        [
                            0 => 'Gandepalli',
                            1 => 'Gollaprolu',
                            2 => 'Jaggampeta',
                            3 => 'Kajuluru',
                            4 => 'Kakinada (Rural]',
                            5 => 'Kakinada (Urban]',
                            6 => 'Karapa',
                            7 => 'Kirlampudi',
                            8 => 'Kotananduru',
                            9 => 'Pedapudi',
                            10 => 'Peddapuram',
                            11 => 'Pithapuram',
                            12 => 'Prathipadu',
                            13 => 'Rowthulapudi',
                            14 => 'Samalkota',
                            15 => 'Sankhavaram',
                            16 => 'Tallarevu',
                            17 => 'Thondangi',
                            18 => 'Tuni',
                            19 => 'U.Kothapalli',
                            20 => 'Yeleswaram',
                        ],
                    ],
                ],
            ],
            6 =>
            [
                'name' => 'Haryana',
                'childs' =>
                [
                    63 =>
                    [
                        'name' => 'Hisar',
                        'childs' =>
                        [
                            0 => 'Adampur',
                            1 => 'Balsamand St',
                            2 => 'Barwala',
                            3 => 'Bass',
                            4 => 'Hansi',
                            5 => 'Hisar',
                            6 => 'Kheri Jalab St',
                            7 => 'Narnaund',
                            8 => 'Uklana St',
                        ],
                    ],
                    65 =>
                    [
                        'name' => 'Jind',
                        'childs' =>
                        [
                            0 => 'Alewa',
                            1 => 'Jind',
                            2 => 'Julana',
                            3 => 'Narwana',
                            4 => 'Pillukhera St',
                            5 => 'Safidon',
                            6 => 'Uchana',
                        ],
                    ],
                    58 =>
                    [
                        'name' => 'Ambala',
                        'childs' =>
                        [
                            0 => 'Ambala',
                            1 => 'Ambala Cantonment',
                            2 => 'Barara',
                            3 => 'Mulana St',
                            4 => 'Naraingarh',
                            5 => 'Saha St',
                            6 => 'Shahzadpur St',
                        ],
                    ],
                    67 =>
                    [
                        'name' => 'Karnal',
                        'childs' =>
                        [
                            0 => 'Assandh',
                            1 => 'Ballah St',
                            2 => 'Gharaunda',
                            3 => 'Indri',
                            4 => 'Karnal',
                            5 => 'Nigdu St',
                            6 => 'Nilokheri',
                            7 => 'Nising St',
                        ],
                    ],
                    69 =>
                    [
                        'name' => 'Mahendragarh',
                        'childs' =>
                        [
                            0 => 'Ateli',
                            1 => 'Kanina',
                            2 => 'Mahendragarh',
                            3 => 'Nangal Chawdhary',
                            4 => 'Narnaul',
                            5 => 'Satnali St',
                        ],
                    ],
                    68 =>
                    [
                        'name' => 'Kurukshetra',
                        'childs' =>
                        [
                            0 => 'Babain St',
                            1 => 'Ismailabad St',
                            2 => 'Ladwa',
                            3 => 'Pehowa',
                            4 => 'Shahbad',
                            5 => 'Thanesar',
                        ],
                    ],
                    701 =>
                    [
                        'name' => 'Charkhi Dadri',
                        'childs' =>
                        [
                            0 => 'Badhra',
                            1 => 'Bondkalan St',
                            2 => 'Dadri',
                        ],
                    ],
                    60 =>
                    [
                        'name' => 'Faridabad',
                        'childs' =>
                        [
                            0 => 'Badkhal',
                            1 => 'Ballabgarh',
                            2 => 'Dayalpur St',
                            3 => 'Dhauj St',
                            4 => 'Faridabad',
                            5 => 'Gaunchi St',
                            6 => 'Mohna St',
                            7 => 'Tigaon St',
                        ],
                    ],
                    64 =>
                    [
                        'name' => 'Jhajjar',
                        'childs' =>
                        [
                            0 => 'Badli',
                            1 => 'Bahadurgarh',
                            2 => 'Beri',
                            3 => 'Jhajjar',
                            4 => 'Matenhail',
                            5 => 'Salhawas St',
                        ],
                    ],
                    62 =>
                    [
                        'name' => 'Gurugram',
                        'childs' =>
                        [
                            0 => 'Badshahpur St',
                            1 => 'Farrukhnagar',
                            2 => 'Gurgaon',
                            3 => 'Harsaru St',
                            4 => 'Kadipur St',
                            5 => 'Manesar',
                            6 => 'Pataudi',
                            7 => 'Sohna',
                            8 => 'Wazirabad St',
                        ],
                    ],
                    59 =>
                    [
                        'name' => 'Bhiwani',
                        'childs' =>
                        [
                            0 => 'Bahal St',
                            1 => 'Bawani Khera',
                            2 => 'Bhiwani',
                            3 => 'Loharu',
                            4 => 'Siwani',
                            5 => 'Tosham',
                        ],
                    ],
                    619 =>
                    [
                        'name' => 'Palwal',
                        'childs' =>
                        [
                            0 => 'Bahin St',
                            1 => 'Hassanpur St',
                            2 => 'Hathin',
                            3 => 'Hodal',
                            4 => 'Palwal',
                        ],
                    ],
                    71 =>
                    [
                        'name' => 'Panipat',
                        'childs' =>
                        [
                            0 => 'Bapoli',
                            1 => 'Israna',
                            2 => 'Matlauda',
                            3 => 'Panipat',
                            4 => 'Samalkha',
                        ],
                    ],
                    70 =>
                    [
                        'name' => 'Panchkula',
                        'childs' =>
                        [
                            0 => 'Barwala St',
                            1 => 'Kalka',
                            2 => 'Morni St',
                            3 => 'Panchkula',
                            4 => 'Raipur Rani',
                        ],
                    ],
                    72 =>
                    [
                        'name' => 'Rewari',
                        'childs' =>
                        [
                            0 => 'Bawal',
                            1 => 'Dahina St',
                            2 => 'Dharuhera St',
                            3 => 'Kosli',
                            4 => 'Manethi St',
                            5 => 'Nahar St',
                            6 => 'Palhawas St',
                            7 => 'Rewari',
                        ],
                    ],
                    61 =>
                    [
                        'name' => 'Fatehabad',
                        'childs' =>
                        [
                            0 => 'Bhattukalan St',
                            1 => 'Bhuna St',
                            2 => 'Fatehabad',
                            3 => 'Jakhal St',
                            4 => 'Kulan St',
                            5 => 'Ratia',
                            6 => 'Tohana',
                        ],
                    ],
                    76 =>
                    [
                        'name' => 'Yamunanagar',
                        'childs' =>
                        [
                            0 => 'Bilaspur',
                            1 => 'Chhachhrauli',
                            2 => 'Jagadhri',
                            3 => 'Pratap Nagar St',
                            4 => 'Radaur',
                            5 => 'Sadhaura St',
                            6 => 'Saraswati Nagar St',
                        ],
                    ],
                    74 =>
                    [
                        'name' => 'Sirsa',
                        'childs' =>
                        [
                            0 => 'Dabwali',
                            1 => 'Ellenabad',
                            2 => 'Goriwala St',
                            3 => 'Kalanwali',
                            4 => 'Nathusari Chopta',
                            5 => 'Rania',
                            6 => 'Sirsa',
                        ],
                    ],
                    66 =>
                    [
                        'name' => 'Kaithal',
                        'childs' =>
                        [
                            0 => 'Dhand St',
                            1 => 'Fatehpur Pundri',
                            2 => 'Guhla',
                            3 => 'Kaithal',
                            4 => 'Kalayat',
                            5 => 'Rajaund St',
                            6 => 'Siwan St',
                        ],
                    ],
                    604 =>
                    [
                        'name' => 'Nuh',
                        'childs' =>
                        [
                            0 => 'Ferozepur Jhirka',
                            1 => 'Indri',
                            2 => 'Nagina St',
                            3 => 'Nuh',
                            4 => 'Punahana',
                            5 => 'Taoru',
                        ],
                    ],
                    75 =>
                    [
                        'name' => 'Sonipat',
                        'childs' =>
                        [
                            0 => 'Ganaur',
                            1 => 'Gohana',
                            2 => 'Khanpur St',
                            3 => 'Kharkhoda',
                            4 => 'Rai St',
                            5 => 'Sonipat',
                        ],
                    ],
                    73 =>
                    [
                        'name' => 'Rohtak',
                        'childs' =>
                        [
                            0 => 'Kalanaur',
                            1 => 'Lakhan Majra St',
                            2 => 'Maham',
                            3 => 'Rohtak',
                            4 => 'Sampla',
                        ],
                    ],
                ],
            ],
            10 =>
            [
                'name' => 'Bihar',
                'childs' =>
                [
                    213 =>
                    [
                        'name' => 'Purbi Champaran',
                        'childs' =>
                        [
                            0 => 'Adapur',
                            1 => 'Areraj',
                            2 => 'Banjaria',
                            3 => 'Bankatwa',
                            4 => 'Chakia(Pipra]',
                            5 => 'Chiraia',
                            6 => 'Dhaka',
                            7 => 'Ghorasahan',
                            8 => 'Harsidhi',
                            9 => 'Kalyanpur',
                            10 => 'Kesaria',
                            11 => 'Kotwa',
                            12 => 'Madhuban',
                            13 => 'Mehsi',
                            14 => 'Motihari',
                            15 => 'Narkatia',
                            16 => 'Paharpur',
                            17 => 'Pakri Dayal',
                            18 => 'Patahi',
                            19 => 'Phenhara',
                            20 => 'Piprakothi',
                            21 => 'Ramgarhwa',
                            22 => 'Raxaul',
                            23 => 'Sangrampur',
                            24 => 'Sugauli',
                            25 => 'Tetaria',
                            26 => 'Turkaulia',
                        ],
                    ],
                    200 =>
                    [
                        'name' => 'Kaimur (Bhabua]',
                        'childs' =>
                        [
                            0 => 'Adhaura',
                            1 => 'Bhabua',
                            2 => 'Bhagwanpur',
                            3 => 'Chainpur',
                            4 => 'Chand',
                            5 => 'Durgawati',
                            6 => 'Kudra',
                            7 => 'Mohania',
                            8 => 'Nuaon',
                            9 => 'Ramgarh',
                            10 => 'Rampur',
                        ],
                    ],
                    193 =>
                    [
                        'name' => 'Bhojpur',
                        'childs' =>
                        [
                            0 => 'Agiaon',
                            1 => 'Arrah',
                            2 => 'Barhara',
                            3 => 'Behea',
                            4 => 'Charpokhari',
                            5 => 'Garhani',
                            6 => 'Jagdishpur',
                            7 => 'Koilwar',
                            8 => 'Piro',
                            9 => 'Sahar',
                            10 => 'Sandesh',
                            11 => 'Shahpur',
                            12 => 'Tarari',
                            13 => 'Udwant Nagar',
                        ],
                    ],
                    210 =>
                    [
                        'name' => 'Nawada',
                        'childs' =>
                        [
                            0 => 'Akbarpur',
                            1 => 'Gobindpur',
                            2 => 'Hisua',
                            3 => 'Kashi Chak',
                            4 => 'Kawakol',
                            5 => 'Meskaur',
                            6 => 'Nardiganj',
                            7 => 'Narhat',
                            8 => 'Nawada',
                            9 => 'Pakribarawan',
                            10 => 'Rajauli',
                            11 => 'Roh',
                            12 => 'Sirdala',
                            13 => 'Warisaliganj',
                        ],
                    ],
                    215 =>
                    [
                        'name' => 'Rohtas',
                        'childs' =>
                        [
                            0 => 'Akorhi Gola',
                            1 => 'Bikramganj',
                            2 => 'Chenari',
                            3 => 'Dawath',
                            4 => 'Dehri',
                            5 => 'Dinara',
                            6 => 'Karakat',
                            7 => 'Kargahar',
                            8 => 'Kochas',
                            9 => 'Nasriganj',
                            10 => 'Nauhatta',
                            11 => 'Nokha',
                            12 => 'Rajpur',
                            13 => 'Rohtas',
                            14 => 'Sanjhauli',
                            15 => 'Sasaram',
                            16 => 'Sheosagar',
                            17 => 'Suryapura',
                            18 => 'Tilouthu',
                        ],
                    ],
                    205 =>
                    [
                        'name' => 'Madhepura',
                        'childs' =>
                        [
                            0 => 'Alamnagar',
                            1 => 'Bihariganj',
                            2 => 'Chausa',
                            3 => 'Gamharia',
                            4 => 'Ghailarh',
                            5 => 'Gwalpara',
                            6 => 'Kishanganj',
                            7 => 'Kumarkhand',
                            8 => 'Madhepura',
                            9 => 'Murliganj',
                            10 => 'Puraini',
                            11 => 'Shankarpur',
                            12 => 'Singheshwar',
                        ],
                    ],
                    202 =>
                    [
                        'name' => 'Khagaria',
                        'childs' =>
                        [
                            0 => 'Alauli',
                            1 => 'Beldaur',
                            2 => 'Chautham',
                            3 => 'Gogri',
                            4 => 'Khagaria',
                            5 => 'Mansi',
                            6 => 'Parbatta',
                        ],
                    ],
                    195 =>
                    [
                        'name' => 'Darbhanga',
                        'childs' =>
                        [
                            0 => 'Alinagar',
                            1 => 'Bahadurpur',
                            2 => 'Baheri',
                            3 => 'Benipur',
                            4 => 'Biraul',
                            5 => 'Darbhanga',
                            6 => 'Ghanshyampur',
                            7 => 'Gora Bauram',
                            8 => 'Hanumannagar',
                            9 => 'Hayaghat',
                            10 => 'Jale',
                            11 => 'Keotiranway',
                            12 => 'Kiratpur',
                            13 => 'Kusheshwar Asthan',
                            14 => 'Kusheshwar Asthan Purbi',
                            15 => 'Manigachhi',
                            16 => 'Singhwara',
                            17 => 'Tardih',
                        ],
                    ],
                    190 =>
                    [
                        'name' => 'Banka',
                        'childs' =>
                        [
                            0 => 'Amarpur',
                            1 => 'Banka',
                            2 => 'Barahat',
                            3 => 'Bausi',
                            4 => 'Belhar',
                            5 => 'Chanan',
                            6 => 'Dhuraiya',
                            7 => 'Katoria',
                            8 => 'Phulidumar',
                            9 => 'Rajaun',
                            10 => 'Shambhuganj',
                        ],
                    ],
                    196 =>
                    [
                        'name' => 'Gaya',
                        'childs' =>
                        [
                            0 => 'Amas',
                            1 => 'Atri',
                            2 => 'Banke Bazar',
                            3 => 'Barachatti',
                            4 => 'Belaganj',
                            5 => 'Bodh Gaya',
                            6 => 'Dobhi',
                            7 => 'Dumaria',
                            8 => 'Fatehpur',
                            9 => 'Gaya Town C.D.Block',
                            10 => 'Guraru',
                            11 => 'Gurua',
                            12 => 'Imamganj',
                            13 => 'Khizirsarai',
                            14 => 'Konch',
                            15 => 'Manpur',
                            16 => 'Mohanpur',
                            17 => 'Muhra',
                            18 => 'Neem Chak Bathani',
                            19 => 'Paraiya',
                            20 => 'Sherghati',
                            21 => 'Tan Kuppa',
                            22 => 'Tikari',
                            23 => 'Wazirganj',
                        ],
                    ],
                    201 =>
                    [
                        'name' => 'Katihar',
                        'childs' =>
                        [
                            0 => 'Amdabad',
                            1 => 'Azamnagar',
                            2 => 'Balrampur',
                            3 => 'Barari',
                            4 => 'Barsoi',
                            5 => 'Dandkhora',
                            6 => 'Falka',
                            7 => 'Hasanganj',
                            8 => 'Kadwa',
                            9 => 'Katihar',
                            10 => 'Korha',
                            11 => 'Kursela',
                            12 => 'Manihari',
                            13 => 'Mansahi',
                            14 => 'Pranpur',
                            15 => 'Sameli',
                        ],
                    ],
                    218 =>
                    [
                        'name' => 'Saran',
                        'childs' =>
                        [
                            0 => 'Amnour',
                            1 => 'Baniapur',
                            2 => 'Chapra',
                            3 => 'Dariapur',
                            4 => 'Dighwara',
                            5 => 'Ekma',
                            6 => 'Garkha',
                            7 => 'Ishupur',
                            8 => 'Jalalpur',
                            9 => 'Lahladpur',
                            10 => 'Maker',
                            11 => 'Manjhi',
                            12 => 'Marhaura',
                            13 => 'Mashrakh',
                            14 => 'Nagra',
                            15 => 'Panapur',
                            16 => 'Parsa',
                            17 => 'Revelganj',
                            18 => 'Sonepur',
                            19 => 'Taraiya',
                        ],
                    ],
                    214 =>
                    [
                        'name' => 'Purnia',
                        'childs' =>
                        [
                            0 => 'Amour',
                            1 => 'Baisa',
                            2 => 'Baisi',
                            3 => 'Banmankhi',
                            4 => 'Barhara',
                            5 => 'Bhawanipur',
                            6 => 'Dagarua',
                            7 => 'Dhamdaha',
                            8 => 'Jalalgarh',
                            9 => 'Kasba',
                            10 => 'Krityanand Nagar',
                            11 => 'Purnia East',
                            12 => 'Rupauli',
                            13 => 'Srinagar',
                        ],
                    ],
                    222 =>
                    [
                        'name' => 'Siwan',
                        'childs' =>
                        [
                            0 => 'Andar',
                            1 => 'Barharia',
                            2 => 'Basantpur',
                            3 => 'Bhagwanpur Hat',
                            4 => 'Darauli',
                            5 => 'Daraundha',
                            6 => 'Goriakothi',
                            7 => 'Guthani',
                            8 => 'Hasanpura',
                            9 => 'Hussainganj',
                            10 => 'Lakri Nabiganj',
                            11 => 'Maharajganj',
                            12 => 'Mairwa',
                            13 => 'Nautan',
                            14 => 'Pachrukhi',
                            15 => 'Raghunathpur',
                            16 => 'Siswan',
                            17 => 'Siwan',
                            18 => 'Ziradei',
                        ],
                    ],
                    206 =>
                    [
                        'name' => 'Madhubani',
                        'childs' =>
                        [
                            0 => 'Andhratharhi',
                            1 => 'Babubarhi',
                            2 => 'Basopatti',
                            3 => 'Benipatti',
                            4 => 'Bisfi',
                            5 => 'Ghoghardiha',
                            6 => 'Harlakhi',
                            7 => 'Jainagar',
                            8 => 'Jhanjharpur',
                            9 => 'Kaluahi',
                            10 => 'Khajauli',
                            11 => 'Ladania',
                            12 => 'Lakhnaur',
                            13 => 'Laukaha',
                            14 => 'Laukahi',
                            15 => 'Madhepur',
                            16 => 'Madhubani',
                            17 => 'Madhwapur',
                            18 => 'Pandaul',
                            19 => 'Phulparas',
                            20 => 'Rajnagar',
                        ],
                    ],
                    188 =>
                    [
                        'name' => 'Araria',
                        'childs' =>
                        [
                            0 => 'Araria',
                            1 => 'Bhargama',
                            2 => 'Forbesganj',
                            3 => 'Jokihat',
                            4 => 'Kursakatta',
                            5 => 'Narpatganj',
                            6 => 'Palasi',
                            7 => 'Raniganj',
                            8 => 'Sikti',
                        ],
                    ],
                    219 =>
                    [
                        'name' => 'Sheikhpura',
                        'childs' =>
                        [
                            0 => 'Ariari',
                            1 => 'Barbigha',
                            2 => 'Chewara',
                            3 => 'Ghat Kusumbha',
                            4 => 'Sheikhpura',
                            5 => 'Shekhopur Sarai',
                        ],
                    ],
                    611 =>
                    [
                        'name' => 'Arwal',
                        'childs' =>
                        [
                            0 => 'Arwal',
                            1 => 'Kaler',
                            2 => 'Karpi',
                            3 => 'Kurtha',
                            4 => 'Sonbhadra Banshi Suryapur',
                        ],
                    ],
                    207 =>
                    [
                        'name' => 'Munger',
                        'childs' =>
                        [
                            0 => 'Asarganj',
                            1 => 'Bariarpur',
                            2 => 'Dharhara',
                            3 => 'Jamalpur',
                            4 => 'Kharagpur',
                            5 => 'Munger',
                            6 => 'Sangrampur',
                            7 => 'Tarapur',
                            8 => 'Tetiha Bambor',
                        ],
                    ],
                    209 =>
                    [
                        'name' => 'Nalanda',
                        'childs' =>
                        [
                            0 => 'Asthawan',
                            1 => 'Ben',
                            2 => 'Bihar',
                            3 => 'Bind',
                            4 => 'Chandi',
                            5 => 'Ekangarsarai',
                            6 => 'Giriak',
                            7 => 'Harnaut',
                            8 => 'Hilsa',
                            9 => 'Islampur',
                            10 => 'Karai Parsurai',
                            11 => 'Katrisarai',
                            12 => 'Nagar Nausa',
                            13 => 'Noorsarai',
                            14 => 'Parbalpur',
                            15 => 'Rahui',
                            16 => 'Rajgir',
                            17 => 'Sarmera',
                            18 => 'Silao',
                            19 => 'Tharthari',
                        ],
                    ],
                    212 =>
                    [
                        'name' => 'Patna',
                        'childs' =>
                        [
                            0 => 'Athmalgola',
                            1 => 'Bakhtiarpur',
                            2 => 'Barh',
                            3 => 'Belchhi',
                            4 => 'Bihta',
                            5 => 'Bikram',
                            6 => 'Daniawan',
                            7 => 'Dhanarua',
                            8 => 'Dinapur-Cum-Khagaul',
                            9 => 'Dulhin Bazar',
                            10 => 'Fatwah',
                            11 => 'Ghoswari',
                            12 => 'Khusrupur',
                            13 => 'Maner',
                            14 => 'Masaurhi',
                            15 => 'Mokameh',
                            16 => 'Naubatpur',
                            17 => 'Paliganj',
                            18 => 'Pandarak',
                            19 => 'Patna Rural',
                            20 => 'Phulwari',
                            21 => 'Punpun',
                            22 => 'Sampatchak',
                        ],
                    ],
                    208 =>
                    [
                        'name' => 'Muzaffarpur',
                        'childs' =>
                        [
                            0 => 'Aurai',
                            1 => 'Bandra',
                            2 => 'Baruraj (Motipur]',
                            3 => 'Bochaha',
                            4 => 'Dholi (Moraul]',
                            5 => 'Gaighat',
                            6 => 'Kanti',
                            7 => 'Katra',
                            8 => 'Kurhani',
                            9 => 'Marwan',
                            10 => 'Minapur',
                            11 => 'Musahri',
                            12 => 'Paroo',
                            13 => 'Sahebganj',
                            14 => 'Sakra',
                            15 => 'Saraiya',
                        ],
                    ],
                    189 =>
                    [
                        'name' => 'Aurangabad',
                        'childs' =>
                        [
                            0 => 'Aurangabad',
                            1 => 'Barun',
                            2 => 'Daudnagar',
                            3 => 'Deo',
                            4 => 'Goh',
                            5 => 'Haspura',
                            6 => 'Kutumba',
                            7 => 'Madanpur',
                            8 => 'Nabinagar',
                            9 => 'Obra',
                            10 => 'Rafiganj',
                        ],
                    ],
                    191 =>
                    [
                        'name' => 'Begusarai',
                        'childs' =>
                        [
                            0 => 'Bachhwara',
                            1 => 'Bakhri',
                            2 => 'Balia',
                            3 => 'Barauni',
                            4 => 'Begusarai',
                            5 => 'Bhagwanpur',
                            6 => 'Birpur',
                            7 => 'Cheria Bariarpur',
                            8 => 'Chhorahi',
                            9 => 'Dandari',
                            10 => 'Garhpura',
                            11 => 'Khudabandpur',
                            12 => 'Mansurchak',
                            13 => 'Matihani',
                            14 => 'Naokothi',
                            15 => 'Sahebpur Kamal',
                            16 => 'Shamho Akha Kurha',
                            17 => 'Teghra',
                        ],
                    ],
                    211 =>
                    [
                        'name' => 'Pashchim Champaran',
                        'childs' =>
                        [
                            0 => 'Bagaha',
                            1 => 'Bairia',
                            2 => 'Bettiah',
                            3 => 'Bhitaha',
                            4 => 'Chanpatia',
                            5 => 'Gaunaha',
                            6 => 'Jogapatti',
                            7 => 'Lauriya',
                            8 => 'Madhubani',
                            9 => 'Mainatanr',
                            10 => 'Majhaulia',
                            11 => 'Narkatiaganj',
                            12 => 'Nautan',
                            13 => 'Piprasi',
                            14 => 'Ramnagar',
                            15 => 'Sidhaw',
                            16 => 'Sikta',
                            17 => 'Thakrahan',
                        ],
                    ],
                    203 =>
                    [
                        'name' => 'Kishanganj',
                        'childs' =>
                        [
                            0 => 'Bahadurganj',
                            1 => 'Dighalbank',
                            2 => 'Kishanganj',
                            3 => 'Kochadhamin',
                            4 => 'Pothia',
                            5 => 'Terhagachh',
                            6 => 'Thakurganj',
                        ],
                    ],
                    197 =>
                    [
                        'name' => 'Gopalganj',
                        'childs' =>
                        [
                            0 => 'Baikunthpur',
                            1 => 'Barauli',
                            2 => 'Bhorey',
                            3 => 'Bijaipur',
                            4 => 'Gopalganj',
                            5 => 'Hathua',
                            6 => 'Katiya',
                            7 => 'Kuchaikote',
                            8 => 'Manjha',
                            9 => 'Pach Deuri',
                            10 => 'Phulwaria',
                            11 => 'Sidhwalia',
                            12 => 'Thawe',
                            13 => 'Uchkagaon',
                        ],
                    ],
                    221 =>
                    [
                        'name' => 'Sitamarhi',
                        'childs' =>
                        [
                            0 => 'Bairgania',
                            1 => 'Bajpatti',
                            2 => 'Bathnaha',
                            3 => 'Belsand',
                            4 => 'Bokhara',
                            5 => 'Charaut',
                            6 => 'Dumra',
                            7 => 'Majorganj',
                            8 => 'Nanpur',
                            9 => 'Parihar',
                            10 => 'Parsauni',
                            11 => 'Pupri',
                            12 => 'Riga',
                            13 => 'Runisaidpur',
                            14 => 'Sonbarsa',
                            15 => 'Suppi',
                            16 => 'Sursand',
                        ],
                    ],
                    216 =>
                    [
                        'name' => 'Saharsa',
                        'childs' =>
                        [
                            0 => 'Banma Itahri',
                            1 => 'Kahara',
                            2 => 'Mahishi',
                            3 => 'Nauhatta',
                            4 => 'Patarghat',
                            5 => 'Salkhua',
                            6 => 'Satar Kataiya',
                            7 => 'Saur Bazar',
                            8 => 'Simri Bakhtiarpur',
                            9 => 'Sonbarsa',
                        ],
                    ],
                    204 =>
                    [
                        'name' => 'Lakhisarai',
                        'childs' =>
                        [
                            0 => 'Barahiya',
                            1 => 'Chanan*',
                            2 => 'Halsi',
                            3 => 'Lakhisarai',
                            4 => 'Pipariya',
                            5 => 'Ramgarh Chowk',
                            6 => 'Surajgarha',
                        ],
                    ],
                    194 =>
                    [
                        'name' => 'Buxar',
                        'childs' =>
                        [
                            0 => 'Barhampur',
                            1 => 'Buxar',
                            2 => 'Chakki',
                            3 => 'Chaugain',
                            4 => 'Chausa',
                            5 => 'Dumraon',
                            6 => 'Itarhi',
                            7 => 'Kesath',
                            8 => 'Nawanagar',
                            9 => 'Rajpur',
                            10 => 'Simri',
                        ],
                    ],
                    198 =>
                    [
                        'name' => 'Jamui',
                        'childs' =>
                        [
                            0 => 'Barhat',
                            1 => 'Chakai',
                            2 => 'Gidhaur',
                            3 => 'Islamnagar Aliganj',
                            4 => 'Jamui',
                            5 => 'Jhajha',
                            6 => 'Khaira',
                            7 => 'Lakshmipur',
                            8 => 'Sikandra',
                            9 => 'Sono',
                        ],
                    ],
                    223 =>
                    [
                        'name' => 'Supaul',
                        'childs' =>
                        [
                            0 => 'Basantpur',
                            1 => 'Chhatapur',
                            2 => 'Kishanpur',
                            3 => 'Marauna',
                            4 => 'Nirmali',
                            5 => 'Pipra',
                            6 => 'Pratapganj',
                            7 => 'Raghopur',
                            8 => 'Saraigarh Bhaptiyahi',
                            9 => 'Supaul',
                            10 => 'Tribeniganj',
                        ],
                    ],
                    224 =>
                    [
                        'name' => 'Vaishali',
                        'childs' =>
                        [
                            0 => 'Bhagwanpur',
                            1 => 'Bidupur',
                            2 => 'Chehra Kalan',
                            3 => 'Desri',
                            4 => 'Goraul',
                            5 => 'Hajipur',
                            6 => 'Jandaha',
                            7 => 'Lalganj',
                            8 => 'Mahnar',
                            9 => 'Mahua',
                            10 => 'Patepur',
                            11 => 'Paterhi Belsar',
                            12 => 'Raghopur',
                            13 => 'Raja Pakar',
                            14 => 'Sahdai Buzurg',
                            15 => 'Vaishali',
                        ],
                    ],
                    217 =>
                    [
                        'name' => 'Samastipur',
                        'childs' =>
                        [
                            0 => 'Bibhutpur',
                            1 => 'Bithan',
                            2 => 'Dalsinghsarai',
                            3 => 'Hasanpur',
                            4 => 'Kalyanpur',
                            5 => 'Khanpur',
                            6 => 'Mohanpur',
                            7 => 'Mohiuddinagar',
                            8 => 'Morwa',
                            9 => 'Patori',
                            10 => 'Pusa',
                            11 => 'Rosera',
                            12 => 'Samastipur',
                            13 => 'Sarairanjan',
                            14 => 'Shivaji Nagar',
                            15 => 'Singhia',
                            16 => 'Tajpur',
                            17 => 'Ujiarpur',
                            18 => 'Vidyapati Nagar',
                            19 => 'Warisnagar',
                        ],
                    ],
                    192 =>
                    [
                        'name' => 'Bhagalpur',
                        'childs' =>
                        [
                            0 => 'Bihpur',
                            1 => 'Gopalpur',
                            2 => 'Goradih',
                            3 => 'Ismailpur',
                            4 => 'Jagdishpur',
                            5 => 'Kahalgaon',
                            6 => 'Kharik',
                            7 => 'Narayanpur',
                            8 => 'Nathnagar',
                            9 => 'Naugachhia',
                            10 => 'Pirpainti',
                            11 => 'Rangra Chowk',
                            12 => 'Sabour',
                            13 => 'Shahkund',
                            14 => 'Sonhaula',
                            15 => 'Sultanganj',
                        ],
                    ],
                    220 =>
                    [
                        'name' => 'Sheohar',
                        'childs' =>
                        [
                            0 => 'Dumri Katsari',
                            1 => 'Piprarhi',
                            2 => 'Purnahiya',
                            3 => 'Sheohar',
                            4 => 'Tariani Chowk',
                        ],
                    ],
                    199 =>
                    [
                        'name' => 'Jehanabad',
                        'childs' =>
                        [
                            0 => 'Ghoshi',
                            1 => 'Hulasganj',
                            2 => 'Jehanabad',
                            3 => 'Kako',
                            4 => 'Makhdumpur',
                            5 => 'Modanganj',
                            6 => 'Ratni Faridpur',
                        ],
                    ],
                ],
            ],
            23 =>
            [
                'name' => 'Madhya Pradesh',
                'childs' =>
                [
                    411 =>
                    [
                        'name' => 'Jabalpur',
                        'childs' =>
                        [
                            0 => 'Adhartal',
                            1 => 'Gorakhpur',
                            2 => 'Jabalpur',
                            3 => 'Kundam',
                            4 => 'Majholi',
                            5 => 'Panagar',
                            6 => 'Patan',
                            7 => 'Ranjhi',
                            8 => 'Shahpura',
                            9 => 'Sihora',
                        ],
                    ],
                    667 =>
                    [
                        'name' => 'Agar-Malwa',
                        'childs' =>
                        [
                            0 => 'Agar',
                            1 => 'Badod',
                            2 => 'Nalkheda',
                            3 => 'Soyatkala',
                            4 => 'Susner',
                        ],
                    ],
                    420 =>
                    [
                        'name' => 'Panna',
                        'childs' =>
                        [
                            0 => 'Ajaygarh',
                            1 => 'Amanganj',
                            2 => 'Devendranagar',
                            3 => 'Gunnor',
                            4 => 'Panna',
                            5 => 'Pawai',
                            6 => 'Raipura',
                            7 => 'Shahnagar',
                            8 => 'Simariya',
                        ],
                    ],
                    639 =>
                    [
                        'name' => 'Alirajpur',
                        'childs' =>
                        [
                            0 => 'Alirajpur',
                            1 => 'Chandra Shekhar Azad Nagar',
                            2 => 'Jobat',
                            3 => 'Katthiwara',
                            4 => 'Sondawa',
                        ],
                    ],
                    423 =>
                    [
                        'name' => 'Ratlam',
                        'childs' =>
                        [
                            0 => 'Alot',
                            1 => 'Bajna',
                            2 => 'Jaora',
                            3 => 'Piploda',
                            4 => 'Raoti',
                            5 => 'Ratlam',
                            6 => 'Ratlam Nagar',
                            7 => 'Sailana',
                            8 => 'Tal',
                        ],
                    ],
                    784 =>
                    [
                        'name' => 'Maihar',
                        'childs' =>
                        [
                            0 => 'Amarpatan',
                            1 => 'Maihar',
                            2 => 'Ramnagar',
                        ],
                    ],
                    399 =>
                    [
                        'name' => 'Chhindwara',
                        'childs' =>
                        [
                            0 => 'Amarwara',
                            1 => 'Bichhua',
                            2 => 'Chand',
                            3 => 'Chaurai',
                            4 => 'Chhindwara',
                            5 => 'Chhindwara Nagar',
                            6 => 'Harrai',
                            7 => 'Jamai (Junnardeo]',
                            8 => 'Mohkhed',
                            9 => 'Parasia',
                            10 => 'Tamia',
                            11 => 'Umreth',
                        ],
                    ],
                    417 =>
                    [
                        'name' => 'Morena',
                        'childs' =>
                        [
                            0 => 'Ambah',
                            1 => 'Bamor',
                            2 => 'Joura',
                            3 => 'Kailaras',
                            4 => 'Morena',
                            5 => 'Morena Nagar',
                            6 => 'Porsa',
                            7 => 'Sabalgarh',
                        ],
                    ],
                    394 =>
                    [
                        'name' => 'Betul',
                        'childs' =>
                        [
                            0 => 'Amla',
                            1 => 'Athner',
                            2 => 'Betul',
                            3 => 'Betul Nagar',
                            4 => 'Bhainsdehi',
                            5 => 'Bhimpur',
                            6 => 'Chicholi',
                            7 => 'Ghoda Dongri',
                            8 => 'Multai',
                            9 => 'Prabhatpttan',
                            10 => 'Shahpur',
                        ],
                    ],
                    393 =>
                    [
                        'name' => 'Barwani',
                        'childs' =>
                        [
                            0 => 'Anjad',
                            1 => 'Barwani',
                            2 => 'Niwali',
                            3 => 'Pansemal',
                            4 => 'Pati',
                            5 => 'Rajpur',
                            6 => 'Sendhwa',
                            7 => 'Thikri',
                            8 => 'Varla',
                        ],
                    ],
                    390 =>
                    [
                        'name' => 'Anuppur',
                        'childs' =>
                        [
                            0 => 'Anuppur',
                            1 => 'Jaithari',
                            2 => 'Kotma',
                            3 => 'Pushparajgarh',
                        ],
                    ],
                    406 =>
                    [
                        'name' => 'Guna',
                        'childs' =>
                        [
                            0 => 'Aron',
                            1 => 'Bamori',
                            2 => 'Chachaura',
                            3 => 'Guna',
                            4 => 'Guna Nagar',
                            5 => 'Kumbhraj',
                            6 => 'Maksoodangarh',
                            7 => 'Raghogarh',
                        ],
                    ],
                    391 =>
                    [
                        'name' => 'Ashoknagar',
                        'childs' =>
                        [
                            0 => 'Ashoknagar',
                            1 => 'Bahadurpur',
                            2 => 'Chanderi',
                            3 => 'Isagarh',
                            4 => 'Mungaoli',
                            5 => 'Nai Sarai',
                            6 => 'Piprai',
                            7 => 'Shadhora',
                        ],
                    ],
                    427 =>
                    [
                        'name' => 'Sehore',
                        'childs' =>
                        [
                            0 => 'Ashta',
                            1 => 'Bhairunda',
                            2 => 'Budni',
                            3 => 'Doraha',
                            4 => 'Ichhawar',
                            5 => 'Jawar',
                            6 => 'Rehti',
                            7 => 'Sehore',
                            8 => 'Sehore Nagar',
                            9 => 'Shyampur',
                        ],
                    ],
                    395 =>
                    [
                        'name' => 'Bhind',
                        'childs' =>
                        [
                            0 => 'Ater',
                            1 => 'Bhind',
                            2 => 'Bhind Nagar',
                            3 => 'Gohad',
                            4 => 'Gormi',
                            5 => 'Lahar',
                            6 => 'Mau',
                            7 => 'Mehgaon',
                            8 => 'Mihona',
                            9 => 'Ron',
                        ],
                    ],
                    430 =>
                    [
                        'name' => 'Shajapur',
                        'childs' =>
                        [
                            0 => 'Avantipur Barodia',
                            1 => 'Gulana',
                            2 => 'Kalapipal',
                            3 => 'Moman Badodiya',
                            4 => 'Polaykala',
                            5 => 'Shajapur',
                            6 => 'Shujalpur',
                        ],
                    ],
                    434 =>
                    [
                        'name' => 'Tikamgarh',
                        'childs' =>
                        [
                            0 => 'Badagaon (Dhasan]',
                            1 => 'Baldeogarh',
                            2 => 'Dighora',
                            3 => 'Jatara',
                            4 => 'Khargapur',
                            5 => 'Lidhora',
                            6 => 'Mohangarh',
                            7 => 'Palera',
                            8 => 'Tikamgarh',
                        ],
                    ],
                    398 =>
                    [
                        'name' => 'Chhatarpur',
                        'childs' =>
                        [
                            0 => 'Bada Malhera',
                            1 => 'Bijawar',
                            2 => 'Buxwaha',
                            3 => 'Chandla',
                            4 => 'Chhatarpur',
                            5 => 'Chhatarpur Nagar',
                            6 => 'Gaurihar',
                            7 => 'Ghuwara',
                            8 => 'Lavkush Nagar',
                            9 => 'Maharajpur',
                            10 => 'Nowgong',
                            11 => 'Rajnagar',
                            12 => 'Satai',
                        ],
                    ],
                    432 =>
                    [
                        'name' => 'Shivpuri',
                        'childs' =>
                        [
                            0 => 'Badarwas',
                            1 => 'Bairad',
                            2 => 'Karera',
                            3 => 'Khaniyadhana',
                            4 => 'Kolaras',
                            5 => 'Narwar',
                            6 => 'Pichhore',
                            7 => 'Pohri',
                            8 => 'Rannod',
                            9 => 'Shivpuri',
                            10 => 'Shivpuri Nagar',
                        ],
                    ],
                    421 =>
                    [
                        'name' => 'Raisen',
                        'childs' =>
                        [
                            0 => 'Badi',
                            1 => 'Baraily',
                            2 => 'Begamganj',
                            3 => 'Deori',
                            4 => 'Gairatganj',
                            5 => 'Goharganj',
                            6 => 'Raisen',
                            7 => 'Silwani',
                            8 => 'Sultanpur',
                            9 => 'Udaipura',
                        ],
                    ],
                    435 =>
                    [
                        'name' => 'Ujjain',
                        'childs' =>
                        [
                            0 => 'Badnagar',
                            1 => 'Ghatiya',
                            2 => 'Jharda',
                            3 => 'Khacharod',
                            4 => 'Kothi Mahal',
                            5 => 'Mahidpur',
                            6 => 'Makdon',
                            7 => 'Nagda',
                            8 => 'Tarana',
                            9 => 'Ujjain',
                            10 => 'Ujjain Nagar',
                            11 => 'Unhel',
                        ],
                    ],
                    403 =>
                    [
                        'name' => 'Dhar',
                        'childs' =>
                        [
                            0 => 'Badnawar',
                            1 => 'Dahi',
                            2 => 'Dhar',
                            3 => 'Dharampuri',
                            4 => 'Gandhwani',
                            5 => 'Kukshi',
                            6 => 'Manawar',
                            7 => 'Pithampur',
                            8 => 'Sardarpur',
                        ],
                    ],
                    431 =>
                    [
                        'name' => 'Sheopur',
                        'childs' =>
                        [
                            0 => 'Badoda',
                            1 => 'Beerpur',
                            2 => 'Karahal',
                            3 => 'Sheopur',
                            4 => 'Vijaypur',
                        ],
                    ],
                    413 =>
                    [
                        'name' => 'Katni',
                        'childs' =>
                        [
                            0 => 'Badwara',
                            1 => 'Bahoriband',
                            2 => 'Barhi',
                            3 => 'Dhimarkheda',
                            4 => 'Katni Nagar',
                            5 => 'Murwara Or Katni',
                            6 => 'Rithi',
                            7 => 'Sleemnabad',
                            8 => 'Vijayraghavgarh',
                        ],
                    ],
                    402 =>
                    [
                        'name' => 'Dewas',
                        'childs' =>
                        [
                            0 => 'Bagli',
                            1 => 'Dewas',
                            2 => 'Dewas Nagar',
                            3 => 'Hatpiplya',
                            4 => 'Kannod',
                            5 => 'Khategaon',
                            6 => 'Satwas',
                            7 => 'Sonkatch',
                            8 => 'Tonk Khurd',
                            9 => 'Udaynagar',
                        ],
                    ],
                    433 =>
                    [
                        'name' => 'Sidhi',
                        'childs' =>
                        [
                            0 => 'Bahari',
                            1 => 'Churhat',
                            2 => 'Gopadbanas',
                            3 => 'Kusmi',
                            4 => 'Madwas',
                            5 => 'Majhauli',
                            6 => 'Rampur Naikin',
                            7 => 'Sihawal',
                        ],
                    ],
                    392 =>
                    [
                        'name' => 'Balaghat',
                        'childs' =>
                        [
                            0 => 'Baihar',
                            1 => 'Balaghat',
                            2 => 'Birsa',
                            3 => 'Katangi',
                            4 => 'Khairlanji',
                            5 => 'Kirnapur',
                            6 => 'Lalbarra',
                            7 => 'Lamta',
                            8 => 'Lanji',
                            9 => 'Paraswada',
                            10 => 'Tirodi',
                            11 => 'Waraseoni',
                        ],
                    ],
                    404 =>
                    [
                        'name' => 'Dindori',
                        'childs' =>
                        [
                            0 => 'Bajag',
                            1 => 'Dindori',
                            2 => 'Shahpura',
                        ],
                    ],
                    425 =>
                    [
                        'name' => 'Sagar',
                        'childs' =>
                        [
                            0 => 'Banda',
                            1 => 'Bandari',
                            2 => 'Bina',
                            3 => 'Deori',
                            4 => 'Garhakota',
                            5 => 'Jaisinagar',
                            6 => 'Kesli',
                            7 => 'Khurai',
                            8 => 'Malthon',
                            9 => 'Rahatgarh',
                            10 => 'Rehli',
                            11 => 'Sagar',
                            12 => 'Sagar Nagar',
                            13 => 'Shahgarh',
                        ],
                    ],
                    436 =>
                    [
                        'name' => 'Umaria',
                        'childs' =>
                        [
                            0 => 'Bandhavgarh',
                            1 => 'Bilaspur',
                            2 => 'Chandia',
                            3 => 'Karkeli',
                            4 => 'Manpur',
                            5 => 'Nowrozabad',
                            6 => 'Pali',
                        ],
                    ],
                    409 =>
                    [
                        'name' => 'Narmadapuram',
                        'childs' =>
                        [
                            0 => 'Bankhedi',
                            1 => 'Dolariya',
                            2 => 'Hoshangabad',
                            3 => 'Hoshangabad Nagar',
                            4 => 'Itarsi',
                            5 => 'Makhan Nagar',
                            6 => 'Pipariya',
                            7 => 'Seoni Malwa',
                            8 => 'Sohagpur',
                        ],
                    ],
                    638 =>
                    [
                        'name' => 'Singrauli',
                        'childs' =>
                        [
                            0 => 'Bargawan',
                            1 => 'Chitrangi',
                            2 => 'Deosar',
                            3 => 'Dudhamania',
                            4 => 'Mada',
                            5 => 'Sarai',
                            6 => 'Singrauli',
                            7 => 'Singrauli Nagar',
                        ],
                    ],
                    428 =>
                    [
                        'name' => 'Seoni',
                        'childs' =>
                        [
                            0 => 'Barghat',
                            1 => 'Chhapara',
                            2 => 'Dhanora',
                            3 => 'Ghansaur',
                            4 => 'Keolari',
                            5 => 'Kurai',
                            6 => 'Lakhnadon',
                            7 => 'Seoni',
                            8 => 'Seoni Nagar',
                        ],
                    ],
                    401 =>
                    [
                        'name' => 'Datia',
                        'childs' =>
                        [
                            0 => 'Baroni',
                            1 => 'Bhander',
                            2 => 'Datia',
                            3 => 'Datia Nagar',
                            4 => 'Indergarh',
                            5 => 'Seondha',
                        ],
                    ],
                    414 =>
                    [
                        'name' => 'Khargone (West Nimar]',
                        'childs' =>
                        [
                            0 => 'Barwaha',
                            1 => 'Bhagwanpura',
                            2 => 'Bhikangaon',
                            3 => 'Gogaon',
                            4 => 'Jhiranya',
                            5 => 'Kasrawad',
                            6 => 'Khargone',
                            7 => 'Khargone Nagar',
                            8 => 'Maheshwar',
                            9 => 'Sanawad',
                            10 => 'Segaon',
                        ],
                    ],
                    437 =>
                    [
                        'name' => 'Vidisha',
                        'childs' =>
                        [
                            0 => 'Basoda',
                            1 => 'Gulabganj',
                            2 => 'Gyaraspur',
                            3 => 'Kurwai',
                            4 => 'Lateri',
                            5 => 'Nateran',
                            6 => 'Pathari',
                            7 => 'Shamshabad',
                            8 => 'Sironj',
                            9 => 'Tyonda',
                            10 => 'Vidisha',
                            11 => 'Vidisha Nagar',
                        ],
                    ],
                    400 =>
                    [
                        'name' => 'Damoh',
                        'childs' =>
                        [
                            0 => 'Batiyagarh',
                            1 => 'Damoh',
                            2 => 'Damyanti Nagar',
                            3 => 'Hatta',
                            4 => 'Jabera',
                            5 => 'Patera',
                            6 => 'Patharia',
                            7 => 'Tendukheda',
                        ],
                    ],
                    429 =>
                    [
                        'name' => 'Shahdol',
                        'childs' =>
                        [
                            0 => 'Beohari',
                            1 => 'Burhar',
                            2 => 'Gohparu',
                            3 => 'Jaisinghnagar',
                            4 => 'Jaitpur',
                            5 => 'Sohagpur',
                        ],
                    ],
                    396 =>
                    [
                        'name' => 'Bhopal',
                        'childs' =>
                        [
                            0 => 'Berasia',
                            1 => 'Huzur',
                            2 => 'Kolar',
                        ],
                    ],
                    416 =>
                    [
                        'name' => 'Mandsaur',
                        'childs' =>
                        [
                            0 => 'Bhanpura',
                            1 => 'Daloda',
                            2 => 'Garoth',
                            3 => 'Malhargarh',
                            4 => 'Mandsaur',
                            5 => 'Mandsaur Nagar',
                            6 => 'Shamgarh',
                            7 => 'Sitamau',
                            8 => 'Suwasara',
                        ],
                    ],
                    407 =>
                    [
                        'name' => 'Gwalior',
                        'childs' =>
                        [
                            0 => 'Bhitarwar',
                            1 => 'Chinour',
                            2 => 'City Center',
                            3 => 'Dabra',
                            4 => 'Ghatigaon',
                            5 => 'Gird',
                            6 => 'Gwalior Gramin',
                            7 => 'Morar',
                            8 => 'Pichhor',
                            9 => 'Tansen',
                        ],
                    ],
                    422 =>
                    [
                        'name' => 'Rajgarh',
                        'childs' =>
                        [
                            0 => 'Biaora',
                            1 => 'Jeerapur',
                            2 => 'Khilchipur',
                            3 => 'Khujner',
                            4 => 'Narsinghgarh',
                            5 => 'Pachore',
                            6 => 'Rajgarh',
                            7 => 'Sarangpur',
                            8 => 'Suthaliya',
                        ],
                    ],
                    415 =>
                    [
                        'name' => 'Mandla',
                        'childs' =>
                        [
                            0 => 'Bichhiya',
                            1 => 'Ghughari',
                            2 => 'Mandla',
                            3 => 'Nainpur',
                            4 => 'Narayanganj',
                            5 => 'Niwas',
                        ],
                    ],
                    410 =>
                    [
                        'name' => 'Indore',
                        'childs' =>
                        [
                            0 => 'Bicholi Hapsi',
                            1 => 'Depalpur',
                            2 => 'Dr. Ambedkar Nagar (Mhow]',
                            3 => 'Hatod',
                            4 => 'Indore',
                            5 => 'Kanadia',
                            6 => 'Khudel',
                            7 => 'Malharganj',
                            8 => 'Rau',
                            9 => 'Sanwer',
                        ],
                    ],
                    426 =>
                    [
                        'name' => 'Satna',
                        'childs' =>
                        [
                            0 => 'Birsinghpur',
                            1 => 'Kotar',
                            2 => 'Kothi',
                            3 => 'Majhgawan',
                            4 => 'Nagod',
                            5 => 'Raghurajnagar (Nagriya]',
                            6 => 'Rampur Baghelan',
                            7 => 'Unchehara',
                        ],
                    ],
                    397 =>
                    [
                        'name' => 'Burhanpur',
                        'childs' =>
                        [
                            0 => 'Burhanpur',
                            1 => 'Burhanpur Nagar',
                            2 => 'Dhulcot',
                            3 => 'Khaknar',
                            4 => 'Nepanagar',
                        ],
                    ],
                    405 =>
                    [
                        'name' => 'Khandwa (East Nimar]',
                        'childs' =>
                        [
                            0 => 'Chhaigaon Makhan',
                            1 => 'Harsud',
                            2 => 'Khalwa',
                            3 => 'Khandwa',
                            4 => 'Khandwa Nagar',
                            5 => 'Killod',
                            6 => 'Mundi',
                            7 => 'Pandhana',
                            8 => 'Punasa',
                        ],
                    ],
                    418 =>
                    [
                        'name' => 'Narsimhapur',
                        'childs' =>
                        [
                            0 => 'Gadarwara',
                            1 => 'Gotegaon',
                            2 => 'Kareli',
                            3 => 'Narsimhapur',
                            4 => 'Saikheda',
                            5 => 'Tendukheda',
                        ],
                    ],
                    424 =>
                    [
                        'name' => 'Rewa',
                        'childs' =>
                        [
                            0 => 'Gurh',
                            1 => 'Huzur',
                            2 => 'Huzur Nagar',
                            3 => 'Jawa',
                            4 => 'Mangawan',
                            5 => 'Raipur Karchuliyan',
                            6 => 'Semaria',
                            7 => 'Sirmour',
                            8 => 'Teonthar',
                        ],
                    ],
                    408 =>
                    [
                        'name' => 'Harda',
                        'childs' =>
                        [
                            0 => 'Handiya',
                            1 => 'Harda',
                            2 => 'Khirkiya',
                            3 => 'Rehatgaon',
                            4 => 'Sirali',
                            5 => 'Timarni',
                        ],
                    ],
                    766 =>
                    [
                        'name' => 'MAUGANJ',
                        'childs' =>
                        [
                            0 => 'Hanumana',
                            1 => 'Mauganj',
                            2 => 'Naigarhi',
                        ],
                    ],
                    419 =>
                    [
                        'name' => 'Neemuch',
                        'childs' =>
                        [
                            0 => 'Jawad',
                            1 => 'Jiran',
                            2 => 'Manasa',
                            3 => 'Neemuch',
                            4 => 'Neemuch Nagar',
                            5 => 'Rampura',
                            6 => 'Singoli',
                        ],
                    ],
                    412 =>
                    [
                        'name' => 'Jhabua',
                        'childs' =>
                        [
                            0 => 'Jhabua',
                            1 => 'Meghnagar',
                            2 => 'Petlawad',
                            3 => 'Rama',
                            4 => 'Ranapur',
                            5 => 'Thandla',
                        ],
                    ],
                    722 =>
                    [
                        'name' => 'Niwari',
                        'childs' =>
                        [
                            0 => 'Niwari',
                            1 => 'Orchha',
                            2 => 'Prithvipur',
                        ],
                    ],
                    785 =>
                    [
                        'name' => 'Pandhurna',
                        'childs' =>
                        [
                            0 => 'Pandhurna',
                            1 => 'Sausar',
                        ],
                    ],
                ],
            ],
            5 =>
            [
                'name' => 'Uttarakhand',
                'childs' =>
                [
                    47 =>
                    [
                        'name' => 'Chamoli',
                        'childs' =>
                        [
                            0 => 'Adibadri',
                            1 => 'Chamoli',
                            2 => 'Dewal',
                            3 => 'Gairsain',
                            4 => 'Ghaat',
                            5 => 'Jilasu',
                            6 => 'Joshimath',
                            7 => 'Karnaprayag',
                            8 => 'Nandprayag',
                            9 => 'Narayanbagar',
                            10 => 'Pokhari',
                            11 => 'Tharali',
                        ],
                    ],
                    45 =>
                    [
                        'name' => 'Almora',
                        'childs' =>
                        [
                            0 => 'Almora',
                            1 => 'Bagwalipokhar',
                            2 => 'Bhanoli',
                            3 => 'Bhikiasain',
                            4 => 'Chaukhutiya',
                            5 => 'Dhaulchhina',
                            6 => 'Dhyari',
                            7 => 'Dwarahat',
                            8 => 'Jainti',
                            9 => 'Jalali',
                            10 => 'Lamgada',
                            11 => 'Machhor',
                            12 => 'Ranikhet',
                            13 => 'Someshwar',
                            14 => 'Sult',
                            15 => 'Syalde',
                        ],
                    ],
                    46 =>
                    [
                        'name' => 'Bageshwar',
                        'childs' =>
                        [
                            0 => 'Bageshwar',
                            1 => 'Dugnakuri',
                            2 => 'Garud',
                            3 => 'Kafligair',
                            4 => 'Kanda',
                            5 => 'Kapkot',
                            6 => 'Shama',
                        ],
                    ],
                    56 =>
                    [
                        'name' => 'Udam Singh Nagar',
                        'childs' =>
                        [
                            0 => 'Bajpur',
                            1 => 'Gadarpur',
                            2 => 'Jaspur',
                            3 => 'Kashipur',
                            4 => 'Khatima',
                            5 => 'Kichha',
                            6 => 'Nanakmatta',
                            7 => 'Rudrapur',
                            8 => 'Sitarganj',
                        ],
                    ],
                    55 =>
                    [
                        'name' => 'Tehri Garhwal',
                        'childs' =>
                        [
                            0 => 'Balganga',
                            1 => 'Devprayag',
                            2 => 'Dhanaulti',
                            3 => 'Gaja',
                            4 => 'Ghansali',
                            5 => 'Jakhani Dhar',
                            6 => 'Kandisaur',
                            7 => 'Kirtinagar',
                            8 => 'Madannegi',
                            9 => 'Nainbag',
                            10 => 'Narendra Nagar',
                            11 => 'Pawkidevi',
                            12 => 'Pratapnagar',
                            13 => 'Tehri',
                        ],
                    ],
                    53 =>
                    [
                        'name' => 'Pithoragarh',
                        'childs' =>
                        [
                            0 => 'Bangapani',
                            1 => 'Berinag',
                            2 => 'Devalthal',
                            3 => 'Dharchula',
                            4 => 'Didihat',
                            5 => 'Ganai Gangoli',
                            6 => 'Gangolihat',
                            7 => 'Kanalichhina',
                            8 => 'Munsiari',
                            9 => 'Pankhu',
                            10 => 'Pithoragarh',
                            11 => 'Tejam',
                            12 => 'Thal',
                        ],
                    ],
                    48 =>
                    [
                        'name' => 'Champawat',
                        'childs' =>
                        [
                            0 => 'Barakot',
                            1 => 'Champawat',
                            2 => 'Lohaghat',
                            3 => 'Manch',
                            4 => 'Pati',
                            5 => 'Poornagiri',
                            6 => 'Pulla Gumdesh',
                        ],
                    ],
                    57 =>
                    [
                        'name' => 'Uttar Kashi',
                        'childs' =>
                        [
                            0 => 'Barnigad',
                            1 => 'Bhatwari',
                            2 => 'Chinyali Saur',
                            3 => 'Dhauntari',
                            4 => 'Dunda',
                            5 => 'Joshiyara',
                            6 => 'Mori',
                            7 => 'Puraula',
                            8 => 'Rajgarhi',
                            9 => 'Sankari',
                        ],
                    ],
                    54 =>
                    [
                        'name' => 'Rudra Prayag',
                        'childs' =>
                        [
                            0 => 'Basukedar',
                            1 => 'Jakholi*',
                            2 => 'Rudraprayag',
                            3 => 'Ukhimath',
                        ],
                    ],
                    51 =>
                    [
                        'name' => 'Nainital',
                        'childs' =>
                        [
                            0 => 'Betalghat',
                            1 => 'Dhari',
                            2 => 'Haldwani',
                            3 => 'Kaladhungi',
                            4 => 'Kosya Kutauli',
                            5 => 'Lalkuan',
                            6 => 'Nainital',
                            7 => 'Okhalkanda',
                            8 => 'Ramgarh',
                            9 => 'Ramnagar',
                        ],
                    ],
                    50 =>
                    [
                        'name' => 'Haridwar',
                        'childs' =>
                        [
                            0 => 'Bhagwanpur',
                            1 => 'Hardwar',
                            2 => 'Laksar',
                            3 => 'Laldhang',
                            4 => 'Narsan',
                            5 => 'Roorkee',
                        ],
                    ],
                    52 =>
                    [
                        'name' => 'Pauri Garhwal',
                        'childs' =>
                        [
                            0 => 'Bironkhal',
                            1 => 'Chakisain',
                            2 => 'Chaubatta Khal',
                            3 => 'Dhoomakot',
                            4 => 'Jakhanikhal',
                            5 => 'Kotdwara',
                            6 => 'Lansdowne',
                            7 => 'Pauri',
                            8 => 'Rikhanikhal',
                            9 => 'Satpuli',
                            10 => 'Srinagar',
                            11 => 'Thailisain',
                            12 => 'Yamkeshwar',
                        ],
                    ],
                    49 =>
                    [
                        'name' => 'Dehradun',
                        'childs' =>
                        [
                            0 => 'Chakrata',
                            1 => 'Dehradun',
                            2 => 'Doiwala',
                            3 => 'Kalsi',
                            4 => 'Mussoorie',
                            5 => 'Rishikesh',
                            6 => 'Tyuni',
                            7 => 'Vikas Nagar',
                        ],
                    ],
                ],
            ],
            20 =>
            [
                'name' => 'Jharkhand',
                'childs' =>
                [
                    341 =>
                    [
                        'name' => 'Saraikela Kharsawan',
                        'childs' =>
                        [
                            0 => 'Adityapur(Gamharia]',
                            1 => 'Chandil',
                            2 => 'Gobindpur(Rajnagar]',
                            3 => 'Ichagarh',
                            4 => 'Kharsawan',
                            5 => 'Kuchai',
                            6 => 'Kukru',
                            7 => 'Nimdih',
                            8 => 'Saraikela',
                        ],
                    ],
                    331 =>
                    [
                        'name' => 'Gumla',
                        'childs' =>
                        [
                            0 => 'Albert Ekka(Jari]',
                            1 => 'Basia',
                            2 => 'Bharno',
                            3 => 'Bishunpur',
                            4 => 'Chainpur',
                            5 => 'Dumri',
                            6 => 'Ghaghra',
                            7 => 'Gumla',
                            8 => 'Kamdara',
                            9 => 'Palkot',
                            10 => 'Raidih',
                            11 => 'Sisai',
                        ],
                    ],
                    337 =>
                    [
                        'name' => 'Pakur',
                        'childs' =>
                        [
                            0 => 'Amrapara',
                            1 => 'Hiranpur',
                            2 => 'Litipara',
                            3 => 'Maheshpur',
                            4 => 'Pakaur',
                            5 => 'Pakuria',
                        ],
                    ],
                    343 =>
                    [
                        'name' => 'West Singhbhum',
                        'childs' =>
                        [
                            0 => 'Anandpur',
                            1 => 'Bandgaon',
                            2 => 'Chaibasa',
                            3 => 'Chakradharpur',
                            4 => 'Goilkera',
                            5 => 'Gudri',
                            6 => 'Hat Gamharia',
                            7 => 'Jagannathpur',
                            8 => 'Jhinkpani',
                            9 => 'Khuntpani',
                            10 => 'Kumardungi',
                            11 => 'Majhgaon',
                            12 => 'Manjhari',
                            13 => 'Manoharpur',
                            14 => 'Noamundi',
                            15 => 'Sonua',
                            16 => 'Tantnagar',
                            17 => 'Tonto',
                        ],
                    ],
                    339 =>
                    [
                        'name' => 'Ranchi',
                        'childs' =>
                        [
                            0 => 'Angara',
                            1 => 'Bero',
                            2 => 'Bundu',
                            3 => 'Burmu',
                            4 => 'Chanho',
                            5 => 'Itki',
                            6 => 'Kanke',
                            7 => 'Khelari',
                            8 => 'Lapung',
                            9 => 'Mandar',
                            10 => 'Nagri',
                            11 => 'Namkum',
                            12 => 'Ormanjhi',
                            13 => 'Rahe',
                            14 => 'Ratu',
                            15 => 'Silli',
                            16 => 'Sonahatu',
                            17 => 'Tamar I',
                        ],
                    ],
                    325 =>
                    [
                        'name' => 'Dhanbad',
                        'childs' =>
                        [
                            0 => 'Baghmara-Cum-Katras',
                            1 => 'Baliapur',
                            2 => 'Dhanbad-Cum-Kenduadih-Cum-Jagata',
                            3 => 'Egarkund',
                            4 => 'Gobindpur',
                            5 => 'Jharia-Cum-Jorapokhar-Cum-Sindri',
                            6 => 'Kariasol',
                            7 => 'Nirsa-Cum-Chirkunda',
                            8 => 'Purbi Tundi*',
                            9 => 'Topchanchi',
                            10 => 'Tundi',
                        ],
                    ],
                    329 =>
                    [
                        'name' => 'Giridih',
                        'childs' =>
                        [
                            0 => 'Bagodar',
                            1 => 'Bengabad',
                            2 => 'Birni',
                            3 => 'Deori',
                            4 => 'Dhanwar',
                            5 => 'Dumri',
                            6 => 'Gandey',
                            7 => 'Gawan',
                            8 => 'Giridih',
                            9 => 'Jamua',
                            10 => 'Pirtanr',
                            11 => 'Suriya',
                            12 => 'Tisri',
                        ],
                    ],
                    327 =>
                    [
                        'name' => 'East Singhbum',
                        'childs' =>
                        [
                            0 => 'Baharagora',
                            1 => 'Boram',
                            2 => 'Chakulia',
                            3 => 'Dhalbhumgarh',
                            4 => 'Dumaria',
                            5 => 'Ghatshila',
                            6 => 'Golmuri-Cum-Jugsalai',
                            7 => 'Gurbandha',
                            8 => 'Musabani',
                            9 => 'Patamda',
                            10 => 'Potka',
                        ],
                    ],
                    335 =>
                    [
                        'name' => 'Latehar',
                        'childs' =>
                        [
                            0 => 'Balumath',
                            1 => 'Bariyatu',
                            2 => 'Barwadih',
                            3 => 'Chandwa',
                            4 => 'Garu',
                            5 => 'Herhanj',
                            6 => 'Latehar',
                            7 => 'Mahuadanr',
                            8 => 'Manika',
                        ],
                    ],
                    342 =>
                    [
                        'name' => 'Simdega',
                        'childs' =>
                        [
                            0 => 'Bano',
                            1 => 'Bansjor',
                            2 => 'Bolba',
                            3 => 'Jaldega',
                            4 => 'Kersai',
                            5 => 'Kolebira',
                            6 => 'Kurdeg',
                            7 => 'Pakar Tanr',
                            8 => 'Simdega',
                            9 => 'Thethaitangar',
                        ],
                    ],
                    328 =>
                    [
                        'name' => 'Garhwa',
                        'childs' =>
                        [
                            0 => 'Bardiha',
                            1 => 'Bargarh',
                            2 => 'Bhandaria',
                            3 => 'Bhawnathpur',
                            4 => 'Bishunpura',
                            5 => 'Chinia',
                            6 => 'Danda',
                            7 => 'Dandai',
                            8 => 'Dhurki',
                            9 => 'Garhwa',
                            10 => 'Kandi',
                            11 => 'Ketar*',
                            12 => 'Kharaundhi',
                            13 => 'Majhiaon',
                            14 => 'Meral (Pipra Kalan]',
                            15 => 'Nagaruntari',
                            16 => 'Ramkanda',
                            17 => 'Ramna',
                            18 => 'Ranka',
                            19 => 'Sagma',
                        ],
                    ],
                    340 =>
                    [
                        'name' => 'Sahebganj',
                        'childs' =>
                        [
                            0 => 'Barhait',
                            1 => 'Barharwa',
                            2 => 'Borio',
                            3 => 'Mandro',
                            4 => 'Pathna',
                            5 => 'Rajmahal',
                            6 => 'Sahibganj',
                            7 => 'Taljhari',
                            8 => 'Udhwa',
                        ],
                    ],
                    332 =>
                    [
                        'name' => 'Hazaribagh',
                        'childs' =>
                        [
                            0 => 'Barhi',
                            1 => 'Barkagaon',
                            2 => 'Barkatha',
                            3 => 'Bishungarh',
                            4 => 'Chalkusa',
                            5 => 'Chauparan',
                            6 => 'Churchu',
                            7 => 'Dadi',
                            8 => 'Daru',
                            9 => 'Hazaribag',
                            10 => 'Ichak',
                            11 => 'Katamdag',
                            12 => 'Katkamsandi',
                            13 => 'Keredari',
                            14 => 'Padma',
                            15 => 'Tati Jhariya',
                        ],
                    ],
                    330 =>
                    [
                        'name' => 'Godda',
                        'childs' =>
                        [
                            0 => 'Bashant Rai*',
                            1 => 'Boarijor',
                            2 => 'Godda',
                            3 => 'Mahagama',
                            4 => 'Meherma',
                            5 => 'Pathargama',
                            6 => 'Poreyahat',
                            7 => 'Sundarpahari',
                            8 => 'Thakurgangti',
                        ],
                    ],
                    322 =>
                    [
                        'name' => 'Bokaro',
                        'childs' =>
                        [
                            0 => 'Bermo',
                            1 => 'Chandankiyari',
                            2 => 'Chandrapura',
                            3 => 'Chas',
                            4 => 'Gomia',
                            5 => 'Jaridih',
                            6 => 'Kasmar',
                            7 => 'Nawadih',
                            8 => 'Peterwar',
                        ],
                    ],
                    336 =>
                    [
                        'name' => 'Lohardaga',
                        'childs' =>
                        [
                            0 => 'Bhandra',
                            1 => 'Kairo',
                            2 => 'Kisko',
                            3 => 'Kuru',
                            4 => 'Lohardaga',
                            5 => 'Peshrar',
                            6 => 'Senha',
                        ],
                    ],
                    338 =>
                    [
                        'name' => 'Palamu',
                        'childs' =>
                        [
                            0 => 'Bishrampur',
                            1 => 'Chainpur',
                            2 => 'Chhatarpur',
                            3 => 'Haidernagar',
                            4 => 'Hariharganj',
                            5 => 'Hussainabad',
                            6 => 'Manatu',
                            7 => 'Medininagar(Daltonganj]',
                            8 => 'Mohammad Ganj',
                            9 => 'Nawa Bazar',
                            10 => 'Nawadiha Bazar/Nawadiha*',
                            11 => 'Nilambar-Pitambarpur(Lesliganj]',
                            12 => 'Padwa',
                            13 => 'Pandu',
                            14 => 'Panki',
                            15 => 'Patan',
                            16 => 'Pipra',
                            17 => 'Ramgarh',
                            18 => 'Satbarwa',
                            19 => 'Tarhasi',
                            20 => 'Untari Road',
                        ],
                    ],
                    334 =>
                    [
                        'name' => 'Koderma',
                        'childs' =>
                        [
                            0 => 'Chandwara',
                            1 => 'Domchanch',
                            2 => 'Jainagar',
                            3 => 'Koderma',
                            4 => 'Markacho',
                            5 => 'Satgawan',
                        ],
                    ],
                    323 =>
                    [
                        'name' => 'Chatra',
                        'childs' =>
                        [
                            0 => 'Chatra',
                            1 => 'Gidhaur',
                            2 => 'Itkhori',
                            3 => 'Kanha Chatti',
                            4 => 'Kunda',
                            5 => 'Lawalaung',
                            6 => 'Mayur Hand',
                            7 => 'Pathalgora',
                            8 => 'Pratappur',
                            9 => 'Shaligram Ram Narayanpur(Hunterganj]',
                            10 => 'Simaria',
                            11 => 'Tandwa',
                        ],
                    ],
                    607 =>
                    [
                        'name' => 'Ramgarh',
                        'childs' =>
                        [
                            0 => 'Chitarpur',
                            1 => 'Dulmi',
                            2 => 'Gola',
                            3 => 'Mandu',
                            4 => 'Patratu',
                            5 => 'Ramgarh',
                        ],
                    ],
                    324 =>
                    [
                        'name' => 'Deoghar',
                        'childs' =>
                        [
                            0 => 'Deoghar',
                            1 => 'Devipur',
                            2 => 'Karon',
                            3 => 'Madhupur',
                            4 => 'Margo Munda',
                            5 => 'Mohanpur',
                            6 => 'Palojori',
                            7 => 'Sarath',
                            8 => 'Sarwan',
                            9 => 'Sona Rai Tharhi',
                        ],
                    ],
                    326 =>
                    [
                        'name' => 'Dumka',
                        'childs' =>
                        [
                            0 => 'Dumka',
                            1 => 'Gopikandar',
                            2 => 'Jama',
                            3 => 'Jarmundi',
                            4 => 'Kathikund',
                            5 => 'Masalia',
                            6 => 'Ramgarh',
                            7 => 'Ranishwar',
                            8 => 'Saraiyahat',
                            9 => 'Shikaripara',
                        ],
                    ],
                    606 =>
                    [
                        'name' => 'Khunti',
                        'childs' =>
                        [
                            0 => 'Erki(Tamar Ii]',
                            1 => 'Karra',
                            2 => 'Khunti',
                            3 => 'Murhu',
                            4 => 'Rania',
                            5 => 'Torpa',
                        ],
                    ],
                    333 =>
                    [
                        'name' => 'Jamtara',
                        'childs' =>
                        [
                            0 => 'Fatehpur',
                            1 => 'Jamtara',
                            2 => 'Karma Tanr Vidyasagar*',
                            3 => 'Kundhit',
                            4 => 'Nala',
                            5 => 'Narayanpur',
                        ],
                    ],
                ],
            ],
            17 =>
            [
                'name' => 'Meghalaya',
                'childs' =>
                [
                    656 =>
                    [
                        'name' => 'North Garo Hills',
                        'childs' =>
                        [
                            0 => 'Adokgre',
                            1 => 'Bajengdoba',
                            2 => 'Kharkutta',
                            3 => 'Resubelpara',
                        ],
                    ],
                    275 =>
                    [
                        'name' => 'West Jaintia Hills',
                        'childs' =>
                        [
                            0 => 'Amlarem',
                            1 => 'Laskein',
                            2 => 'Namdong',
                            3 => 'Thadlaskein',
                        ],
                    ],
                    277 =>
                    [
                        'name' => 'South Garo Hills',
                        'childs' =>
                        [
                            0 => 'Baghmara',
                            1 => 'Chokpot',
                            2 => 'Gasuapara',
                            3 => 'Rongara',
                            4 => 'Siju',
                        ],
                    ],
                    278 =>
                    [
                        'name' => 'West Garo Hills',
                        'childs' =>
                        [
                            0 => 'Batabari',
                            1 => 'Dadenggiri',
                            2 => 'Dalu',
                            3 => 'Demdema',
                            4 => 'Gambegre',
                            5 => 'Rongram',
                            6 => 'Selsella',
                            7 => 'Tikrikilla',
                        ],
                    ],
                    663 =>
                    [
                        'name' => 'South West Garo Hills',
                        'childs' =>
                        [
                            0 => 'Betasing',
                            1 => 'Purakhasia',
                            2 => 'Rerapara',
                            3 => 'Zikzak',
                        ],
                    ],
                    276 =>
                    [
                        'name' => 'Ri Bhoi',
                        'childs' =>
                        [
                            0 => 'Bhoirymbong',
                            1 => 'Jirang',
                            2 => 'Umling',
                            3 => 'Umsning',
                        ],
                    ],
                    273 =>
                    [
                        'name' => 'East Garo Hills',
                        'childs' =>
                        [
                            0 => 'Dambo Rongjeng',
                            1 => 'Samanda',
                            2 => 'Songsak',
                        ],
                    ],
                    274 =>
                    [
                        'name' => 'East Khasi Hills',
                        'childs' =>
                        [
                            0 => 'Khatarshnong Laitkroh',
                            1 => 'Mawkynrew',
                            2 => 'Mawlai',
                            3 => 'Mawpat',
                            4 => 'Mawphlang',
                            5 => 'Mawryngkneng',
                            6 => 'Mawsynram',
                            7 => 'Mylliem',
                            8 => 'Pynursla',
                            9 => 'Shella Bholaganj',
                            10 => 'Sohiong',
                        ],
                    ],
                    657 =>
                    [
                        'name' => 'East Jaintia Hills',
                        'childs' =>
                        [
                            0 => 'Lumshnong',
                            1 => 'Saipung',
                            2 => 'Wapung',
                        ],
                    ],
                    740 =>
                    [
                        'name' => 'Eastern West Khasi Hills',
                        'childs' =>
                        [
                            0 => 'Mairang',
                            1 => 'Mawthadraishan',
                        ],
                    ],
                    658 =>
                    [
                        'name' => 'South West Khasi Hills',
                        'childs' =>
                        [
                            0 => 'Mawkyrwat',
                            1 => 'Ranikor',
                        ],
                    ],
                    279 =>
                    [
                        'name' => 'West Khasi Hills',
                        'childs' =>
                        [
                            0 => 'Mawshynrut',
                            1 => 'Nongstoin',
                            2 => 'Rambrai',
                            3 => 'Ri Muliang',
                            4 => 'Shallang',
                        ],
                    ],
                ],
            ],
            32 =>
            [
                'name' => 'Kerala',
                'childs' =>
                [
                    564 =>
                    [
                        'name' => 'Pathanamthitta',
                        'childs' =>
                        [
                            0 => 'Adoor',
                            1 => 'Konni',
                            2 => 'Kozhenchery',
                            3 => 'Mallappally',
                            4 => 'Ranni',
                            5 => 'Thiruvalla',
                        ],
                    ],
                    563 =>
                    [
                        'name' => 'Palakkad',
                        'childs' =>
                        [
                            0 => 'Alathur',
                            1 => 'Chittur',
                            2 => 'Mannarkad',
                            3 => 'Ottappalam',
                            4 => 'Palakkad',
                            5 => 'Pattambi',
                            6 => 'Tribal Taluk Office Attappadi',
                        ],
                    ],
                    555 =>
                    [
                        'name' => 'Ernakulam',
                        'childs' =>
                        [
                            0 => 'Aluva',
                            1 => 'Kanayannur',
                            2 => 'Kochi',
                            3 => 'Kothamangalam',
                            4 => 'Kunnathunad',
                            5 => 'Muvattupuzha',
                            6 => 'Paravur',
                        ],
                    ],
                    554 =>
                    [
                        'name' => 'Alappuzha',
                        'childs' =>
                        [
                            0 => 'Ambalappuzha',
                            1 => 'Chengannur',
                            2 => 'Cherthala',
                            3 => 'Karthikappally',
                            4 => 'Kuttanad',
                            5 => 'Mavelikkara',
                        ],
                    ],
                    566 =>
                    [
                        'name' => 'Thrissur',
                        'childs' =>
                        [
                            0 => 'Chalakkudy',
                            1 => 'Chavakkad',
                            2 => 'Kodungallur',
                            3 => 'Kunnamkulam',
                            4 => 'Mukundapuram',
                            5 => 'Talappilly',
                            6 => 'Thrissur',
                        ],
                    ],
                    560 =>
                    [
                        'name' => 'Kottayam',
                        'childs' =>
                        [
                            0 => 'Changanassery',
                            1 => 'Kanjirappally',
                            2 => 'Kottayam',
                            3 => 'Meenachil',
                            4 => 'Vaikom',
                        ],
                    ],
                    565 =>
                    [
                        'name' => 'Thiruvananthapuram',
                        'childs' =>
                        [
                            0 => 'Chirayinkeezhu',
                            1 => 'Kattakkada',
                            2 => 'Nedumangad',
                            3 => 'Neyyattinkara',
                            4 => 'Thiruvananthapuram',
                            5 => 'Varkala',
                        ],
                    ],
                    556 =>
                    [
                        'name' => 'Idukki',
                        'childs' =>
                        [
                            0 => 'Devikulam',
                            1 => 'Idukki',
                            2 => 'Peerumade',
                            3 => 'Thodupuzha',
                            4 => 'Udumbanchola',
                        ],
                    ],
                    562 =>
                    [
                        'name' => 'Malappuram',
                        'childs' =>
                        [
                            0 => 'Ernad',
                            1 => 'Kondotty',
                            2 => 'Nilambur',
                            3 => 'Perinthalmanna',
                            4 => 'Ponnani',
                            5 => 'Tirur',
                            6 => 'Tirurangadi',
                        ],
                    ],
                    558 =>
                    [
                        'name' => 'Kasaragod',
                        'childs' =>
                        [
                            0 => 'Hosdurg',
                            1 => 'Kasaragod',
                            2 => 'Manjeswaram',
                            3 => 'Vellarikkundu',
                        ],
                    ],
                    557 =>
                    [
                        'name' => 'Kannur',
                        'childs' =>
                        [
                            0 => 'Iritty',
                            1 => 'Kannur',
                            2 => 'Payyannur',
                            3 => 'Taliparamba',
                            4 => 'Thalassery',
                        ],
                    ],
                    559 =>
                    [
                        'name' => 'Kollam',
                        'childs' =>
                        [
                            0 => 'Karunagappally',
                            1 => 'Kollam',
                            2 => 'Kottarakkara',
                            3 => 'Kunnathur',
                            4 => 'Pathanapuram',
                            5 => 'Punalur',
                        ],
                    ],
                    561 =>
                    [
                        'name' => 'Kozhikode',
                        'childs' =>
                        [
                            0 => 'Koyilandi',
                            1 => 'Kozhikode',
                            2 => 'Thamarassery',
                            3 => 'Vatakara',
                        ],
                    ],
                    567 =>
                    [
                        'name' => 'Wayanad',
                        'childs' =>
                        [
                            0 => 'Mananthavady',
                            1 => 'Sulthanbathery',
                            2 => 'Vythiri',
                        ],
                    ],
                ],
            ],
            29 =>
            [
                'name' => 'Karnataka',
                'childs' =>
                [
                    538 =>
                    [
                        'name' => 'Kalaburagi',
                        'childs' =>
                        [
                            0 => 'Afzalpur',
                            1 => 'Aland',
                            2 => 'Chincholi',
                            3 => 'Chittapur',
                            4 => 'Jevargi',
                            5 => 'Kalaburagi',
                            6 => 'Kalagi',
                            7 => 'Kamalapur',
                            8 => 'Sedam',
                            9 => 'Shahabad',
                            10 => 'Yadrami',
                        ],
                    ],
                    532 =>
                    [
                        'name' => 'Chikkamagaluru',
                        'childs' =>
                        [
                            0 => 'Ajjampura',
                            1 => 'Chikkamagaluru',
                            2 => 'Kadur',
                            3 => 'Kalasa',
                            4 => 'Koppa',
                            5 => 'Mudigere',
                            6 => 'Narasimharajapura',
                            7 => 'Sringeri',
                            8 => 'Tarikere',
                        ],
                    ],
                    530 =>
                    [
                        'name' => 'Vijayapura',
                        'childs' =>
                        [
                            0 => 'Almel',
                            1 => 'Babaleshwar',
                            2 => 'Basavana Bagevadi',
                            3 => 'Chadchan',
                            4 => 'Devara Hipparagi',
                            5 => 'Indi',
                            6 => 'Kolhar',
                            7 => 'Muddebihal',
                            8 => 'Nidagundi',
                            9 => 'Sindgi',
                            10 => 'Talikoti',
                            11 => 'Tikota',
                            12 => 'Vijayapura',
                        ],
                    ],
                    536 =>
                    [
                        'name' => 'Dharwad',
                        'childs' =>
                        [
                            0 => 'Alnavar',
                            1 => 'Annigeri',
                            2 => 'Dharwad',
                            3 => 'Hubballi',
                            4 => 'Hubballi Urban',
                            5 => 'Kalghatgi',
                            6 => 'Kundgol',
                            7 => 'Navalgund',
                        ],
                    ],
                    539 =>
                    [
                        'name' => 'Hassan',
                        'childs' =>
                        [
                            0 => 'Alur',
                            1 => 'Arkalgud',
                            2 => 'Arsikere',
                            3 => 'Belur',
                            4 => 'Channarayapatna',
                            5 => 'Hassan',
                            6 => 'Hole Narsipur',
                            7 => 'Sakleshpur',
                        ],
                    ],
                    525 =>
                    [
                        'name' => 'Bengaluru Urban',
                        'childs' =>
                        [
                            0 => 'Anekal',
                            1 => 'Bengaluru East',
                            2 => 'Bengaluru North',
                            3 => 'Bengaluru South',
                            4 => 'Yelahanka',
                        ],
                    ],
                    550 =>
                    [
                        'name' => 'Uttara Kannada',
                        'childs' =>
                        [
                            0 => 'Ankola',
                            1 => 'Bhatkal',
                            2 => 'Dandeli',
                            3 => 'Haliyal',
                            4 => 'Honavar',
                            5 => 'Karwar',
                            6 => 'Kumta',
                            7 => 'Mundgod',
                            8 => 'Siddapur',
                            9 => 'Sirsi',
                            10 => 'Supa',
                            11 => 'Yellapur',
                        ],
                    ],
                    546 =>
                    [
                        'name' => 'Raichur',
                        'childs' =>
                        [
                            0 => 'Arakera',
                            1 => 'Devadurga',
                            2 => 'Lingasugur',
                            3 => 'Manvi',
                            4 => 'Maski',
                            5 => 'Raichur',
                            6 => 'Sindhnur',
                            7 => 'Sirwar',
                        ],
                    ],
                    527 =>
                    [
                        'name' => 'Belagavi',
                        'childs' =>
                        [
                            0 => 'Athni',
                            1 => 'Bailahongala',
                            2 => 'Belagavi',
                            3 => 'Chikodi',
                            4 => 'Gokak',
                            5 => 'Hukkeri',
                            6 => 'Kagwad',
                            7 => 'Khanapur',
                            8 => 'Kittur',
                            9 => 'Mudalgi',
                            10 => 'Nippani',
                            11 => 'Ramdurg',
                            12 => 'Raybag',
                            13 => 'Savadatti',
                            14 => 'Yaragatti',
                        ],
                    ],
                    529 =>
                    [
                        'name' => 'Bidar',
                        'childs' =>
                        [
                            0 => 'Aurad',
                            1 => 'Basavakalyan',
                            2 => 'Bhalki',
                            3 => 'Bidar',
                            4 => 'Chitaguppa',
                            5 => 'Homnabad',
                            6 => 'Hulsoor',
                            7 => 'Kamalnagar',
                        ],
                    ],
                    524 =>
                    [
                        'name' => 'Bagalkote',
                        'childs' =>
                        [
                            0 => 'Badami',
                            1 => 'Bagalkot',
                            2 => 'Bilgi',
                            3 => 'Guledgudda',
                            4 => 'Hungund',
                            5 => 'Ilkal',
                            6 => 'Jamkhandi',
                            7 => 'Mudhol',
                            8 => 'Rabakavi Banahatti',
                            9 => 'Terdal',
                        ],
                    ],
                    630 =>
                    [
                        'name' => 'Chikkaballapura',
                        'childs' =>
                        [
                            0 => 'Bagepalli',
                            1 => 'Chelur',
                            2 => 'Chikkaballapura',
                            3 => 'Chintamani',
                            4 => 'Gauribidanur',
                            5 => 'Gudibanda',
                            6 => 'Manchenahalli',
                            7 => 'Sidlaghatta',
                        ],
                    ],
                    549 =>
                    [
                        'name' => 'Udupi',
                        'childs' =>
                        [
                            0 => 'Bainduru',
                            1 => 'Brahmavara',
                            2 => 'Hebri',
                            3 => 'Kapu',
                            4 => 'Karkal',
                            5 => 'Kundapura',
                            6 => 'Udupi',
                        ],
                    ],
                    528 =>
                    [
                        'name' => 'Ballari',
                        'childs' =>
                        [
                            0 => 'Ballari',
                            1 => 'Kampli',
                            2 => 'Kurugodu',
                            3 => 'Sandur',
                            4 => 'Siruguppa',
                        ],
                    ],
                    542 =>
                    [
                        'name' => 'Kolar',
                        'childs' =>
                        [
                            0 => 'Bangarapet',
                            1 => 'Kolar',
                            2 => 'Kolar Gold Field',
                            3 => 'Malur',
                            4 => 'Mulbagal',
                            5 => 'Srinivaspur',
                        ],
                    ],
                    534 =>
                    [
                        'name' => 'Dakshina Kannada',
                        'childs' =>
                        [
                            0 => 'Bantval',
                            1 => 'Beltangadi',
                            2 => 'Kadaba',
                            3 => 'Mangaluru',
                            4 => 'Moodubidire',
                            5 => 'Mulki',
                            6 => 'Puttur',
                            7 => 'Sulya',
                            8 => 'Ullala',
                        ],
                    ],
                    547 =>
                    [
                        'name' => 'Shivamogga',
                        'childs' =>
                        [
                            0 => 'Bhadravati',
                            1 => 'Hosanagara',
                            2 => 'Sagar',
                            3 => 'Shikarpur',
                            4 => 'Shivamogga',
                            5 => 'Sorab',
                            6 => 'Tirthahalli',
                        ],
                    ],
                    540 =>
                    [
                        'name' => 'Haveri',
                        'childs' =>
                        [
                            0 => 'Byadgi',
                            1 => 'Hangal',
                            2 => 'Haveri',
                            3 => 'Hirekerur',
                            4 => 'Ranibennur',
                            5 => 'Rattihalli',
                            6 => 'Savanur',
                            7 => 'Shiggaon',
                        ],
                    ],
                    533 =>
                    [
                        'name' => 'Chitradurga',
                        'childs' =>
                        [
                            0 => 'Challakere',
                            1 => 'Chitradurga',
                            2 => 'Hiriyur',
                            3 => 'Holalkere',
                            4 => 'Hosdurga',
                            5 => 'Molakalmuru',
                        ],
                    ],
                    531 =>
                    [
                        'name' => 'Chamarajanagar',
                        'childs' =>
                        [
                            0 => 'Chamarajanagar',
                            1 => 'Gundlupet',
                            2 => 'Hanur',
                            3 => 'Kollegal',
                            4 => 'Yelandur',
                        ],
                    ],
                    535 =>
                    [
                        'name' => 'Davanagere',
                        'childs' =>
                        [
                            0 => 'Channagiri',
                            1 => 'Davanagere',
                            2 => 'Harihar',
                            3 => 'Honnali',
                            4 => 'Jagalur',
                            5 => 'Nyamathi',
                        ],
                    ],
                    631 =>
                    [
                        'name' => 'Ramanagara',
                        'childs' =>
                        [
                            0 => 'Channapatna',
                            1 => 'Harohalli',
                            2 => 'Kanakapura',
                            3 => 'Magadi',
                            4 => 'Ramanagara',
                        ],
                    ],
                    548 =>
                    [
                        'name' => 'Tumakuru',
                        'childs' =>
                        [
                            0 => 'Chiknayakanhalli',
                            1 => 'Gubbi',
                            2 => 'Koratagere',
                            3 => 'Kunigal',
                            4 => 'Madhugiri',
                            5 => 'Pavagada',
                            6 => 'Sira',
                            7 => 'Tiptur',
                            8 => 'Tumakuru',
                            9 => 'Turuvekere',
                        ],
                    ],
                    526 =>
                    [
                        'name' => 'Bengaluru Rural',
                        'childs' =>
                        [
                            0 => 'Devanahalli',
                            1 => 'Doddaballapura',
                            2 => 'Hosakote',
                            3 => 'Nelamangala',
                        ],
                    ],
                    537 =>
                    [
                        'name' => 'Gadag',
                        'childs' =>
                        [
                            0 => 'Gadag',
                            1 => 'Gajendragad',
                            2 => 'Laxmeshwar',
                            3 => 'Mundargi',
                            4 => 'Nargund',
                            5 => 'Ron',
                            6 => 'Shirahatti',
                        ],
                    ],
                    543 =>
                    [
                        'name' => 'Koppal',
                        'childs' =>
                        [
                            0 => 'Gangawati',
                            1 => 'Kanakagiri',
                            2 => 'Karatagi',
                            3 => 'Koppal',
                            4 => 'Kukanoor',
                            5 => 'Kushtagi',
                            6 => 'Yelbarga',
                        ],
                    ],
                    635 =>
                    [
                        'name' => 'Yadgir',
                        'childs' =>
                        [
                            0 => 'Gurmitakal',
                            1 => 'Hunasagi',
                            2 => 'Shahpur',
                            3 => 'Shorapur',
                            4 => 'Wadagera',
                            5 => 'Yadgir',
                        ],
                    ],
                    738 =>
                    [
                        'name' => 'Vijayanagara',
                        'childs' =>
                        [
                            0 => 'Hadagalli',
                            1 => 'Hagaribommanahalli',
                            2 => 'Harapanahalli',
                            3 => 'Hosapete',
                            4 => 'Kotturu',
                            5 => 'Kudligi',
                        ],
                    ],
                    545 =>
                    [
                        'name' => 'Mysuru',
                        'childs' =>
                        [
                            0 => 'Heggadadevankote',
                            1 => 'Hunsur',
                            2 => 'Krishnarajanagara',
                            3 => 'Mysuru',
                            4 => 'Nanjangud',
                            5 => 'Piriyapatna',
                            6 => 'Saligrama',
                            7 => 'Saraguru',
                            8 => 'Tirumakudal - Narsipur',
                        ],
                    ],
                    544 =>
                    [
                        'name' => 'Mandya',
                        'childs' =>
                        [
                            0 => 'Krishnarajpet',
                            1 => 'Maddur',
                            2 => 'Malavalli',
                            3 => 'Mandya',
                            4 => 'Nagamangala',
                            5 => 'Pandavapura',
                            6 => 'Shrirangapattana',
                        ],
                    ],
                    541 =>
                    [
                        'name' => 'Kodagu',
                        'childs' =>
                        [
                            0 => 'Kushalnagar',
                            1 => 'Madikeri',
                            2 => 'Ponnampet',
                            3 => 'Somvarpet',
                            4 => 'Virajpet',
                        ],
                    ],
                ],
            ],
            18 =>
            [
                'name' => 'Assam',
                'childs' =>
                [
                    285 =>
                    [
                        'name' => 'Dhubri',
                        'childs' =>
                        [
                            0 => 'Agamoni',
                            1 => 'Athani',
                            2 => 'Bilasipara Pt',
                            3 => 'Chapar Pt',
                            4 => 'Dhubri',
                            5 => 'Gauripur',
                            6 => 'Golakganj Pt',
                            7 => 'South Salmara Pt',
                        ],
                    ],
                    289 =>
                    [
                        'name' => 'Hailakandi',
                        'childs' =>
                        [
                            0 => 'Algapur',
                            1 => 'Hailakandi',
                            2 => 'Katlichara',
                            3 => 'Lala',
                        ],
                    ],
                    300 =>
                    [
                        'name' => 'Sivasagar',
                        'childs' =>
                        [
                            0 => 'Amguri',
                            1 => 'Dimow',
                            2 => 'Nazira',
                            3 => 'Sibsagar',
                        ],
                    ],
                    618 =>
                    [
                        'name' => 'Kamrup Metro',
                        'childs' =>
                        [
                            0 => 'Azara',
                            1 => 'Chandrapur',
                            2 => 'Dispur',
                            3 => 'Guwahati',
                            4 => 'Sonapur',
                        ],
                    ],
                    293 =>
                    [
                        'name' => 'Karimganj',
                        'childs' =>
                        [
                            0 => 'Badarpur',
                            1 => 'Karimganj',
                            2 => 'Nilambazar',
                            3 => 'Patharkandi',
                            4 => 'Ramkrishna Nagar',
                        ],
                    ],
                    616 =>
                    [
                        'name' => 'Baksa',
                        'childs' =>
                        [
                            0 => 'Baganpara (Pt]',
                            1 => 'Barama Pt',
                            2 => 'Baska',
                            3 => 'Jalah (Pt]',
                        ],
                    ],
                    280 =>
                    [
                        'name' => 'Barpeta',
                        'childs' =>
                        [
                            0 => 'Baghbor',
                            1 => 'Barnagar (Pt]',
                            2 => 'Barpeta',
                            3 => 'Chenga',
                            4 => 'Kalgachia',
                            5 => 'Sarthebari',
                        ],
                    ],
                    294 =>
                    [
                        'name' => 'Kokrajhar',
                        'childs' =>
                        [
                            0 => 'Bagribari (Pt]',
                            1 => 'Bhowraguri',
                            2 => 'Dotoma',
                            3 => 'Gossaigaon (Pt]',
                            4 => 'Kokrajhar',
                        ],
                    ],
                    739 =>
                    [
                        'name' => 'Bajali',
                        'childs' =>
                        [
                            0 => 'Bajali (Pt]',
                            1 => 'Sarupeta (Pt]',
                        ],
                    ],
                    287 =>
                    [
                        'name' => 'Goalpara',
                        'childs' =>
                        [
                            0 => 'Balijana',
                            1 => 'Dudhnai',
                            2 => 'Lakhipur',
                            3 => 'Matia',
                            4 => 'Rangjuli',
                        ],
                    ],
                    298 =>
                    [
                        'name' => 'Nalbari',
                        'childs' =>
                        [
                            0 => 'Banekuchi',
                            1 => 'Barbhag',
                            2 => 'Barkhetri',
                            3 => 'Ghograpar (Pt]',
                            4 => 'Nalbari',
                            5 => 'Pachim Nalbari',
                            6 => 'Tihu (Pt]',
                        ],
                    ],
                    612 =>
                    [
                        'name' => 'Chirang',
                        'childs' =>
                        [
                            0 => 'Bengtol',
                            1 => 'Bijni (Pt]',
                            2 => 'Sidli (Pt]',
                        ],
                    ],
                    296 =>
                    [
                        'name' => 'Marigaon',
                        'childs' =>
                        [
                            0 => 'Bhuragaon',
                            1 => 'Laharighat',
                            2 => 'Marigaon',
                            3 => 'Mayong',
                            4 => 'Mikirbheta',
                        ],
                    ],
                    295 =>
                    [
                        'name' => 'Lakhimpur',
                        'childs' =>
                        [
                            0 => 'Bihpuria',
                            1 => 'Dhakuakhana (Pt-I]',
                            2 => 'Kadam',
                            3 => 'Naobaicha',
                            4 => 'Narayanpur',
                            5 => 'North Lakhimpur',
                            6 => 'Subansiri (Pt-I]',
                        ],
                    ],
                    705 =>
                    [
                        'name' => 'Biswanath',
                        'childs' =>
                        [
                            0 => 'Biswanath',
                            1 => 'Gohpur',
                            2 => 'Helem',
                            3 => 'Na-Duar',
                        ],
                    ],
                    281 =>
                    [
                        'name' => 'Bongaigaon',
                        'childs' =>
                        [
                            0 => 'Boitamari',
                            1 => 'Bongaigaon (Pt]',
                            2 => 'Dangtal Revenue Circle',
                            3 => 'Manikpur',
                            4 => 'Srijangram',
                        ],
                    ],
                    288 =>
                    [
                        'name' => 'Golaghat',
                        'childs' =>
                        [
                            0 => 'Bokakhat',
                            1 => 'Dergaon',
                            2 => 'Golaghat',
                            3 => 'Khumtai',
                            4 => 'Morangi',
                            5 => 'Sarupathar',
                        ],
                    ],
                    291 =>
                    [
                        'name' => 'Kamrup',
                        'childs' =>
                        [
                            0 => 'Boko',
                            1 => 'Chamaria',
                            2 => 'Chhaygaon',
                            3 => 'Goroimari',
                            4 => 'Hajo',
                            5 => 'Kamalpur',
                            6 => 'Koya',
                            7 => 'Nagarbera',
                            8 => 'North Guwahati',
                            9 => 'Palasbari',
                            10 => 'Rangia',
                        ],
                    ],
                    286 =>
                    [
                        'name' => 'Dibrugarh',
                        'childs' =>
                        [
                            0 => 'Chabua',
                            1 => 'Dibrugarh East',
                            2 => 'Dibrugarh West',
                            3 => 'Moran',
                            4 => 'Naharkatiya',
                            5 => 'Tengakhat',
                            6 => 'Tingkhong',
                        ],
                    ],
                    301 =>
                    [
                        'name' => 'Sonitpur',
                        'childs' =>
                        [
                            0 => 'Chariduar',
                            1 => 'Dhekiajuli (Pt]',
                            2 => 'Naduar',
                            3 => 'Tezpur',
                            4 => 'Thelamara',
                        ],
                    ],
                    283 =>
                    [
                        'name' => 'Darrang',
                        'childs' =>
                        [
                            0 => 'Dalgaon (Pt]',
                            1 => 'Mangaldoi (Pt]',
                            2 => 'Pathorighat (Pt]',
                            3 => 'Pub Mangaldai',
                            4 => 'Sipajhar',
                        ],
                    ],
                    284 =>
                    [
                        'name' => 'Dhemaji',
                        'childs' =>
                        [
                            0 => 'Dhemaji',
                            1 => 'Gogamukh',
                            2 => 'Jonai',
                            3 => 'Sissibargaon',
                        ],
                    ],
                    297 =>
                    [
                        'name' => 'Nagaon',
                        'childs' =>
                        [
                            0 => 'Dhing',
                            1 => 'Kaliabor',
                            2 => 'Kampur',
                            3 => 'Nagaon',
                            4 => 'Raha',
                            5 => 'Rupahi',
                            6 => 'Samaguri',
                        ],
                    ],
                    292 =>
                    [
                        'name' => 'Karbi Anglong',
                        'childs' =>
                        [
                            0 => 'Diphu',
                            1 => 'Phuloni',
                            2 => 'Silonijan',
                        ],
                    ],
                    709 =>
                    [
                        'name' => 'Hojai',
                        'childs' =>
                        [
                            0 => 'Doboka',
                            1 => 'Hojai',
                            2 => 'Lanka',
                        ],
                    ],
                    710 =>
                    [
                        'name' => 'West Karbi Anglong',
                        'childs' =>
                        [
                            0 => 'Donka',
                        ],
                    ],
                    302 =>
                    [
                        'name' => 'Tinsukia',
                        'childs' =>
                        [
                            0 => 'Doom Dooma',
                            1 => 'Margherita',
                            2 => 'Sadiya',
                            3 => 'Tinsukia',
                        ],
                    ],
                    756 =>
                    [
                        'name' => 'Tamulpur',
                        'childs' =>
                        [
                            0 => 'Goreswar (Pt]',
                            1 => 'Tamulpur',
                        ],
                    ],
                    299 =>
                    [
                        'name' => 'Dima Hasao',
                        'childs' =>
                        [
                            0 => 'Haflong',
                        ],
                    ],
                    617 =>
                    [
                        'name' => 'Udalguri',
                        'childs' =>
                        [
                            0 => 'Harisinga',
                            1 => 'Kalaigaon',
                            2 => 'Khoirabari',
                            3 => 'Mazbat',
                            4 => 'Udalguri',
                        ],
                    ],
                    290 =>
                    [
                        'name' => 'Jorhat',
                        'childs' =>
                        [
                            0 => 'Jorhat East',
                            1 => 'Jorhat West',
                            2 => 'Mariani',
                            3 => 'Teok',
                            4 => 'Titabor',
                        ],
                    ],
                    282 =>
                    [
                        'name' => 'Cachar',
                        'childs' =>
                        [
                            0 => 'Katigora',
                            1 => 'Lakhipur',
                            2 => 'Silchar',
                            3 => 'Sonai',
                            4 => 'Udarbond',
                        ],
                    ],
                    708 =>
                    [
                        'name' => 'Charaideo',
                        'childs' =>
                        [
                            0 => 'Mahmora',
                            1 => 'Sapekhati',
                            2 => 'Sonari',
                        ],
                    ],
                    706 =>
                    [
                        'name' => 'Majuli',
                        'childs' =>
                        [
                            0 => 'Majuli',
                            1 => 'Ujani Majuli Circle',
                        ],
                    ],
                    707 =>
                    [
                        'name' => 'South Salmara Mancachar',
                        'childs' =>
                        [
                            0 => 'Mankachar',
                            1 => 'South Salmara',
                        ],
                    ],
                ],
            ],
            33 =>
            [
                'name' => 'Tamil Nadu',
                'childs' =>
                [
                    575 =>
                    [
                        'name' => 'Kanniyakumari',
                        'childs' =>
                        [
                            0 => 'Agastheeswaram',
                            1 => 'Kalkulam',
                            2 => 'Killiyoor',
                            3 => 'Thiruvattar',
                            4 => 'Thovala',
                            5 => 'Vilavancode',
                        ],
                    ],
                    568 =>
                    [
                        'name' => 'Chennai',
                        'childs' =>
                        [
                            0 => 'Alandur',
                            1 => 'Ambattur',
                            2 => 'Aminjikarai',
                            3 => 'Ayanavaram',
                            4 => 'Egmore',
                            5 => 'Guindy',
                            6 => 'Maduravoyal',
                            7 => 'Mambalam',
                            8 => 'Mathavaram',
                            9 => 'Mylapore',
                            10 => 'Perambur',
                            11 => 'Purasawalkam',
                            12 => 'Sholinganallur',
                            13 => 'Tiruvottiyur',
                            14 => 'Tondiarpet',
                            15 => 'Velachery',
                        ],
                    ],
                    582 =>
                    [
                        'name' => 'Pudukkottai',
                        'childs' =>
                        [
                            0 => 'Alangudi',
                            1 => 'Aranthangi',
                            2 => 'Avudayarkoil',
                            3 => 'Gandarvakkottai',
                            4 => 'Iluppur',
                            5 => 'Karambakudi',
                            6 => 'Kulathur',
                            7 => 'Manamelkudi',
                            8 => 'Ponnamaravathi',
                            9 => 'Pudukkottai',
                            10 => 'Thirumayam',
                            11 => 'Viralimalai',
                        ],
                    ],
                    733 =>
                    [
                        'name' => 'Tenkasi',
                        'childs' =>
                        [
                            0 => 'Alangulam',
                            1 => 'Kadayanallur',
                            2 => 'Sankarankoil',
                            3 => 'Shenkottai',
                            4 => 'Sivagiri',
                            5 => 'Tenkasi',
                            6 => 'Tiruvengadam',
                            7 => 'Veerakeralamputhur',
                        ],
                    ],
                    581 =>
                    [
                        'name' => 'Perambalur',
                        'childs' =>
                        [
                            0 => 'Alathur',
                            1 => 'Kunnam',
                            2 => 'Perambalur',
                            3 => 'Veppanthattai',
                        ],
                    ],
                    592 =>
                    [
                        'name' => 'Tirunelveli',
                        'childs' =>
                        [
                            0 => 'Ambasamudram',
                            1 => 'Cheranmahadevi',
                            2 => 'Manur',
                            3 => 'Nanguneri',
                            4 => 'Palayamkottai',
                            5 => 'Radhapuram',
                            6 => 'Tirunelveli',
                            7 => 'Tisayanvilai',
                        ],
                    ],
                    732 =>
                    [
                        'name' => 'Tirupathur',
                        'childs' =>
                        [
                            0 => 'Ambur',
                            1 => 'Natrampalli',
                            2 => 'Tirupathur',
                            3 => 'Vaniyambadi',
                        ],
                    ],
                    595 =>
                    [
                        'name' => 'Vellore',
                        'childs' =>
                        [
                            0 => 'Anaicut',
                            1 => 'Gudiyatham',
                            2 => 'Katpadi',
                            3 => 'K V Kuppam',
                            4 => 'Pernambut',
                            5 => 'Vellore',
                        ],
                    ],
                    569 =>
                    [
                        'name' => 'Coimbatore',
                        'childs' =>
                        [
                            0 => 'Anaimalai',
                            1 => 'Annur',
                            2 => 'Coimbatore North',
                            3 => 'Coimbatore South',
                            4 => 'Kinathukadavu',
                            5 => 'Madukkari',
                            6 => 'Mettupalayam',
                            7 => 'Perur',
                            8 => 'Pollachi',
                            9 => 'Sulur',
                            10 => 'Valparai',
                        ],
                    ],
                    577 =>
                    [
                        'name' => 'Krishnagiri',
                        'childs' =>
                        [
                            0 => 'Anchetty',
                            1 => 'Bargur',
                            2 => 'Denkanikottai',
                            3 => 'Hosur',
                            4 => 'Krishnagiri',
                            5 => 'Pochampalli',
                            6 => 'Shoolagiri',
                            7 => 'Uthangarai',
                        ],
                    ],
                    610 =>
                    [
                        'name' => 'Ariyalur',
                        'childs' =>
                        [
                            0 => 'Andimadam',
                            1 => 'Ariyalur',
                            2 => 'Sendurai',
                            3 => 'Udayarpalayam',
                        ],
                    ],
                    588 =>
                    [
                        'name' => 'Theni',
                        'childs' =>
                        [
                            0 => 'Andipatti',
                            1 => 'Bodinayakanur',
                            2 => 'Periyakulam',
                            3 => 'Theni',
                            4 => 'Uthamapalayam',
                        ],
                    ],
                    573 =>
                    [
                        'name' => 'Erode',
                        'childs' =>
                        [
                            0 => 'Anthiyur',
                            1 => 'Bhavani',
                            2 => 'Erode',
                            3 => 'Gobichettipalayam',
                            4 => 'Kodumudi',
                            5 => 'Modakkurichi',
                            6 => 'Nambiyur',
                            7 => 'Perundurai',
                            8 => 'Sathyamangalam',
                            9 => 'Thalavadi',
                        ],
                    ],
                    731 =>
                    [
                        'name' => 'Ranipet',
                        'childs' =>
                        [
                            0 => 'Arakonam',
                            1 => 'Arcot',
                            2 => 'Kalavai',
                            3 => 'Nemili',
                            4 => 'Sholinghur',
                            5 => 'Wallajah',
                        ],
                    ],
                    593 =>
                    [
                        'name' => 'Tiruvannamalai',
                        'childs' =>
                        [
                            0 => 'Arani',
                            1 => 'Chengam',
                            2 => 'Chetpet',
                            3 => 'Cheyyar',
                            4 => 'Jamunamarathoor',
                            5 => 'Kalasapakkam',
                            6 => 'Kilpennathur',
                            7 => 'Polur',
                            8 => 'Thandrampet',
                            9 => 'Tiruvannamalai',
                            10 => 'Vandavasi',
                            11 => 'Vembakkam',
                        ],
                    ],
                    576 =>
                    [
                        'name' => 'Karur',
                        'childs' =>
                        [
                            0 => 'Aravakurichi',
                            1 => 'Kadavur',
                            2 => 'Karur',
                            3 => 'Krishnarayapuram',
                            4 => 'Kulithalai',
                            5 => 'Manmangalam',
                            6 => 'Pugalur',
                        ],
                    ],
                    597 =>
                    [
                        'name' => 'Virudhunagar',
                        'childs' =>
                        [
                            0 => 'Aruppukkottai',
                            1 => 'Kariapatti',
                            2 => 'Rajapalayam',
                            3 => 'Sattur',
                            4 => 'Sivakasi',
                            5 => 'Srivilliputhur',
                            6 => 'Tiruchuli',
                            7 => 'Vembakottai',
                            8 => 'Virudhunagar',
                            9 => 'Watrap',
                        ],
                    ],
                    572 =>
                    [
                        'name' => 'Dindigul',
                        'childs' =>
                        [
                            0 => 'Athoor',
                            1 => 'Dindiguleast',
                            2 => 'Dindigulwest',
                            3 => 'Gujiliamparai',
                            4 => 'Kodaikanal',
                            5 => 'Natham',
                            6 => 'Nilakkottai',
                            7 => 'Oddanchatram',
                            8 => 'Palani',
                            9 => 'Vedasandur',
                        ],
                    ],
                    584 =>
                    [
                        'name' => 'Salem',
                        'childs' =>
                        [
                            0 => 'Attur',
                            1 => 'Edappadi',
                            2 => 'Gangavalli',
                            3 => 'Kadayampatti',
                            4 => 'Mettur',
                            5 => 'Omalur',
                            6 => 'Pethanaickanpalayam',
                            7 => 'Salem',
                            8 => 'Salem South',
                            9 => 'Salem West',
                            10 => 'Sankari',
                            11 => 'Thalaivasal',
                            12 => 'Vazhapadi',
                            13 => 'Yercaud',
                        ],
                    ],
                    589 =>
                    [
                        'name' => 'Thiruvallur',
                        'childs' =>
                        [
                            0 => 'Avadi',
                            1 => 'Gummidipoondi',
                            2 => 'Pallipattu',
                            3 => 'Ponneri',
                            4 => 'Poonamallee',
                            5 => 'R K Pet',
                            6 => 'Thiruvallur',
                            7 => 'Tiruttani',
                            8 => 'Uthukkottai',
                        ],
                    ],
                    634 =>
                    [
                        'name' => 'Tiruppur',
                        'childs' =>
                        [
                            0 => 'Avinashi',
                            1 => 'Dharapuram',
                            2 => 'Kangeyam',
                            3 => 'Madathukulam',
                            4 => 'Palladam',
                            5 => 'Tiruppurnorth',
                            6 => 'Tiruppursouth',
                            7 => 'Udumalaipettai',
                            8 => 'Uthukuli',
                        ],
                    ],
                    570 =>
                    [
                        'name' => 'Cuddalore',
                        'childs' =>
                        [
                            0 => 'Bhuvanagiri',
                            1 => 'Chidambaram',
                            2 => 'Cuddalore',
                            3 => 'Kattumannarkoil',
                            4 => 'Kurinjipadi',
                            5 => 'Panruti',
                            6 => 'Srimushnam',
                            7 => 'Tittakudi',
                            8 => 'Veppur',
                            9 => 'Virudhachalam',
                        ],
                    ],
                    586 =>
                    [
                        'name' => 'Thanjavur',
                        'childs' =>
                        [
                            0 => 'Budalur',
                            1 => 'Kumbakonam',
                            2 => 'Orathanadu',
                            3 => 'Papanasam',
                            4 => 'Pattukkottai',
                            5 => 'Peravurani',
                            6 => 'Thanjavur',
                            7 => 'Thiruvaiyaru',
                            8 => 'Thiruvidaimarudur',
                            9 => 'Thiruvonam',
                        ],
                    ],
                    730 =>
                    [
                        'name' => 'Chengalpattu',
                        'childs' =>
                        [
                            0 => 'Chengalpattu',
                            1 => 'Cheyyur',
                            2 => 'Maduranthakam',
                            3 => 'Pallavaram',
                            4 => 'Tambaram',
                            5 => 'Tirukalukundram',
                            6 => 'Tiruporur',
                            7 => 'Vandalur',
                        ],
                    ],
                    729 =>
                    [
                        'name' => 'Kallakurichi',
                        'childs' =>
                        [
                            0 => 'Chinnasalem',
                            1 => 'Kallakkurichi',
                            2 => 'Kalvarayan Hills',
                            3 => 'Sankarapuram',
                            4 => 'Tirukkoyilur',
                            5 => 'Ulundurpettai',
                            6 => 'Vanapuram',
                        ],
                    ],
                    587 =>
                    [
                        'name' => 'The Nilgiris',
                        'childs' =>
                        [
                            0 => 'Coonoor',
                            1 => 'Gudalur',
                            2 => 'Kotagiri',
                            3 => 'Kundah',
                            4 => 'Panthalur',
                            5 => 'Udhagamandalam',
                        ],
                    ],
                    585 =>
                    [
                        'name' => 'Sivaganga',
                        'childs' =>
                        [
                            0 => 'Devakottai',
                            1 => 'Ilayangudi',
                            2 => 'Kalaiyarkoil',
                            3 => 'Karaikkudi',
                            4 => 'Manamadurai',
                            5 => 'Singampunari',
                            6 => 'Sivaganga',
                            7 => 'Thiruppathur',
                            8 => 'Thiruppuvanam',
                        ],
                    ],
                    571 =>
                    [
                        'name' => 'Dharmapuri',
                        'childs' =>
                        [
                            0 => 'Dharmapuri',
                            1 => 'Harur',
                            2 => 'Karimangalam',
                            3 => 'Nallampalli',
                            4 => 'Palakkodu',
                            5 => 'Pappireddipatti',
                            6 => 'Pennagaram',
                        ],
                    ],
                    594 =>
                    [
                        'name' => 'Thoothukkudi',
                        'childs' =>
                        [
                            0 => 'Eral',
                            1 => 'Ettayapuram',
                            2 => 'Kayathar',
                            3 => 'Kovilpatti',
                            4 => 'Ottapidaram',
                            5 => 'Sathankulam',
                            6 => 'Srivaikuntam',
                            7 => 'Thoothukkudi',
                            8 => 'Tiruchendur',
                            9 => 'Vilathikulam',
                        ],
                    ],
                    596 =>
                    [
                        'name' => 'Viluppuram',
                        'childs' =>
                        [
                            0 => 'Gingee',
                            1 => 'Kandachipuram',
                            2 => 'Marakanam',
                            3 => 'Melmalaiyanur',
                            4 => 'Thiruvennainallur',
                            5 => 'Tindivanam',
                            6 => 'Vanur',
                            7 => 'Vikravandi',
                            8 => 'Viluppuram',
                        ],
                    ],
                    583 =>
                    [
                        'name' => 'Ramanathapuram',
                        'childs' =>
                        [
                            0 => 'Kadaladi',
                            1 => 'Kamuthi',
                            2 => 'Kilakarai',
                            3 => 'Mudukulathur',
                            4 => 'Paramakudi',
                            5 => 'Rajasingamangalam',
                            6 => 'Ramanathapuram',
                            7 => 'Rameswaram',
                            8 => 'Tiruvadanai',
                        ],
                    ],
                    578 =>
                    [
                        'name' => 'Madurai',
                        'childs' =>
                        [
                            0 => 'Kalligudi',
                            1 => 'Madurai East',
                            2 => 'Madurai North',
                            3 => 'Madurai South',
                            4 => 'Madurai West',
                            5 => 'Melur',
                            6 => 'Peraiyur',
                            7 => 'Thirumangalam',
                            8 => 'Thirupparankundram',
                            9 => 'Usilampatti',
                            10 => 'Vadipatti',
                        ],
                    ],
                    574 =>
                    [
                        'name' => 'Kancheepuram',
                        'childs' =>
                        [
                            0 => 'Kancheepuram',
                            1 => 'Kundrathur',
                            2 => 'Sriperumbudur',
                            3 => 'Uthiramerur',
                            4 => 'Walajabad',
                        ],
                    ],
                    579 =>
                    [
                        'name' => 'Nagapattinam',
                        'childs' =>
                        [
                            0 => 'Kilvelur',
                            1 => 'Nagapattinam',
                            2 => 'Thirukkuvalai',
                            3 => 'Vedaranyam',
                        ],
                    ],
                    580 =>
                    [
                        'name' => 'Namakkal',
                        'childs' =>
                        [
                            0 => 'Kolli Hills',
                            1 => 'Kumarapalayam',
                            2 => 'Mohanur',
                            3 => 'Namakkal',
                            4 => 'Paramathi-Velur',
                            5 => 'Rasipuram',
                            6 => 'Sendamangalam',
                            7 => 'Tiruchengode',
                        ],
                    ],
                    590 =>
                    [
                        'name' => 'Thiruvarur',
                        'childs' =>
                        [
                            0 => 'Koothanallur',
                            1 => 'Kudavasal',
                            2 => 'Mannargudi',
                            3 => 'Muthupettai',
                            4 => 'Nannilam',
                            5 => 'Needamangalam',
                            6 => 'Thiruthuraipoondi',
                            7 => 'Thiruvarur',
                            8 => 'Valangaiman',
                        ],
                    ],
                    735 =>
                    [
                        'name' => 'Mayiladuthurai',
                        'childs' =>
                        [
                            0 => 'Kuthalam',
                            1 => 'Mayiladuthurai',
                            2 => 'Sirkali',
                            3 => 'Tharangambadi',
                        ],
                    ],
                    591 =>
                    [
                        'name' => 'Tiruchirappalli',
                        'childs' =>
                        [
                            0 => 'Lalgudi',
                            1 => 'Manachanallur',
                            2 => 'Manapparai',
                            3 => 'Marungapuri',
                            4 => 'Musiri',
                            5 => 'Srirangam',
                            6 => 'Thiruverumbur',
                            7 => 'Thottiyam',
                            8 => 'Thuraiyur',
                            9 => 'Tiruchirappalli East',
                            10 => 'Tiruchirappalli West',
                        ],
                    ],
                ],
            ],
            31 =>
            [
                'name' => 'Lakshadweep',
                'childs' =>
                [
                    553 =>
                    [
                        'name' => 'Lakshadweep District',
                        'childs' =>
                        [
                            0 => 'Agatti',
                            1 => 'Amini',
                            2 => 'Andrott',
                            3 => 'Bitra',
                            4 => 'Chetlat',
                            5 => 'Kadmat',
                            6 => 'Kalpeni',
                            7 => 'Kavaratti',
                            8 => 'Kiltan',
                            9 => 'Minicoy',
                        ],
                    ],
                ],
            ],
            9 =>
            [
                'name' => 'Uttar Pradesh',
                'childs' =>
                [
                    118 =>
                    [
                        'name' => 'Agra',
                        'childs' =>
                        [
                            0 => 'Agra',
                            1 => 'Bah',
                            2 => 'Etmadpur',
                            3 => 'Fatehabad',
                            4 => 'Kheragarh',
                            5 => 'Kiraoli',
                        ],
                    ],
                    122 =>
                    [
                        'name' => 'Auraiya',
                        'childs' =>
                        [
                            0 => 'Ajitmal',
                            1 => 'Auraiya',
                            2 => 'Bidhuna',
                        ],
                    ],
                    156 =>
                    [
                        'name' => 'Kanpur Dehat',
                        'childs' =>
                        [
                            0 => 'Akbarpur',
                            1 => 'Bhognipur',
                            2 => 'Derapur',
                            3 => 'Maitha',
                            4 => 'Rasulabad',
                            5 => 'Sikandra',
                        ],
                    ],
                    121 =>
                    [
                        'name' => 'Ambedkar Nagar',
                        'childs' =>
                        [
                            0 => 'Akbarpur',
                            1 => 'Allapur',
                            2 => 'Bhiti',
                            3 => 'Jalalpur',
                            4 => 'Tanda',
                        ],
                    ],
                    138 =>
                    [
                        'name' => 'Etah',
                        'childs' =>
                        [
                            0 => 'Aliganj',
                            1 => 'Etah',
                            2 => 'Jalesar',
                        ],
                    ],
                    120 =>
                    [
                        'name' => 'Prayagraj',
                        'childs' =>
                        [
                            0 => 'Allahabad',
                            1 => 'Bara',
                            2 => 'Handia',
                            3 => 'Karchhana',
                            4 => 'Koraon',
                            5 => 'Meja',
                            6 => 'Phulpur',
                            7 => 'Soraon',
                        ],
                    ],
                    173 =>
                    [
                        'name' => 'Pilibhit',
                        'childs' =>
                        [
                            0 => 'Amariya',
                            1 => 'Bisalpur',
                            2 => 'Kalinagar',
                            3 => 'Pilibhit',
                            4 => 'Puranpur',
                        ],
                    ],
                    640 =>
                    [
                        'name' => 'Amethi',
                        'childs' =>
                        [
                            0 => 'Amethi',
                            1 => 'Gauriganj',
                            2 => 'Musafirkhana',
                            3 => 'Tiloi',
                        ],
                    ],
                    141 =>
                    [
                        'name' => 'Farrukhabad',
                        'childs' =>
                        [
                            0 => 'Amritpur',
                            1 => 'Farrukhabad',
                            2 => 'Kaimganj',
                        ],
                    ],
                    154 =>
                    [
                        'name' => 'Amroha',
                        'childs' =>
                        [
                            0 => 'Amroha',
                            1 => 'Dhanaura',
                            2 => 'Hasanpur',
                            3 => 'Naugawan Sadat',
                        ],
                    ],
                    134 =>
                    [
                        'name' => 'Bulandshahr',
                        'childs' =>
                        [
                            0 => 'Anupshahr',
                            1 => 'Bulandshahr',
                            2 => 'Debai',
                            3 => 'Khurja',
                            4 => 'Shikarpur',
                            5 => 'Siana',
                            6 => 'Sikandrabad',
                        ],
                    ],
                    130 =>
                    [
                        'name' => 'Bareilly',
                        'childs' =>
                        [
                            0 => 'Aonla',
                            1 => 'Baheri',
                            2 => 'Bareilly',
                            3 => 'Faridpur',
                            4 => 'Meerganj',
                            5 => 'Nawabganj',
                        ],
                    ],
                    128 =>
                    [
                        'name' => 'Banda',
                        'childs' =>
                        [
                            0 => 'Atarra',
                            1 => 'Baberu',
                            2 => 'Banda',
                            3 => 'Naraini',
                            4 => 'Pailani',
                        ],
                    ],
                    119 =>
                    [
                        'name' => 'Aligarh',
                        'childs' =>
                        [
                            0 => 'Atrauli',
                            1 => 'Gabhana',
                            2 => 'Iglas',
                            3 => 'Khair',
                            4 => 'Koil',
                        ],
                    ],
                    179 =>
                    [
                        'name' => 'Bhadohi',
                        'childs' =>
                        [
                            0 => 'Aurai',
                            1 => 'Bhadohi',
                            2 => 'Gyanpur',
                        ],
                    ],
                    123 =>
                    [
                        'name' => 'Azamgarh',
                        'childs' =>
                        [
                            0 => 'Azamgarh',
                            1 => 'Burhanpur',
                            2 => 'Lalganj',
                            3 => 'Martinganjon',
                            4 => 'Mehnagar',
                            5 => 'Nizamabad',
                            6 => 'Phulpur',
                            7 => 'Sagri',
                        ],
                    ],
                    152 =>
                    [
                        'name' => 'Jaunpur',
                        'childs' =>
                        [
                            0 => 'Badlapur',
                            1 => 'Jaunpur',
                            2 => 'Kerakat',
                            3 => 'Machhlishahr',
                            4 => 'Mariahu',
                            5 => 'Shahganj',
                        ],
                    ],
                    124 =>
                    [
                        'name' => 'Baghpat',
                        'childs' =>
                        [
                            0 => 'Baghpat',
                            1 => 'Baraut',
                            2 => 'Khekada',
                        ],
                    ],
                    125 =>
                    [
                        'name' => 'Bahraich',
                        'childs' =>
                        [
                            0 => 'Bahraich',
                            1 => 'Kaiserganj',
                            2 => 'Mahasi',
                            3 => 'Mihinpurwa Motipur',
                            4 => 'Nanpara',
                            5 => 'Payagpur',
                        ],
                    ],
                    126 =>
                    [
                        'name' => 'Ballia',
                        'childs' =>
                        [
                            0 => 'Bairia',
                            1 => 'Ballia',
                            2 => 'Bansdih',
                            3 => 'Belthara Road',
                            4 => 'Rasra',
                            5 => 'Sikanderpur',
                        ],
                    ],
                    162 =>
                    [
                        'name' => 'Lucknow',
                        'childs' =>
                        [
                            0 => 'Bakshi Ka Talab',
                            1 => 'Malihabad',
                            2 => 'Mohanlalganj',
                            3 => 'Sadar',
                            4 => 'Sarojani Nagar',
                        ],
                    ],
                    185 =>
                    [
                        'name' => 'Sultanpur',
                        'childs' =>
                        [
                            0 => 'Baldirai',
                            1 => 'Jaisinghpur',
                            2 => 'Kadipur',
                            3 => 'Lambhua',
                            4 => 'Sultanpur',
                        ],
                    ],
                    127 =>
                    [
                        'name' => 'Balrampur',
                        'childs' =>
                        [
                            0 => 'Balrampur',
                            1 => 'Tulsipur',
                            2 => 'Utraula',
                        ],
                    ],
                    186 =>
                    [
                        'name' => 'Unnao',
                        'childs' =>
                        [
                            0 => 'Bangarmau',
                            1 => 'Bighapur',
                            2 => 'Hasanganj',
                            3 => 'Purwa',
                            4 => 'Safipur',
                            5 => 'Unnao',
                        ],
                    ],
                    148 =>
                    [
                        'name' => 'Gorakhpur',
                        'childs' =>
                        [
                            0 => 'Bansgaon',
                            1 => 'Campierganj',
                            2 => 'Chauri Chaura',
                            3 => 'Gola',
                            4 => 'Gorakhpur',
                            5 => 'Khajni',
                            6 => 'Sahjanwa',
                        ],
                    ],
                    182 =>
                    [
                        'name' => 'Siddharthnagar',
                        'childs' =>
                        [
                            0 => 'Bansi',
                            1 => 'Domariyaganj',
                            2 => 'Itwa',
                            3 => 'Naugarh',
                            4 => 'Shohratgarh',
                        ],
                    ],
                    137 =>
                    [
                        'name' => 'Deoria',
                        'childs' =>
                        [
                            0 => 'Barhaj',
                            1 => 'Bhatpar Rani',
                            2 => 'Deoria',
                            3 => 'Rudrapur',
                            4 => 'Salempur',
                        ],
                    ],
                    131 =>
                    [
                        'name' => 'Basti',
                        'childs' =>
                        [
                            0 => 'Basti',
                            1 => 'Bhanpur',
                            2 => 'Harraiya',
                            3 => 'Rudhauli',
                        ],
                    ],
                    177 =>
                    [
                        'name' => 'Saharanpur',
                        'childs' =>
                        [
                            0 => 'Behat',
                            1 => 'Deoband',
                            2 => 'Nakur',
                            3 => 'Rampur Maniharan',
                            4 => 'Saharanpur',
                        ],
                    ],
                    139 =>
                    [
                        'name' => 'Etawah',
                        'childs' =>
                        [
                            0 => 'Bharthana',
                            1 => 'Chakarnagar',
                            2 => 'Etawah',
                            3 => 'Jaswantnagar',
                            4 => 'Saifai',
                            5 => 'Takha',
                        ],
                    ],
                    181 =>
                    [
                        'name' => 'Shrawasti',
                        'childs' =>
                        [
                            0 => 'Bhinga',
                            1 => 'Ikauna',
                            2 => 'Jamunaha',
                        ],
                    ],
                    166 =>
                    [
                        'name' => 'Mainpuri',
                        'childs' =>
                        [
                            0 => 'Bhogaon',
                            1 => 'Ghiror',
                            2 => 'Karhal',
                            3 => 'Kishni',
                            4 => 'Kurawali',
                            5 => 'Mainpuri',
                        ],
                    ],
                    132 =>
                    [
                        'name' => 'Bijnor',
                        'childs' =>
                        [
                            0 => 'Bijnor',
                            1 => 'Chandpur',
                            2 => 'Dhampur',
                            3 => 'Nagina',
                            4 => 'Najibabad',
                        ],
                    ],
                    140 =>
                    [
                        'name' => 'Ayodhya',
                        'childs' =>
                        [
                            0 => 'Bikapur',
                            1 => 'Faizabad',
                            2 => 'Milkipur',
                            3 => 'Rudauli',
                            4 => 'Sohawal',
                        ],
                    ],
                    171 =>
                    [
                        'name' => 'Moradabad',
                        'childs' =>
                        [
                            0 => 'Bilari',
                            1 => 'Kanth',
                            2 => 'Moradabad',
                            3 => 'Thakurdwara',
                        ],
                    ],
                    176 =>
                    [
                        'name' => 'Rampur',
                        'childs' =>
                        [
                            0 => 'Bilaspur',
                            1 => 'Milak',
                            2 => 'Rampur',
                            3 => 'Shahabad',
                            4 => 'Suar',
                            5 => 'Tanda',
                        ],
                    ],
                    150 =>
                    [
                        'name' => 'Hardoi',
                        'childs' =>
                        [
                            0 => 'Bilgram',
                            1 => 'Hardoi',
                            2 => 'Sandila',
                            3 => 'Sawayajpur',
                            4 => 'Shahabad',
                        ],
                    ],
                    157 =>
                    [
                        'name' => 'Kanpur Nagar',
                        'childs' =>
                        [
                            0 => 'Bilhaur',
                            1 => 'Ghatampur',
                            2 => 'Kanpur',
                            3 => 'Narwal',
                        ],
                    ],
                    133 =>
                    [
                        'name' => 'Budaun',
                        'childs' =>
                        [
                            0 => 'Bilsi',
                            1 => 'Bisauli',
                            2 => 'Budaun',
                            3 => 'Dataganj',
                            4 => 'Sahaswan',
                        ],
                    ],
                    142 =>
                    [
                        'name' => 'Fatehpur',
                        'childs' =>
                        [
                            0 => 'Bindki',
                            1 => 'Fatehpur',
                            2 => 'Khaga',
                        ],
                    ],
                    183 =>
                    [
                        'name' => 'Sitapur',
                        'childs' =>
                        [
                            0 => 'Biswan',
                            1 => 'Laharpur',
                            2 => 'Mahmudabad',
                            3 => 'Maholi',
                            4 => 'Misrikh',
                            5 => 'Sidhauli',
                            6 => 'Sitapur',
                        ],
                    ],
                    172 =>
                    [
                        'name' => 'Muzaffarnagar',
                        'childs' =>
                        [
                            0 => 'Budhana',
                            1 => 'Jansath',
                            2 => 'Khatauli',
                            3 => 'Muzaffarnagar',
                        ],
                    ],
                    158 =>
                    [
                        'name' => 'Kaushambi',
                        'childs' =>
                        [
                            0 => 'Chail',
                            1 => 'Manjhanpur',
                            2 => 'Sirathu',
                        ],
                    ],
                    135 =>
                    [
                        'name' => 'Chandauli',
                        'childs' =>
                        [
                            0 => 'Chakia',
                            1 => 'Chandauli',
                            2 => 'Naugarh',
                            3 => 'Pandit Deendayal Upadhyay Nagar',
                            4 => 'Sakaldiha',
                        ],
                    ],
                    659 =>
                    [
                        'name' => 'Sambhal',
                        'childs' =>
                        [
                            0 => 'Chandausi',
                            1 => 'Gunnaur',
                            2 => 'Sambhal',
                        ],
                    ],
                    165 =>
                    [
                        'name' => 'Mahoba',
                        'childs' =>
                        [
                            0 => 'Charkhari',
                            1 => 'Kulpahar',
                            2 => 'Mahoba',
                        ],
                    ],
                    167 =>
                    [
                        'name' => 'Mathura',
                        'childs' =>
                        [
                            0 => 'Chhata',
                            1 => 'Govardhan',
                            2 => 'Mahavan',
                            3 => 'Mat',
                            4 => 'Mathura',
                        ],
                    ],
                    155 =>
                    [
                        'name' => 'Kannauj',
                        'childs' =>
                        [
                            0 => 'Chhibramau',
                            1 => 'Kannauj',
                            2 => 'Tirwa',
                        ],
                    ],
                    170 =>
                    [
                        'name' => 'Mirzapur',
                        'childs' =>
                        [
                            0 => 'Chunar',
                            1 => 'Lalganj',
                            2 => 'Marihan',
                            3 => 'Mirzapur',
                        ],
                    ],
                    147 =>
                    [
                        'name' => 'Gonda',
                        'childs' =>
                        [
                            0 => 'Colonelganj',
                            1 => 'Gonda',
                            2 => 'Mankapur',
                            3 => 'Tarabganj',
                        ],
                    ],
                    144 =>
                    [
                        'name' => 'Gautam Buddha Nagar',
                        'childs' =>
                        [
                            0 => 'Dadri',
                            1 => 'Gautam Buddha Nagar',
                            2 => 'Jewar',
                        ],
                    ],
                    175 =>
                    [
                        'name' => 'Rae Bareli',
                        'childs' =>
                        [
                            0 => 'Dalmau',
                            1 => 'Lalganj',
                            2 => 'Maharajganj',
                            3 => 'Rae Bareli',
                            4 => 'Salon',
                            5 => 'Unchahar',
                        ],
                    ],
                    661 =>
                    [
                        'name' => 'Hapur',
                        'childs' =>
                        [
                            0 => 'Dhaulana',
                            1 => 'Garhmukteshwar',
                            2 => 'Hapur',
                        ],
                    ],
                    159 =>
                    [
                        'name' => 'Kheri',
                        'childs' =>
                        [
                            0 => 'Dhaurahara',
                            1 => 'Gola Gokaran Nath',
                            2 => 'Lakhimpur',
                            3 => 'Mitauli',
                            4 => 'Mohammdi',
                            5 => 'Nighasan',
                            6 => 'Palia',
                        ],
                    ],
                    184 =>
                    [
                        'name' => 'Sonbhadra',
                        'childs' =>
                        [
                            0 => 'Dudhi',
                            1 => 'Ghorawal',
                            2 => 'Obra',
                            3 => 'Robertsganj',
                        ],
                    ],
                    129 =>
                    [
                        'name' => 'Bara Banki',
                        'childs' =>
                        [
                            0 => 'Fatehpur',
                            1 => 'Haidergarh',
                            2 => 'Nawabganj',
                            3 => 'Ramnagar',
                            4 => 'Ramsanehighat',
                            5 => 'Sirauli Gauspur',
                        ],
                    ],
                    143 =>
                    [
                        'name' => 'Firozabad',
                        'childs' =>
                        [
                            0 => 'Firozabad',
                            1 => 'Jasrana',
                            2 => 'Shikohabad',
                            3 => 'Sirsaganj',
                            4 => 'Tundla',
                        ],
                    ],
                    153 =>
                    [
                        'name' => 'Jhansi',
                        'childs' =>
                        [
                            0 => 'Garautha',
                            1 => 'Jhansi',
                            2 => 'Mauranipur',
                            3 => 'Moth',
                            4 => 'Tahrauli',
                        ],
                    ],
                    178 =>
                    [
                        'name' => 'Sant Kabir Nagar',
                        'childs' =>
                        [
                            0 => 'Ghanghata',
                            1 => 'Khalilabad',
                            2 => 'Mehdawal',
                        ],
                    ],
                    145 =>
                    [
                        'name' => 'Ghaziabad',
                        'childs' =>
                        [
                            0 => 'Ghaziabad',
                            1 => 'Loni',
                            2 => 'Modinagar',
                        ],
                    ],
                    146 =>
                    [
                        'name' => 'Ghazipur',
                        'childs' =>
                        [
                            0 => 'Ghazipur',
                            1 => 'Jakhania',
                            2 => 'Kasimabad',
                            3 => 'Mohammadabad',
                            4 => 'Saidpur',
                            5 => 'Sewarai',
                            6 => 'Zamania',
                        ],
                    ],
                    168 =>
                    [
                        'name' => 'Mau',
                        'childs' =>
                        [
                            0 => 'Ghosi',
                            1 => 'Madhuban',
                            2 => 'Maunath Bhanjan',
                            3 => 'Muhammadabad Gohna',
                        ],
                    ],
                    149 =>
                    [
                        'name' => 'Hamirpur',
                        'childs' =>
                        [
                            0 => 'Hamirpur',
                            1 => 'Maudaha',
                            2 => 'Rath',
                            3 => 'Sarila',
                        ],
                    ],
                    160 =>
                    [
                        'name' => 'Kushinagar',
                        'childs' =>
                        [
                            0 => 'Hata',
                            1 => 'Kaptanganj',
                            2 => 'Kasya',
                            3 => 'Khadda',
                            4 => 'Padrauna',
                            5 => 'Tamkuhi Raj',
                        ],
                    ],
                    163 =>
                    [
                        'name' => 'Hathras',
                        'childs' =>
                        [
                            0 => 'Hathras',
                            1 => 'Sadabad',
                            2 => 'Sasni',
                            3 => 'Sikandra Rao',
                        ],
                    ],
                    180 =>
                    [
                        'name' => 'Shahjahanpur',
                        'childs' =>
                        [
                            0 => 'Jalalabad',
                            1 => 'Kalan',
                            2 => 'Powayan',
                            3 => 'Shahjahanpur',
                            4 => 'Tilhar',
                        ],
                    ],
                    151 =>
                    [
                        'name' => 'Jalaun',
                        'childs' =>
                        [
                            0 => 'Jalaun',
                            1 => 'Kalpi',
                            2 => 'Konch',
                            3 => 'Madhogarh',
                            4 => 'Orai',
                        ],
                    ],
                    660 =>
                    [
                        'name' => 'Shamli',
                        'childs' =>
                        [
                            0 => 'Kairana',
                            1 => 'Shamli',
                            2 => 'Un',
                        ],
                    ],
                    136 =>
                    [
                        'name' => 'Chitrakoot',
                        'childs' =>
                        [
                            0 => 'Karwi',
                            1 => 'Manikpur',
                            2 => 'Mau',
                            3 => 'Rajapur',
                        ],
                    ],
                    633 =>
                    [
                        'name' => 'Kasganj',
                        'childs' =>
                        [
                            0 => 'Kasganj',
                            1 => 'Patiyali',
                            2 => 'Sahawar',
                        ],
                    ],
                    174 =>
                    [
                        'name' => 'Pratapgarh',
                        'childs' =>
                        [
                            0 => 'Kunda',
                            1 => 'Lalganj',
                            2 => 'Patti',
                            3 => 'Pratapgarh',
                            4 => 'Raniganj',
                        ],
                    ],
                    161 =>
                    [
                        'name' => 'Lalitpur',
                        'childs' =>
                        [
                            0 => 'Lalitpur',
                            1 => 'Mahroni',
                            2 => 'Mdawara',
                            3 => 'Pali',
                            4 => 'Talbehat',
                        ],
                    ],
                    164 =>
                    [
                        'name' => 'Mahrajganj',
                        'childs' =>
                        [
                            0 => 'Maharajganj',
                            1 => 'Nautanwa',
                            2 => 'Nichlaul',
                            3 => 'Pharenda',
                        ],
                    ],
                    169 =>
                    [
                        'name' => 'Meerut',
                        'childs' =>
                        [
                            0 => 'Mawana',
                            1 => 'Meerut',
                            2 => 'Sardhana',
                        ],
                    ],
                    187 =>
                    [
                        'name' => 'Varanasi',
                        'childs' =>
                        [
                            0 => 'Pindra',
                            1 => 'Rajatalab',
                            2 => 'Varanasi Sadar',
                        ],
                    ],
                ],
            ],
            15 =>
            [
                'name' => 'Mizoram',
                'childs' =>
                [
                    261 =>
                    [
                        'name' => 'Aizawl',
                        'childs' =>
                        [
                            0 => 'Aibawk',
                            1 => 'Darlawn',
                            2 => 'Thingsulthliah',
                            3 => 'Tlangnuam',
                        ],
                    ],
                    263 =>
                    [
                        'name' => 'Kolasib',
                        'childs' =>
                        [
                            0 => 'Bilkhawthlir',
                            1 => 'N Thingdawl',
                        ],
                    ],
                    265 =>
                    [
                        'name' => 'Lunglei',
                        'childs' =>
                        [
                            0 => 'Bunghmun',
                            1 => 'Lunglei',
                            2 => 'Lungsen',
                            3 => 'Tlabung',
                        ],
                    ],
                    264 =>
                    [
                        'name' => 'Lawngtlai',
                        'childs' =>
                        [
                            0 => 'Bungtlang S',
                            1 => 'Chawngte',
                            2 => 'Lawngtlai',
                            3 => 'Sangau',
                        ],
                    ],
                    262 =>
                    [
                        'name' => 'Champhai',
                        'childs' =>
                        [
                            0 => 'Champhai',
                            1 => 'Khawbung',
                        ],
                    ],
                    268 =>
                    [
                        'name' => 'Serchhip',
                        'childs' =>
                        [
                            0 => 'East Lungdar',
                            1 => 'Serchhip',
                        ],
                    ],
                    726 =>
                    [
                        'name' => 'Hnahthial',
                        'childs' =>
                        [
                            0 => 'Hnahthial',
                        ],
                    ],
                    266 =>
                    [
                        'name' => 'Mamit',
                        'childs' =>
                        [
                            0 => 'Kawrtethawveng',
                            1 => 'Reiek',
                            2 => 'West Phaileng',
                            3 => 'Zawlnuam',
                        ],
                    ],
                    728 =>
                    [
                        'name' => 'Khawzawl',
                        'childs' =>
                        [
                            0 => 'Khawzawl',
                        ],
                    ],
                    727 =>
                    [
                        'name' => 'Saitual',
                        'childs' =>
                        [
                            0 => 'Ngopa',
                            1 => 'Phullen',
                        ],
                    ],
                    267 =>
                    [
                        'name' => 'Siaha',
                        'childs' =>
                        [
                            0 => 'Siaha',
                            1 => 'Tipa',
                        ],
                    ],
                ],
            ],
            1 =>
            [
                'name' => 'Jammu And Kashmir',
                'childs' =>
                [
                    623 =>
                    [
                        'name' => 'Bandipora',
                        'childs' =>
                        [
                            0 => 'Ajas',
                            1 => 'Aloosa',
                            2 => 'Bandipora',
                            3 => 'Gurez',
                            4 => 'Hajin',
                            5 => 'Sonawari',
                            6 => 'Tulail',
                        ],
                    ],
                    5 =>
                    [
                        'name' => 'Jammu',
                        'childs' =>
                        [
                            0 => 'Akhnoor',
                            1 => 'Arnia',
                            2 => 'Bahu',
                            3 => 'Bhalwal',
                            4 => 'Bishnah',
                            5 => 'Chowki Choura',
                            6 => 'Dansal',
                            7 => 'Jammu',
                            8 => 'Jammu North',
                            9 => 'Jammu South',
                            10 => 'Jammu West',
                            11 => 'Jourian',
                            12 => 'Kharah Balli',
                            13 => 'Khour',
                            14 => 'Maira Mandrian',
                            15 => 'Mandal',
                            16 => 'Marh',
                            17 => 'Nagrota',
                            18 => 'Pargwal',
                            19 => 'R. S. Pura',
                            20 => 'Suchetgarh',
                        ],
                    ],
                    1 =>
                    [
                        'name' => 'Anantnag',
                        'childs' =>
                        [
                            0 => 'Anantnag',
                            1 => 'Anantnag East (Mattan]',
                            2 => 'Bijbehara',
                            3 => 'Dooru',
                            4 => 'Kokernag',
                            5 => 'Larnoo',
                            6 => 'Pahalgam',
                            7 => 'Qazigund',
                            8 => 'Sallar',
                            9 => 'Shahbad Bala',
                            10 => 'Shangus',
                            11 => 'Srigufwara',
                        ],
                    ],
                    11 =>
                    [
                        'name' => 'Pulwama',
                        'childs' =>
                        [
                            0 => 'Aripal',
                            1 => 'Awantipora',
                            2 => 'Kakapora',
                            3 => 'Pampore',
                            4 => 'Pulwama',
                            5 => 'Rajpora',
                            6 => 'Shahoora (Litter]',
                            7 => 'Tral',
                        ],
                    ],
                    627 =>
                    [
                        'name' => 'Reasi',
                        'childs' =>
                        [
                            0 => 'Arnas',
                            1 => 'Bhomag',
                            2 => 'Chassana',
                            3 => 'Katra',
                            4 => 'Mahore',
                            5 => 'Pouni',
                            6 => 'Reasi',
                            7 => 'Thakrakote',
                            8 => 'Thuroo',
                        ],
                    ],
                    4 =>
                    [
                        'name' => 'Doda',
                        'childs' =>
                        [
                            0 => 'Assar',
                            1 => 'Bhaderwah',
                            2 => 'Bhagwa',
                            3 => 'Bhalessa (Gandoh]',
                            4 => 'Bhalla',
                            5 => 'Bharath Bagla',
                            6 => 'Bhella',
                            7 => 'Chilly Pingal',
                            8 => 'Chiralla',
                            9 => 'Doda',
                            10 => 'Gundna',
                            11 => 'Kahara',
                            12 => 'Kashtigarh',
                            13 => 'Marmat',
                            14 => 'Mohalla',
                            15 => 'Phigsoo',
                            16 => 'Thathri',
                        ],
                    ],
                    620 =>
                    [
                        'name' => 'Kishtwar',
                        'childs' =>
                        [
                            0 => 'Atholi (Paddar]',
                            1 => 'Bunjwah',
                            2 => 'Chhatroo',
                            3 => 'Dachhan',
                            4 => 'Drabshala',
                            5 => 'Kishtwar',
                            6 => 'Machail',
                            7 => 'Marwah',
                            8 => 'Mugal Maidan',
                            9 => 'Nagseni',
                            10 => 'Warwan',
                        ],
                    ],
                    10 =>
                    [
                        'name' => 'Poonch',
                        'childs' =>
                        [
                            0 => 'Balakote',
                            1 => 'Haveli',
                            2 => 'Mandi',
                            3 => 'Mankote',
                            4 => 'Mendhar',
                            5 => 'Surankote',
                        ],
                    ],
                    7 =>
                    [
                        'name' => 'Kathua',
                        'childs' =>
                        [
                            0 => 'Bani',
                            1 => 'Basohli',
                            2 => 'Billawar',
                            3 => 'Dinga Amb',
                            4 => 'Hiranagar',
                            5 => 'Kathua',
                            6 => 'Lohai Malhar',
                            7 => 'Mahanpur',
                            8 => 'Marheen',
                            9 => 'Nagri Parole',
                            10 => 'Ramkote',
                        ],
                    ],
                    621 =>
                    [
                        'name' => 'Ramban',
                        'childs' =>
                        [
                            0 => 'Banihal',
                            1 => 'Batote',
                            2 => 'Gool',
                            3 => 'Khari',
                            4 => 'Pogal Paristan',
                            5 => 'Rajgarh',
                            6 => 'Ramban',
                            7 => 'Ramsoo',
                        ],
                    ],
                    3 =>
                    [
                        'name' => 'Baramulla',
                        'childs' =>
                        [
                            0 => 'Baramulla',
                            1 => 'Boniyar',
                            2 => 'Dangarpora',
                            3 => 'Dongiwacha',
                            4 => 'Kawarhama',
                            5 => 'Khoie',
                            6 => 'Kreeri',
                            7 => 'Kunzer',
                            8 => 'Narwav',
                            9 => 'Pattan',
                            10 => 'Rafiabad',
                            11 => 'Singhpora',
                            12 => 'Sopore',
                            13 => 'Tangmarg',
                            14 => 'Uri',
                            15 => 'Wagoora',
                            16 => 'Watergam',
                            17 => 'Zaingeer',
                        ],
                    ],
                    625 =>
                    [
                        'name' => 'Shopian',
                        'childs' =>
                        [
                            0 => 'Barbugh Imam Sahib',
                            1 => 'Chitragam',
                            2 => 'Harmain',
                            3 => 'Keegam',
                            4 => 'Keller',
                            5 => 'Shopian',
                            6 => 'Zainapora',
                        ],
                    ],
                    624 =>
                    [
                        'name' => 'Samba',
                        'childs' =>
                        [
                            0 => 'Bari Brahamana',
                            1 => 'Ghagwal',
                            2 => 'Rajpura',
                            3 => 'Ramgarh',
                            4 => 'Samba',
                            5 => 'Vijaypur',
                        ],
                    ],
                    14 =>
                    [
                        'name' => 'Udhampur',
                        'childs' =>
                        [
                            0 => 'Basantgarh',
                            1 => 'Chenani',
                            2 => 'Latti',
                            3 => 'Majalta',
                            4 => 'Moungri',
                            5 => 'Panchari',
                            6 => 'Ramnagar',
                            7 => 'Udhampur',
                        ],
                    ],
                    2 =>
                    [
                        'name' => 'Budgam',
                        'childs' =>
                        [
                            0 => 'Beerwah',
                            1 => 'Bk Pora',
                            2 => 'Budgam',
                            3 => 'Chadoora',
                            4 => 'Charar- E- Shrief',
                            5 => 'Khag',
                            6 => 'Khansahib',
                            7 => 'Magam',
                            8 => 'Narbal',
                        ],
                    ],
                    12 =>
                    [
                        'name' => 'Rajouri',
                        'childs' =>
                        [
                            0 => 'Beri Pattan',
                            1 => 'Darhal',
                            2 => 'Kalakote',
                            3 => 'Khawas',
                            4 => 'Koteranka',
                            5 => 'Laroka',
                            6 => 'Manjakote',
                            7 => 'Nowshera',
                            8 => 'Qila Darhal',
                            9 => 'Rajouri',
                            10 => 'Siot',
                            11 => 'Sunderbani',
                            12 => 'Taryath',
                            13 => 'Thana Mandi',
                        ],
                    ],
                    13 =>
                    [
                        'name' => 'Srinagar',
                        'childs' =>
                        [
                            0 => 'Channapora/Natipora',
                            1 => 'Eidgah',
                            2 => 'Khanyar',
                            3 => 'Pantha Chowk',
                            4 => 'Srinagar Central',
                            5 => 'Srinagar North',
                            6 => 'Srinagar South',
                        ],
                    ],
                    622 =>
                    [
                        'name' => 'Kulgam',
                        'childs' =>
                        [
                            0 => 'Devsar',
                            1 => 'D H Pora',
                            2 => 'Frisal',
                            3 => 'Kulgam',
                            4 => 'Pahloo',
                            5 => 'Qoimoh',
                            6 => 'Yaripora',
                        ],
                    ],
                    8 =>
                    [
                        'name' => 'Kupwara',
                        'childs' =>
                        [
                            0 => 'Dragmulla',
                            1 => 'Handwara',
                            2 => 'Karnah',
                            3 => 'Keran',
                            4 => 'Kralpora',
                            5 => 'Kupwara',
                            6 => 'Lalpora',
                            7 => 'Langate',
                            8 => 'Lolab (Sogam]',
                            9 => 'Machil',
                            10 => 'Qalamabad',
                            11 => 'Qaziabad (Kralgund]',
                            12 => 'Ramhal (Tarathpora]',
                            13 => 'Trehgam',
                            14 => 'Villgam',
                            15 => 'Zachaldara',
                        ],
                    ],
                    626 =>
                    [
                        'name' => 'Ganderbal',
                        'childs' =>
                        [
                            0 => 'Ganderbal',
                            1 => 'Gund',
                            2 => 'Kangan',
                            3 => 'Lar',
                            4 => 'Tullamulla Khirbhawani',
                            5 => 'Wakoora',
                        ],
                    ],
                ],
            ],
            2 =>
            [
                'name' => 'Himachal Pradesh',
                'childs' =>
                [
                    18 =>
                    [
                        'name' => 'Kangra',
                        'childs' =>
                        [
                            0 => 'Alampur',
                            1 => 'Baijnath',
                            2 => 'Baroh',
                            3 => 'Bharoli',
                            4 => 'Bhawarna',
                            5 => 'Chadiyar',
                            6 => 'Dadasiba',
                            7 => 'Dareeni',
                            8 => 'Dera Gopipur',
                            9 => 'Dharmsala',
                            10 => 'Dhira',
                            11 => 'Fatehpur',
                            12 => 'Gangath',
                            13 => 'Harchakian',
                            14 => 'Haripur',
                            15 => 'Indora',
                            16 => 'Jaisinghpur',
                            17 => 'Jaswan',
                            18 => 'Jawalamukhi',
                            19 => 'Jawali',
                            20 => 'Kangra',
                            21 => 'Khundian',
                            22 => 'Kotla',
                            23 => 'Lagru',
                            24 => 'Majheen',
                            25 => 'Multhan',
                            26 => 'Nagrota Bagwan',
                            27 => 'Nagrota Surian',
                            28 => 'Nurpur',
                            29 => 'Palampur',
                            30 => 'Panchrukhi',
                            31 => 'Pragpur',
                            32 => 'Raja Ka Talab',
                            33 => 'Rakkar',
                            34 => 'Rey',
                            35 => 'Sadwan',
                            36 => 'Shahpur',
                            37 => 'Sulah',
                            38 => 'Thakurdwara',
                            39 => 'Thural',
                        ],
                    ],
                    26 =>
                    [
                        'name' => 'Una',
                        'childs' =>
                        [
                            0 => 'Amb',
                            1 => 'Bangana',
                            2 => 'Bharwain',
                            3 => 'Birhu Kalan',
                            4 => 'Dulahar',
                            5 => 'Gagret',
                            6 => 'Ghanari',
                            7 => 'Haroli',
                            8 => 'Ispur',
                            9 => 'Jol',
                            10 => 'Mehatpur',
                            11 => 'Una',
                        ],
                    ],
                    20 =>
                    [
                        'name' => 'Kullu',
                        'childs' =>
                        [
                            0 => 'Ani',
                            1 => 'Banjar',
                            2 => 'Bhuntar',
                            3 => 'Jari',
                            4 => 'Kullu',
                            5 => 'Manali',
                            6 => 'Nirmand',
                            7 => 'Nither',
                            8 => 'Sainj',
                        ],
                    ],
                    25 =>
                    [
                        'name' => 'Solan',
                        'childs' =>
                        [
                            0 => 'Arki',
                            1 => 'Baddi',
                            2 => 'Darlaghat',
                            3 => 'Kandaghat',
                            4 => 'Kasauli',
                            5 => 'Krishangarh',
                            6 => 'Kunihar',
                            7 => 'Mamlig',
                            8 => 'Nalagarh',
                            9 => 'Panjehra',
                            10 => 'Parwanoo',
                            11 => 'Ramshahr',
                            12 => 'Solan',
                        ],
                    ],
                    22 =>
                    [
                        'name' => 'Mandi',
                        'childs' =>
                        [
                            0 => 'Aut',
                            1 => 'Bagachnogi',
                            2 => 'Bagshad',
                            3 => 'Baldwara',
                            4 => 'Balh',
                            5 => 'Bali Chowki',
                            6 => 'Bhadrota',
                            7 => 'Chachyot',
                            8 => 'Chhatri',
                            9 => 'Dehar',
                            10 => 'Dhalwan',
                            11 => 'Dharmpur',
                            12 => 'Jogindarnagar',
                            13 => 'Karsog',
                            14 => 'Katoula',
                            15 => 'Kotli',
                            16 => 'Lad Bharol',
                            17 => 'Makridi',
                            18 => 'Mandap',
                            19 => 'Mandi',
                            20 => 'Nihri',
                            21 => 'Padhar',
                            22 => 'Pangna',
                            23 => 'Rewalsar',
                            24 => 'Sandhol',
                            25 => 'Sarkaghat',
                            26 => 'Sundarnagar',
                            27 => 'Thachi',
                            28 => 'Thunag',
                            29 => 'Tihra',
                            30 => 'Tikken',
                        ],
                    ],
                    17 =>
                    [
                        'name' => 'Hamirpur',
                        'childs' =>
                        [
                            0 => 'Barsar',
                            1 => 'Bhoranj',
                            2 => 'Bhota',
                            3 => 'Dhatwal',
                            4 => 'Galore',
                            5 => 'Hamirpur',
                            6 => 'Jahu',
                            7 => 'Kangoo',
                            8 => 'Lambloo',
                            9 => 'Nadaun',
                            10 => 'Tauni Devi Bamson',
                            11 => 'Tira Sujanpur',
                        ],
                    ],
                    16 =>
                    [
                        'name' => 'Chamba',
                        'childs' =>
                        [
                            0 => 'Bhalai',
                            1 => 'Bharmour',
                            2 => 'Bhattiyat',
                            3 => 'Chamba',
                            4 => 'Churah',
                            5 => 'Dalhousie',
                            6 => 'Dharwala',
                            7 => 'Holi',
                            8 => 'Kakira',
                            9 => 'Pangi',
                            10 => 'Pukhri',
                            11 => 'Saluni',
                            12 => 'Sihunta',
                            13 => 'Telka',
                        ],
                    ],
                    15 =>
                    [
                        'name' => 'Bilaspur',
                        'childs' =>
                        [
                            0 => 'Bharari',
                            1 => 'Bilaspur Sadar',
                            2 => 'Ghumarwin',
                            3 => 'Jhanduta',
                            4 => 'Kalol',
                            5 => 'Naina Devi',
                            6 => 'Namhol',
                        ],
                    ],
                    23 =>
                    [
                        'name' => 'Shimla',
                        'childs' =>
                        [
                            0 => 'Chaupal',
                            1 => 'Chirgaon',
                            2 => 'Deha',
                            3 => 'Dhami',
                            4 => 'Dodra Kwar',
                            5 => 'Jalog',
                            6 => 'Jangla',
                            7 => 'Jubbal',
                            8 => 'Junga',
                            9 => 'Kalbog',
                            10 => 'Kotgarh',
                            11 => 'Kotkhai',
                            12 => 'Kumarsain',
                            13 => 'Kupvi',
                            14 => 'Nankhari',
                            15 => 'Nerwa',
                            16 => 'Rampur',
                            17 => 'Rohru',
                            18 => 'Sarahan',
                            19 => 'Sarawati Nagar',
                            20 => 'Shimla ( Rural ]',
                            21 => 'Shimla (Urban]',
                            22 => 'Sunni',
                            23 => 'Taklech',
                            24 => 'Theog',
                            25 => 'Tikar',
                        ],
                    ],
                    24 =>
                    [
                        'name' => 'Sirmaur',
                        'childs' =>
                        [
                            0 => 'Dadahu',
                            1 => 'Haripurdhar',
                            2 => 'Kamrau',
                            3 => 'Majra',
                            4 => 'Nahan',
                            5 => 'Narag',
                            6 => 'Nohra',
                            7 => 'Pachhad',
                            8 => 'Pajhota',
                            9 => 'Paonta Sahib',
                            10 => 'Rajgarh',
                            11 => 'Renuka',
                            12 => 'Ronhat',
                            13 => 'Shalai',
                        ],
                    ],
                    19 =>
                    [
                        'name' => 'Kinnaur',
                        'childs' =>
                        [
                            0 => 'Hangrang',
                            1 => 'Kalpa',
                            2 => 'Morang',
                            3 => 'Nichar',
                            4 => 'Poo',
                            5 => 'Sangla',
                            6 => 'Tapri',
                        ],
                    ],
                    21 =>
                    [
                        'name' => 'Lahaul And Spiti',
                        'childs' =>
                        [
                            0 => 'Lahul',
                            1 => 'Spiti',
                            2 => 'Udaipur',
                        ],
                    ],
                ],
            ],
            7 =>
            [
                'name' => 'Delhi',
                'childs' =>
                [
                    80 =>
                    [
                        'name' => 'North',
                        'childs' =>
                        [
                            0 => 'Alipur',
                            1 => 'Model Town',
                            2 => 'Narela',
                        ],
                    ],
                    79 =>
                    [
                        'name' => 'New Delhi',
                        'childs' =>
                        [
                            0 => 'Chanakya Puri',
                            1 => 'Delhi Cantonment',
                            2 => 'Vasant Vihar',
                        ],
                    ],
                    77 =>
                    [
                        'name' => 'Central',
                        'childs' =>
                        [
                            0 => 'Civil Lines',
                            1 => 'Karol Bagh',
                            2 => 'Kotwali',
                        ],
                    ],
                    670 =>
                    [
                        'name' => 'South East',
                        'childs' =>
                        [
                            0 => 'Defence Colony',
                            1 => 'Kalkaji',
                            2 => 'Sarita Vihar',
                        ],
                    ],
                    84 =>
                    [
                        'name' => 'South West',
                        'childs' =>
                        [
                            0 => 'Dwarka',
                            1 => 'Kapeshera',
                            2 => 'Najafgarh',
                        ],
                    ],
                    78 =>
                    [
                        'name' => 'East',
                        'childs' =>
                        [
                            0 => 'Gandhi Nagar',
                            1 => 'Mayur Vihar',
                            2 => 'Preet Vihar',
                        ],
                    ],
                    83 =>
                    [
                        'name' => 'South',
                        'childs' =>
                        [
                            0 => 'Hauz Khas',
                            1 => 'Mehrauli',
                            2 => 'Saket',
                        ],
                    ],
                    82 =>
                    [
                        'name' => 'North West',
                        'childs' =>
                        [
                            0 => 'Kanjhawala',
                            1 => 'Rohini',
                            2 => 'Saraswati Vihar',
                        ],
                    ],
                    81 =>
                    [
                        'name' => 'North East',
                        'childs' =>
                        [
                            0 => 'Karawal Nagar',
                            1 => 'Seelam Pur',
                            2 => 'Yamuna Vihar',
                        ],
                    ],
                    85 =>
                    [
                        'name' => 'West',
                        'childs' =>
                        [
                            0 => 'Patel Nagar',
                            1 => 'Punjabi Bagh',
                            2 => 'Rajouri Garden',
                        ],
                    ],
                    671 =>
                    [
                        'name' => 'Shahdara',
                        'childs' =>
                        [
                            0 => 'Seema Puri',
                            1 => 'Shahdara',
                            2 => 'Vivek Vihar',
                        ],
                    ],
                ],
            ],
            19 =>
            [
                'name' => 'West Bengal',
                'childs' =>
                [
                    664 =>
                    [
                        'name' => 'Alipurduar',
                        'childs' =>
                        [
                            0 => 'Alipurduar - I',
                            1 => 'Alipurduar - II',
                            2 => 'Falakata',
                            3 => 'Kalchini',
                            4 => 'Kumargram',
                            5 => 'Madarihat',
                        ],
                    ],
                    303 =>
                    [
                        'name' => 'North 24 Parganas',
                        'childs' =>
                        [
                            0 => 'Amdanga',
                            1 => 'Baduria',
                            2 => 'Bagda',
                            3 => 'Barasat - I',
                            4 => 'Barasat - II',
                            5 => 'Barrackpur - I',
                            6 => 'Barrackpur - II',
                            7 => 'Basirhat - I',
                            8 => 'Basirhat - II',
                            9 => 'Bongaon',
                            10 => 'Deganga',
                            11 => 'Gaighata',
                            12 => 'Habra - I',
                            13 => 'Habra - II',
                            14 => 'Haroa',
                            15 => 'Hasnabad',
                            16 => 'Hingalganj',
                            17 => 'Minakhan',
                            18 => 'Rajarhat',
                            19 => 'Sandeshkhali - I',
                            20 => 'Sandeshkhali - II',
                            21 => 'Swarupnagar',
                        ],
                    ],
                    313 =>
                    [
                        'name' => 'Howrah',
                        'childs' =>
                        [
                            0 => 'Amta - I',
                            1 => 'Amta - II',
                            2 => 'Bagnan - I',
                            3 => 'Bagnan - II',
                            4 => 'Bally Jagachha',
                            5 => 'Domjur',
                            6 => 'Jagatballavpur',
                            7 => 'Panchla',
                            8 => 'Sankrail',
                            9 => 'Shyampur - I',
                            10 => 'Shyampur - II',
                            11 => 'Udaynarayanpur',
                            12 => 'Uluberia - I',
                            13 => 'Uluberia - II',
                        ],
                    ],
                    704 =>
                    [
                        'name' => 'Paschim Bardhaman',
                        'childs' =>
                        [
                            0 => 'Andal',
                            1 => 'Barabani',
                            2 => 'Durgapur Faridpur',
                            3 => 'Jamuria',
                            4 => 'Kanksa',
                            5 => 'Pandabeswar',
                            6 => 'Raniganj',
                            7 => 'Salanpur',
                        ],
                    ],
                    312 =>
                    [
                        'name' => 'Hooghly',
                        'childs' =>
                        [
                            0 => 'Arambag',
                            1 => 'Balagarh',
                            2 => 'Chanditala - I',
                            3 => 'Chanditala - II',
                            4 => 'Chinsurah - Magra',
                            5 => 'Dhaniakhali',
                            6 => 'Goghat - I',
                            7 => 'Goghat - II',
                            8 => 'Haripal',
                            9 => 'Jangipara',
                            10 => 'Khanakul - I',
                            11 => 'Khanakul - II',
                            12 => 'Pandua',
                            13 => 'Polba - Dadpur',
                            14 => 'Pursura',
                            15 => 'Serampur Uttarpara',
                            16 => 'Singur',
                            17 => 'Tarakeswar',
                        ],
                    ],
                    321 =>
                    [
                        'name' => 'Purulia',
                        'childs' =>
                        [
                            0 => 'Arsha',
                            1 => 'Bagmundi',
                            2 => 'Balarampur',
                            3 => 'Barabazar',
                            4 => 'Bundwan',
                            5 => 'Hura',
                            6 => 'Jaipur',
                            7 => 'Jhalda - I',
                            8 => 'Jhalda - II',
                            9 => 'Kashipur',
                            10 => 'Manbazar - I',
                            11 => 'Manbazar - II',
                            12 => 'Neturia',
                            13 => 'Para',
                            14 => 'Puncha',
                            15 => 'Purulia - I',
                            16 => 'Purulia - II',
                            17 => 'Raghunathpur - I',
                            18 => 'Raghunathpur - II',
                            19 => 'Santuri',
                        ],
                    ],
                    306 =>
                    [
                        'name' => 'Purba Bardhaman',
                        'childs' =>
                        [
                            0 => 'Ausgram - I',
                            1 => 'Ausgram - II',
                            2 => 'Bhatar',
                            3 => 'Burdwan - I',
                            4 => 'Burdwan - II',
                            5 => 'Galsi - I',
                            6 => 'Galsi - II',
                            7 => 'Jamalpur',
                            8 => 'Kalna - I',
                            9 => 'Kalna - II',
                            10 => 'Katwa - I',
                            11 => 'Katwa - II',
                            12 => 'Ketugram - I',
                            13 => 'Ketugram - II',
                            14 => 'Khandaghosh',
                            15 => 'Mangolkote',
                            16 => 'Manteswar',
                            17 => 'Memari - I',
                            18 => 'Memari - II',
                            19 => 'Purbasthali - I',
                            20 => 'Purbasthali - II',
                            21 => 'Raina - I',
                            22 => 'Raina - II',
                        ],
                    ],
                    310 =>
                    [
                        'name' => 'Dakshin Dinajpur',
                        'childs' =>
                        [
                            0 => 'Balurghat',
                            1 => 'Bansihari',
                            2 => 'Gangarampur',
                            3 => 'Harirampur',
                            4 => 'Hilli',
                            5 => 'Kumarganj',
                            6 => 'Kushmundi',
                            7 => 'Tapan',
                        ],
                    ],
                    316 =>
                    [
                        'name' => 'Malda',
                        'childs' =>
                        [
                            0 => 'Bamangola',
                            1 => 'Chanchal - I',
                            2 => 'Chanchal - II',
                            3 => 'English Bazar',
                            4 => 'Gazole',
                            5 => 'Habibpur',
                            6 => 'Harischandrapur - I',
                            7 => 'Harischandrapur - II',
                            8 => 'Kaliachak - I',
                            9 => 'Kaliachak - II',
                            10 => 'Kaliachak - III',
                            11 => 'Maldah (Old]',
                            12 => 'Manikchak',
                            13 => 'Ratua - I',
                            14 => 'Ratua - II',
                        ],
                    ],
                    314 =>
                    [
                        'name' => 'Jalpaiguri',
                        'childs' =>
                        [
                            0 => 'Banarhat',
                            1 => 'Dhupguri',
                            2 => 'Jalpaiguri',
                            3 => 'Kranti',
                            4 => 'Mal',
                            5 => 'Matiali',
                            6 => 'Maynaguri',
                            7 => 'Nagrakata',
                            8 => 'Rajganj',
                        ],
                    ],
                    305 =>
                    [
                        'name' => 'Bankura',
                        'childs' =>
                        [
                            0 => 'Bankura - I',
                            1 => 'Bankura - II',
                            2 => 'Barjora',
                            3 => 'Chhatna',
                            4 => 'Gangajalghati',
                            5 => 'Hirbandh',
                            6 => 'Indpur',
                            7 => 'Indus',
                            8 => 'Jaypur',
                            9 => 'Khatra',
                            10 => 'Kotulpur',
                            11 => 'Mejhia',
                            12 => 'Onda',
                            13 => 'Patrasayer',
                            14 => 'Raipur',
                            15 => 'Ranibandh',
                            16 => 'Saltora',
                            17 => 'Sarenga',
                            18 => 'Simlapal',
                            19 => 'Sonamukhi',
                            20 => 'Taldangra',
                            21 => 'Vishnupur',
                        ],
                    ],
                    304 =>
                    [
                        'name' => 'South 24 Parganas',
                        'childs' =>
                        [
                            0 => 'Baruipur',
                            1 => 'Basanti',
                            2 => 'Bhangar - I',
                            3 => 'Bhangar - II',
                            4 => 'Bishnupur - I',
                            5 => 'Bishnupur - II',
                            6 => 'Budge Budge - I',
                            7 => 'Budge Budge - II',
                            8 => 'Canning - I',
                            9 => 'Canning - II',
                            10 => 'Diamond Harbour - I',
                            11 => 'Diamond Harbour - II',
                            12 => 'Falta',
                            13 => 'Gosaba',
                            14 => 'Jaynagar - I',
                            15 => 'Jaynagar - II',
                            16 => 'Kakdwip',
                            17 => 'Kulpi',
                            18 => 'Kultali',
                            19 => 'Magrahat - I',
                            20 => 'Magrahat - II',
                            21 => 'Mandirbazar',
                            22 => 'Mathurapur - I',
                            23 => 'Mathurapur - II',
                            24 => 'Namkhana',
                            25 => 'Patharpratima',
                            26 => 'Sagar',
                            27 => 'Sonarpur',
                            28 => 'Thakurpukur Mahestola',
                        ],
                    ],
                    319 =>
                    [
                        'name' => 'Murshidabad',
                        'childs' =>
                        [
                            0 => 'Beldanga - I',
                            1 => 'Beldanga - II',
                            2 => 'Berhampore',
                            3 => 'Bhagawangola - I',
                            4 => 'Bhagawangola - II',
                            5 => 'Bharatpur - I',
                            6 => 'Bharatpur - II',
                            7 => 'Burwan',
                            8 => 'Domkal',
                            9 => 'Farakka',
                            10 => 'Hariharpara',
                            11 => 'Jalangi',
                            12 => 'Kandi',
                            13 => 'Khargram',
                            14 => 'Lalgola',
                            15 => 'Murshidabad Jiaganj',
                            16 => 'Nabagram',
                            17 => 'Nawda',
                            18 => 'Raghunathganj - I',
                            19 => 'Raghunathganj - II',
                            20 => 'Raninagar - I',
                            21 => 'Raninagar - II',
                            22 => 'Sagardighi',
                            23 => 'Samserganj',
                            24 => 'Suti - I',
                            25 => 'Suti - II',
                        ],
                    ],
                    317 =>
                    [
                        'name' => 'Purba Medinipur',
                        'childs' =>
                        [
                            0 => 'Bhagawanpur - I',
                            1 => 'Bhagawanpur - II',
                            2 => 'Chandipur',
                            3 => 'Contai - I',
                            4 => 'Contai - III',
                            5 => 'Deshopran',
                            6 => 'Egra - I',
                            7 => 'Egra - II',
                            8 => 'Haldia',
                            9 => 'Khejuri - I',
                            10 => 'Khejuri - II',
                            11 => 'Kolaghat',
                            12 => 'Mahisadal',
                            13 => 'Moyna',
                            14 => 'Nanda Kumar',
                            15 => 'Nandigram - I',
                            16 => 'Nandigram - II',
                            17 => 'Panskura',
                            18 => 'Potashpur - I',
                            19 => 'Potashpur - II',
                            20 => 'Ramnagar - I',
                            21 => 'Ramnagar - II',
                            22 => 'Sahid Matangini',
                            23 => 'Sutahata',
                            24 => 'Tamluk',
                        ],
                    ],
                    703 =>
                    [
                        'name' => 'Jhargram',
                        'childs' =>
                        [
                            0 => 'Binpur - I',
                            1 => 'Binpur - II',
                            2 => 'Gopiballavpur - I',
                            3 => 'Gopiballavpur - II',
                            4 => 'Jamboni',
                            5 => 'Jhargram',
                            6 => 'Nayagram',
                            7 => 'Sankrail',
                        ],
                    ],
                    307 =>
                    [
                        'name' => 'Birbhum',
                        'childs' =>
                        [
                            0 => 'Bolpur Sriniketan',
                            1 => 'Dubrajpur',
                            2 => 'Illambazar',
                            3 => 'Khoyrasol',
                            4 => 'Labpur',
                            5 => 'Mayureswar - I',
                            6 => 'Mayureswar - II',
                            7 => 'Mohammad Bazar',
                            8 => 'Murarai - I',
                            9 => 'Murarai - II',
                            10 => 'Nalhati - I',
                            11 => 'Nalhati - II',
                            12 => 'Nanoor',
                            13 => 'Rajnagar',
                            14 => 'Rampurhat - I',
                            15 => 'Rampurhat - II',
                            16 => 'Sainthia',
                            17 => 'Suri - I',
                            18 => 'Suri - II',
                        ],
                    ],
                    320 =>
                    [
                        'name' => 'Nadia',
                        'childs' =>
                        [
                            0 => 'Chakdah',
                            1 => 'Chapra',
                            2 => 'Hanskhali',
                            3 => 'Haringhata',
                            4 => 'Kaliganj',
                            5 => 'Kalyani',
                            6 => 'Karimpur - I',
                            7 => 'Karimpur - II',
                            8 => 'Krishnaganj',
                            9 => 'Krishnagar - I',
                            10 => 'Krishnagar - II',
                            11 => 'Nabadwip',
                            12 => 'Nakashipara',
                            13 => 'Ranaghat - I',
                            14 => 'Ranaghat - II',
                            15 => 'Santipur',
                            16 => 'Tehatta - I',
                            17 => 'Tehatta - II',
                        ],
                    ],
                    318 =>
                    [
                        'name' => 'Paschim Medinipur',
                        'childs' =>
                        [
                            0 => 'Chandrakona - I',
                            1 => 'Chandrakona - II',
                            2 => 'Dantan - I',
                            3 => 'Dantan - II',
                            4 => 'Daspur - I',
                            5 => 'Daspur - II',
                            6 => 'Debra',
                            7 => 'Garbeta - I',
                            8 => 'Garbeta - II',
                            9 => 'Garbeta - III',
                            10 => 'Ghatal',
                            11 => 'Keshiary',
                            12 => 'Keshpur',
                            13 => 'Kharagpur - I',
                            14 => 'Kharagpur - II',
                            15 => 'Midnapore',
                            16 => 'Mohanpur',
                            17 => 'Narayangarh',
                            18 => 'Pingla',
                            19 => 'Sabang',
                            20 => 'Salbani',
                        ],
                    ],
                    311 =>
                    [
                        'name' => 'Uttar Dinajpur',
                        'childs' =>
                        [
                            0 => 'Chopra',
                            1 => 'Goalpokher - I',
                            2 => 'Goalpokher - II',
                            3 => 'Hemtabad',
                            4 => 'Islampur',
                            5 => 'Itahar',
                            6 => 'Kaliaganj',
                            7 => 'Karandighi',
                            8 => 'Raiganj',
                        ],
                    ],
                    308 =>
                    [
                        'name' => 'Cooch Behar',
                        'childs' =>
                        [
                            0 => 'Cooch Behar - I',
                            1 => 'Cooch Behar - II',
                            2 => 'Dinhata - I',
                            3 => 'Dinhata - II',
                            4 => 'Haldibari',
                            5 => 'Mathabhanga - I',
                            6 => 'Mathabhanga-II',
                            7 => 'Mekliganj',
                            8 => 'Sitai',
                            9 => 'Sitalkuchi',
                            10 => 'Tufanganj - I',
                            11 => 'Tufanganj - II',
                        ],
                    ],
                    309 =>
                    [
                        'name' => 'Darjeeling',
                        'childs' =>
                        [
                            0 => 'Darjeeling Pulbazar',
                            1 => 'Jorebunglow Sukiapokhri',
                            2 => 'Kharibari',
                            3 => 'Kurseong',
                            4 => 'Matigara',
                            5 => 'Mirik',
                            6 => 'Naxalbari',
                            7 => 'Phansidewa',
                            8 => 'Rangli Rangliot',
                        ],
                    ],
                    702 =>
                    [
                        'name' => 'Kalimpong',
                        'childs' =>
                        [
                            0 => 'Gorubathan',
                            1 => 'Kalimpong -I',
                            2 => 'Lava',
                            3 => 'Lavaold',
                            4 => 'Pedong',
                        ],
                    ],
                    315 =>
                    [
                        'name' => 'Kolkata',
                        'childs' =>
                        [],
                    ],
                ],
            ],
            16 =>
            [
                'name' => 'Tripura',
                'childs' =>
                [
                    654 =>
                    [
                        'name' => 'Gomati',
                        'childs' =>
                        [
                            0 => 'Amarpur',
                            1 => 'Karbook',
                            2 => 'Udaipur',
                        ],
                    ],
                    269 =>
                    [
                        'name' => 'Dhalai',
                        'childs' =>
                        [
                            0 => 'Ambassa',
                            1 => 'Gonda Twisa',
                            2 => 'Kamalpur',
                            3 => 'Longtharai Valley',
                        ],
                    ],
                    271 =>
                    [
                        'name' => 'South Tripura',
                        'childs' =>
                        [
                            0 => 'Belonia',
                            1 => 'Sabroom',
                            2 => 'Santirbazar',
                        ],
                    ],
                    653 =>
                    [
                        'name' => 'Sepahijala',
                        'childs' =>
                        [
                            0 => 'Bishalgarh',
                            1 => 'Jampuijala',
                            2 => 'Sonamura',
                        ],
                    ],
                    270 =>
                    [
                        'name' => 'North Tripura',
                        'childs' =>
                        [
                            0 => 'Dharmanagar',
                            1 => 'Kanchanpur',
                            2 => 'Panisagar',
                        ],
                    ],
                    272 =>
                    [
                        'name' => 'West Tripura',
                        'childs' =>
                        [
                            0 => 'Jirania',
                            1 => 'Mohanpur',
                            2 => 'Sadar',
                        ],
                    ],
                    655 =>
                    [
                        'name' => 'Unakoti',
                        'childs' =>
                        [
                            0 => 'Kailashahar',
                            1 => 'Kumarghat',
                        ],
                    ],
                    652 =>
                    [
                        'name' => 'Khowai',
                        'childs' =>
                        [
                            0 => 'Khowai',
                            1 => 'Teliamura',
                        ],
                    ],
                ],
            ],
            34 =>
            [
                'name' => 'Puducherry',
                'childs' =>
                [
                    600 =>
                    [
                        'name' => 'Puducherry',
                        'childs' =>
                        [
                            0 => 'Bahour',
                            1 => 'Mahe',
                            2 => 'Oulgaret',
                            3 => 'Puducherry',
                            4 => 'Villianur',
                            5 => 'Yanam',
                        ],
                    ],
                    598 =>
                    [
                        'name' => 'Karaikal',
                        'childs' =>
                        [
                            0 => 'Karaikal',
                            1 => 'Thirunallar',
                        ],
                    ],
                ],
            ],
            30 =>
            [
                'name' => 'Goa',
                'childs' =>
                [
                    551 =>
                    [
                        'name' => 'North Goa',
                        'childs' =>
                        [
                            0 => 'Bardez',
                            1 => 'Bicholim',
                            2 => 'Pernem',
                            3 => 'Satari',
                            4 => 'Tiswadi',
                        ],
                    ],
                    552 =>
                    [
                        'name' => 'South Goa',
                        'childs' =>
                        [
                            0 => 'Canacona',
                            1 => 'Dharbandora',
                            2 => 'Mormugao',
                            3 => 'Ponda',
                            4 => 'Quepem',
                            5 => 'Salcete',
                            6 => 'Sanguem',
                        ],
                    ],
                ],
            ],
            14 =>
            [
                'name' => 'Manipur',
                'childs' =>
                [
                    252 =>
                    [
                        'name' => 'Bishnupur',
                        'childs' =>
                        [
                            0 => 'Bishnupur',
                            1 => 'Moirang',
                            2 => 'Nambol',
                        ],
                    ],
                    713 =>
                    [
                        'name' => 'Jiribam',
                        'childs' =>
                        [
                            0 => 'Borobekra',
                            1 => 'Jiribam',
                        ],
                    ],
                    712 =>
                    [
                        'name' => 'Kangpokpi',
                        'childs' =>
                        [
                            0 => 'Bungte Chiru',
                            1 => 'Champhai',
                            2 => 'Island',
                            3 => 'Kangchup Geljang',
                            4 => 'Kangpokpi',
                            5 => 'Lhungtin',
                            6 => 'Saikul',
                            7 => 'Saitu-Gamphazol',
                            8 => 'T Vaichong',
                        ],
                    ],
                    253 =>
                    [
                        'name' => 'Chandel',
                        'childs' =>
                        [
                            0 => 'Chakpikarong',
                            1 => 'Chandel',
                            2 => 'Khengjoy',
                        ],
                    ],
                    257 =>
                    [
                        'name' => 'Senapati',
                        'childs' =>
                        [
                            0 => 'Chilivai Phaibung',
                            1 => 'Paomata',
                            2 => 'Purul',
                            3 => 'Song Song',
                            4 => 'Tadubi',
                            5 => 'Willong',
                        ],
                    ],
                    260 =>
                    [
                        'name' => 'Ukhrul',
                        'childs' =>
                        [
                            0 => 'Chingai',
                            1 => 'Jessami',
                            2 => 'LM',
                            3 => 'Ukhrul',
                        ],
                    ],
                    254 =>
                    [
                        'name' => 'Churachandpur',
                        'childs' =>
                        [
                            0 => 'Churachandpur',
                            1 => 'Henglep',
                            2 => 'Kangvai',
                            3 => 'Mualnuam',
                            4 => 'Saikot',
                            5 => 'Samulamlan',
                            6 => 'Sangaikot',
                            7 => 'Singngat',
                            8 => 'Suangdoh',
                            9 => 'Tuibong',
                        ],
                    ],
                    714 =>
                    [
                        'name' => 'Noney',
                        'childs' =>
                        [
                            0 => 'Haochong',
                            1 => 'Khoupum',
                            2 => 'Longmai',
                            3 => 'Nungba',
                        ],
                    ],
                    711 =>
                    [
                        'name' => 'Kakching',
                        'childs' =>
                        [
                            0 => 'Kakching',
                            1 => 'Waikhong',
                        ],
                    ],
                    717 =>
                    [
                        'name' => 'Kamjong',
                        'childs' =>
                        [
                            0 => 'Kamjong',
                            1 => 'Kasom Khullen',
                            2 => 'Phungyar',
                            3 => 'Sahamphung',
                        ],
                    ],
                    255 =>
                    [
                        'name' => 'Imphal East',
                        'childs' =>
                        [
                            0 => 'Keirao Bitra',
                            1 => 'Porompat',
                            2 => 'Sawombung',
                        ],
                    ],
                    256 =>
                    [
                        'name' => 'Imphal West',
                        'childs' =>
                        [
                            0 => 'Lamphelpat',
                            1 => 'Lamsang',
                            2 => 'Patsoi',
                            3 => 'Wangoi',
                        ],
                    ],
                    259 =>
                    [
                        'name' => 'Thoubal',
                        'childs' =>
                        [
                            0 => 'Lilong',
                            1 => 'Thoubal',
                        ],
                    ],
                    716 =>
                    [
                        'name' => 'Tengnoupal',
                        'childs' =>
                        [
                            0 => 'Machi',
                            1 => 'Moreh',
                            2 => 'Tengnoupal',
                        ],
                    ],
                    258 =>
                    [
                        'name' => 'Tamenglong',
                        'childs' =>
                        [
                            0 => 'Tamenglong',
                            1 => 'Tamenglong North',
                            2 => 'Tamenglong West',
                        ],
                    ],
                    715 =>
                    [
                        'name' => 'Pherzawl',
                        'childs' =>
                        [
                            0 => 'Thanlon',
                            1 => 'Tipaimukh',
                            2 => 'Vangai Range',
                        ],
                    ],
                ],
            ],
            35 =>
            [
                'name' => 'Andaman And Nicobar Islands',
                'childs' =>
                [
                    603 =>
                    [
                        'name' => 'Nicobars',
                        'childs' =>
                        [
                            0 => 'Car Nicobar',
                            1 => 'Great Nicobar',
                            2 => 'Nancowry',
                        ],
                    ],
                    632 =>
                    [
                        'name' => 'North And Middle Andaman',
                        'childs' =>
                        [
                            0 => 'Diglipur',
                            1 => 'Mayabunder',
                            2 => 'Rangat',
                        ],
                    ],
                    602 =>
                    [
                        'name' => 'South Andamans',
                        'childs' =>
                        [
                            0 => 'Ferrargunj',
                            1 => 'Little Andaman',
                            2 => 'Port Blair',
                        ],
                    ],
                ],
            ],
            4 =>
            [
                'name' => 'Chandigarh',
                'childs' =>
                [
                    44 =>
                    [
                        'name' => 'Chandigarh',
                        'childs' =>
                        [
                            0 => 'Chandigarh',
                        ],
                    ],
                ],
            ],
            11 =>
            [
                'name' => 'Sikkim',
                'childs' =>
                [
                    226 =>
                    [
                        'name' => 'Mangan',
                        'childs' =>
                        [
                            0 => 'Chungthang',
                            1 => 'Dzongu',
                            2 => 'Kabi',
                            3 => 'Mangan',
                        ],
                    ],
                    228 =>
                    [
                        'name' => 'Gyalshing',
                        'childs' =>
                        [
                            0 => 'Dentam',
                            1 => 'Gyalshing',
                            2 => 'Yuksom',
                        ],
                    ],
                    225 =>
                    [
                        'name' => 'Gangtok',
                        'childs' =>
                        [
                            0 => 'Gangtok',
                            1 => 'Rabdang',
                        ],
                    ],
                    227 =>
                    [
                        'name' => 'Namchi',
                        'childs' =>
                        [
                            0 => 'Jorethang',
                            1 => 'Namchi',
                            2 => 'Ravangla',
                            3 => 'Yangang',
                        ],
                    ],
                    742 =>
                    [
                        'name' => 'Soreng',
                        'childs' =>
                        [
                            0 => 'Mangalbarey',
                            1 => 'Soreng',
                        ],
                    ],
                    741 =>
                    [
                        'name' => 'Pakyong',
                        'childs' =>
                        [
                            0 => 'Pakyong',
                            1 => 'Rangpo',
                            2 => 'Rongli',
                        ],
                    ],
                ],
            ],
            38 =>
            [
                'name' => 'The Dadra And Nagar Haveli And Daman And Diu',
                'childs' =>
                [
                    465 =>
                    [
                        'name' => 'Dadra And Nagar Haveli',
                        'childs' =>
                        [
                            0 => 'Dadra & Nagar Haveli',
                        ],
                    ],
                    463 =>
                    [
                        'name' => 'Daman',
                        'childs' =>
                        [
                            0 => 'Daman',
                        ],
                    ],
                    464 =>
                    [
                        'name' => 'Diu',
                        'childs' =>
                        [
                            0 => 'Diu',
                        ],
                    ],
                ],
            ],
            37 =>
            [
                'name' => 'Ladakh',
                'childs' =>
                [
                    9 =>
                    [
                        'name' => 'Leh Ladakh',
                        'childs' =>
                        [
                            0 => 'Diskit',
                            1 => 'Durbuk',
                            2 => 'Khaltsi',
                            3 => 'Kharu',
                            4 => 'Leh',
                            5 => 'Nyoma',
                            6 => 'Saspol',
                            7 => 'Sumoor',
                        ],
                    ],
                    6 =>
                    [
                        'name' => 'Kargil',
                        'childs' =>
                        [
                            0 => 'Drass',
                            1 => 'Kargil',
                            2 => 'Sankoo',
                            3 => 'Shakar Chiktan',
                            4 => 'Shergole',
                            5 => 'Taisuru',
                            6 => 'Zanskar',
                        ],
                    ],
                ],
            ],
        ];

        // first lets insert India as country
        $dbRow = [
            'locationName' => "India",
            'locationType' => 'Country',
            'parentLocationId' => null
        ];

        $this->db->table('locationMaster')->insert($dbRow);
        $countryId = $this->db->insertID();

        foreach ($locationData as $i => $states) {

            $dbRow = [
                'locationName' => $states["name"],
                'locationType' => 'State',
                'parentLocationId' => $countryId
            ];

            $this->db->table('locationMaster')->insert($dbRow);
            $parentId = $this->db->insertID();

            foreach ($states["childs"] as $j => $districts) {

                $dbRow = [
                    'locationName' => $districts["name"],
                    'locationType' => 'District',
                    'parentLocationId' => $parentId
                ];

                $this->db->table('locationMaster')->insert($dbRow);

                $parentId2 = $this->db->insertID();


                foreach ($districts["childs"] as $taluka) {

                    $dbRow = [
                        'locationName' => $taluka,
                        'locationType' => 'Taluka',
                        'parentLocationId' => $parentId2
                    ];

                    $this->db->table('locationMaster')->insert($dbRow);
                }
            }
        }

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
