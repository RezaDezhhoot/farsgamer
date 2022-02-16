<?php

namespace App\Http\Livewire\Admin\Securities;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Setting;

class IndexSecurity extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $max_order_image_size , $data = [] , $boycott = [] , $google , $min_price_to_request,
        $password_length , $dos_count , $max_profile_image_size , $valid_ticket_files , $valid_order_images , $ticket_per_day,
        $auth_image_pattern , $auth_note;
    public function mount()
    {
        $this->authorize('show_securities');
        $this->header = 'امنیت';
        $this->data['boycott'] = [
            'A' => [
                'AF' => 'Afghanistan',
                'AX' => 'Aland Islands',
                'AL' => 'Albania',
                'DZ' => 'Algeria',
                'AS' => 'American Samoa',
                'AD' => 'Andorra',
                'AO' => 'Angola',
                'AI' => 'Anguilla',
                'AQ' => 'Antarctica',
                'AG' => 'Antigua and Barbuda',
                'AR' => 'Argentina',
                'AM' => 'Armenia',
                'AW' => 'Aruba',
                'AU' => 'Australia',
                'AT' => 'Austria',
                'AZ' => 'Azerbaijan'
            ],
            'B' => [
                'BS' => 'Bahamas',
                'BH' => 'Bahrain',
                'BD' => 'Bangladesh',
                'BB' => 'Barbados',
                'BY' => 'Belarus',
                'BE' => 'Belgium',
                'BZ' => 'Belize',
                'BJ' => 'Benin',
                'BM' => 'Bermuda',
                'BT' => 'Bhutan',
                'BO' => 'Bolivia',
                'BA' => 'Bosnia and Herzegovina',
                'BW' => 'Botswana',
                'BV' => 'Bouvet Island',
                'BR' => 'Brazil',
                'VG' => 'British Virgin Islands',
                'IO' => 'British Indian Ocean Territory',
                'BN' => 'Brunei Darussalam',
                'BG' => 'Bulgaria',
                'BF' => 'Burkina Faso',
                'BI' => 'Burundi'
            ],
            'C' => [
                'KH' => 'Cambodia',
                'CM' => 'Cameroon',
                'CA' => 'Canada',
                'CV' => 'Cape Verde',
                'KY' => 'Cayman Islands',
                'CF' => 'Central African Republic',
                'TD' => 'Chad',
                'CL' => 'Chile',
                'CN' => 'China',
                'HK' => 'Hong Kong, SAR China',
                'MO' => 'Macao, SAR China',
                'CX' => 'Christmas Island',
                'CC' => 'Cocos (Keeling) Islands',
                'CO' => 'Colombia',
                'KM' => 'Comoros',
                'CG' => 'Congo (Brazzaville)',
                'CD' => 'Congo, (Kinshasa)',
                'CK' => 'Cook Islands',
                'CR' => 'Costa Rica',
                'CI' => 'Côte dIvoire',
                'HR' => 'Cuba',
                'CY' => 'Cyprus',
                'CZ' => 'Czech Republic'
            ],
            'D' => [
                'DK' => 'Denmark',
                'DJ' => 'Djibouti',
                'DM' => 'Dominica',
                'DO' => 'Dominican Republic'
            ],
            'E' => [
                'EC' => 'Ecuador',
                'EG' => 'Egypt',
                'SV' => 'El Salvador',
                'GQ' => 'Equatorial Guinea',
                'ER' => 'Eritrea',
                'EE' => 'Estonia',
                'ET' => 'Ethiopia'
            ],
            'F' => [
                'FK' => 'Falkland Islands (Malvinas)',
                'FO' => 'Faroe Islands',
                'FJ' => 'Fiji',
                'FI' => 'Finland',
                'FR' => 'France',
                'GF' => 'French Guiana',
                'PF' => 'French Polynesia',
                'TF' => 'French Southern Territories',
            ],
            'G' => [
                'GA' => 'Gabon',
                'GM' => 'GMGambia',
                'GE' => 'Georgia',
                'DE' => 'Germany',
                'GH' => 'Ghana',
                'GI' => 'Gibraltar',
                'GR' => 'Greece',
                'GL' => 'Greenland',
                'GD' => 'Grenada',
                'GP' => 'Guadeloupe',
                'GU' => 'Guam',
                'GT' => 'Guatemala',
                'GG' => 'Guernsey',
                'GN' => 'Guinea',
                'GW' => 'Guinea-Bissau',
                'GY' => 'Guyana'
            ],
            'H' => [
                'HT' => 'Haiti',
                'HM' => 'Heard and Mcdonald Islands',
                'VA' => 'Holy See (Vatican City State)',
                'HN' => 'Honduras',
                'HU' => 'Hungary'
            ],
            'I' => [
                'IS' => 'Iceland',
                'IN' => 'India',
                'ID' => 'Indonesia',
                'IR' => 'Iran, Islamic Republic of',
                'IQ' => 'Iraq',
                'IE' => 'Ireland',
                'IM' => 'Isle of Man',
                'IL' => 'Israel',
                'IT' => 'Italy'
            ],
            'J' => [
                'JM' => 'Jamaica',
                'JP' => 'Japan',
                'JE' => 'Jersey',
                'JO' => 'Jordan'
            ],
            'K' => [
                'KZ' => 'Kazakhstan',
                'KE' => 'Kenya',
                'KI' => 'Kiribati',
                'KP' => 'Korea (North)',
                'KR' => 'Korea (South)',
                'KW' => 'Kuwait',
                'KG' => 'Kyrgyzstan'
            ],
            'L' => [
                'LA' => 'Lao PDR',
                'LV' => 'Latvia',
                'LB' => 'Lebanon',
                'LS' => 'Lesotho',
                'LR' => 'Liberia',
                'LY' => 'Libya',
                'LI' => 'Liechtenstein',
                'LT' => 'Lithuania',
                'LU' => 'Luxembourg'
            ],
            'M' => [
                'MK' => 'Macedonia, Republic of',
                'MG' => 'Madagascar',
                'MW' => 'Malawi',
                'MY' => 'Malaysia',
                'MV' => 'Maldives',
                'ML' => 'Mali',
                'MT' => 'Malta',
                'MH' => 'Marshall Islands',
                'MQ' => 'Martinique',
                'MR' => 'Mauritania',
                'MU' => 'Mauritius',
                'YT' => 'Mayotte',
                'MX' => 'Mexico',
                'FM' => 'Micronesia, Federated States of',
                'MD' => 'Moldova',
                'MC' => 'Monaco',
                'MN' => 'Mongolia',
                'ME' => 'Montenegro',
                'MS' => 'Montserrat',
                'MA' => 'Morocco',
                'MZ' => 'Mozambique',
                'MM' => 'Myanmar'
            ],
            'N' => [
                'NA' => 'Namibia',
                'NR' => 'Nauru',
                'NP' => 'Nepal',
                'NL' => 'Netherlands',
                'AN' => 'Netherlands Antilles',
                'NC' => 'New Caledonia',
                'NZ' => 'New Zealand',
                'NI' => 'Nicaragua',
                'NE' => 'Niger',
                'NG' => 'Nigeria',
                'NU' => 'Niue',
                'NF' => 'Norfolk Island',
                'MP' => 'Northern Mariana Islands',
                'NO' => 'Norway'
            ],
            'O' => [
                'OM' => 'Oman'
            ],
            'P' => [
                'PK' => 'Pakistan',
                'PW' => 'Palau',
                'PS' => 'Palestinian Territory',
                'PA' => 'Panama',
                'PG' => 'Papua New Guinea',
                'PY' => 'Paraguay',
                'PE' => 'Peru',
                'PH' => 'Philippines',
                'PN' => 'Pitcairn',
                'PL' => 'Poland',
                'PT' => 'Portugal',
                'PR' => 'Puerto Rico'
            ],
            'Q' => [
                'QA' => 'Qatar'
            ],
            'R' => [
                'RE' => 'Réunion',
                'RO' => 'Romania',
                'RU' => 'Russian Federation',
                'RW' => 'Rwanda'
            ],
            'S' => [
                'BL' => 'Saint-Barthélemy',
                'SH' => 'Saint Helena',
                'KN' => 'Saint Kitts and Nevis',
                'LC' => 'Saint Lucia',
                'SA' => 'Saudi Arabia',
                'SN' => 'Senegal',
                'RS' => 'Serbia',
                'SK' => 'Slovakia',
                'SI' => 'Slovenia',
                'ZA' => 'South Africa',
                'ES' => 'Spain',
                'LK' => 'Sri Lanka',
                'SD' => 'Sudan',
                'SZ' => 'Swaziland',
                'SE' => 'Sweden',
                'CH' => 'Switzerland',
                'SY' => 'Syrian Arab Republic'
            ],
            'T' => [
                'TW' => 'Taiwan',
                'TJ' => 'Tajikistan',
                'TH' => 'Thailand',
                'TN' => 'Tunisia',
                'TR' => 'Turkey',
                'TM' => 'Turkmenistan'
            ],
            'U' => [
                'UA' => 'Ukraine',
                'AE' => 'United Arab Emirates',
                'GB' => 'United Kingdom',
                'US' => 'United States of America',
                'UZ' => 'Uzbekistan'
            ],
            'V' => [
                'VN' => 'Viet Nam'
            ],
        ];
        $this->boycott = Setting::getSingleRow('boycott',[]);
        $this->google = Setting::getSingleRow('google');
        $this->password_length = Setting::getSingleRow('password_length');
        $this->dos_count = Setting::getSingleRow('dos_count');
        $this->max_profile_image_size = Setting::getSingleRow('max_profile_image_size');
        $this->max_order_image_size = Setting::getSingleRow('max_order_image_size');
        $this->valid_order_images = Setting::getSingleRow('valid_order_images');
        $this->valid_ticket_files = Setting::getSingleRow('valid_ticket_files');
        $this->ticket_per_day = Setting::getSingleRow('ticket_per_day');
        $this->min_price_to_request = Setting::getSingleRow('min_price_to_request');
        $this->auth_image_pattern = Setting::getSingleRow('auth_image_pattern');
        $this->auth_note = Setting::getSingleRow('auth_note');
    }
    public function render()
    {
        return view('livewire.admin.securities.index-security')->extends('livewire.admin.layouts.admin');
    }

    public function store()
    {
        $this->authorize('edit_securities');
        $this->validate([
            'boycott' => ['nullable','array'],
            'google' => ['required','string'],
            'password_length' => ['required','integer','min:5'],
            'dos_count' => ['required','integer','min:3'],
            'max_profile_image_size' => ['required','integer','min:1024'],
            'max_order_image_size' => ['required','integer','min:1024'],
            'valid_order_images' => ['required','string','max:255'],
            'valid_ticket_files' => ['required','string','max:255'],
            'ticket_per_day' => ['required','integer','min:1'],
            'min_price_to_request' => ['required','integer','min:1'],
            'auth_image_pattern' => ['nullable','string','max:300'],
            'auth_note' => ['nullable','string','max:250']
        ], [], [
            'boycott' => 'تحریم ها',
            'google' => 'شناسه گوگل',
            'password_length' => 'حداقل طول پسورد',
            'dos_count' => 'حداکثر امکان برای درخواست های پیوسته سمت سرور',
            'max_profile_image_size' => 'حداکثر حجم تصاویر پروفایل',
            'max_order_image_size' => 'حداکثر حجم تصاویر اگهی ها',
            'valid_order_images' => 'فرمت های مجاز تصاویر اگهی ها',
            'valid_ticket_files' => 'فرمت های مجاز فایل های تیکت',
            'ticket_per_day' => 'حداکثر دفعات ارسال تیکت در روز',
            'min_price_to_request' => 'حداقل موجودی لازم برای برداشت',
            'auth_image_pattern' => 'تصویر نمونه برای احراز هویت',
            'auth_note' => 'متن توضیح برای احراز هویت',
        ]);
        Setting::updateOrCreate(['name' => 'boycott'], ['value' => json_encode($this->boycott)]);
        Setting::updateOrCreate(['name' => 'google'], ['value' => $this->google]);
        Setting::updateOrCreate(['name' => 'password_length'], ['value' => $this->password_length]);
        Setting::updateOrCreate(['name' => 'dos_count'], ['value' => $this->dos_count]);
        Setting::updateOrCreate(['name' => 'max_profile_image_size'], ['value' => $this->max_profile_image_size]);
        Setting::updateOrCreate(['name' => 'max_order_image_size'], ['value' => $this->max_order_image_size]);
        Setting::updateOrCreate(['name' => 'valid_order_images'], ['value' => $this->valid_order_images]);
        Setting::updateOrCreate(['name' => 'valid_ticket_files'], ['value' => $this->valid_ticket_files]);
        Setting::updateOrCreate(['name' => 'ticket_per_day'], ['value' => $this->ticket_per_day]);
        Setting::updateOrCreate(['name' => 'auth_image_pattern'], ['value' => $this->auth_image_pattern]);
        Setting::updateOrCreate(['name' => 'min_price_to_request'], ['value' => $this->min_price_to_request]);
        Setting::updateOrCreate(['name' => 'auth_note'], ['value' => $this->auth_note]);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}
