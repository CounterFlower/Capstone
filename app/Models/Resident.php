namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    // Specify non-standard table and primary key
    protected $table = 'resident';
    protected $primaryKey = 'Resident_ID';
    public $timestamps = false; // Add if you don't have created_at/updated_at columns

    protected $fillable = [
        'Household_Index',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Date_of_Birth',
        'Gender',
        'Contact_Number',
        'Is_Verified',
        'Place_of_Birth',
        'Civil_Status',
    ];

    protected $casts = [
        'Date_of_Birth' => 'date',
        'Is_Verified' => 'boolean',
    ];

    /**
     * Helper to get full name dynamically: $resident->full_name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->First_Name} {$this->Middle_Name} {$this->Last_Name}");
    }
}