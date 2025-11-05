# Support Chat API - Flutter Quick Start Guide

## Quick Reference

### Base URL
```
http://your-domain.com/api
```

### Authentication
All requests require Bearer token in header:
```
Authorization: Bearer {token}
```

---

## User Endpoints (For Mobile App Users)

### 1. Get or Create Room
```
POST /support-chat/room
Headers: Authorization: Bearer {token}
Body: None
```

### 2. Get Messages
```
GET /support-chat/messages
Headers: Authorization: Bearer {token}
```

### 3. Send Message
```
POST /support-chat/send
Headers: Authorization: Bearer {token}
Body: { "message": "Your message here" }
```

---

## Flutter Implementation Example

### 1. HTTP Service Class

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SupportChatService {
  final String baseUrl = 'http://your-domain.com/api';
  final storage = FlutterSecureStorage();

  Future<String?> _getToken() async {
    return await storage.read(key: 'auth_token');
  }

  // Get or create chat room
  Future<Map<String, dynamic>> getOrCreateRoom() async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/support-chat/room'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return json.decode(response.body);
  }

  // Get messages
  Future<Map<String, dynamic>> getMessages() async {
    final token = await _getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/support-chat/messages'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return json.decode(response.body);
  }

  // Send message
  Future<Map<String, dynamic>> sendMessage(String message) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/support-chat/send'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({'message': message}),
    );
    return json.decode(response.body);
  }
}
```

### 2. Polling for New Messages

```dart
import 'dart:async';

Timer? _pollingTimer;

void startPolling() {
  _pollingTimer = Timer.periodic(Duration(seconds: 10), (timer) async {
    final service = SupportChatService();
    final response = await service.getMessages();
    if (response['status'] == true) {
      // Update your UI with new messages
      updateMessages(response['data']);
    }
  });
}

void stopPolling() {
  _pollingTimer?.cancel();
}
```

### 3. Response Structure

```dart
// Get Messages Response
{
  "status": true,
  "data": [
    {
      "id": 1,
      "message": "Hello!",
      "sender_id": 2,
      "sender_name": "User Name",
      "is_admin": false,  // false = user message, true = admin message
      "is_read": false,
      "created_at": "2025-01-15 10:30:00",
      "created_at_human": "2 hours ago"
    }
  ]
}

// Send Message Response
{
  "status": true,
  "message": "Message sent successfully",
  "data": {
    "id": 1,
    "message": "Your message",
    "sender_id": 2,
    "created_at": "2025-01-15 10:30:00"
  }
}
```

---

## Important Notes

1. **Polling Interval**: Poll every 5-10 seconds for real-time feel
2. **Error Handling**: Always check `status` field in response
3. **Token Storage**: Use secure storage for tokens
4. **Message Length**: Max 5000 characters per message
5. **Auto Room Creation**: Room is created automatically on first message

---

## Full Documentation

See `SUPPORT_CHAT_API_DOCUMENTATION.md` for complete API documentation.

## Postman Collection

Import `Support_Chat_API.postman_collection.json` into Postman for testing.

