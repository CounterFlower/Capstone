@extends('layouts.public')

@section('content')
    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Public Service Module</p>
                <h2>Document Requests</h2>
            </div>
            <a class="button secondary" href="{{ route('home') }}">Back to Home</a>
        </div>

        <div class="service-grid">
            <article class="service-card">
                <h3>Barangay Clearance</h3>
                <p>General-purpose certification for local records and transactions.</p>
            </article>
            <article class="service-card">
                <h3>Certificate of Residency</h3>
                <p>Proof of current residence within Barangay Bagumbayan.</p>
            </article>
            <article class="service-card">
                <h3>Business Endorsement</h3>
                <p>Prototype request type for small business-related endorsements.</p>
            </article>
        </div>

        <section class="section">
            <div class="content-grid">
                <article class="card">
                    <h3>Prototype request form</h3>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Resident Name</th>
                                <td>Input field placeholder</td>
                            </tr>
                            <tr>
                                <th>Document Type</th>
                                <td>Dropdown placeholder</td>
                            </tr>
                            <tr>
                                <th>Purpose</th>
                                <td>Text field placeholder</td>
                            </tr>
                            <tr>
                                <th>Date Needed</th>
                                <td>Date field placeholder</td>
                            </tr>
                            <tr>
                                <th>Supporting Notes</th>
                                <td>Multiline note placeholder</td>
                            </tr>
                        </tbody>
                    </table>
                </article>

                <article class="card">
                    <h3>Processing stages</h3>
                    <div class="list-item">1. Request intake from resident</div>
                    <div class="list-item">2. Profile and record verification</div>
                    <div class="list-item">3. Approval by authorized barangay staff</div>
                    <div class="list-item">4. Printing, release, and request closure</div>
                </article>
            </div>
        </section>
    </section>
@endsection
