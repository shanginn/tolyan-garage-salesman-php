<?php

declare(strict_types=1);

namespace Tolyan\Openai;

use Throwable;
use Tolyan\Openai\ChatCompletion\CompletionRequest;
use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolChoice;
use Tolyan\Openai\ChatCompletion\CompletionRequest\ToolInterface;
use Tolyan\Openai\ChatCompletion\CompletionResponse;
use Tolyan\Openai\ChatCompletion\Message\Assistant\KnownFunctionCall;
use Tolyan\Openai\ChatCompletion\Message\Assistant\UnknownFunctionCall;
use Tolyan\Openai\ChatCompletion\Message\AssistantMessage;
use Tolyan\Openai\ChatCompletion\Message\MessageInterface;
use Tolyan\Openai\ChatCompletion\Message\SystemMessage;
use Tolyan\Openai\Openai\OpenaiClientInterface;
use Tolyan\Openai\Openai\OpenaiSerializer;
use Tolyan\Openai\Openai\OpenaiSerializerInterface;

final readonly class Openai
{
    private OpenaiSerializerInterface $serializer;

    public function __construct(
        private OpenaiClientInterface $client,
        private string $model = 'gpt-4o-mini',
    ) {
        $this->serializer = new OpenaiSerializer();
    }

    /**
     * Sends a message to the model and retrieves the response.
     *
     * @param array<MessageInterface>                 $messages    array of input messages
     * @param ?string                                 $system      the system message to send to the model
     * @param ?float                                  $temperature What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random
     * @param int                                     $maxTokens   the maximum number of tokens to generate before stopping
     * @param ToolChoice|null                         $toolChoice  specifies how the model should use the provided tools
     * @param array<class-string<ToolInterface>>|null $tools       definitions and descriptions of tools that the model may use during the response generation
     *
     * @return CompletionResponse
     */
    public function completion(
        array $messages,
        ?string $system = null,
        ?float $temperature = 0.0,
        int $maxTokens = 1024,
        ?ToolChoice $toolChoice = null,
        ?array $tools = null,
    ): CompletionResponse {
        $body = $this->serializer->serialize(new CompletionRequest(
            model: $this->model,
            messages: array_merge(
                [new SystemMessage($system)],
                $messages
            ),
            maxTokens: $maxTokens,
            temperature: $temperature,
            toolChoice: $toolChoice,
            tools: $tools,
        ));

        $responseJson = $this->client->sendRequest('/chat/completions', $body);

        /** @var CompletionResponse $response */
        $response = $this->serializer->deserialize($responseJson, CompletionResponse::class);

        /** @var array<string,class-string<ToolInterface>> $toolsMap */
        $toolsMap = array_merge(...array_map(
            fn (string $tool) => [$tool::getName() => $tool],
            $tools
        ));

        foreach ($response->choices as $i => $choice) {
            if ($choice->message instanceof AssistantMessage && count($choice->message->toolCalls ?? []) > 0) {
                foreach ($choice->message->toolCalls as $calledTool) {
                    if (!$calledTool instanceof UnknownFunctionCall || !isset($toolsMap[$calledTool->name])) {
                        continue;
                    }

                    $tool = $toolsMap[$calledTool->name];

                    try {
                        $toolInput = $this->serializer->deserialize(
                            serialized: $calledTool->arguments,
                            to: $tool::getSchemaClass(),
                        );
                    } catch (Throwable $e) {
                        dump($e);

                        continue;
                    }

                    $response->choices[$i] = new KnownFunctionCall(
                        id: $calledTool->id,
                        tool: $tool,
                        arguments: $toolInput,
                    );
                }
            }
        }

        return $response;
    }
}
