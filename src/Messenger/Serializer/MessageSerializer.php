<?php


namespace App\Messenger\Serializer;


use App\Exception\Request\JsonNotParsableException;
use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class MessageSerializer implements SerializerInterface
{
    public function __construct(private \Symfony\Component\Serializer\SerializerInterface $serializer)
    {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        try {
            $message = $this->serializer->deserialize($body,ArrayMessage::class,'json');
        } catch (ExceptionInterface $e) {
            throw new JsonNotParsableException();
        }

        $envelope = new Envelope($message);

        $stamps = [];
        if(isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }
        $envelope = $envelope->with(... $stamps);

        return $envelope;
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        $allStamps = [];
        foreach ($envelope->all() as $stamp) {
            $allStamps = array_merge($allStamps, $stamp);
        }

        return [
            'body' => $this->serializer->serialize($message,'json'),
            'headers' => [
                'stamps' => serialize($allStamps)
            ]
        ];
    }
}
