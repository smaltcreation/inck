<?php

namespace Inck\NotifBundle\Topic;

use JDare\ClankBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface as Conn;

class UserTopic implements TopicInterface
{
    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onSubscribe(Conn $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . " has joined " . $topic->getId());
        echo "subscribe to ".$topic->getId()."\n";
        var_dump($conn->Session->get('test'));
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onUnSubscribe(Conn $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . " has left " . $topic->getId());
    }


    /**
     * This will receive any Publish requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        /*
        $topic->getId() will contain the FULL requested uri, so you can proceed based on that

        e.g.

        if ($topic->getId() == "acme/channel/shout")
            //shout something to all subs.
        */


        $topic->broadcast(array(
            "sender" => $conn->resourceId,
            "topic" => $topic->getId(),
            "event" => $event
        ));
    }

}
