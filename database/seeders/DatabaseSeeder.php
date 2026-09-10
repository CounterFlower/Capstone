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
        // 1. Admin User
        DB::table('system_user')->insertOrIgnore([
            'User_ID'       => 1,
            'Username'      => 'admin',
            'Password_Hash' => Hash::make('password'),
            'Role'          => 'admin',
            'Full_Name'     => 'System Administrator',
            'Is_Active'     => 1,
        ]);

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
                $resident['Purok'] = $purokNumber;
            }
            if (Schema::hasColumn('resident', 'Purok_Number')) {
                $resident['Purok_Number'] = $purokNumber;
            }
            if (Schema::hasColumn('resident', 'Address')) {
                $resident['Address'] = "Purok {$purokNumber}, Bagumbayan, Daraga, Albay";
            }
        }
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
                'Years_Stayed'    => 3,
                'Document_Type'   => 'Certificate of Indigency',
                'Purpose'         => 'Hospital Bill Discount',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-09-07 09:30:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 10,
                'Resident_ID'     => 2,
                'Date_Requested'  => '2026-09-08 16:00:00',
                'Years_Stayed'    => 4,
                'Document_Type'   => 'Barangay Clearance',
                'Purpose'         => 'Job Application - Call Center',
                'Status'          => 'Released',
                'Pickup_Schedule' => '2026-09-09 10:00:00',
                'QR_Hash'         => null,
                'Processed_By'    => 1,
            ],
            [
                'Request_ID'      => 11,
                'Resident_ID'     => 6,
                'Date_Requested'  => '2026-09-09 11:45:00',
                'Years_Stayed'    => 2,
                'Document_Type'   => 'Barangay Business Clearance',
                'Purpose'         => 'Food Stall Business Permit',
                'Status'          => 'Pending',
                'Pickup_Schedule' => null,
                'QR_Hash'         => null,
                'Processed_By'    => null,
            ],
            [
                'Request_ID'      => 12,
                'Resident_ID'     => 3,
                'Date_Requested'  => '2026-09-10 13:10:00',
                'Years_Stayed'    => 10,
                'Document_Type'   => 'Certificate of Residency',
                'Purpose'         => 'Proof of Address for Credit Card',
                'Status'          => 'Pending',
                'Pickup_Schedule' => null,
                'QR_Hash'         => null,
                'Processed_By'    => null,
            ],
        ];

        foreach ($docRequests as &$doc) {
            if (Schema::hasColumn('document_request', 'created_at')) {
                $doc['created_at'] = $doc['Date_Requested'];
            }
        }
        DB::table('document_request')->insertOrIgnore($docRequests);

        // 9. Community Events
        DB::table('event')->insertOrIgnore([
            [
                'Event_ID'        => 1,
                'Event_Name'      => 'Free Rabies Vaccination',
                'Event_Date'      => '2026-08-31 08:00:00',
                'End_Date'        => '2026-08-31 12:00:00',
                'Location'        => 'Bagumbayan Covered Court',
                'Available_Slots' => 100,
                'Created_By'      => 1,
                'Summary'         => 'Free anti-rabies vaccination outreach for cats and dogs.',
                'Cover_Image'     => null,
            ],
            [
                'Event_ID'        => 2,
                'Event_Name'      => 'Barangay Clean-Up Drive',
                'Event_Date'      => '2026-09-12 06:00:00',
                'End_Date'        => '2026-09-12 11:00:00',
                'Location'        => 'Purok 1 to Purok 6 Main Canals',
                'Available_Slots' => 80,
                'Created_By'      => 1,
                'Summary'         => 'Community waterway desilting and street clearing activity.',
                'Cover_Image'     => null,
            ],
        ]);

        // 10. Event RSVPs
        DB::table('event_rsvp')->insertOrIgnore([
            [
                'RSVP_ID'           => 1,
                'Event_ID'          => 1,
                'Resident_ID'       => 4,
                'Date_Registered'   => '2026-08-30 10:00:00',
                'Attendance_Status' => 'Confirmed',
            ],
            [
                'RSVP_ID'           => 2,
                'Event_ID'          => 2,
                'Resident_ID'       => 5,
                'Date_Registered'   => '2026-09-02 09:30:00',
                'Attendance_Status' => 'Confirmed',
            ],
            [
                'RSVP_ID'           => 3,
                'Event_ID'          => 2,
                'Resident_ID'       => 1,
                'Date_Registered'   => '2026-09-04 14:15:00',
                'Attendance_Status' => 'Confirmed',
            ],
        ]);

   // 11. Incident Blotter (Multi-date timeline for case resolution graph)
        DB::table('incident_blotter')->insertOrIgnore([
            [
                'Incident_ID'       => 1,
                'Complainant_Id'    => 1,
                'Respondent_Id'     => 2,
                'Guest_Id'          => null,
                'Category_Id'       => 2,
                'Description'       => 'Purok 1 boundary fence dispute regarding drainage encroachment.',
                'Requested_Relief'  => 'Barangay mediation to verify land boundaries.',
                'Date_Reported'     => '2026-08-29 10:15:00',
                'Date_Filed'        => '2026-08-29 10:15:00',
                'Resolution_Status' => 'Settled',
                'Latitude'          => 13.142100,
                'Longitude'         => 123.714500,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 2,
                'Complainant_Id'    => 4,
                'Respondent_Id'     => 5,
                'Guest_Id'          => null,
                'Category_Id'       => 1,
                'Description'       => 'Maribukon. Makusugon ang patugtog sa videoke maski lagpas na curfew sa Purok 4.',
                'Requested_Relief'  => 'Patanidon ang respondent dapit sa curfew ordinance.',
                'Date_Reported'     => '2026-08-31 23:30:00',
                'Date_Filed'        => '2026-08-31 23:30:00',
                'Resolution_Status' => 'Settled',
                'Latitude'          => 13.141515,
                'Longitude'         => 123.715020,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 3,
                'Complainant_Id'    => 3,
                'Respondent_Id'     => 6,
                'Guest_Id'          => null,
                'Category_Id'       => 6,
                'Description'       => 'Unleashed pet dog frequently barking and chasing passersby in Purok 5.',
                'Requested_Relief'  => 'Proper dog leashing inside owner premises.',
                'Date_Reported'     => '2026-09-02 14:00:00',
                'Date_Filed'        => '2026-09-02 14:00:00',
                'Resolution_Status' => 'Resolved',
                'Latitude'          => 13.140800,
                'Longitude'         => 123.716100,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 4,
                'Complainant_Id'    => 6,
                'Respondent_Id'     => null,
                'Guest_Id'          => 1,
                'Category_Id'       => 4,
                'Description'       => 'Graffiti vandalism on the Purok 6 waiting shed wall.',
                'Requested_Relief'  => 'Repainting and review of neighborhood CCTV.',
                'Date_Reported'     => '2026-09-03 16:45:00',
                'Date_Filed'        => '2026-09-03 16:45:00',
                'Resolution_Status' => 'Pending',
                'Latitude'          => 13.143200,
                'Longitude'         => 123.713800,
                'Handled_By'        => null,
            ],
            [
                'Incident_ID'       => 5,
                'Complainant_Id'    => 2,
                'Respondent_Id'     => 3,
                'Guest_Id'          => null,
                'Category_Id'       => 5,
                'Description'       => 'Minors gathering outside past the 10:00 PM curfew hour near Purok 2 chapel.',
                'Requested_Relief'  => 'Tanod roving and advisory to parents.',
                'Date_Reported'     => '2026-09-05 22:15:00',
                'Date_Filed'        => '2026-09-05 22:15:00',
                'Resolution_Status' => 'Settled',
                'Latitude'          => 13.142500,
                'Longitude'         => 123.715400,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 6,
                'Complainant_Id'    => 1,
                'Respondent_Id'     => 4,
                'Guest_Id'          => null,
                'Category_Id'       => 1,
                'Description'       => 'Loud motorcycle modified muffler revving late night in Purok 1.',
                'Requested_Relief'  => 'Warning and muffler compliance check.',
                'Date_Reported'     => '2026-09-06 21:00:00',
                'Date_Filed'        => '2026-09-06 21:00:00',
                'Resolution_Status' => 'Pending',
                'Latitude'          => 13.141900,
                'Longitude'         => 123.714200,
                'Handled_By'        => null,
            ],
            [
                'Incident_ID'       => 7,
                'Complainant_Id'    => 5,
                'Respondent_Id'     => 2,
                'Guest_Id'          => null,
                'Category_Id'       => 3,
                'Description'       => 'Heated altercation during basketball match in Purok 6 covered court.',
                'Requested_Relief'  => 'Barangay mediation agreement and peace covenant.',
                'Date_Reported'     => '2026-09-07 17:30:00',
                'Date_Filed'        => '2026-09-07 17:30:00',
                'Resolution_Status' => 'Resolved',
                'Latitude'          => 13.142800,
                'Longitude'         => 123.715800,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 8,
                'Complainant_Id'    => 3,
                'Respondent_Id'     => null,
                'Guest_Id'          => 2,
                'Category_Id'       => 4,
                'Description'       => 'Stray cattle damaging vegetable garden plot in Purok 3.',
                'Requested_Relief'  => 'Identification of livestock owner for crop restitution.',
                'Date_Reported'     => '2026-09-08 07:15:00',
                'Date_Filed'        => '2026-09-08 07:15:00',
                'Resolution_Status' => 'Pending',
                'Latitude'          => 13.141100,
                'Longitude'         => 123.713500,
                'Handled_By'        => null,
            ],
            [
                'Incident_ID'       => 9,
                'Complainant_Id'    => 2,
                'Respondent_Id'     => 6,
                'Guest_Id'          => null,
                'Category_Id'       => 2,
                'Description'       => 'Overhanging tree branches threatening roof line after windy rainfall in Purok 2.',
                'Requested_Relief'  => 'Permit and assistance for tree trimming.',
                'Date_Reported'     => '2026-09-09 10:00:00',
                'Date_Filed'        => '2026-09-09 10:00:00',
                'Resolution_Status' => 'Settled',
                'Latitude'          => 13.143000,
                'Longitude'         => 123.714800,
                'Handled_By'        => 1,
            ],
            [
                'Incident_ID'       => 10,
                'Complainant_Id'    => 4,
                'Respondent_Id'     => 1,
                'Guest_Id'          => null,
                'Category_Id'       => 1,
                'Description'       => 'Late evening construction and hammering noise along Purok 4 alley.',
                'Requested_Relief'  => 'Enforce construction cutoff hours at 6:00 PM.',
                'Date_Reported'     => '2026-09-10 19:40:00',
                'Date_Filed'        => '2026-09-10 19:40:00',
                'Resolution_Status' => 'Pending',
                'Latitude'          => 13.141700,
                'Longitude'         => 123.715100,
                'Handled_By'        => null,
            ],
        ]);
    }
}