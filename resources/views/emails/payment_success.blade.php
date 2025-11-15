@extends('emails.layouts.app')

@section('content')
    <h2 style="color: #2d3748;">Payment Confirmation</h2>

    @if($recipientType === 'user')
        <p style="color: #4a5568;">Dear {{ $data['user_name'] }},</p>
        <p style="color: #4a5568;">Thank you for your payment. Here are your order details:</p>
    @elseif($recipientType === 'company')
        <p style="color: #4a5568;">Hello {{ $data['company_name'] }},</p>
        <p style="color: #4a5568;">A new payment has been completed for a valuation request placed by {{ $data['user_name'] }}.</p>
    @elseif($recipientType === 'admin')
        <p style="color: #4a5568;">Hello Admin,</p>
        <p style="color: #4a5568;">A new payment has been successfully processed.</p>
    @endif

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

    @if(isset($textDocuments) && count($textDocuments) > 0)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <h3 style="color: #2d3748; margin-bottom: 15px;">Document Information:</h3>
            @foreach($textDocuments as $textDoc)
                <div style="margin-bottom: 15px;">
                    <p style="color: #2d3748; font-weight: bold; margin-bottom: 5px;">
                        {{ $textDoc['document_name'] ?? 'Document' }}:
                    </p>
                    <p style="color: #4a5568; background-color: #f7fafc; padding: 10px; border-left: 3px solid #4299e1; margin: 0;">
                        {{ $textDoc['text_value'] ?? '' }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <p style="color: #4a5568; margin-top: 20px;">If you have any questions, please contact support.</p>

    <p style="color: #4a5568;">Best regards,<br><strong>Valumate Team</strong></p>
@endsection
