@extends('emails.layouts.app')

@section('content')
    <h2 style="color: #2d3748;">Valuation Request Status Updated</h2>

    <p style="color: #4a5568;">Dear {{ $data['user_name'] }},</p>

    <p style="color: #4a5568;">
        This is to inform you that the status of your valuation request (Reference: <strong>{{ $data['reference'] }}</strong>) has been updated to:
    </p>

    <p style="font-size: 18px; color: #1a202c;"><strong>{{ $data['status'] }}</strong></p>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 15px;">
        <tbody>
            <tr>
                <td style="padding: 6px 0; font-weight: bold; color: #2d3748;">Property Type:</td>
                <td style="padding: 6px 0; color: #4a5568;">{{ $data['property_type'] }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold; color: #2d3748;">Location:</td>
                <td style="padding: 6px 0; color: #4a5568;">{{ $data['location'] }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold; color: #2d3748;">Order Date:</td>
                <td style="padding: 6px 0; color: #4a5568;">{{ $data['created_at_date'] }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold; color: #2d3748;">Order Time:</td>
                <td style="padding: 6px 0; color: #4a5568;">{{ $data['created_at_time'] }}</td>
            </tr>
        </tbody>
    </table>

    <p style="color: #4a5568;">If you have any questions, please contact support.</p>

    <p style="color: #4a5568;">Best regards,<br><strong>Valumate Team</strong></p>
@endsection
