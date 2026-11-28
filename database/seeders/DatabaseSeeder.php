<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Users (Super Admin / Punong Barangay, Barangay Staff / Secretary, and BHW)
        $systemUsers = [
            [
                'Username'      => 'admin',
                'Password_Hash' => Hash::make('password'),
                'Role'          => 'Super Admin',
                'Full_Name'     => 'System Administrator',
                'Is_Active'     => 1,
            ],
            [
                'Username'      => 'kap',
                'Password_Hash' => Hash::make('12345678'),
                'Role'          => 'Punong Barangay',
                'Full_Name'     => 'Prince Marvin E. Azul',
                'Is_Active'     => 1,
            ],
            [
                'Username'      => 'staff',
                'Password_Hash' => Hash::make('12345678'),
                'Role'          => 'Barangay Secretary',
                'Full_Name'     => 'Francis Julius G. Castuera',
                'Is_Active'     => 1,
            ],
            [
                'Username'      => 'bhw_bagumbayan',
                'Password_Hash' => Hash::make('12345678'),
                'Role'          => 'BHW',
                'Full_Name'     => 'Maria Elena R. Santos (BHW)',
                'Is_Active'     => 1,
            ],
        ];

        foreach ($systemUsers as$user) {
            DB::table('system_user')->updateOrInsert(
                ['Username' => $user['Username']],
                [
                    'Password_Hash' => $user['Password_Hash'],
                    'Role'          => $user['Role'],
                    'Full_Name'     => $user['Full_Name'],
                    'Is_Active'     => $user['Is_Active'],
                ]
            );
        }

        // 2. Incident Categories
        DB::table('incident_types')->insertOrIgnore([
            ['Category_Id' => 1, 'Category' => 'Noise Complaint'],
            ['Category_Id' => 2, 'Category' => 'Property Boundary Dispute'],
            ['Category_Id' => 3, 'Category' => 'Physical Altercation'],
            ['Category_Id' => 4, 'Category' => 'Vandalism / Property Damage'],
            ['Category_Id' => 5, 'Category' => 'Curfew Violation'],
            ['Category_Id' => 6, 'Category' => 'Pet / Animal Nuisance'],
        ]);

        // 3. Households (Purok 1 through 6 in Bagumbayan, Daraga, Albay)
        DB::table('household')->insertOrIgnore([
            ['Household_Index' => 1, 'Household_Id' => 3321412, 'House_Number' => '381', 'Zone_Purok' => 'Purok 4'],
            ['Household_Index' => 2, 'Household_Id' => 3312341, 'House_Number' => '241', 'Zone_Purok' => 'Purok 2'],
            ['Household_Index' => 3, 'Household_Id' => 4412312, 'House_Number' => '112', 'Zone_Purok' => 'Purok 6'],
            ['Household_Index' => 4, 'Household_Id' => 1132123, 'House_Number' => '540', 'Zone_Purok' => 'Purok 1'],
            ['Household_Index' => 5, 'Household_Id' => 2245123, 'House_Number' => '082', 'Zone_Purok' => 'Purok 3'],
            ['Household_Index' => 6, 'Household_Id' => 5567231, 'House_Number' => '195', 'Zone_Purok' => 'Purok 5'],
        ]);

        // 4. Guests
        DB::table('guest')->insertOrIgnore([
            [
                'Guest_Id'       => 1,
                'First_Name'     => 'Brad',
                'Middle_Name'    => 'Bull',
                'Last_Name'      => 'Pit',
                'Contact_Number' => '09123456789',
                'Address'        => 'Brgy. Bagumbayan, Daraga, Albay',
            ],
            [
                'Guest_Id'       => 2,
                'First_Name'     => 'Maria Clara',
                'Middle_Name'    => 'Santos',
                'Last_Name'      => 'Salazar',
                'Contact_Number' => '09287765432',
                'Address'        => 'Purok 3, Bagumbayan, Daraga, Albay',
            ],
        ]);

        // 5. Residents across Purok 1 to 6
        $residents = [
            [
                'Resident_ID'     => 1,
                'Household_Index' => 4,
                'First_Name'      => 'Juan',
                'Middle_Name'     => 'Dela',
                'Last_Name'       => 'Cruz',
                'Date_of_Birth'   => '1998-04-12',
                'Gender'          => 'Male',
                'Contact_Number'  => '09171234561',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Daraga',
                'Civil_Status'    => 'Single',
            ],
            [
                'Resident_ID'     => 2,
                'Household_Index' => 2,
                'First_Name'      => 'Elena',
                'Middle_Name'     => 'Reyes',
                'Last_Name'       => 'Morales',
                'Date_of_Birth'   => '1995-11-03',
                'Gender'          => 'Female',
                'Contact_Number'  => '09192345672',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Legazpi City',
                'Civil_Status'    => 'Married',
            ],
            [
                'Resident_ID'     => 3,
                'Household_Index' => 5,
                'First_Name'      => 'Ramon',
                'Middle_Name'     => 'Bautista',
                'Last_Name'       => 'Santos',
                'Date_of_Birth'   => '1987-06-20',
                'Gender'          => 'Male',
                'Contact_Number'  => '09203456783',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Daraga',
                'Civil_Status'    => 'Married',
            ],
            [
                'Resident_ID'     => 4,
                'Household_Index' => 1,
                'First_Name'      => 'Prince Marvin',
                'Middle_Name'     => 'Engay',
                'Last_Name'       => 'Azul',
                'Date_of_Birth'   => '2004-08-25',
                'Gender'          => 'Male',
                'Contact_Number'  => '09189231139',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Legazpi City',
                'Civil_Status'    => 'Single',
            ],
            [
                'Resident_ID'     => 5,
                'Household_Index' => 6,
                'First_Name'      => 'Francis Julius',
                'Middle_Name'     => 'Galias',
                'Last_Name'       => 'Castuera',
                'Date_of_Birth'   => '2004-03-03',
                'Gender'          => 'Male',
                'Contact_Number'  => '09093241232',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Daraga',
                'Civil_Status'    => 'Single',
            ],
            [
                'Resident_ID'     => 6,
                'Household_Index' => 3,
                'First_Name'      => 'Alyssa',
                'Middle_Name'     => 'Mae',
                'Last_Name'       => 'Balmes',
                'Date_of_Birth'   => '2001-09-18',
                'Gender'          => 'Female',
                'Contact_Number'  => '09214567894',
                'Is_Verified'     => 1,
                'Place_of_Birth'  => 'Daraga',
                'Civil_Status'    => 'Single',
            ],
        ];

        // Ensure Purok and Address fields are present if defined in schema
        foreach ($residents as &$resident) {
            $purokNumber = ($resident['Household_Index'] ?? 1);
            if (Schema::hasColumn('resident', 'Purok')) {
                $resident['Purok'] =$purokNumber;
            }
            if (Schema::hasColumn('resident', 'Purok_Number')) {
                $resident['Purok_Number'] =$purokNumber;
            }
            if (Schema::hasColumn('resident', 'Address')) {
                $resident['Address'] = "Purok {$purokNumber}, Bagumbayan, Daraga, Albay";
            }
        }
        unset($resident);
        DB::table('resident')->insertOrIgnore($residents);

        // 6. Announcements
        DB::table('announcement')->insertOrIgnore([
            [
                'Announcement_ID' => 1,
                'Title'           => 'Barangay General Assembly',
                'Content'         => 'All household heads are invited to attend the assembly at Bagumbayan Covered Court.',
                'Date_Posted'     => '2026-08-30 08:00:00',
                'Posted_By'       => 1,
            ],
            [
                'Announcement_ID' => 2,
                'Title'           => 'Anti-Dengue Fogging Operations',
                'Content'         => 'Fogging scheduled across Purok 1 to Purok 6 starting Friday morning.',
                'Date_Posted'     => '2026-09-02 09:00:00',
                'Posted_By'       => 1,
            ],
        ]);

        // 7. Audit Logs
        DB::table('audit_log')->insertOrIgnore([
            [
                'Log_ID'           => 1,
                'User_ID'          => 1,
                'Action_Performed' => 'System initialized and default administrator logged in.',
                'Log_Timestamp'    => '2026-08-30 08:30:00',
            ],
        ]);

        // 8. Document Requests (Varied dates, types, and statuses)
        $docRequests = [
            [
                'Request_ID'      => 1,
                'Resident_ID'     => 4,
                'Date_Requested'  => '2026-08-28 09:15:00',
                'Years_Stayed'    => 5,
                'Document_Type'   => 'Barangay Clearance',
                'Purpose'         => 'Employment Requirements',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-08-29 14:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 2,
                'Resident_ID'     => 5,
                'Date_Requested'  => '2026-08-29 10:30:00',
                'Years_Stayed'    => 8,
                'Document_Type'   => 'Certificate of Residency',
                'Purpose'         => 'Bank Account Application',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-08-30 10:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 3,
                'Resident_ID'     => 1,
                'Date_Requested'  => '2026-08-31 13:45:00',
                'Years_Stayed'    => 3,
                'Document_Type'   => 'Certificate of Indigency',
                'Purpose'         => 'Medical Assistance / DSWD',
                'Status'          => 'Pending',
                'Pickup_Schedule' => null,
                'QR_Hash'         => null,
                'Processed_By'    => null,
            ],
            [
                'Request_ID'      => 4,
                'Resident_ID'     => 2,
                'Date_Requested'  => '2026-09-01 11:20:00',
                'Years_Stayed'    => 4,
                'Document_Type'   => 'Barangay Clearance',
                'Purpose'         => 'Passport Renewal',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-09-02 15:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 5,
                'Resident_ID'     => 6,
                'Date_Requested'  => '2026-09-02 15:10:00',
                'Years_Stayed'    => 2,
                'Document_Type'   => 'Barangay Business Clearance',
                'Purpose'         => 'Sari-Sari Store Permit Renewal',
                'Status'          => 'Not Approved',
                'Pickup_Schedule' => null,
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 6,
                'Resident_ID'     => 3,
                'Date_Requested'  => '2026-09-03 08:30:00',
                'Years_Stayed'    => 10,
                'Document_Type'   => 'Certificate of Good Moral Character',
                'Purpose'         => 'Scholarship Requirement',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-09-04 11:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 7,
                'Resident_ID'     => 4,
                'Date_Requested'  => '2026-09-04 09:00:00',
                'Years_Stayed'    => 5,
                'Document_Type'   => 'Barangay Clearance',
                'Purpose'         => 'Police Clearance Attachment',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-09-05 13:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 8,
                'Resident_ID'     => 5,
                'Date_Requested'  => '2026-09-05 14:20:00',
                'Years_Stayed'    => 8,
                'Document_Type'   => 'Certificate of Residency',
                'Purpose'         => 'Driver License Application',
                'Status'          => 'Pending',
                'Pickup_Schedule' => null,
                'QR_Hash'         => null,
                'Processed_By'    => null,
            ],
            [
                'Request_ID'      => 9,
                'Resident_ID'     => 1,
                'Date_Requested'  => '2026-09-06 10:15:00',
                'Years
