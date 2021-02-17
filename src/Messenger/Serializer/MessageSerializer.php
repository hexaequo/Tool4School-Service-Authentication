<?php


namespace App\Messenger\Serializer;


use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessageSerializer implements SerializerInterface
{
    public function __construct(private \Symfony\Component\Serializer\SerializerInterface $serializer)
    {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);

        if(!$data) throw new MessageDecodingFailedException('Invalid json');

        if(isset($data['id']) and isset($data['data'])) {
            $message = new ArrayMessage($data['id'],$data['data']);
        }
        else {
            $message = new ArrayMessage(uniqid(),$data);
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
