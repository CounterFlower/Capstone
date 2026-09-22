# Barangay Bagumbayan Management & E-Governance Information System

A centralized web-based management and public service platform developed for Barangay Bagumbayan, Daraga, Albay. The system streamlines resident recordkeeping, automates official document issuance with digital anti-counterfeit QR verification, records blotter/incident cases, and manages community activity registrations.

------------------------------------------------------------------------------------------------------------------
Project Overview

Traditional barangay operations face challenges with paper-based filing, slow manual certification processing, difficulty monitoring community program attendance, and vulnerability to forged clearances. 

This platform addresses these operational bottlenecks by providing:
- A secure administrative dashboard for barangay officials and staff.
- Centralized resident and household profiling across all puroks.
- On-demand document generation with tamper-proof QR code verification.
- Incident and blotter management compliant with Katarungang Pambarangay procedures.
- Community events scheduling and resident RSVP tracking.

------------------------------------------------------------------------------------------------------------------
Key Modules & Features

1. Resident & Household Profiling
- Centralized demographic database (Purok distribution, age, civil status, contact information).
- Household indexing and resident relationship management.

2. Document Issuance & Anti-Counterfeit QR Verification
- Generation of Barangay Clearances, Certificates of Indigency, and Certificates of Residency.
- Automated creation of unique SHA-256 digital verification hashes (`QR_Hash`).
- Public verification portal: scan the printed document's QR code using any smartphone to authenticate records directly against the barangay database.

3. Katarungang Pambarangay (Blotter & Incident Reporting)
- Case filing for complainants, respondents, and witnesses.
- Case tracking pipeline (Pending, Active, Resolved, Escalated).
- Detailed incident review and official resolution logging.

4. Community Events & Activity Enlistment
- Barangay activity scheduler with capacity management and live slot tracking.
- Public resident self-enlistment / RSVP tracking with attendee logging.

5. Administrative Security & Governance
- Role-based staff authentication.
- Server-side inactivity automatic logout (2-minute session timeout).

------------------------------------------------------------------------------------------------------------------
Technology Stack

- Backend Framework: PHP 8.2+ / Laravel
- Database: MySQL (`capstone`)
- Frontend: Blade Templating, HTML5, CSS3, JavaScript
- QR Code Engine: `bacon/bacon-qr-code` (Vector SVG generation)
- Version Control: Git & GitHub

------------------------------------------------------------------------------------------------------------------
Installation & Local Setup

Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL (via XAMPP or native service)

------------------------------------------------------------------------------------------------------------------
Setup Steps

1. Clone the repository:
   ```bash
   git clone [https://github.com/CounterFlower/Capstone.git](https://github.com/CounterFlower/Capstone.git)
   cd Capstone