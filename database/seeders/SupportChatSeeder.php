<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SupportChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create client role
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        
        // Get admin user (assuming admin with ID 1 exists)
        $admin = User::find(1);
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run DatabaseSeeder first.');
            return;
        }

        // Create test users if they don't exist
        $testUsers = [];
        
        for ($i = 2; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "testuser{$i}@example.com"],
                [
                    'first_name' => "Test",
                    'last_name' => "User {$i}",
                    'email' => "testuser{$i}@example.com",
                    'password' => Hash::make('password'),
                ]
            );
            
            if (!$user->hasRole('client')) {
                $user->assignRole($clientRole);
            }
            
            $testUsers[] = $user;
        }

        // Create chat rooms and messages for each test user
        $messages = [
            [
                'user_messages' => [
                    ['message' => 'Hello, I need help with my valuation request.', 'created_at' => now()->subDays(2)],
                    ['message' => 'I submitted a request yesterday but haven\'t received any update yet.', 'created_at' => now()->subDays(1)],
                    ['message' => 'Can someone please check my request? Reference: REFS123', 'created_at' => now()->subHours(5)],
                ],
                'admin_replies' => [
                    ['message' => 'Hello! Thank you for contacting us. Let me check your request.', 'created_at' => now()->subDays(1)->addHours(2)],
                    ['message' => 'I can see your request is currently being processed. You should receive an update within 24 hours.', 'created_at' => now()->subHours(4)],
                ],
                'unread' => true, // Has unread messages
            ],
            [
                'user_messages' => [
                    ['message' => 'Hi, I want to know about the pricing for property valuation.', 'created_at' => now()->subDays(3)],
                    ['message' => 'What documents do I need to submit?', 'created_at' => now()->subDays(2)],
                ],
                'admin_replies' => [
                    ['message' => 'Hello! The pricing depends on the property type and area. Could you please provide more details?', 'created_at' => now()->subDays(2)->addHours(3)],
                ],
                'unread' => false, // All messages read
            ],
            [
                'user_messages' => [
                    ['message' => 'I have a question about payment methods.', 'created_at' => now()->subHours(10)],
                    ['message' => 'Which payment gateways do you accept?', 'created_at' => now()->subHours(8)],
                ],
                'admin_replies' => [],
                'unread' => true, // No admin reply yet
            ],
            [
                'user_messages' => [
                    ['message' => 'Thank you for the excellent service!', 'created_at' => now()->subDays(5)],
                ],
                'admin_replies' => [
                    ['message' => 'You\'re very welcome! We\'re glad we could help.', 'created_at' => now()->subDays(4)],
                ],
                'unread' => false, // All messages read
            ],
            [
                'user_messages' => [
                    ['message' => 'I need to cancel my valuation request. How do I proceed?', 'created_at' => now()->subHours(2)],
                    ['message' => 'It\'s urgent!', 'created_at' => now()->subHours(1)],
                ],
                'admin_replies' => [],
                'unread' => true, // No admin reply yet
            ],
        ];

        foreach ($testUsers as $index => $user) {
            // Create or get chat room for user
            $room = ChatRoom::firstOrCreate(
                ['user_id' => $user->id],
                ['user_id' => $user->id]
            );

            $messageData = $messages[$index] ?? $messages[0];

            // Create user messages
            foreach ($messageData['user_messages'] as $msgData) {
                $isRead = false; // User messages start as unread
                
                // If there's an admin reply after this message, mark as read
                if (isset($messageData['admin_replies']) && count($messageData['admin_replies']) > 0) {
                    $firstAdminReplyTime = $messageData['admin_replies'][0]['created_at'];
                    if ($msgData['created_at'] < $firstAdminReplyTime) {
                        $isRead = true; // Admin has seen and replied
                    }
                }
                
                ChatMessage::create([
                    'chat_room_id' => $room->id,
                    'sender_id' => $user->id,
                    'message' => $msgData['message'],
                    'is_read' => $isRead,
                    'created_at' => $msgData['created_at'],
                    'updated_at' => $msgData['created_at'],
                ]);
            }

            // Create admin replies
            foreach ($messageData['admin_replies'] as $replyData) {
                ChatMessage::create([
                    'chat_room_id' => $room->id,
                    'sender_id' => $admin->id,
                    'message' => $replyData['message'],
                    'is_read' => true, // Admin messages are auto-read
                    'created_at' => $replyData['created_at'],
                    'updated_at' => $replyData['created_at'],
                ]);
            }

            // Update room's updated_at to reflect latest activity
            $latestMessage = ChatMessage::where('chat_room_id', $room->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($latestMessage) {
                $room->updated_at = $latestMessage->created_at;
                $room->save();
            }
        }

        $this->command->info('Support chat seeder completed successfully!');
        $this->command->info('Created chat rooms and messages for ' . count($testUsers) . ' test users.');
    }
}

