<?php

declare(strict_types=1);

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Database\UserRepository;
use App\Services\TelegramNotificationService;
use App\DTOs\UserRegistrationDTO;

/**
 * Registration conversation: asks for name and programming language, then confirms registration.
 * @see https://botman.io/2.0/conversations
 */
class RegistrationConversation extends Conversation
{
    protected string $name;

    protected string $programmingLanguage;

    public function __construct(
        protected UserRepository|null $userRepository = null,
        protected TelegramNotificationService $notificationService
    ) {
    }

    public function run(): void
    {
        $this->askName();
    }

    public function askName(): void
    {
        $this->ask('What is your name Bro?', function (Answer $answer) {
            $this->name = $answer->getText();

            $this->say('Hello ' . $this->name . '!');
            $this->askProgrammingLanguage();
        });
    }

    public function askProgrammingLanguage(): void
    {
        $this->ask('Do you know any programming language?', function (Answer $answer) {
            $this->programmingLanguage = $answer->getText();

            // Save user to database
            try {
                // Create DTO from collected data
                $userData = new UserRegistrationDTO(
                    telegramId: (int)$this->bot->getUser()->getId(),
                    name: $this->name,
                    programmingLanguage: $this->programmingLanguage,
                );

                // Save user to database
                // $this->userRepository->saveUser($userData);

                // Send notification to Telegram channel
                $this->notificationService->sendRegistrationNotification($userData);

                $this->say('✅ Thank you, you have been registered successfully!');
            } catch (\Exception $e) {
                error_log('Registration Error: ' . $e->getMessage());
                $this->say('❌ Sorry, an error occurred during registration. Please try again later.');
            }
        });
    }
}
