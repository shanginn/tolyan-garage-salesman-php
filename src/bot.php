<?php

require __DIR__ . '/../vendor/autoload.php';

use function React\Async\await;

use Shanginn\TelegramBotApiBindings\Types\Update;
use Shanginn\TelegramBotApiFramework\TelegramBot;
use Tolyan\Character\InteractionSchema;
use Tolyan\Character\InteractionTool;
use Tolyan\EchoLogger;
use Tolyan\MessagesStore\MessagesStore;
use Tolyan\Novita\Novita;
use Tolyan\Novita\Novita\NovitaClient;
use Tolyan\OneMessageAtOneTimeMiddleware;
use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolChoice;
use Tolyan\Openai\ChatCompletion\Message\Assistant\KnownFunctionCall;
use Tolyan\Openai\ChatCompletion\Message\AssistantMessage;
use Tolyan\Openai\ChatCompletion\Message\User\ImageContentPart;
use Tolyan\Openai\ChatCompletion\Message\User\ImageDetailLevelEnum;
use Tolyan\Openai\ChatCompletion\Message\User\TextContentPart;

use Tolyan\Openai\ChatCompletion\Message\UserMessage;

use Tolyan\Openai\Openai;
use Tolyan\Openai\Openai\OpenaiClient;

Dotenv\Dotenv::createImmutable(__DIR__)->load();

$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
assert(is_string($botToken), 'Bot token must be a string');

$openaiKey = $_ENV['OPENAI_API_KEY'];
assert(is_string($openaiKey), 'Openai API key must be a string');

$novitaKey = $_ENV['NOVITA_API_KEY'];
assert(is_string($novitaKey), 'Novita API key must be a string');

$novita = new Novita(
    new NovitaClient($novitaKey),
);

$bot = new TelegramBot($botToken, logger: new EchoLogger());

[
    'tolyan'                    => $tolyan,
    'systemPrompt'              => $systemPrompt,
    'finalSystemPromptTemplate' => $finalSystemPromptTemplate,
] = require __DIR__ . '/../config/config.php';

$oai = new Openai(
    new OpenaiClient($openaiKey),
);

$imageGenerator = new class($novita) {
    private Novita $novita;

    public function __construct(Novita $novita)
    {
        $this->novita = $novita;
    }

    public function generateImage(string $prompt): string
    {
        dump($prompt);

        return $this->novita->flux(
            prompt: $prompt,
            width: 512,
            height: 512,
        )->images[0]->imageUrl;
    }
};

$messages = new MessagesStore();

$echoHandler = function (Update $update, TelegramBot $bot) use (
    &$messages,
    $oai,
    $imageGenerator,
    $systemPrompt,
    $tolyan,
) {
    $chatId = $update->message->chat->id;
    await($bot->api->sendChatAction(
        chatId: $chatId,
        action: 'typing',
    ));

    // if message has images:
    if (isset($update->message->photo)) {
        // get last element: from $update->message->photo
        $photo   = end($update->message->photo);
        $file    = await($bot->api->getFile($photo->fileId));
        $fileUrl = 'https://api.telegram.org/file/bot' . $bot->getToken() . '/' . $file->filePath;

        $content = [
            new ImageContentPart(
                url: $fileUrl,
                detail: ImageDetailLevelEnum::LOW,
            ),
        ];

        if ($update->message->caption !== null) {
            $content[] = new TextContentPart($update->message->caption);
        }
    } else {
        $content = $update->message->text;
    }

    $messages->add(
        $chatId,
        new UserMessage($content)
    );

    $response = $oai->completion(
        system: $systemPrompt,
        messages: $messages->get($chatId)->toArray(),
        tools: [InteractionTool::class],
        toolChoice: ToolChoice::useTool(InteractionTool::class)
    );

    dump($response);

    if (count($response->choices) === 0) {
        await($bot->api->sendMessage(
            chatId: $chatId,
            text: 'Не могу ответить на ваш вопрос. Попробуйте переформулировать его.',
        ));

        return;
    }

    $choice = $response->choices[0];

    if ($choice instanceof KnownFunctionCall && $choice->arguments instanceof InteractionSchema) {
        await($bot->api->sendChatAction(
            chatId: $chatId,
            action: 'typing',
        ));

        $image = $imageGenerator->generateImage(
            <<<PROMPT
                Scene description: {$choice->arguments->sceneDescriptionEnglish}.
                Tolyan is {$tolyan->characterDescription}, {$tolyan->looksDescription}
                PROMPT
        );

        await($bot->api->sendPhoto(
            chatId: $chatId,
            photo: $image,
        ));

        await($bot->api->sendMessage(
            chatId: $chatId,
            text: $choice->arguments->speechAndActions
        ));

        // debug:
        await($bot->api->sendMessage(
            chatId: $chatId,
            text: <<<TEXT
                Internal monologue: {$choice->arguments->internalMonologue}
                
                Scene description: {$choice->arguments->sceneDescriptionEnglish}
                TEXT,
        ));

        $messages->add($chatId, new AssistantMessage(
            content: <<<TEXT
                Speech and actions: {$choice->arguments->speechAndActions}
                Internal monologue (hidden from the player): {$choice->arguments->internalMonologue}
                Scene description: {$choice->arguments->sceneDescriptionEnglish}
                TEXT,
        ));

        return;
    }

    await($bot->api->sendMessage(
        chatId: $chatId,
        text: $choice->message->content,
    ));

    $messages->add($chatId, $choice->message);
};

$bot->addHandler($echoHandler)
    ->middleware(new OneMessageAtOneTimeMiddleware())
    ->supports(fn (Update $update) => isset($update->message->text));

$pressedCtrlC     = false;
$gracefulShutdown = function (int $signal) use (&$pressedCtrlC): void {
    if ($pressedCtrlC) {
        echo "Shutting down now...\n";
        exit(0);
    }

    $keysCombination = match ($signal) {
        SIGINT  => 'Ctrl+C',
        SIGTERM => 'Ctrl+Break',
        default => 'unknown',
    };

    echo "\n{$keysCombination} pressed. Gracefully shutting down...\nPress it again to force shutdown.\n\n";

    $pressedCtrlC = true;

    exit(0);
};

pcntl_signal(SIGTERM, $gracefulShutdown);
pcntl_signal(SIGINT, $gracefulShutdown);

$bot->run();