<?php

namespace App\Observers;

use App\Models\Vendor;

class VendorObserver
{
    private function encrypt($phone) {
        $array = [
            0 => 'a', 1 => 'g', 2 => 'y', 3 => 'h', 4 => 'k',
            5 => 'l', 6 => 'p', 7 => 'm', 8 => 'x', 9 => 'z',
            '+' => 'u'
        ];
        $numbers = array_keys($array);
        $letters = array_values($array);
        return str_ireplace($numbers, $letters, $phone);
    }


    public function creating(Vendor $vendor) {
        if($vendor->isDirty('contacts')) {
            $newContacts = [];
            foreach ($vendor->contacts as $contact) {
                $phone = $contact['phone'];
                $contact['phone_code'] = $this->encrypt($phone);
                $newContacts[] = $contact;
            }
            $vendor->contacts = $newContacts;
        }
    }

    public function updating(Vendor $vendor) {
        if($vendor->isDirty('contacts')) {
            $newContacts = [];
            foreach ($vendor->contacts as $contact) {
                $phone = $contact['phone'];
                $contact['phone_code'] = $this->encrypt($phone);
                $newContacts[] = $contact;
            }
            $vendor->contacts = $newContacts;
        }
    }
}
