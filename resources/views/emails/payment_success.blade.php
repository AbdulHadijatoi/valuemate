@extends('emails.layouts.app')

@section('content')
    <h2 style="color: #2d3748;">Payment Confirmation</h2>

    <p style="color: #4a5568;">Dear {{ $data['user_name'] }},</p>

    <p style="color: #4a5568;">Thank you for your payment. Here are your order details:</p>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 15px;">
        <tbody>
            @foreach([
                'Company' => $data['company_name'],
                'Reference' => $data['reference'],
                'Property Type' => $data['property_type'],
                'Service Type' => $data['service_type'],
                'Request Type' => $data['request_type'],
                'Location' => $data['location'],
                'Area' => $data['area'],
                'Total Amount' => $data['total_amount'] . ' OMR',
                'Order Date' => $data['created_at_date'],
                'Order Time' => $data['created_at_time'],
                'Payment Status' => $data['payment_status'],
                'Valuation Request Status' => $data['status'],
            ] as $label => $value)
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; color: #2d3748;">{{ $label }}:</td>
                    <td style="padding: 6px 0; color: #4a5568;">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="color: #4a5568;">If you have any questions, please contact support.</p>

    <p style="color: #4a5568;">Best regards,<br><strong>Valumate Team</strong></p>
@endsection
