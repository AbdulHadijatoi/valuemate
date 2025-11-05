# Support Chat API Documentation

This document provides complete API documentation for the Support Chat system. The API works for both Vue3 frontend and Flutter mobile app.

## Base URL

```
http://your-domain.com/api
```

Replace with your actual API base URL.

## Authentication

All endpoints require Bearer token authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your_token}
```

Get the token by logging in through the `/login` endpoint.

---

## User Endpoints

### 1. Get or Create Chat Room

Get or create a chat room for the authenticated user.

**Endpoint:** `POST /support-chat/room`

**Headers:**
```
Authorization: Bearer {user_token}
Accept: application/json
```

**Request Body:** None (empty body)

**Response (200 OK):**
```json
{
    "status": true,
    "data": {
        "id": 1,
        "user_id": 2,
        "created_at": "2025-01-15T10:30:00.000000Z"
    }
}
```

**Notes:**
- If the user doesn't have a room, one will be created automatically
- Each user can only have one chat room
- The room ID is used in other endpoints

---

### 2. Get User Messages

Get all messages for the authenticated user's chat room.

**Endpoint:** `GET /support-chat/messages`

**Headers:**
```
Authorization: Bearer {user_token}
Accept: application/json
```

**Response (200 OK):**
```json
{
    "status": true,
    "data": [
        {
            "id": 1,
            "message": "Hello, I need help with my valuation request.",
            "sender_id": 2,
            "sender_name": "Test User 2",
            "is_admin": false,
            "is_read": false,
            "created_at": "2025-01-15 10:30:00",
            "created_at_human": "2 hours ago"
        },
        {
            "id": 2,
            "message": "Hello! Thank you for contacting us.",
            "sender_id": 1,
            "sender_name": "Admin User",
            "is_admin": true,
            "is_read": true,
            "created_at": "2025-01-15 11:00:00",
            "created_at_human": "1 hour ago"
        }
    ]
}
```

**Field Descriptions:**
- `id`: Message ID
- `message`: Message content
- `sender_id`: ID of the user who sent the message
- `sender_name`: Full name of the sender
- `is_admin`: `true` if message is from admin, `false` if from user
- `is_read`: Read status (always `true` for admin messages, may be `false` for user messages)
- `created_at`: Timestamp in format `Y-m-d H:i:s`
- `created_at_human`: Human-readable time (e.g., "2 hours ago")

**Notes:**
- Messages are ordered by creation date (oldest first)
- Poll this endpoint every 5-10 seconds for real-time updates

---

### 3. Send Message

Send a message to support.

**Endpoint:** `POST /support-chat/send`

**Headers:**
```
Authorization: Bearer {user_token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
    "message": "Hello, I need help with my valuation request."
}
```

**Validation Rules:**
- `message`: Required, string, max 5000 characters

**Response (200 OK):**
```json
{
    "status": true,
    "message": "Message sent successfully",
    "data": {
        "id": 1,
        "message": "Hello, I need help with my valuation request.",
        "sender_id": 2,
        "created_at": "2025-01-15 10:30:00"
    }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
    "message": "The message field is required.",
    "errors": {
        "message": [
            "The message field is required."
        ]
    }
}
```

**Notes:**
- If the user doesn't have a chat room, one will be created automatically
- Message is immediately visible to admin users

---

## Admin Endpoints

All admin endpoints require the `manage chats` permission.

### 1. Get All Chat Rooms

Get all chat rooms with unread message counts.

**Endpoint:** `GET /admin/support-chat/rooms`

**Headers:**
```
Authorization: Bearer {admin_token}
Accept: application/json
```

**Response (200 OK):**
```json
{
    "status": true,
    "data": [
        {
            "id": 1,
            "user_id": 2,
            "user_name": "Test User 2",
            "user_email": "testuser2@example.com",
            "unread_count": 3,
            "last_message": {
                "message": "Can someone please check my request?",
                "created_at": "2025-01-15 05:00:00",
                "created_at_human": "5 hours ago"
            },
            "updated_at": "2025-01-15 05:00:00",
            "created_at": "2025-01-13 10:30:00"
        },
        {
            "id": 2,
            "user_id": 3,
            "user_name": "Test User 3",
            "user_email": "testuser3@example.com",
            "unread_count": 0,
            "last_message": {
                "message": "Thank you for the excellent service!",
                "created_at": "2025-01-10 14:20:00",
                "created_at_human": "5 days ago"
            },
            "updated_at": "2025-01-10 14:20:00",
            "created_at": "2025-01-10 14:20:00"
        }
    ],
    "total_unread": 3
}
```

**Field Descriptions:**
- `id`: Chat room ID
- `user_id`: ID of the user who owns the room
- `user_name`: Full name of the user
- `user_email`: Email of the user
- `unread_count`: Number of unread messages from the user
- `last_message`: Last message in the conversation
- `total_unread`: Total unread messages across all rooms

**Notes:**
- Rooms are ordered by last activity (most recent first)
- `unread_count` only counts messages from users (not admin messages)
- Poll this endpoint every 10 seconds for real-time updates

---

### 2. Get Unread Count

Get the total count of unread messages from all users.

**Endpoint:** `GET /admin/support-chat/unread-count`

**Headers:**
```
Authorization: Bearer {admin_token}
Accept: application/json
```

**Response (200 OK):**
```json
{
    "status": true,
    "data": {
        "unread_count": 5
    }
}
```

**Notes:**
- Use this endpoint to display a badge/notification count
- Poll this endpoint every 10 seconds for real-time updates
- Only counts messages from users (not admin messages)

---

### 3. Get Room Messages

Get all messages for a specific chat room.

**Endpoint:** `GET /admin/support-chat/room/{roomId}/messages`

**Headers:**
```
Authorization: Bearer {admin_token}
Accept: application/json
```

**URL Parameters:**
- `roomId`: The ID of the chat room (integer)

**Response (200 OK):**
```json
{
    "status": true,
    "data": {
        "room": {
            "id": 1,
            "user_id": 2,
            "user_name": "Test User 2",
            "user_email": "testuser2@example.com"
        },
        "messages": [
            {
                "id": 1,
                "message": "Hello, I need help with my valuation request.",
                "sender_id": 2,
                "sender_name": "Test User 2",
                "is_admin": false,
                "is_read": true,
                "created_at": "2025-01-15 10:30:00",
                "created_at_human": "2 hours ago"
            },
            {
                "id": 2,
                "message": "Hello! Thank you for contacting us.",
                "sender_id": 1,
                "sender_name": "Admin User",
                "is_admin": true,
                "is_read": true,
                "created_at": "2025-01-15 11:00:00",
                "created_at_human": "1 hour ago"
            }
        ]
    }
}
```

**Notes:**
- When admin views a room, all unread messages from that user are automatically marked as read
- Messages are ordered by creation date (oldest first)
- `is_admin`: `true` if message is from admin, `false` if from user

**Error Response (404 Not Found):**
```json
{
    "message": "No query results for model [App\\Models\\ChatRoom] {roomId}"
}
```

---

### 4. Send Admin Reply

Send a reply message to a user in a specific chat room.

**Endpoint:** `POST /admin/support-chat/room/{roomId}/reply`

**Headers:**
```
Authorization: Bearer {admin_token}
Accept: application/json
Content-Type: application/json
```

**URL Parameters:**
- `roomId`: The ID of the chat room (integer)

**Request Body:**
```json
{
    "message": "Hello! Thank you for contacting us. Let me check your request."
}
```

**Validation Rules:**
- `message`: Required, string, max 5000 characters

**Response (200 OK):**
```json
{
    "status": true,
    "message": "Reply sent successfully",
    "data": {
        "id": 3,
        "message": "Hello! Thank you for contacting us. Let me check your request.",
        "sender_id": 1,
        "created_at": "2025-01-15 11:00:00"
    }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
    "message": "The message field is required.",
    "errors": {
        "message": [
            "The message field is required."
        ]
    }
}
```

**Error Response (404 Not Found):**
```json
{
    "message": "No query results for model [App\\Models\\ChatRoom] {roomId}"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```
Occurs when:
- No token is provided
- Token is invalid or expired

### 403 Forbidden
```json
{
    "message": "You do not have the 'manage chats' permission."
}
```
Occurs when admin endpoints are accessed without proper permissions.

### 422 Unprocessable Entity
```json
{
    "message": "The message field is required.",
    "errors": {
        "message": [
            "The message field is required."
        ]
    }
}
```
Occurs when validation fails.

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\ChatRoom] {roomId}"
}
```
Occurs when a chat room doesn't exist.

---

## Implementation Tips for Flutter

### 1. Polling for Real-time Updates

Since we're not using WebSockets, implement polling:

```dart
// Poll every 10 seconds for new messages
Timer.periodic(Duration(seconds: 10), (timer) {
  fetchMessages();
});
```

### 2. Store Token Securely

Use `flutter_secure_storage` or similar to store the authentication token:

```dart
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final storage = FlutterSecureStorage();
await storage.write(key: 'token', value: userToken);
```

### 3. HTTP Client Setup

```dart
import 'package:http/http.dart' as http;

Future<Map<String, dynamic>> getMessages() async {
  final token = await storage.read(key: 'token');
  
  final response = await http.get(
    Uri.parse('$baseUrl/support-chat/messages'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  return json.decode(response.body);
}
```

### 4. Badge Notification

For admin, poll the unread count endpoint:

```dart
Future<int> getUnreadCount() async {
  final token = await storage.read(key: 'admin_token');
  
  final response = await http.get(
    Uri.parse('$baseUrl/admin/support-chat/unread-count'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  final data = json.decode(response.body);
  return data['data']['unread_count'];
}
```

---

## Testing

Use the provided Postman collection (`Support_Chat_API.postman_collection.json`) to test all endpoints.

1. Import the collection into Postman
2. Set the `base_url` variable to your API URL
3. Set `user_token` and `admin_token` variables after logging in
4. Test each endpoint

---

## Support

For issues or questions, contact the development team.

